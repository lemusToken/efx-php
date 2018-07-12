<?php

namespace Efx\Router\Util;

/**
 * 字符串差异比较
 * Class DiffMatch
 * @package Efx\Router\Util
 * @author xule
 */
class DiffMatch extends IMatch {

    /**
     * 依次匹配规则
     * @param string $input 输入的字符串
     * @param array $map 规则数组
     * @return array
     */
    public function match($input,array $map) {
        $result = [
            'match'=>[],
            'rule'=>'',
            'input'=>'',
            'hit'=>false,
        ];
        foreach ($map as $k=>$v) {
            $result = $this->matchOne($input,$k);
            if (!empty($result['hit'])) {
                $result['rule'] = $k;
                $result['input'] = $v;
                break;
            }
        }

        return $result;
    }

    /**
     * 差异匹配
     * @param string $input 输入字符串
     * @param string $rule 规则
     * @return array
     */
    private function matchOne($input,$rule) {
        $result = [
            'match'=>[
                ':'=>[],
                '$'=>[]
            ],
            //匹配命中
            'hit'=>true
        ];
        $diff = $this->diffStrBlock($input,$rule);
        foreach ($diff as $kk=>$vv) {
            $temp = [];
            if (empty($vv[0]) || empty($vv[1])) {
                $result['hit'] = false;
                break;
            } else if ($temp=$this->colonMatch($vv[0],$vv[1])) {
                $result['match'][':'][$temp[0]] = $temp[1];
            } else if ($temp=$this->colonMatch($vv[1],$vv[0])) {
                $result['match'][':'][$temp[0]] = $temp[1];
            } else if ($temp=$this->regMatch($vv[0],$vv[1])) {
                $result['match']['$'] = array_merge($result['match']['$'],$temp);
            } else if ($temp=$this->regMatch($vv[1],$vv[0])) {
                $result['match']['$'] = array_merge($result['match']['$'],$temp);
            } else if ($vv[0] !== $vv[1]) {
                $result['hit'] = false;
                break;
            }
        }
        return $result;
    }

    /**
     * 符号匹配
     * @param string $str1 带有符号的字符串
     * @param string $str2 普通字符串
     * @return array|bool
     */
    private function colonMatch($str1,$str2) {
        if ($str1{0} === ':'&&strpos($str1,$this->delimiter)===false) {
            return [substr($str1, 1),$str2];
        }
        return false;
    }


    /**
     * 正则匹配
     * @param string $str1 带有正则的字符串
     * @param string $str2 普通字符串
     * @return array|bool
     */
    private function regMatch($str1,$str2) {
        if (self::isRegular($str1)) {
            $result = [];
            preg_match('~^' . $str1 . '$~', $str2, $m);
            if (!empty($m)) {
                for ($k = 1; $k <= count($m) - 1; $k++) {
                    $result[] = $m[$k];
                }
                return $result;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * 判断是否是正则
     * @param $str
     * @return bool
     */
    private static function isRegular($str) {
        return strpos($str,'*')!==false||strpos($str,'?')!==false||strpos($str,'(')!==false||strpos($str,')')!==false||
            strpos($str,'{')!==false||strpos($str,'}')!==false||strpos($str,'+')!==false||strpos($str,'[')!==false||
            strpos($str,']')!==false;
    }
}