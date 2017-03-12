<?php

 /**
 * Album For Typecho, base on 1.0/14.10.10
 * @copyright  Copyright (c) 2014 我本奈何 (https://plsYou.com)
 * @license    GNU General Public License 2.0
 * @version    $Id: Post.php 2014/3/13 9:52:51
 */

 class Post{
 	
 	const UPLOAD_PATH = '/usr/plugins/Album/Data/Attachment';
 	
 	public function route( $POST, $FILES ,$ajax = false ){
 		
 		if ( Common::admin() == false ) { 
 			$msg['op']['0']['err'] = 99 ;
 			$msg['op']['0']['msg'] = '';
 			return $msg; 
 		}
 		
 		$POST = array_map('Common::form' , $POST);
 		$res = array();
 		
 		//if ( !in_array( $POST['method'], array('local','website','scan','move','del') ) ) return false ;
 		
 		//$res = self::$POST['method']( $POST, $FILES );
    if ( 'local' == $POST['method'] ){
      $res = self::local( $POST, $FILES );
    }else if('website' == $POST['method']){
      $res = self::website( $POST, $FILES );
    }else if('scan' == $POST['method']){
      $res = self::scan( $POST, $FILES );
    }else if('move' == $POST['method']){
      $res = self::move( $POST, $FILES );
    }else if('del' == $POST['method']){
      $res = self::del( $POST, $FILES );
    }else {
      $res = false;
    }

 		return $res;
 	}
 	
 	private function local( $POST, $FILES ){
 		include_once 'usr/plugins/Album/Service/Upload.php';
 		$upload = new Upload();
 		$result = $upload->upload_file( $FILES, $url = NULL, $remote_Curl = false);

 		for( $i=0; $i<count($result['op']); $i++ ){
 			
 			if ( isset($result['op'][$i]['err']) ){
 				$msg[$i]['err'] = $result['op'][$i]['err'];
 				$msg[$i]['msg'] = $result['op'][$i]['msg'];
 			}
 			
 			if ( $msg[$i]['err'] == 0 ){   
 				if ( empty($POST['description'][$i]) ){
 					$POST['description'][$i] = $result['name'][$i];
 				}
 				$db_album[$i] = self::db_album( $POST, $result, $i, $result['server'] );
 				$db_album_category[$i] = self::db_album_category( $POST['category'] );
 				if ( isset($result['EXIF'][$i]['err']) ){
 					$db_album_local[$i] = self::db_album_local( $POST, $tid=NULL, $pid=NULL, $title=$POST['description'][$i], $db_album[$i]['iid'] );
 					$db_album_count[$i] = self::db_album_count( 'local' );
 				}else{
 					$db_album_shoot[$i] = self::db_album_shoot( $POST, $result, $i, $db_album[$i]['iid'] );
 					$db_album_count[$i] = self::db_album_count( 'shoot' );
 				}
 			}
 		}
 		return $msg;
 	}
 	
 	private function website( $POST ){
 		
 		$options = Typecho_Widget::widget('Widget_Options');
 		$siteUrl = $options->siteUrl;
 		$date = new Typecho_Date($options->gmtTime);
 		$path = Typecho_Common::url('/usr/plugins/Album/Data/Attachment', __TYPECHO_ROOT_DIR__). DIRECTORY_SEPARATOR . $date->year . DIRECTORY_SEPARATOR . $date->month . DIRECTORY_SEPARATOR;
 		$thumb_path = $path . 'thumb/';
 		
 		if (!is_dir($thumb_path)) {
 			if (!Common::makeDir($thumb_path)) {
 				$msg['0']['err'] = 12 ;
 				$msg['0']['msg'] = $thumb_path ;
 				return $msg;
 			}
 		}
 		
 		$type = array( 'jpg','jpeg','gif','png' );
 		$msg['op'] = array();
 		for( $i=0; $i<count($POST['path']); $i++ ){
 			
 			$info = pathinfo($POST['path'][$i]);
 			if ( empty($POST['path'][$i]) ){ $POST['path'][$i] = 'NULL';}
 			if ( Common::CheckWebAddr($POST['path'][$i]) == false || !isset($info['extension']) || Common::check_remote($POST['path'][$i]) == false ){
 				$msg['op'][$i]['err'] = 9 ;
 				$msg['op'][$i]['msg'] = $POST['path'][$i];
 				continue;
 			}

 			if ( in_array(strtolower($info['extension']),$type) == false ){
 				$msg['op'][$i]['err'] = 2 ;
 				$msg['op'][$i]['msg'] = $info['basename'] ;
 				continue;
 			}
 			
 			if ( is_int(strpos( $POST['path'][$i],$siteUrl)) ){

 				$msg['url'][$i] = $POST['path'][$i];
 				$img_path[$i] =  __TYPECHO_ROOT_DIR__  . DIRECTORY_SEPARATOR . str_replace( $siteUrl, '', $POST['path'][$i] );

 				$msg['name'][$i] = $info['basename'];
 				$msg['size'][$i] = filesize($img_path[$i]) ;
 				$msg['pixel'][$i] = @getimagesize($img_path[$i]);
 				$msg['type'][$i] = $msg['pixel'][$i]['mime'];
 				$msg['EXIF'][$i] = Common::exif($img_path[$i],$msg['type'][$i]);
 				
 				$thumb[$i] = Common::thumb( $img_path[$i], $thumb_path, $msg['pixel'][$i], $rename = true );
 				
 				if ( isset( $thumb[$i]['err'] ) ){
 					$msg['op'][$i]['err'] = 4 ;
 					$msg['op'][$i]['msg'] = $msg['name'][$i];
 					@unlink($img_path[$i]);
 					continue;
 				}else{
 					$msg['thumb'][$i]['url'] = $siteUrl.str_replace( __TYPECHO_ROOT_DIR__.DIRECTORY_SEPARATOR ,'',$thumb[$i]['url'] );
 				}
 				
 				if ( isset($msg['EXIF'][$i]['err']) ){
 					$msg['from'][$i] = 'local';
 				}else{
 					$msg['from'][$i] = 'shoot';
 				}
 				
 				if ( empty($POST['description'][$i]) ) {
 					$POST['description'][$i] = $msg['name'][$i];
 				}
 				
 				$db_album[$i] = self::db_album( $POST, $msg, $i, $msg['server'] = 'local' );
 				
 				$db_album_category[$i] = self::db_album_category( $POST['category'] );
 				if ( isset($msg['EXIF'][$i]['err']) ){
 					$db_album_local[$i] = self::db_album_local( $POST, $tid=NULL, $pid=NULL, $title=$POST['description'][$i], $db_album[$i]['iid'] );
 					$db_album_count[$i] = self::db_album_count( 'local' );
 				}else{
 					$db_album_shoot[$i] = self::db_album_shoot( $POST, $msg, $i, $db_album[$i]['iid'] );
 					$db_album_count[$i] = self::db_album_count( 'shoot' );
 				}
 				
 			}else{
 				
 				include 'usr/plugins/Album/Data/Config.inc.php';

 				if ( $remote_Curl === true ){
 					
 					if ( $upload_Server == 'local' ){
 						include_once 'usr/plugins/Album/Service/Upload.php';
 						$upload = new Upload();
 						$result = $upload->upload_file( $file=array(),$POST['path'][$i], $remote_Curl = true );
 						
 						if ( isset( $result['err'] ) ){
 							$msg['op'][$i]['err'] = $result['err'] ;
 							$msg['op'][$i]['msg'] = $info['basename'] ;
 							continue;
 						}
 						
 						$msg['url'][$i] = $result['url'];
 						$img_path[$i] = $result['path'];
 						$msg['name'][$i] = $result['name'];
 						$msg['size'][$i] = $result['size'];
 						$msg['pixel'][$i] = $result['pixel'];
 						$msg['type'][$i] = $msg['pixel'][$i]['mime'];
 						$msg['server'] = 'local' ;
 						
 					}else{
 						
 						include_once 'usr/plugins/Album/Service/CloudStorage/Index.php';
 						$upload_path = $date->year . DIRECTORY_SEPARATOR . $date->month ;
 						$upload_name = rand(0,1000) . $info['basename'] ;
 						
 						$msg['url'][$i] = CloudStorage::post($upload_Server,$POST['path'][$i],'curl',$upload_path,$upload_name);

 						$msg['name'][$i] = $info['basename'];
 						$msg['size'][$i] = Common::remote_filesize($POST['path'][$i]);
 						$msg['pixel'][$i] = @getimagesize($POST['path'][$i]);
 						$msg['type'][$i] = $msg['pixel'][$i]['mime'];
 						$img_path[$i] = $POST['path'][$i];
 						
 						$msg['op'][$i]['err'] = 4 ;
 						$msg['op'][$i]['msg'] = $msg['url'][$i];
 						$msg['server'] = $upload_Server ;
 						
 					}
 					
 				}else{

 					$msg['url'][$i] = $POST['path'][$i];
 					$msg['name'][$i] = $info['basename'];
 					$msg['size'][$i] = Common::remote_filesize($POST['path'][$i]);
 					$msg['pixel'][$i] = @getimagesize($POST['path'][$i]);
 					$msg['type'][$i] = $msg['pixel'][$i]['mime'];
 					$img_path[$i] = $POST['path'][$i];
 					
 					$msg['server'] = 'local' ;
 					
 				}
 				
 				$thumb[$i] = Common::thumb( $img_path[$i], $thumb_path, $msg['pixel'][$i], $rename = true );
 				
 				if ( isset( $thumb[$i]['err'] ) ){
 					$msg['op'][$i]['err'] = 4 ;
 					$msg['op'][$i]['msg'] = $msg['name'][$i];
 					@unlink($img_path[$i]);
 					continue;
 				}else{
 					$msg['thumb'][$i]['url'] = $siteUrl.str_replace( __TYPECHO_ROOT_DIR__.DIRECTORY_SEPARATOR ,'',$thumb[$i]['url'] );
 				}
 				
 				if ($POST['from'] == 'shoot'){
 					$msg['EXIF'][$i] = Common::exif($img_path[$i],$msg['type'][$i]);
 				}else{
 					$msg['EXIF'][$i]['err'] = '';
 					$msg['EXIF'][$i]['msg'] = 'Local Select!';
 				}
 				
 				if ( !isset($msg['EXIF'][$i]['err']) ){
 					$msg['from'][$i] = 'shoot';
 				}else{
 					$msg['from'][$i] = 'network';
 				}
 				
 				if ( empty($POST['description'][$i]) ) {
 					$POST['description'][$i] = $msg['name'][$i];
 				}
 				
 				$db_album[$i] = self::db_album( $POST, $msg, $i ,$msg['server'] );
 				$db_album_category[$i] = self::db_album_category( $POST['category'] );
 				
 				if ( !isset($msg['EXIF'][$i]['err'])  ){
 					$db_album_shoot[$i] = self::db_album_shoot( $POST, $msg, $i, $db_album[$i]['iid'] );
 					$db_album_count[$i] = self::db_album_count( 'shoot' );
 				}else{
  				//preg_match("/[\w\-]+\.\w+(?=\/)/", $POST['path'][$i], $description[$i]);
  				$host[$i] = parse_url($POST['path'][$i]);//2014/1/10 11:14:38
  				$db_album_local[$i] = self::db_album_network( $POST, $host[$i]['host'], $db_album[$i]['iid'] );
  				$db_album_count[$i] = self::db_album_count( 'network' );
  			}
  			$msg['op'][$i]['err'] = 0 ;
  			$msg['op'][$i]['msg'] = $info['basename'];
  		}
  	}
  	
  	return $msg['op'];
  }
  
  private function scan( $POST ){
  	
  	$result = array();
  	$msg['op'] = array();
  	$path = __TYPECHO_ROOT_DIR__ . DIRECTORY_SEPARATOR . $POST['path']  ;
  	
  	$dir = realpath($path);
  	if ( $dir == false ){
  		$msg['op']['0']['err'] = 10 ;
  		$msg['op']['0']['msg'] = $POST['path'] ;
  		return $msg['op'];
  	}
  	
  	$type = array( 'jpg','jpeg','gif','png' );
  	$stack = array($dir);
  	
  	while(NULL!==($dir=array_shift($stack))&&false!==($handle=opendir($dir))){
  		while($file=readdir($handle)){
  			if($file=='.'||$file==".."){continue 1; }
  			$filename = $dir.DIRECTORY_SEPARATOR.$file;
  			if(is_dir($filename)){
  				array_push($stack,$filename);
  			}else{		
  				$info = pathinfo($filename);
  				if ( in_array(strtolower($info['extension']),$type) == false ){
  					continue;
  				}
					// ? 2014/1/10 15:37:20
  				$filename = iconv('utf-8', 'gb2312', $filename);
  				$filename = iconv('gb2312', 'utf-8', $filename);
  				$result[] = $filename;
  			}
  		}
  	}
  	
  	if ( count($result) < 1 ){
  		$msg['op']['0']['err'] = 11 ;
  		$msg['op']['0']['msg'] = $POST['path'];
  		return $msg['op'];
  	}
  	
  	$options = Typecho_Widget::widget('Widget_Options');
  	$siteUrl = $options->siteUrl;
  	$date = new Typecho_Date($options->gmtTime);
  	$thumb_path = Typecho_Common::url(self::UPLOAD_PATH, __TYPECHO_ROOT_DIR__). DIRECTORY_SEPARATOR . $date->year . DIRECTORY_SEPARATOR . $date->month . DIRECTORY_SEPARATOR . 'thumb/';
  	
  	if (!is_dir($thumb_path)) {
  		if (!Common::makeDir($thumb_path)) {
  			return false;
  		}
  	}
  	
  	for ( $i=0; $i < count($result); $i++ ){
  		
  		$pathinfo = pathinfo($result[$i]);
  		$msg['url'][$i] = $siteUrl.str_replace( __TYPECHO_ROOT_DIR__.DIRECTORY_SEPARATOR ,'',$result[$i] );
  		$msg['name'][$i] = $pathinfo['basename'];
  		$msg['size'][$i] = filesize($result[$i]);
  		$msg['pixel'][$i] = @getimagesize($result[$i]);
  		$msg['type'][$i] = $msg['pixel'][$i]['mime'];
  		$img_path[$i] = $result[$i];
  		
  		$thumb[$i] = Common::thumb( $img_path[$i], $thumb_path, $msg['pixel'][$i], $rename = true );
  		
  		if ( isset( $thumb[$i]['err'] ) ){
  			$msg['op'][$i]['err'] = 4 ;
  			$msg['op'][$i]['msg'] = $msg['name'][$i];
  			@unlink($img_path[$i]);
  			continue;
  		}else{
  			$msg['thumb'][$i]['url'] = $siteUrl.str_replace( __TYPECHO_ROOT_DIR__.DIRECTORY_SEPARATOR ,'',$thumb[$i]['url'] );
  		}
  		
  		if ($POST['from'] == 'shoot'){
  			$msg['EXIF'][$i] = Common::exif($img_path[$i],$msg['type'][$i]);
  		}else{
  			$msg['EXIF'][$i]['err'] = '';
  			$msg['EXIF'][$i]['msg'] = 'Local Select!';
  		}
  		
  		if ( !isset($msg['EXIF'][$i]['err']) ){
  			$msg['from'][$i] = 'shoot';
  		}else{
  			$msg['from'][$i] = 'local';
  		}
  		
  		$POST['description'][$i] = $msg['name'][$i];
  		$db_album[$i] = self::db_album( $POST, $msg, $i , 'local' );
  		$db_album_category[$i] = self::db_album_category( $POST['category'] );
  		
  		if ( isset($msg['EXIF'][$i]['err']) ){
  			$db_album_local[$i] = self::db_album_local( $POST, $tid=NULL, $pid=NULL, $title=$POST['description'][$i], $db_album[$i]['iid'] );
  			$db_album_count[$i] = self::db_album_count( 'local' );
  		}else{
  			$db_album_shoot[$i] = self::db_album_shoot( $POST, $msg, $i, $db_album[$i]['iid'] );
  			$db_album_count[$i] = self::db_album_count( 'shoot' );
  		}
  		
  		$msg['op'][$i]['err'] = 0 ;
  		$msg['op'][$i]['msg'] = $msg['name'][$i];
  	}
  	return $msg['op'];
  }
  
  private function del( $POST ){
  	include 'usr/plugins/Album/Data/Config.inc.php';
  	$options = Typecho_Widget::widget('Widget_Options');
  	$siteUrl = $options->siteUrl;
  	$db = Typecho_Db::get();
  	$prefix = $db->getPrefix();
  	
  	for( $i = 0; $i < count( $POST['img_id'] ); $i++ ){
  		
  		$result = $db->fetchRow($db->select('id','url','thumb','from','category','server')->from('table.album')->where('id = ?', $POST['img_id'][$i]));
  		
  		$img_path =  __TYPECHO_ROOT_DIR__  . DIRECTORY_SEPARATOR . str_replace( $siteUrl, '', $result['url'] );
  		$thumb_path =  __TYPECHO_ROOT_DIR__  . DIRECTORY_SEPARATOR . str_replace( $siteUrl, '', $result['thumb'] );
  		
  		if ( is_int(strpos( $result['url'],self::UPLOAD_PATH)) ){
  			@unlink($img_path);
  		}
  		
			//for CloudStorage
  		if ( $result['server'] !== 'local' ){
  			include_once 'usr/plugins/Album/Service/CloudStorage/Index.php';
  			$_del = CloudStorage::post($result['server'],$file=NULL,$method='del',$result['url'],$name = '');
  		}

  		@unlink($thumb_path);
  		
  		$db->query($db->delete('table.album')->where('id = ?', $result['id']));
  		$db->query($db->delete("table.album_".$result['from']."")->where('iid = ?', $result['id']));
  		$db->query( "UPDATE {$prefix}album_category SET count=count-1 WHERE id={$result['category']} ;");
  		$db->query( "UPDATE {$prefix}album_count SET total=total-1, {$result['from']}={$result['from']}-1 WHERE id=1;");
  		
  	}
  	$msg['0']['err'] = 0 ;
  	$msg['0']['msg'] = 'UPDATE';
  	return $msg;
  }
  
  private function move( $POST ){
  	
  	$db = Typecho_Db::get();
  	$prefix = $db->getPrefix();
  	
  	for( $i = 0; $i < count( $POST['img_id'] ); $i++ ){
  		
  		$result = $db->fetchRow($db->select('category')->from('table.album')->where('id = ?', $POST['img_id'][$i]));
  		$category = $db->fetchRow($db->select()->from('table.album_category')->where('id = ?', $POST['move']));
  		
  		$db->query( "UPDATE {$prefix}album_category SET count=count-1 WHERE id={$result['category']} ;");
  		$db->query( "UPDATE {$prefix}album_category SET count=count+1 WHERE id={$POST['move']} ;");
  		$db->query( "UPDATE {$prefix}album SET public={$category['public']}, category={$POST['move']} WHERE id={$POST['img_id'][$i]} ;");
  		
  	}
  	$msg['0']['err'] = 0 ;
  	$msg['0']['msg'] = 'UPDATE';
  	return $msg;
  }
  
  private function db_album( $POST, $result, $i , $server = 'local' ){
  	
  	$db = Typecho_Db::get();
  	$category = $db->fetchRow($db->select()->from('table.album_category')->where('id = ?', $POST['category']));
  	
  	$db->query(
  		$db->insert('table.album')->rows(
  			array(
  				'name' => $result['name'][$i],
  				'mime' => $result['type'][$i], 
  				'pixel' => join( ',', $result['pixel'][$i]), 
  				'size' => $result['size'][$i], 
  				'created' => time(), 
  				'description' => $POST['description'][$i], 
  				'url' => $result['url'][$i], 
  				'thumb' => $result['thumb'][$i]['url'],
  				'public' => $category['public'],
  				'from' => $result['from'][$i], 
  				'category' => $POST['category'],
  				'server' => $server )
  			)
  		);
  	if(function_exists('mysqli_insert_id')){  
      $prefix = $db->getPrefix();
      $rrr = $db->fetchAll($db->query( "select * from {$prefix}album order by id desc limit 1;"));
      $album['iid'] = $rrr['0']['id'];
    }else{
      $album['iid'] = mysql_insert_id() ;
    }
  	return $album;
  }
  
  private function db_album_local( $POST, $tid=NULL, $pid=NULL, $title=NULL, $iid ){
  	$db = Typecho_Db::get();
    $db->query(
  		$db->insert('table.album_local')->rows(
  			array( 
  				'iid' => $iid, 
  				'tid' => $tid,
  				'pid' => $pid,
  				'title' => $title,
  				'category' => $POST['category'] )
  			)
  		);
  	return true;
  }
  
  private function db_album_shoot( $POST, $result, $i, $iid ){
  	$db = Typecho_Db::get();
  	$db->query(
  		$db->insert('table.album_shoot')->rows(
  			array( 
  				'iid' => $iid, 
  				'camera' => $result['EXIF'][$i]['camera'], 
  				'lens' => $result['EXIF'][$i]['lens'], 
  				'aperture' => $result['EXIF'][$i]['aperture'], 
  				'shutterSpeed' => $result['EXIF'][$i]['shutterSpeed'], 
  				'focalLength' => $result['EXIF'][$i]['focalLength'], 
  				'focalLength35mmFilm' => $result['EXIF'][$i]['focalLength35mmFilm'],
  				'ISO' =>  $result['EXIF'][$i]['ISO'],
  				'time' => $result['EXIF'][$i]['time'], 
  				'category' => $POST['category'] )
  			)
  		);
  	return true;
  }
  
  private function db_album_network( $POST, $description, $iid ){
  	$db = Typecho_Db::get();
  	$db->query(
  		$db->insert('table.album_network')->rows(
  			array( 
  				'iid' => $iid,
  				'description' => $description,
  				'category' => $POST['category'] )
  			)
  		);
  	return true;
  }
  
  private function db_album_count( $key ){
  	$db = Typecho_Db::get();
  	$prefix = $db->getPrefix();
  	$db->query( "UPDATE {$prefix}album_count SET total=total+1, {$key}={$key}+1 WHERE id=1;");
  	return true;
  }
  
  private function db_album_category( $key ){
  	$db = Typecho_Db::get();
  	$prefix = $db->getPrefix();
  	$db->query( "UPDATE {$prefix}album_category SET count=count+1 WHERE id={$key} ;");
  	return true;
  }
  
}