<?php

 /**
 * Album For Typecho, base on 1.0/14.10.10
 * @copyright  Copyright (c) 2014 我本奈何 (https://plsYou.com)
 * @license 	GNU General Public License 2.0
 * @version 	$Id: Upload.php 2014/3/13 9:53:01
 */
 
 class Upload{

 	const UPLOAD_PATH = '/usr/plugins/Album/Data/Attachment';

 	public static function upload_file( $file=array(), $url, $remote_Curl = false ){

 		if ( $remote_Curl === true ){
 			$res = self::curl( $url );
 		}else{
 			$res = self::save( $file );
 		}
 		return $res;
 	}

 	private function save( $file=array() ){
 		include 'usr/plugins/Album/Data/Config.inc.php';
 		$options = Typecho_Widget::widget('Widget_Options');
 		$siteUrl = $options->siteUrl;
 		$date = new Typecho_Date($options->gmtTime);
 		$path = Typecho_Common::url(self::UPLOAD_PATH, __TYPECHO_ROOT_DIR__). DIRECTORY_SEPARATOR . $date->year . DIRECTORY_SEPARATOR . $date->month . DIRECTORY_SEPARATOR;
 		$thumb_path = $path . 'thumb/';

 		if (!is_dir($thumb_path)) {
 			if (!Common::makeDir($thumb_path)) {
 				$msg['op']['0']['err'] = 12 ;
 				$msg['op']['0']['msg'] = $thumb_path;
 				return $msg['op'];
 			}
 		}

 		$msg = array();
 		$MIME = array('image/jpeg','image/gif','image/png');
 		$type = array('jpeg','jpg','gif','png');
 		$op = array('0'=>'Upload Complete!','1'=>'upload_max_filesize Limit','2'=>'MAX_FILE_SIZE Limit','3'=>'Not Complete!','4'=>'No File Select!','5'=>'Unknown Error!','6'=>'Cant Find TMP File','7'=>'Write File Error!');


 		for($i=0;$i<count($file['file']['name']);$i++){

 			if ( in_array($file['file']['type'][$i],$MIME) && $file['file']['error'][$i] == 0 ){

 				if ( $file['file']['name'][$i] == '' ) {
 					$msg['op'][$i]['err'] = 1 ;
 					$msg['op'][$i]['msg'] = 'NULL';
 					continue;
 				}

 				$msg['type'][$i] = $file['file']['type'][$i];
 				$msg['name'][$i] = $file['file']['name'][$i];
 				$msg['size'][$i] = $file['file']['size'][$i];
 				$msg['pixel'][$i] = @getimagesize($file['file']['tmp_name'][$i]);
 				$msg['EXIF'][$i] = Common::exif($file['file']['tmp_name'][$i],$file['file']['type'][$i]);

 				if ( isset($msg['EXIF'][$i]['err']) ){
 					$msg['from'][$i] = 'local';
 				}else{
 					$msg['from'][$i] = 'shoot';
 				}

 				$part[$i] = explode('.', $file['file']['name'][$i]);
 				if (($length[$i] = count($part[$i])) > 1) {
 					$ext[$i] = '.'.strtolower($part[$i][$length[$i] - 1]);
 				}

 				if ( in_array($ext[$i],$type) ){
 					$msg['op'][$i]['err'] = 2 ;
 					$msg['op'][$i]['msg'] = $msg['name'][$i] ;
 					continue;
 				}

 				$file_path = $path.time().rand(0,1000).$ext[$i];

 				if ( $upload_Server == 'local' ){

 					if( ! @move_uploaded_file($file['file']['tmp_name'][$i], $file_path))  { 
 						$msg['op'][$i]['err'] = 3 ; 
 						$msg['op'][$i]['msg'] = $msg['name'][$i];
 						continue;
 					} 

 					$msg['url'][$i] = $siteUrl.str_replace( __TYPECHO_ROOT_DIR__.DIRECTORY_SEPARATOR ,'',$file_path  );
 					$thumb[$i] = Common::thumb( $file_path, $thumb_path, $msg['pixel'][$i] );

 					if ( isset( $thumb[$i]['err'] ) ){
 						$msg['op'][$i]['err'] = 4;
 						$msg['op'][$i]['msg'] = $msg['name'][$i];
 						@unlink($file_path);
 						continue;
 					}else{
 						$msg['thumb'][$i]['url'] = $siteUrl.str_replace( __TYPECHO_ROOT_DIR__.DIRECTORY_SEPARATOR ,'',$thumb[$i]['url'] );
 					}

 				}else{

 					include_once 'usr/plugins/Album/Service/CloudStorage/Index.php';
 					$upload_path = $date->year . DIRECTORY_SEPARATOR . $date->month ;
 					$upload_name = rand(0,1000) . $file['file']['name'][$i] ;
 					$msg['url'][$i] = CloudStorage::post($upload_Server,$file['file']['tmp_name'][$i],'upload',$upload_path,$upload_name);
 					$_msg_url = CloudStorage::post($upload_Server,NULL,'mkurl',$msg['url'][$i],NULL);
 					$thumb[$i] = Common::thumb( $_msg_url, $thumb_path, $msg['pixel'][$i],$rename = false ,$token = true );

 					if ( isset( $thumb[$i]['err'] ) ){
 						$msg['op'][$i]['err'] = 4;
 						$msg['op'][$i]['msg'] = $msg['name'][$i];
 						@unlink($file_path);
 						continue;
 					}else{
 						$msg['thumb'][$i]['url'] = $siteUrl.str_replace( __TYPECHO_ROOT_DIR__.DIRECTORY_SEPARATOR ,'',$thumb[$i]['url'] );
 					}

 				} 

 				$msg['op'][$i]['err'] = 0; //2014/1/10 20:53:42
 				$msg['op'][$i]['msg'] = $msg['name'][$i];

 			}else{
 				if ( $file['file']['name'][$i] === '' ){
 					$msg['op'][$i]['err'] = 1 ;
 					$msg['op'][$i]['msg'] = 'NULL';
 					continue;
 				}else{
 					$msg['op'][$i]['err'] = 2 ;
 					$msg['op'][$i]['msg'] = $file['file']['name'][$i];
 					continue;
 				}
 			}

 		}

 		$msg['server'] = $upload_Server ;
 		return $msg;
 	}

 	private function curl( $url ){
 		include 'usr/plugins/Album/Data/Config.inc.php';

 		$type = array( '.jpg','.jpeg','.gif','.png' );
 		$ext = strrchr($url, ".");

 		if ( in_array(strtolower($ext),$type) === false ){
 			$msg['err'] = 13 ;
 			$msg['msg'] = $url;
 			return $msg;
 		}

 		$options = Typecho_Widget::widget('Widget_Options');
 		$siteUrl = $options->siteUrl;
 		$date = new Typecho_Date($options->gmtTime);
 		$path = Typecho_Common::url(self::UPLOAD_PATH, __TYPECHO_ROOT_DIR__). DIRECTORY_SEPARATOR . $date->year . DIRECTORY_SEPARATOR . $date->month . DIRECTORY_SEPARATOR;
 		$thumb_path = $path . 'thumb/';
 		$new_name = time().rand(0,1000).$ext ;

 		if (!is_dir($thumb_path)) {
 			if (!Common::makeDir($thumb_path)) {
 				$msg['err'] = 12 ;
 				$msg['msg'] = $thumb_path ;
 				return $msg;
 			}
 		}

 		$Info = pathinfo($url);

 		if ( $remote_Method == 'curl'){
 			$referer = $Info['dirname'];
 			$ch = curl_init();
 			if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
 				curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
 			}
 			$useragent="Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.56 Safari/536.5";
 			curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
 			curl_setopt($ch, CURLOPT_REFERER, $referer);
 			curl_setopt($ch, CURLOPT_COOKIESESSION, true);
 			curl_setopt($ch, CURLOPT_URL, $url);
 			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
 			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //2014/1/10 14:00:16
 			curl_setopt($ch, CURLOPT_HEADER, 0);
 			$content = curl_exec($ch);
 			$img_info = curl_getinfo($ch);
 			curl_close($ch);
 		
 			if ($img_info['http_code'] == 200){
 				@file_put_contents($path.$new_name, $content);
 				$msg['size'] = $img_info['size_download'];
 			}else{
 				$msg['err'] = 14;
 				$msg['msg'] = $Info['dirname'];
 				return $msg;
 			}

 		}else{
 			ob_start();
 			readfile($url);
 			$img = ob_get_contents();
 			ob_end_clean();

 			$msg['size'] = strlen($img);

 			$fp = @fopen($path.$new_name, "a");
 			fwrite($fp, $img);
 			fclose($fp);
 		}

 	$msg['name'] = $Info['basename'];
 	$msg['path'] = $path.$new_name;
 	$msg['pixel'] = @getimagesize($path.$new_name);
 	$msg['url'] = $siteUrl.str_replace( __TYPECHO_ROOT_DIR__.DIRECTORY_SEPARATOR ,'',$path.$new_name );
 	$msg['server'] = $upload_Server ;
 	return $msg;

}


}
