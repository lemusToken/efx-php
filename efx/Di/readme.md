# 依赖注入(DI)和控制反转(IOC)

依赖注入 (Dependency Injection)：应用组件不应该负责查找资源或者其他依赖的协作对象。配置对象的工
作应该由IoC容器负责，“查找资源”的逻辑应该从应用组件的代码中抽取出来，交给IoC容器负
责。

控制反转即IoC (Inversion of Control)：它把传统上由程序代码直接操控的对象的调用权交给
容器，通过容器来实现对象组件的装配和管理。所谓的“控制反转”概念就是对组件对象控制权
的转移，从程序代码本身转移到了外部容器。

### 使用方法

1. 依赖注入

    ```php
    <?php
    class D{
        
        public function t() {
           return 't';
        }
    }
 
    class Test{
       //构造注入
       //也可以用字符串'ioc:\D'，字符串会由ioc容器自动解析
       public function __construct(\D $d) {
           echo $d->t();
       }
    }
    ```

1. 控制反转

    1. `ioc`：普通类反转，反转后直接生成实例
    
        ```php
        <?php
        use \Efx\Di\Ioc;
        Ioc::makeWith('ioc:\Test');
        //或者
        Ioc::create('\Test');
        ```
    
    1. `ioc-static`：类中的静态方法调用闭包反转，反转后生成闭包
    
        ```php
        <?php
        use \Efx\Di\Ioc;
        Ioc::makeWith('ioc-static:\Test1');
        //或者
        Ioc::createStatic('\Test1');
        ```
    
    1. `ioc-make`：类中的方法反转，反转后直接调用方法(CLASS->METHOD)
    
        ```php
        <?php
        use \Efx\Di\Ioc;
        Ioc::makeWith('ioc-make:\Test@test');
        //或者
        Ioc::make('\Test','test');
        ```
    
    1. `ioc-static-make`：类中的静态方法反转，反正后直接调用静态方法(CLASS::METHOD)
    
        ```php
        <?php
        use \Efx\Di\Ioc;
        Ioc::makeWith('ioc-static-make:\Test1@test');
        //或者
        Ioc::makeStatic('\Test1','test');
        ```
        
    1. 如果依赖的是interface或者abstract，则需要手动传入需要的实例
    
        ```php
        <?php
        use \Efx\Di\Ioc;
        //参数为数组，可以使用以形参名为下标，也可以使用数组下标
        //注意，当以形参名为下标时，只会给对应形参赋值
        Ioc::makeWith('ioc:\Test',[new \D]);
        ```