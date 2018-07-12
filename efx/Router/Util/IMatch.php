<?php

namespace Efx\Router\Util;

/**
 * 字符串匹配接口
 * Class IMatch
 * @package Efx\Router\Util
 * @author xule
 */
abstract class IMatch {

    protected $delimiter;

    abstract public function match($input,array $map);

    /**
     * 设置分割字符
     * @param string $char
     */
    public function setDelimiter($char='/') {
        $this->delimiter = $char;
    }

    /**
     * 核心方法
     * 查找差别
     * 查找字符串各分隔符之间块的区别
     * @param string $str1 字符串1
     * @param string $str2 字符串2
     * @param string $delimiter 分隔符
     * @return array
     */
    protected function diffStrBlock($str1,$str2,$delimiter='/') {
        $delimiter = $delimiter?:$this->delimiter;
        $diff = [];
        //str1的字符串偏移值
        $str1Offset = 0;
        //str2的字符串偏移值
        $str2Offset = 0;
        //str1的临时字符串
        $diffStr1 = '';
        //str2的临时字符串
        $diffStr2 = '';
        //str1的块结束标志
        $isEnd1 = false;
        //str2的块结束标志
        $isEnd2 = false;
        //str1分隔字符的位置
        $delimiterIndex1 = 0;
        //str1分隔符的个数
        $delimiterCount1 = 0;
        //str2分隔字符的位置
        $delimiterIndex2 = 0;
        //str2分隔符的个数
        $delimiterCount2 = 0;

        while(1) {
            $s1 = isset($str1{$str1Offset})?$str1{$str1Offset}:null;
            $s2 = isset($str2{$str2Offset})?$str2{$str2Offset}:null;

            //当当前字符是分隔符，或者是字符串末尾
            if ($s1===$delimiter) {
                if ($delimiterIndex1!==$str1Offset) {
                    $delimiterCount1 += 1;
                }
                $delimiterIndex1 = $str1Offset;
                $isEnd1 = true;
            }
            else if (!$s1) {
                $isEnd1 = true;
            }
            else {
                $diffStr1 .= $s1;
                $str1Offset += 1;
            }

            if ($s2===$delimiter) {
                if ($delimiterIndex2!==$str2Offset) {
                    $delimiterCount2 += 1;
                }
                $delimiterIndex2 = $str2Offset;
                $isEnd2 = true;
            }
            else if (!$s2) {
                $isEnd2 = true;
            }
            else {
                $diffStr2 .= $s2;
                $str2Offset += 1;
            }

            //只有当两个字符串对应块都取出来后
            if ($isEnd1&&$isEnd2) {
                $isEnd1 = false;
                $isEnd2 = false;
                $diff[0][] = $diffStr1;
                $diff[1][] = $diffStr2;
                $diffStr1 = '';
                $diffStr2 = '';
                $str1Offset += 1;
                $str2Offset += 1;
            }

            if (!isset($str1{$str1Offset-1})&&!isset($str2{$str2Offset-1})) {
                break;
            }
        }

        $result = [];
        if ($delimiterCount1===$delimiterCount2) {
            foreach ($diff[0] as $k=>$v) {
                if ($v===$diff[1][$k]) continue;
                $result[] = [$v,$diff[1][$k]];
            }
        }
        else {
            $start = min($delimiterCount1,$delimiterCount2);
            $t = [];

            foreach ($diff[0] as $k=>$v) {
                if ($v===$diff[1][$k]) continue;
                if ($k>=$start) {
                    if (!empty($v)) {
                        $t[0][] = $v;
                    }
                    else if (empty($diff[1][$k])) {
                        $t[0] = [];
                    }
                    if (!empty($diff[1][$k])) {
                        $t[1][] = $diff[1][$k];
                    }
                    else if (empty($diff[0][$k])) {
                        $t[1] = [];
                    }
                }
                else {
                    $result[] = [$v,$diff[1][$k]];
                }
            }

            if (!empty($t)) {
                $result[] = [empty($t[0])?'':implode($delimiter,$t[0]),empty($t[1])?'':implode($delimiter,$t[1])];
            }

            $t = null;
        }

        return $result;
    }
}