<?php

namespace Efx\Util\Singleton;

use Efx\Di\Ioc;

/**
 * 单例trait，如果ioc类存在，则使用ioc生成实例
 * Class InstanceTrait
 * @package Efx\Util\Singleton
 */
trait InstanceTrait {
    //缓存实例
    private static $_instance=null;

    /**
     * 单例模式加载实例
     * @return $this
     */
    public static function loadInst() {
        $class = __CLASS__;
        $inst = self::$_instance?:null;
        $arguments = func_get_args();
        if (empty($inst)) {
            if (empty($arguments)) {
                $inst = self::$_instance = call_user_func([$class,'createInst']);
            }
            else {
                $inst = self::$_instance = call_user_func_array([$class,'createInst'],$arguments);
            }
        }
        return $inst;
    }

    /**
     * 生成实例
     * @return $this
     */
    public static function createInst() {
        $class = __CLASS__;
        // 如果有ioc控制类，则直接使用ioc生成实例
        if (is_callable(['\Efx\Di\Ioc','create'])) {
            return Ioc::create($class);
        }

        $arguments = func_get_args();
        $inst = null;
        if (empty($arguments)) {
            $inst = new self;
        }
        else {
            $reflection = new \ReflectionClass($class);
            if ($reflection->getConstructor()) {
                $inst = $reflection->newInstanceArgs($arguments);
            }
            else {
                $inst = $reflection->newInstanceWithoutConstructor();
            }
        }
        return $inst;
    }

    public static function clearInst() {
        self::$_instance = null;
    }
}