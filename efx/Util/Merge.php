<?php

namespace Efx\Util;

/**
 * 数组合并
 * Class Merge
 * @package Efx\Util
 */
class Merge {

    /**
     * 合并数组
     * 合并数组中相同的下标数据，深度合并时，会合并所有维度
     * @params ...array 可变数组参数，第一个参数如果是bool，表示是否深度合并
     * @return array|mixed
     */
    public static function combineAll() {
        $args = func_get_args();
        $result = [];
        $deep = false;
        if (is_bool($args[0])) {
            $deep = $args[0];
        }
        else {
            $result = $args[0];
        }
        for ($i=1;$i<count($args);$i++) {
            $result = self::combine($result,$args[$i],$deep);
        }
        return $result;
    }

    /**
     * 合并输入的两个数组
     * @param array $a1 数组1
     * @param array $a2 数组2
     * @param bool $deep 深度合并
     * @return mixed
     */
    public static function combine($a1,$a2,$deep=false) {
        if (empty($a1)) return $a2;
        if (empty($a2)) return $a1;
        if (!is_array($a1)||!is_array($a2)) return $a2;
        $tmp = [];
        foreach ($a1 as $k=>$v) {
            foreach ($a2 as $kk=>$vv) {
                if ($k===$kk) {
                    if ($deep&&is_array($v)&&is_array($vv)) {
                        $a1[$k] = self::combine($v,$vv);
                    }
                    else {
                        $a1[$k] = $a2[$kk];
                    }
                }
                else if (!isset($a1[$kk])&&!in_array($kk,$tmp)) {
                    $tmp[] = $kk;
                    $a1[$kk] = $vv;
                }
            }
        }
        return $a1;
    }
}