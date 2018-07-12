<?php

namespace Efx\Di;

/**
 * 解析ioc字符串
 * Class ParseInput
 * @package Efx\Di
 * @author xule
 */
class ParseInput {

    //生成类实例
    const IOC = 'ioc';
    //生成类静态方法调用闭包
    const IOC_STATIC = 'ioc-static';
    //调用类实例方法
    const IOC_MAKE = 'ioc-make';
    //调用类静态方法
    const IOC_STATIC_MAKE = 'ioc-static-make';
    //类中的方法参数
    const IOC_ARGS = 'ioc-args';

    public static function parse($str) {
        if ($result = self::parseClass($str)) {
        }
        else if ($result = self::parseClassMethod($str)) {
        }
        return $result;
    }

    public static function parseClass($str) {
        if ($result = self::_parse(self::IOC,$str)) {
        }
        else if ($result = self::_parse(self::IOC_STATIC,$str)) {
        }
        return $result;
    }

    public static function parseClassMethod($str) {
        if ($result = self::_parse(self::IOC_MAKE,$str)) {
            $result[1] = explode('@',$result[1]);
        }
        else if ($result = self::_parse(self::IOC_STATIC_MAKE,$str)) {
            $result[1] = explode('@',$result[1]);
        }
        else if ($result = self::_parse(self::IOC_ARGS,$str)) {
            $result[1] = explode('@',$result[1]);
        }
        return $result;
    }

    private static function _parse($type,$str) {
        if (!is_string($str)) return false;
        $s = $type.':';
        $len = strlen($s);
        if (substr($str,0,$len)===$s) {
            return [$type,substr($str,$len)];
        }
        return false;
    }
}