<?php
Yii::import('ext.ueditor.UeditorController');
/**
 * 扩展UeditorController包含七牛的图片管理
 * @author Liv
 *
 */
class XUeditorController extends UeditorController {
	/**
	 * 是否开启七牛
	 * @var unknown
	 */
	public $useQiniu = true;
	
	/**
	 * 编辑器上传的图片前缀
	 * @var unknown
	 */
	public $prefix = '"ueditor_user".Yii::app()->user->getId()';
	
	/**
	 * 缩略图后缀
	 * @var unknown
	 */
	public $suffix_thumbnail = '!1s160x160';
	
	/**
	 * 编辑器图片后缀
	 * @var unknown
	 */
	public $suffix_big = '!1s320x160';
	
	/**
	 * 七牛位置标记名称
	 * @var unknown
	 */
	public $qiniuMarkerName = 'qiniu_marker';
	
	/**
	 * 七牛组件
	 * @var BsQiniu
	 */
	private $_qiniu;
	
	public function init(){
		if($this->useQiniu){
			$qiniu = $this->getQiniu();
			$this->config['useQiniu'] = true;
			$this->config['imageUrl'] = $qiniu->upHost;
			$this->config['token'] = $this->_getQiniuToken();
			$this->config['imageFieldName'] = 'file';
			$this->config['suffix_thumbnail'] = $this->suffix_thumbnail;
			$this->config['suffix_big'] = $this->suffix_big;
		}else{
			!isset($this->config['imageUrl']) && $this->config['imageUrl'] = $this->createUrl('ueditor/index',array('action'=>'uploadimage'));
		}
		
		parent::init();
	}
	
	/**
	 *  图片列表
	 */
	public function actionListImage(){
		$allowFiles = $this->config['imageManagerAllowFiles'];
		$listSize = $this->config['imageManagerListSize'];
		$path = $this->config['imageManagerListPath'];
		if($this->useQiniu){
			$this->getQiniuList($allowFiles,$listSize,$path);
		}else{
			$this->manage($allowFiles,$listSize,$path);
		}
	}
	
	/**
	 * 获取七牛列表
	 * @param unknown $allowFiles
	 * @param unknown $listSize
	 * @param unknown $path
	 */
	protected function getQiniuList($allowFiles, $listSize, $path){
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
        /* 获取参数 */
        $size = isset($_GET['size']) ? $_GET['size'] : $listSize;
        $start = isset($_GET['start']) ? $_GET['start'] : '';
        $end = $start + $size;
        $marker = '';	//七牛位置标记
        if(Yii::app()->user->hasFlash($this->qiniuMarkerName)){
        	$marker = Yii::app()->user->getFlash($this->qiniuMarkerName);
        }

        /* 获取文件列表 */
        $prefix = $this->evaluateExpression($this->prefix);
        $res = Yii::app()->qiniu->getList($prefix,$marker,$size);
        Yii::app()->user->setFlash($this->qiniuMarkerName,$res['marker']);	//设置用户的位置标记
        $files = $res['items'];	//获取图片列表
        if($res['err'] !== Qiniu_RSF_EOF ){	//判断文件是否结束
        	$total = $end + 20;
        }
        if (!count($files)) {
            $result =  json_encode(array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files),
            ));
            $this->show($result);
        }
        /* 获取指定范围的列表 */
        $qiniu = $this->getQiniu();
        $list = array();
        foreach ( (array)$files as $file ){
        	$list[] = array(
        		'url'=>$qiniu->getPublicImgUrl($file['key']).$this->suffix_thumbnail,
        		'mtime'=>$file['putTime'],
        	);
        }
        /* 返回数据 */
        $result = json_encode(array(
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => $total,
        ));
        $this->show($result);
    }
    
    /**
     * 获取七牛组件
     * @throws CException
     */
    public function getQiniu(){
    	if(!$this->_qiniu){
    		if(Yii::app()->hasComponent('qiniu')){
    			$this->_qiniu = Yii::app()->qiniu;
    		}else {
    			throw new CException('Missing Qiniu Component');
    		}
    	}
    	
    	return $this->_qiniu;
    }
    
    /**
     * 获取编辑器七牛的token
     */
    private function _getQiniuToken(){
    	$qiniu = $this->getQiniu();
    	return $qiniu->getToken(array(
    		'ReturnBody' => '{ "state":"SUCCESS", "url":"http://$(bucket).qiniudn.com/$(key)'.$this->suffix_thumbnail.'", "title":"$(fname)", "original":"$(fname)", "name": $(key), "size": $(fsize), "type": $(mimeType), "hash": $(etag), "key":$(key) }',
    		'SaveKey' => $this->evaluateExpression($this->prefix).'_$(year)$(mon)$(day)$(etag)',
    	));
    }
}

?>
