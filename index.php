<?php

define('ROOT_PATH',__DIR__);

//类自动加载
require_once ROOT_PATH.'/vendor/autoload.php';

\Efx\Autoload\Load::loadInst()->psr4([
    'App\\'=>'app'
]);

//$inst = \Efx\Config\Create::load();
//$inst->add('a.b',1);
//$inst->add('a.b.c',1);
//$inst->add('a.c',2);
//$inst->add('a.d',3);
//$inst->add('a.e.t',1);
//$inst->add('c.e.t.p',1);
//print_r($inst->getArray());
//die;

//路由配置
$config = [
    //端口5051，代表host:5051
    '::5051'=>[
        '/a'=>'App\Pc\A@index'
    ],
    //域名包括端口
    'localhost:5052'=>[
        '/a'=>'App\Wap\A@index',
        '/a/:id'=>'App\Wap\A@index1',
    ],
    //默认地址
    '::1'=>[
        //url
        '/a'=>'app=a&act=index',
        '/a/:b/(.+)'=>['i=$1&h=1','Efx\Pc\A@:b','get'],
        //url method
        '/a2'=>['app=a&act=index','get'],
        //url router method
        '/a3'=>['app=a1&act=index','Efx\Pc\A@index','get'],
        '/a4'=>['app=a2&act=index','Efx\Pc\A@index','get','continue'],
    ]
];

$router = \Efx\Router\Router::create($config);
$result = $router->run();
var_dump($result);die;