# 自动加载

自动加载核心由`composer`实现

### 使用方法

1. 安装composer
1. 熟悉psr0，psr4
1. 加入代码

    ```php
    <?php
    define('ROOT_PATH',__DIR__);
    
    //类自动加载
    require_once ROOT_PATH.'/vendor/autoload.php';
    
    //loadInst返回单例
    //添加psr4配置
    \Efx\Autoload\Load::loadInst()->psr4([
        'App\\'=>'app'
    ]);
 
    //添加psr0配置
    \Efx\Autoload\Load::loadInst()->psr0([
        'App\\'=>'app'
    ]);
  
    //添加psr4配置
    \Efx\Autoload\Load::loadInst()->files([
       '文件具体路径'
    ]);
    ```