<?php
 /**
 * Album For Typecho, base on 1.0/14.10.10
 *
 * @package Album
 * @author 我本奈何
 * @version 1.2
 * @link https://plsYou.com
 */

class Album_Plugin implements Typecho_Plugin_Interface
{ 
	/**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
     
     
    public static function activate()
    {
    		Typecho_Plugin::factory('Widget_Upload')->upload = array('Album_Plugin', 'ImgSyn');
    		Helper::addAction('Album', 'Album_Action');
    		Helper::addRoute('Album_index', '/Album', 'Album_Action', 'action');
    		Helper::addRoute('Album_request', '/Album/[key]/', 'Album_Action', 'action');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){
        Helper::removeRoute('Album');
        Helper::removeAction('Album_request');
    		Helper::removeAction('Album_index');
    }
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){
    }
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
    
    /**
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    
    public function ImgSyn($data){
    	require_once 'Data/Config.inc.php';
    	require_once 'Service/Common.php';
    	if ( $imgSyn == false || $data->attachment->isImage != 1 ) return true;
    	if ( $imgTag != 'all' && !strpos($data->attachment->name, $imgTag) ) return true;
    	if ( isset($imgCateID) && is_int($imgCateID) ){
    		$category = $imgCateID;
    	}else{
    		$category = '1'; //默认为1
    	}
    	
    	$options = Typecho_Widget::widget('Widget_Options');
			$siteUrl = $options->siteUrl;
    	$date = new Typecho_Date($options->gmtTime);
    	$thumb_path = Typecho_Common::url('/usr/plugins/Album/Data/Attachment', __TYPECHO_ROOT_DIR__). DIRECTORY_SEPARATOR . $date->year . DIRECTORY_SEPARATOR . $date->month . DIRECTORY_SEPARATOR . 'thumb/';
    	$path = __TYPECHO_ROOT_DIR__  . DIRECTORY_SEPARATOR . $data->attachment->path ;
    	
    	$pixel = @getimagesize( $path ) ;
    	if (!is_dir($thumb_path)) Common::makeDir($thumb_path);
    	$thumb = Common::thumb( $path, $thumb_path, $pixel );
    	
    	if ( !isset($thumb['url']) ) return true;
    	$thumb_url = $siteUrl.str_replace( __TYPECHO_ROOT_DIR__.DIRECTORY_SEPARATOR ,'',$thumb['url'] );
    	
			$EXIF = Common::exif($path,$data->attachment->mime);
					
			if ( isset($EXIF['err']) ){
				$from = 'local';
			}else{
				$from = 'shoot';
			}
			
    	$db = Typecho_Db::get();
    	$prefix = $db->getPrefix();
    	
    	$public = $db->fetchRow($db->select()->from('table.album_category')->where( 'id = ?', $category ));
  		$db->query(
  			$db->insert('table.album')->rows(
  				array(
  					'name' => $data->attachment->name,
  					'mime' => $data->attachment->mime, 
  					'pixel' => join( ',',$pixel), 
  					'size' => $data->attachment->size, 
  					'created' => time(), 
  					'description' => '', //模板中处理吧
  					'url' => $data->attachment->url, 
  					'thumb' => $thumb_url,
  					'public' => $public['public'],
  					'from' => $from, 
  					'category'=> $category ,
  					'server' => 'local' //相册不会接管TE本身的存储，所以这里不考虑存储问题。
  				)
  			)
  		);
  	
  		$iid = mysql_insert_id() ;
  		$db->query( "UPDATE {$prefix}album_category SET count=count+1 WHERE id={$category} ;");
    	//接口在数据生成之前，悲剧！所以 tid  和 title 只好在模板中获取了。当初考虑不周全啊。蛋疼。
  		//$tid = $db->fetchRow($db->select()->from('table.contents')->where( 'cid = ?', $data->cid ));
  		
		$tid = '';
    	$title = '';
    	
  		if ( $from == 'local' ){
  			
  			$db->query($db->insert('table.album_local')->rows(
  					array( 
  						'iid' => $iid, 
  						'tid' => $tid,					//悲剧了
  						'pid' => $data->cid,		//图片附件CID
  						'title' => $title,			//悲剧了
  						'category' => $category 
  					)
  			));
  		
    		$db->query( "UPDATE {$prefix}album_count SET total=total+1, local=local+1 WHERE id=1;");
    		
  		}else{
  			
				$db->query($db->insert('table.album_shoot')->rows(
  					array( 
  						'iid' => $iid, 
  						'pid' => $data->cid,
  						'camera' => $EXIF['camera'], 
  						'lens' => $EXIF['lens'], 
  						'aperture' => $EXIF['aperture'], 
  						'shutterSpeed' => $EXIF['shutterSpeed'], 
  						'focalLength' => $EXIF['focalLength'], 
  						'focalLength35mmFilm' => $EXIF['focalLength35mmFilm'],
  						'ISO' =>  $EXIF['ISO'],
  						'time' => $EXIF['time'], 
  						'category' => $category 
  					)
  			));
  			
				$db->query( "UPDATE {$prefix}album_count SET total=total+1, shoot=shoot+1 WHERE id=1;");
				
  		}
    	
    	return true;
    	
    }
    
		public static function url($data){
			
			$options = Typecho_Widget::widget('Widget_Options');
			$date = new Typecho_Date($data['created']);
			
			$db = Typecho_Db::get();
			$category = $db->fetchAll($db
        ->select()->from('table.metas')
        ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
        ->where('table.relationships.cid = ?', $data['cid'])
        ->where('table.metas.type = ?', 'category')
        );
			
      $value['year'] = $date->year;
      $value['month'] = $date->month;
      $value['day'] = $date->day;
      $value['cid'] = $data['cid'];
      $value['slug'] = urlencode($data['slug']);
      $value['category'] = urlencode($category['0']['slug']);
      
			$pathinfo = Typecho_Router::url($data['type'], $value);
			$link = Typecho_Common::url($pathinfo, $options->index);
			
			return $link;
		}
    

	
}

