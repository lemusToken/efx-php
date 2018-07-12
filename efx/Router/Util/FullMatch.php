<?php

namespace Efx\Router\Util;

/**
 * 字符串全字匹配
 * Class FullMatch
 * @package Efx\Router\Util
 * @author xule
 */
class FullMatch extends IMatch {

    /**
     * 全字符串匹配
     * @param $input
     * @param array $map
     * @return array
     */
    public function match($input,array $map) {
        $result = [
            'match'=>[],
            'rule'=>'',
            'input'=>'',
            //匹配未命中
            'hit'=>false
        ];
        if (isset($map[$input])) {
            $result['rule'] = $input;
            $result['input'] = $map[$input];
            $result['hit'] = true;
        }
        return $result;
    }
}