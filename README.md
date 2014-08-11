Yii-Ueditor
===========

Yii-Ueditor插件是使用Ueditor 1.4.3开发的。

该插件在https://github.com/xbzbing/yii1-ueditor-ext 基础上进行开发。在最小破环源码的基础下，添加了七牛上传、图片管理。

配置说明

1、将ueditor放在项目的/protected/extensions/目录下。
2、在config.php中配置controllerMap，来指定ueditor的访问路径
'controllerMap'=>array(
    'ueditor'=>array(
        'class'=>'ext.ueditor.XUeditorController', //此控制器支持本地、七牛图片管理
    ),
),
可选配置：
'controllerMap'=>array(
    'ueditor'=>array(
        'class'=>'ext.ueditor.XeditorController',
        'config'=>array(),//参考config.json的配置，此处的配置具备最高优先级
        'useQiniu' = false, //默认开启七牛存储
        'thumbnail'=>true,//是否开启缩略图
        'watermark'=>'',//水印图片的地址，使用相对路径
        'locate'=>9,//水印位置，1-9，默认为9在右下角
    ),
),
3、在view中使用widget。
$this->widget('ext.ueditor.UeditorWidget',
        array(
                'name'=>'editor',//指定ueditor实例的名称,个页面有多个ueditor实例时使用
                'value'=>'',
        )
);
$this->widget('ext.ueditor.UeditorWidget',
        array(
                'model'=>$model,//指定ueditor实例的名称,个页面有多个ueditor实例时使用
                'attribute'=>'',
        )
);
在原有的view中添加即可，注意id填写为原有的textarea的id。
注意，使用这个widget时，不要删除原有的代码，只要添加此处的代码即可。
$this->widget('ext.ueditor.UeditorWidget',
        array(
                'id'=>'Post_content',//页面中输入框（或其他初始化容器）的ID
                'name'=>'editor',//指定ueditor实例的名称,个页面有多个ueditor实例时使用
        )
);
4、错误排除
出现错误请查看上传目录的权限问题。
默认上传到「应用」根目录（不是网站根目录）的upload/目录。
不要开启Yii的调试，因为UEditor的返回都是json格式，开启调试会导致返回格式不识别。
