<?php

namespace Efx\Router\Util;

/**
 * 字符串匹配
 * 对外输出类
 * Class Match
 * @package Efx\Router\Util
 * @author xule
 */
class Match {

    //单例
    use \Efx\Util\Singleton\InstanceTrait;

    private $map;
    private $fullMatch;
    private $diffMatch;
    private $delimiter;

    public function __construct() {
        $this->setDelimiter('/');
        //全字匹配
        $this->fullMatch = new FullMatch();
        //差异匹配
        $this->diffMatch = new DiffMatch();
        $this->fullMatch->setDelimiter($this->delimiter);
        $this->diffMatch->setDelimiter($this->delimiter);
    }

    public function setDelimiter($char='/') {
        $this->delimiter = $char;
    }

    /**
     * 输入格式化
     * @param $str
     * @return string
     */
    public function input($str) {
        return trim($str,$this->delimiter);
    }

    /**
     * 设置规则级
     * @param $map
     * @return $this
     */
    public function setMap($map) {
        $result = [];
        foreach ($map as $k=>$v) {
            $result[trim($k,$this->delimiter)] = $v;
        }
        $this->map = $result;
        return $this;
    }

    /**
     * 规则级匹配
     * @param $input
     * @return array
     */
    public function match($input) {
        $input = $this->input($input);
        $map = $this->map;
        if (empty($this->map)) return [];
        //1.全字符串匹配
        $result = $this->fullMatch->match($input,$map);
        if (!$result['hit']) {
            //2.差异匹配
            $result = $this->diffMatch->match($input,$map);
        }
        return $result;
    }
}