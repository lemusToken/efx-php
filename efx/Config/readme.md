# 配置

配置生成器

### 使用方法

1. 设置配置

    ```php
    <?php
    $inst = \Efx\Config\Create::load('配置名称可留空');
    //设置完整配置
    $inst->setAll([
       '配置数据'
    ]);
    //单项设置
    $inst->set('配置名称','数据');
    //获取所有配置
    $inst->get();
    //获取单项配置
    $inst->get('配置名称');
    ```

1. 添加配置

    ```php
    <?php
    $inst = \Efx\Config\Create::load('配置名称可留空');
    $inst->add('a',1);
    //1
    $inst->get('a');
    $inst->add('a',2);
    //[1,2]
    //添加同样的配置相当于合并
    $inst->get('a');
    $inst->add('a',['b'=>['b1'=>1]]);
    //[1,2,['b'=>['b1'=>1]]]
    $inst->get('a');
    //深度合并
    $inst->add('a',['b'=>['b2'=>2]],true);
    //[1,2,['b'=>['b1'=>1,'b2'=>2]]]
    $inst->get('a');
    ```
    
1. 字符串转数组

    ```php
    <?php
    $inst = \Efx\Config\Create::load('配置名称可留空');
    $inst->add('a.b',1);
    $inst->add('a.c',2);
    $inst->add('a.d',3);
    //['a'=>['b'=>1,'c'=>2,'d'=>3]]
    //小数点转化为数组维度
    $inst->getArray();
    ```