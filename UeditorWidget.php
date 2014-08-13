<?php
/**
 * @author xbzbing<xbzbing@gmail.com>
 * @link www.crazydb.com
 * UEditor版本v1.4.3
 * Yii版本v1.1.14
 * 使用widget请配置容器的id，如果在一个页面使用多个ueditor，
 * 需要配置name属性，默认的name属性为editor。
 */
class UeditorWidget extends CInputWidget {

    /**
     * 资源地址，也是UE的UEDITOR_HOME_URL，自动生成，一般情况不要修改。
     * @var string
     */
    private $_assetUrl;
    /**
     * 生成的ueditor对象的名称，默认为editor。
     * 主要用于同一个页面的多个editor实例的管理。
     * @var string
     */
    public $name;
    /**
     * 需要引入的JS文件列表，为以后升级添加配置保证兼容。
     * 可以单独引入patch文件
     * @var array js列表
     */
    public $jsFiles = array (
    	'/ueditor.config.js',
        '/ueditor.all.js',
    );
    /**
     * ueditor的初始化配置选项。默认配置为个人喜好，可以根据需求修改。
     * 语言默认中文，修改最大字符为10240，修改提示信息。
     * @var String
     */
    public $options = array();

    /**
     * UEditor 1.4+的统一后台入口
     * @var string
     */
    public $serverUrl;
    /**
     * 初始化高度
     * @var string
     */
    public $initialFrameHeight = '400';
    /**
     * 初始化宽度
     * 默认为100%，会自动匹配父容器宽度
     * @var string
     */
    public $initialFrameWidth = '100%';

    /**
     * 初始化配置，发布资源文件
     */
    public function init() {
        parent::init();
        //发布资源文件
        $assetManager = Yii::app()->assetManager;
        $assetManager->excludeFiles = array(
            'action_crawler.php',
            'action_upload.php',
            'action_list.php',
            'controller.php',
            'Uploader.class.php',
            'config.json',
            'index.html'
        );
        $this->_assetUrl = $assetManager->publish( __DIR__ . DIRECTORY_SEPARATOR . 'resources' );

        //注册资源文件
        $cs = Yii::app()->clientScript;
        foreach( $this->jsFiles as $jsFile)
            $cs->registerScriptFile( $this->_assetUrl . $jsFile, CClientScript::POS_END );

        //拼接UE配置
        if($this->serverUrl==null){
            $this->serverUrl = Yii::app()->createUrl('ueditor/index');
        }
        
        //默认的编辑栏目
        !isset($this->options['toolbars']) && $this->options['toolbars'] = 
        array(
			array (
				'fullscreen',
				'source',
				'undo',
				'redo',
				'|',
				'customstyle',
				'paragraph',
				'fontfamily',
				'fontsize' 
			),
			array (
				'bold',
				'italic',
				'underline',
				'fontborder',
				'strikethrough',
				'superscript',
				'subscript',
				'removeformat',
				'formatmatch',
				'autotypeset',
				'blockquote',
				'pasteplain',
				'|',
				'forecolor',
				'backcolor',
				'insertorderedlist',
				'insertunorderedlist',
				'|',
				'rowspacingtop',
				'rowspacingbottom',
				'lineheight',
				'|',
				'directionalityltr',
				'directionalityrtl',
				'indent',
				'|' 
			),
			array (
				'justifyleft',
				'justifycenter',
				'justifyright',
				'justifyjustify',
				'|',
				'link',
				'unlink',
				'|',
				'insertimage',
				'emotion',
				'scrawl',
				'insertvideo',
				'music',
				'attachment',
				'map',
				'insertcode',
				'pagebreak',
				'|',
				'horizontal',
				'inserttable',
				'|',
				'print',
				'preview',
				'searchreplace',
				'help' 
			) 
		);
        
        isset($this->options['lang']) && $this->options['lang'] = 'zh-cn';
        $this->options['UEDITOR_HOME_URL'] = $this->_assetUrl.'/';
        $this->options['serverUrl'] = $this->serverUrl;
        $this->options['initialFrameHeight'] = $this->initialFrameHeight;
        $this->options['initialFrameWidth'] = $this->initialFrameWidth;
        
        $options = CJavaScript::encode($this->options);
        
        list($name, $id) = $this->resolveNameID();
        
        $this->name = $name;
        $this->id = $id;

        if ($this->hasModel()) {
        	echo CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
        } else {
        	echo CHtml::textArea($name, $this->value, $this->htmlOptions);
        }

        $js = <<<UEDITOR
        
        UE.Editor.prototype._bkGetActionUrl = UE.Editor.prototype.getActionUrl;
		UE.Editor.prototype.getActionUrl = function(action) {
		    if ( this.getOpt('useQiniu') == true && action == 'uploadimage') {
		        return this.getOpt('imageUrl');
		    } else {
		        return this._bkGetActionUrl.call(this, action);
		    }
		}
        
        var {$this->id} = UE.getEditor('{$this->id}', {$options});
        {$this->id}.ready(function(){
        	if(this.getOpt('useQiniu')){
    			this.execCommand('serverparam',{'token':this.getOpt('token')});
        		this.addListener( "beforeInsertImage", function ( type, imgObjs ) {
	                for(var i=0;i < imgObjs.length;i++){
	                    imgObjs[i].src = imgObjs[i].src.replace(this.getOpt('suffix_thumbnail'),this.getOpt('suffix_big'));
	                }
	            });
    		}else{
    			this.addListener( "beforeInsertImage", function ( type, imgObjs ) {
	                for(var i=0;i < imgObjs.length;i++){
	                    imgObjs[i].src = imgObjs[i].src.replace(".thumbnail","");
	                }
	            });
    		}
        });
UEDITOR;
        $cs->registerScript('ueditor_'.$this->id, $js, CClientScript::POS_END);
    }

    /**
     * 获取assetUrl
     * @return string
     */
    public function getAssetUrl(){
        return $this->_assetUrl;
    }
}