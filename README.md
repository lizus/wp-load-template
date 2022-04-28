# wp-load-template
对WP的核心函数get_template_part进行包装，同时添加模板数据传递

## 使用方法
将以下代码复制粘入load.php放于App\Util的对应目录下

```
<?php
namespace App\Util;

/**
 * 使用示例：
 * $l=\App\Util\Load::getInstance();
 * $data=[];//要传入子模板的数据
 * $l->loadHeader('div',$data); //使用loadHeader('div')可载入主题包根目录下：template/header/header-div.php
 */
class Load extends \LizusWPLoad\Load{
    protected $path_root='template/';//根目录
}
```

注意传入的`$data`必须为key-value数组，该项非必须。
如有传入数据，在对应的模板页获取数据使用如下示例方法：

```
<?php
$defaults=array(
    'test'=>null,
);
$data=\App\Util\Load::getData();
$data=wp_parse_args((array)$data,$defaults);
//var_dump($data);
extract($data);

?>
```