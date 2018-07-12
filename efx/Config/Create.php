<?php

namespace Efx\Config;

class Create {

    private $config=[];
    /**
     * @var \Efx\Util\Merge
     */
    private $merge;
    private static $instance=[];

    public function __construct($merge='ioc-static:\Efx\Util\Merge') {
        $this->merge = $merge;
    }

    /**
     * 设置对应配置
     * @param $name
     * @param $val
     * @return $this
     */
    public function set($name,$val) {
        $this->config[$name] = $val;
        return $this;
    }

    /**
     * 设置完整配置
     * @param $val
     * @return $this
     */
    public function setAll($val) {
        $this->config = $val;
        return $this;
    }

    /**
     * 获取配置项
     * @param string $name 配置名
     * @return array|mixed|null
     */
    public function get($name='') {
        return empty($name)?$this->config:(isset($this->config[$name])?$this->config[$name]:null);
    }

    /**
     * 添加配置项
     * 添加同样的配置相当于直接添加数组
     * 数组会直接合并
     * @param string $name 配置名
     * @param $val
     * @param bool $deep 是否深度合并
     * @return $this
     */
    public function add($name,$val,$deep=false) {
        $merge = $this->merge;
        $data = $this->get($name);
        //如果原先没有值，则先赋值
        if (empty($data)) {
            $this->set($name,$val);
            $data = $this->get($name);
        }
        //如果原先有值，但不是数组，则转成数组
        else if (!is_array($data)) {
            $data = [$data];
        }
        //如果原先有值，并且是数组，当输入的值不是数组，则直接添加
        if (is_array($data)&&!is_array($val)) {
            $data[] = $val;
            $this->config[$name] = $data;
        }
        //数组合并
        else {
            $this->config[$name] = $merge('combineAll',[$deep,$data,$val]);
        }
        return $this;
    }

    public function setName($name) {
        self::$instance[$name] = $this;
        return $this;
    }

    public function getArray($name='') {
        $merge = $this->merge;
        $config = $this->get($name);
        $result = [];
        foreach ($config as $k=>$v) {
            $result = $merge('combineAll',[true,$result,self::path2tree($k,$v)]);
        }
        return $result;
    }

    public static function load($name='_') {
        if (!isset(self::$instance[$name])) {
            self::$instance[$name] = \Efx\Di\Ioc::create(__CLASS__);
        }
        return self::$instance[$name];
    }

    /**
     * 字符串转数组
     * @param $str
     * @param $val
     * @param string $delimiter 分隔符
     * @return array
     */
    public static function path2tree($str,$val,$delimiter='.'){
        $split = explode($delimiter,$str);
        $len = count($split);
        //初始化赋值
        $result = [
            $split[$len-1]=>$val
        ];

        //倒序逐一赋值增加数组维数
        for($i=$len-2;$i>=0;$i--) {
            $result[$split[$i]] = $result;
            unset($result[$split[$i+1]]);
        }
        return $result;
    }


}