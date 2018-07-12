<?php
namespace Efx\Autoload;

use Composer\Autoload\ClassLoader;

/**
 * 基于composer的自动加载类(代理)
 * Class Load
 */
class Load {

    //单例
    use \Efx\Util\Singleton\InstanceTrait;
    private $loader;
    private $registered=false;

    public function __construct() {
        $this->loader = new ClassLoader;
    }

    public function psr4($map) {
        if (!empty($map)) {
            foreach ($map as $namespace => $path) {
                $this->loader->setPsr4($namespace, $path);
            };
            $this->register();
        }
        return $this;
    }

    public function psr0($map) {
        if (!empty($map)) {
            foreach ($map as $namespace => $path) {
                $this->loader->set($namespace, $path);
            }
            $this->register();
        }
        return $this;
    }

    public function files($maps) {
        if (is_array($maps)) {
            foreach ($maps as $v) {
                include_once $v;
            }
        }
        else{
            include_once $maps;
        }
        return $this;
    }

    private function register() {
        if ($this->registered) return true;
        $this->loader->register(true);
        $this->registered = true;
        return true;
    }
}