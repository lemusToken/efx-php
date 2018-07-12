<?php
namespace Efx\Router;

/**
 * 路由数据解析
 * Class Parse
 * @package Efx\Router
 * @author xule
 */
class Parse {

    private $rule = [];
    /**
     * @var \Efx\Router\Util\Match
     */
    private $match;
    /**
     * @var \Efx\Util\Url
     */
    private $url;

    /**
     * 构造函数
     * @param string $match 通过IOC注入的依赖，生成单例
     * @param string $url 通过IOC注入的依赖，生成静态方法调用闭包
     */
    public function __construct($match='ioc-static-make:\Efx\Router\Util\Match@loadInst',$url = 'ioc-static:\Efx\Util\Url') {
        $this->match = $match;
        $this->url = $url;
        $this->addItemRule('string_params',function($val) use($url) {
            if (strpos($val,'&')!==false||strpos($val,'=')!==false) {
                //调用Url中的静态方法params
                $result = $url('params',[$val]);
                $url = null;
                return $result;
            }
            return [];
        });
        $this->addItemRule('string_route',function($val){
            $result = [];
            if (strpos($val,'@')!==false) {
                $temp = explode('@',$val);
                $result[0] = $temp[0];
                $result[1] = $temp[1];
            }
            return $result;
        });
    }

    /**
     * 匹配字符串并返回数据解析后的结果
     * @param $val
     * @return array|bool|mixed
     */
    public function parseItem($val) {
        $match = $this->match;
        $matchData = $match->match($val);
        if (!$matchData['hit']) return false;
        $result = $this->parseItemVal($matchData['input']);
        foreach ($result['router'] as &$v) {
            if ($v{0}===':') {
                $v = str_replace(array_keys($matchData['match'][':']),array_values($matchData['match'][':']),substr($v,1));
            }
            else if ($v{0}==='$') {
                $t = [];
                foreach ($matchData['match']['$'] as $kk=>$vv) {
                    $t[] = '$'.($kk+1);
                }
                $v = str_replace($t,array_values($matchData['match']['$']),substr($v,1));
            }
        }
        foreach ($result['params'] as &$v) {
            if ($v{0}===':') {
                $v = str_replace(array_keys($matchData['match'][':']),array_values($matchData['match'][':']),$v);
            }
            else if ($v{0}==='$') {
                $t = [];
                foreach ($matchData['match']['$'] as $kk=>$vv) {
                    $t[] = '$'.($kk+1);
                }
                $v = str_replace($t,array_values($matchData['match']['$']),$v);
            }
        }
        return $result;
    }

    /**
     * 添加解析规则
     * @param $name
     * @param callable $fn
     */
    public function addItemRule($name,callable $fn) {
        $this->rule[$name] = $fn;
    }

    /**
     * 触发规则
     * @param $name
     * @param $val
     * @return null|mixed
     */
    public function fireRule($name,$val) {
        if (isset($this->rule[$name])&&is_callable($this->rule[$name])) {
            return $this->rule[$name]($val);
        }
        return null;
    }

    /**
     * 解析路由匹配后的结果
     * 解析正则$,符号:
     * @param string|array $val
     * '查询语句'或者'类路由'
     * ['查询语句(key=val&key=val...)','类路由(class@ act)','请求方式(get、post、request等)','continue(是否继续匹配)'],顺序不限
     * @return array|mixed
     */
    public function parseItemVal($val) {
        $result = [
            'method'=>[],
            'params'=>[],
            'router'=>[],
            'continue'=>false
        ];
        if (is_string($val)) {
            if ($val==='continue') {
                $result['continue'] = true;
            }
            else if ($result['params'] = $this->fireRule('string_params',$val)) {
            }
            else if ($result['router'] = $this->fireRule('string_route',$val)) {
            }
            else {
                $result['method'] = explode(',',$val);
            }
        }
        else if (is_array($val)) {
            $r = [];
            foreach ($val as $k=>$v) {
                $r[] = $this->parseItemVal($v);
            }
            $result = call_user_func_array([$this,'merge'],$r);
        }
        return $result;
    }

    public function merge() {
        $list = func_get_args();
        $n = count($list);
        $result = [];
        if ($n<=2) {
            return $this->mergeData($list[0],isset($list[1])?$list[1]:null);
        }
        else {
            $result = $this->mergeData($list[0],$list[1]);
            for ($i=2;$i<$n;$i++) {
                $result = $this->mergeData($result,$list[$i]);
            }
        }
        return $result;
    }

    public function mergeData($r1,$r2) {
        if (empty($r2)) return $r1;
        foreach ($r1 as $k=>&$v) {
            if (is_array($v)&&is_array($r2[$k])) {
                $v = array_merge($v,$r2[$k]);
            }
            else {
                $v = $r2[$k];
            }
        }
        $r1['method'] = array_unique($r1['method']);
        return $r1;
    }
}