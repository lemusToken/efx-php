# 路由

1. 路由配置

配置规则(顺序不定)：

* class@method：具体方法的路径
* key=val&key=val：参数数组
* :symbol：符号匹配，匹配从:开始到/或者结束为止
* 正则：不支持/
* 字符串以,隔开：表示请求方式

`暂不支持continue`

```php
<?php

//路由配置
$config = [
    //匹配端口，代表host:5051
    '::5051'=>[
        '/a'=>'App\Pc\A@index'
    ],
    //匹配完整地址，域名包括端口(80端口可隐藏)
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
        '/a3'=>['app=a1&act=index','Efx\Pc\A@index','get']
    ]
];
```

1. 路由匹配

```php
<?php
$router = \Efx\Router\Router::create($config);
//参数为空，则直接使用当前地址
$result = $router->run();
```