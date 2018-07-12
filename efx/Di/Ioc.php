<?php

namespace Efx\Di;

/**
 * 依赖注入和反转
 * 自动化依赖管理
 * Class Ioc
 * @package Efx\Di
 * @author xule
 */
class Ioc {

    /**
     * 生成自动依赖(依赖反转)的实例，参数由所传类的构造函数形参控制
     * 构造函数参数不可由外部传入
     * @param $className
     * @return object
     */
    public static function create($className) {
        return (new \ReflectionClass($className))->newInstanceArgs(self::getArgs($className));
    }

    /**
     * 生成自动依赖(依赖反转)的实例，除第一个参数外，其余参数相当于对应位置的构造函数的形参
     * 构造函数参数可外部传入
     * @param $className
     * @param array $params 外部参数，['方法形参名'=>'值']
     * @return object
     */
    public static function createWithArgs($className, $params=[]) {
        return (new \ReflectionClass($className))->newInstanceArgs(self::getArgs($className, '__construct', false, $params));
    }

    /**
     * 生成静态方法调用的闭包函数
     * @param $className
     * @return \Closure
     */
    public static function createStatic($className) {
        /**
         * 调用方法
         * @params string $name 方法名
         * @params array $args 外部参数，['方法形参名'=>'值']
         * @return mixed
         */
        return function($name, $args) use($className){
            $reflection = new \ReflectionMethod($className,$name);
            return $reflection->invokeArgs(null,self::getArgs($className, $name, true, $args));
        };
    }

    /**
     * 类中的方法依赖注入和反转
     * @param object|string $className 类实例或者类名，如果类实例直接调用
     * @param string $methodName 方法名
     * @param array $params 参数 外部参数，['方法形参名'=>'值']
     * @return mixed
     */
    public static function make($className, $methodName, $params = []) {
        if (is_object($className)) {
            $instance = $className;
        }
        else {
            $instance = self::create($className);
            $params = self::getArgs($className, $methodName, false, $params);
        }
        return call_user_func_array([$instance,$methodName],$params);
    }

    /**
     * 类的静态方法依赖注入和反转
     * @param string $className
     * @param string $methodName
     * @return mixed
     */
    public static function makeStatic($className, $methodName, $params = []) {
        $static = self::createStatic($className);
        $result = $static($methodName,$params);
        $static = null;
        return $result;
    }

    /**
     * 根据输入字符串选择不同反转情景
     * 具体见ParseInput中的常量
     * @param string $str 输入字符串
     * @param array $args 外部参数，['方法形参名'=>'值']或者['数字下标'=>'值']；
     * 当指定形参名时会覆盖方法中的对应形参值，当指定数字下标时会覆盖方法中的对应位置的形参值
     * @return mixed
     */
    public static function makeWith($str,$args=[]) {
        $data = ParseInput::parse($str);
        $className = '';
        $method = '';
        if (is_string($data[1])) {
            $className = $data[1];
        }
        else if (is_array($data[1])) {
            $className = $data[1][0];
            $method = $data[1][1];
        }
        $result = [];
        switch ($data[0]) {
            case 'ioc':
                if (!empty($args)) {
                    $result = self::createWithArgs($className, $args);
                }
                else {
                    $result = self::create($className);
                }
                break;
            case 'ioc-static':
                $result = self::createStatic($className);
                break;
            case 'ioc-make':
                $result = self::make($className,$method,$args);
                break;
            case 'ioc-static-make':
                $result = self::makeStatic($className,$method,$args);
                break;
        }
        return $result;
    }

    /**
     * 获取类的指定方法的参数
     * 如果参数是类则实例化，如果参数是带有class:的字符串则直接生成静态类调用的闭包函数
     * 核心方法
     * @param string $className 类名
     * @param string $methodName 方法名
     * @param bool $isStatic 是否是静态方法
     * @param array $args 外部参数，['方法形参名'=>'值']或者['数字下标'=>'值']
     * @return array
     */
    protected static function getArgs($className, $methodName='__construct', $isStatic=false, $args=[]) {
        $paramsList = [];
        $params = [];

        if (!$isStatic) {
            $reflection = new \ReflectionClass($className);
            // 判断该类是否有该方法
            if ($reflection->hasMethod($methodName)) {
                // 获取方法
                $method = $reflection->getMethod($methodName);
                // 获得方法中带有的形参
                $params = $method->getParameters();
            }
        }
        else {
            $reflection = new \ReflectionMethod($className,$methodName);
            $params = $reflection->getParameters();
        }

        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                $name = $val->getName();
                // 如果外部参数存在，则覆盖对应位置的形参数据
                if (isset($args[$key])) {
                    $paramsList[$key] = $args[$key];
                }
                // 如果外部参数存在，则覆盖原有形参默认值
                else if (isset($args[$name])) {
                    $paramsList[$key] = $args[$name];
                }
                // 如果是可选参数
                else if ($val->isOptional()) {
                    $d = $val->getDefaultValue();
                    if ($v=self::makeWith($d)) {
                        $paramsList[] = $v;
                        $v = null;
                    }
                    else {
                        $paramsList[] = $d;
                    }
                }
                // 如果是类，获取类
                else if ($class = $val->getClass()) {
                    // 如果是接口或者抽象类直接跳过
                    // 注意，需要外部传入对象
                    if ($class->isInterface()||$class->isAbstract()) {
                        continue;
                    }
                    // 获取类名
                    $className = $class->getName();
                    // 递归上述过程并带参数实例化
                    $paramsList[] = self::create($className);
                }
            }
        }

        $lenArgs = count($args);
        $lenParams = count($paramsList);
        if ($lenArgs>$lenParams) {
            for ($i=$lenParams;$i<$lenArgs;$i++) {
                if (!isset($args[$i])) continue;
                $paramsList[] = $args[$i];
            }
        }

        $reflection = null;
        return $paramsList;
    }
}