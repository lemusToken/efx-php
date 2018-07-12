<?php

namespace Efx\Router;

use Efx\Di\Ioc;

/**
 * 路由类
 * 对外输出类
 * Class Router
 * @package Efx\Router
 * @author xule
 */
class Router {

    //单例
    use \Efx\Util\Singleton\InstanceTrait;

    /**
     * @var Parse
     */
    private $parse;
    /**
     * @var \Efx\Util\Url
     */
    private $url;
    /**
     * @var \Efx\Router\Util\Match
     */
    private $match;
    private $config;

    /**
     * 构造函数
     * @param \Efx\Router\Parse $parse
     * @param string $match 通过IOC注入
     * @param string $url 通过IOC注入
     */
    public function __construct(Parse $parse,$url = 'ioc-static:\Efx\Util\Url',$match='ioc-static-make:\Efx\Router\Util\Match@loadInst') {
        $this->parse = $parse;
        $this->url = $url;
        $this->match = $match;
    }

    /**
     * 设置配置
     * @param $config
     */
    public function setConfig($config) {
        $this->config = $config;
    }

    /**
     * 获取配置
     * @return mixed
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * 匹配路由
     * @param string $path 路径
     * @param array $config 临时配置
     * @return array|false
     */
    public function run($path='',$config=[]) {
        $url = $this->url;
        $match = $this->match;
        $parse = $this->parse;
        $config = $config?:$this->getConfig();
        $pathData = $url('parse',$path);
        $map = $this->parseConfig($pathData,$config);
        $match->setMap($map);
        $result = $parse->parseItem($pathData['path']);
        return $result;
    }

    /**
     * @param array|null $config
     * @return $this
     */
    public static function create($config=null) {
        $inst = Ioc::makeWith('ioc-static-make:\Efx\Router\Router@loadInst');
        $config&&$inst->setConfig($config);
        return $inst;
    }

    /**
     * 解析配置
     * @param $data
     * @param $config
     * @return array
     */
    private function parseConfig($data,$config) {
        $full = $data['fullhost'];
        $port = $data['port'];

        $map = [];
        if (!empty($config['::'.$port])) {
            $map = $config['::'.$port];
        }
        else if (!empty($config[$full])) {
            $map = $config[$full];
        }
        else {
            $map = $config['::1'];
        }
        return $map;
    }
}