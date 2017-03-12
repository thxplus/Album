	<?php

 /**
 * Album For Typecho, base on 1.0/14.10.10
 * @copyright  Copyright (c) 2014 我本奈何 (https://plsYou.com)
 * @license    GNU General Public License 2.0
 * @version    $Id: Common.php 2014-8-15 10:28:36
 */

 class Common {
 	
 	public static function request_path(){
 		return self::base() . '/' ;
 	}
 	
 	public static function request_uri( $request=NULL ){
 		$path = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"] ;
 		$uri = str_replace( self::base() ,'',$path);
 		if ( $uri == NULL ){
			$req = '0,0,0,0,1';//c,f,d,m,p
		}else{
			$uri = str_replace( '/','',$uri );
			$k = explode(',',$uri);
			$time = substr($k['2'],1);
			$date = date('Y-m-d H:i:s',$time) ;
			$time == strtotime($date) ? '' : $k['2'] = 0 ;
			$c = array('0','0','0','0','1');
			for( $i=0; $i < count($c); $i++ ){
				if ( empty($k[$i]) || !ctype_digit($k[$i]) ) $k[$i] = $c[$i];
			}
			$req = implode(',',$k);
		}
		return $req ;
	}
	
	private static function base(){
		
	//	include 'usr/plugins/Album/Data/Baseconfig.php';
		$options = Typecho_Widget::widget('Widget_Options');
		$options->rewrite == 0 ? $index = 'index.php/' : $index = '';
	//	$page_rewrite = $options->routingTable;
	//	$length = strlen($page_rewrite['page']['url']);
	//	$page_base = substr($page_rewrite['page']['url'],1/*,($length-1)*/);
	//	$page_base = str_replace('[slug]',$app_path,$page_base);
  //	$db = Typecho_Db::get();
  //	$dbres = $db->fetchAll($db->select('cid')->from('table.contents')->where('slug=?',$app_path));
	//	$page_base = str_replace('[cid:digital]',$dbres['0']['cid'],$page_base);
		return $options->siteUrl . $index . 'Album';
	}
	
	
	
	public static function admin(){
		if ( Typecho_Widget::widget('Widget_User')->pass('administrator', true) ){
			return true;
		}
		return false;
	}
	
	public static function current_uri($uri , $req , $pos){
		$m = explode(',',$uri);
		$n = explode(',',$req);
		$i = $pos - 1 ;
		if ( $m[$i] == $n[$i] ) return 'active';
		return false;
	}
	
	public function check_in($keys, $text){
		foreach($keys as $key){
			if(strstr($text,$key)!=''){
				return true;
			}
			return false;
		}
	}
	
	public static function form($data){
		if ( is_Array($data) ){
			foreach ( $data as $key=>$value ){
				$data[$key] = self::tag($data[$key]);
			}
		}else{
			self::tag($data);
		}
		return $data;
	}
	
	public function tag($data){
		$data = trim ($data);
		$data = strip_tags ($data);
		$data = htmlspecialchars ($data);
		$data = addslashes ($data);
		return $data;
	}
	
	public static function thumb( $img_path, $thumb_path, $img_msg, $rename = false ,$token = false ){
		
		if ( $token == true ){
			$arr_url = parse_url($img_path);
			$_imgUrl = $arr_url['scheme'] . '://' . $arr_url['host'] . $arr_url['path'] ;
		}else{
			$_imgUrl = $img_path;
		}
		
		include 'usr/plugins/Album/Data/Config.inc.php';
		if ($thumb_Engine == 'GD'){
			
			if ($img_msg['2'] > 4) {
				$res['err'] = 4 ;
				$res['msg'] = 'Error Thumb File Type! [GD METHOD]';
				return $res;
			}
			
			$info = pathinfo($_imgUrl);
			$ImgWidth = $img_msg['0'];
			$ImgHeight = $img_msg['1'];
			$WH = $ImgWidth/$ImgHeight;

			switch ( $thumb_Method ){
				case 1 :
				$width = $thumb_width ;
				$height = $width/$WH ;
				if ( $width/$WH > $thumb_MaxHeight ) $height = $thumb_MaxHeight ;
				break;
				case 2 :
				$height = $thumb_height ;
				$height*$WH > $thumb_MaxWidth ? $width = $thumb_MaxWidth : $width = $height*$WH ;
				break;
				case 3 :
				$width = $thumb_width ;
				$height = $thumb_height ;
				break;
			}
			
			switch ($img_msg['2']){
				case 1 : $Img = @imagecreatefromgif($img_path);break;
				case 2 : $Img = @imagecreatefromjpeg($img_path);break;
				case 3 : $Img = @imagecreatefrompng($img_path);break;
			}
			
			if ( $rename === false ){
				$thumb_Path = $thumb_path.$info['basename'];
			}else{
				$thumb_Path = $thumb_path . time() . rand(0,1000) . '.' . $info['extension'];
			}
			
			$imgTemp = imagecreatetruecolor($width, $height);
			
			imagealphablending($imgTemp, FALSE);//2014/1/11 14:35:51
			imagesavealpha($imgTemp,TRUE);//2014/1/11 14:35:53
			
			if ( ( $thumb_Method == '3' ) && ( $thumb_createMethod == '2' ) ){

				$m = $width/$height ;
				$n = $ImgWidth/$ImgHeight ;
				
				if ( $m < $n ){
					$tmp_width = $width;
					$tmp_height = $width/$n ;
				}else{
					$tmp_width = $height*$n ;
					$tmp_height = $height ;
				}
				
				$fill_color = self::hex2rgb($thumb_createColor);
				$color = imagecolorallocate($imgTemp, $fill_color['r'], $fill_color['g'], $fill_color['b']);
				imagefill($imgTemp, 0, 0, $color);
				$x = round(($width - $tmp_width) / 2);
				$y = round(($height - $tmp_height) / 2);
				$width = $tmp_width ;
				$height = $tmp_height;

			}else{
				$x = 0;
				$y = 0;
			}

			imagecopyresampled($imgTemp, $Img, $x, $y, 0, 0, $width, $height, $ImgWidth, $ImgHeight); //debug 2014/3/3 23:24:07
			
			switch ($img_msg['2']){
				case 1 : @imagegif($imgTemp, $thumb_Path);break;
				case 2 : @imagejpeg($imgTemp, $thumb_Path, $thumb_Jpeg);break;
				case 3 : @imagepng($imgTemp, $thumb_Path);break;
			}
			
			@imagedestroy($Img);
			@imagedestroy($imgTemp);
			
			$res['url'] = $thumb_Path ;
			
			return $res;

		}
		
	}
	
	public static function exif($file,$type){
		
		$supported = array('image/jpeg');
		
		if ( !in_array($type,$supported) ){
			$msg['err'] = 6 ;
			$msg['msg'] = 'File not supported! [EXIF]';
			return $msg;
		}
		
		if( !function_exists('read_exif_data') ) {
			$msg['err'] = 7 ;
			$msg['msg'] = 'Function read_exif_data() Not Exists!';
			return $msg;
		}
		
		if ( read_exif_data($file, 'IFD0') === false ){
			$msg['err'] = 8 ;
			$msg['msg'] = 'None EXIF Message Found!';
			return $msg;
		}
		
		$EXIF = read_exif_data($file ,'EXIF' ,0);
		$Undefined = 'Undefined';
		$Defined = false;
		
		if ( isset($EXIF['Make']) ){
			$camera['Make'] = $EXIF['Make'];
		}else{
			$camera['Make'] = false;
		}
		
		if ( isset($EXIF['Model']) ){
			$camera['Model'] = $EXIF['Model'];
		}else{
			$camera['Model'] = false;
		}
		
		if ( /*($camera['Make'] != false) && ($camera['Model'] != false) ){
			$msg['camera'] = $camera['Make'].' / '.$camera['Model'];
			$Defined = true;
		}else if ( */$camera['Model'] != false ){
			$msg['camera'] = $camera['Model'];
			$Defined = true;
		}else if ( $camera['Make'] != false ){
			$msg['camera'] = $camera['Make'];
			$Defined = true;
		}else{
			$msg['camera'] = $Undefined;
		}
		
		if ( isset($EXIF['UndefinedTag:0xA434']) ){ // Nikon Lens
			$msg['lens'] = $EXIF['UndefinedTag:0xA434'];
			$Defined = true;
		}else if ( isset($EXIF['UndefinedTag:0x0095']) ){ // Canon Lens
			$msg['lens'] = $EXIF['UndefinedTag:0x0095'];
			$Defined = true;
		}else{
			$msg['lens'] = $Undefined;
		}
		
		if ( isset($EXIF['COMPUTED']['ApertureFNumber']) ){
			$msg['aperture'] = $EXIF['COMPUTED']['ApertureFNumber'];
			$Defined = true;
		}else{
			$msg['aperture'] = $Undefined;
		}
		
		if ( isset($EXIF['ExposureTime']) ){
			$msg['shutterSpeed'] = $EXIF['ExposureTime'];
			$Defined = true;
		}else{
			$msg['shutterSpeed'] = $Undefined;
		}
		
		if ( isset($EXIF['FocalLength']) ){
			$msg['focalLength'] = self::exif_get_float($EXIF['FocalLength']);
			$Defined = true;
		}else{
			$msg['focalLength'] = $Undefined;
		}
		
		if ( isset($EXIF['FocalLengthIn35mmFilm']) ){
			$msg['focalLength35mmFilm'] = $EXIF['FocalLengthIn35mmFilm'];
			$Defined = true;
		}else{
			$msg['focalLength35mmFilm'] = $Undefined;
		}
		
		if ( isset($EXIF['ISOSpeedRatings']) ){
			$msg['ISO'] = $EXIF['ISOSpeedRatings'];
			$Defined = true;
		}else{
			$msg['ISO'] = $Undefined;
		}
		
		if ( isset($EXIF['DateTimeOriginal']) ){ 
			$msg['time'] = $EXIF['DateTimeOriginal'];
			$Defined = true;	// fixed 2014/2/26 20:49:19
		}else{
			$msg['time'] = $Undefined;
		}
		
		if ( $Defined == false ){
			$msg['err'] = 5 ;
			$msg['msg'] = 'All EXIF Message Undefined!';
			return $msg;
		}
		
		return $msg;
	}
	
	public static function makeDir($path){    
		return is_dir($path) || (self::makeDir(dirname($path)) && mkdir($path,0777));
	} 
	
	public function exif_get_float($value) { 
		$pos = strpos($value, '/'); 
		if ($pos === false) return (float) $value; 
		$a = (float) substr($value, 0, $pos); 
		$b = (float) substr($value, $pos+1); 
		return ($b == 0) ? ($a) : ($a / $b); 
	} 
	
	public function hex2rgb($hexColor) { 

		$color = str_replace('#', '', $hexColor); 
		if (strlen($color) > 3) { 
			$rgb = array( 
				'r' => hexdec(substr($color, 0, 2)), 
				'g' => hexdec(substr($color, 2, 2)), 
				'b' => hexdec(substr($color, 4, 2)) 
				); 
		} else { 
			$r = substr($color, 0, 1); 
			$g = substr($color, 1, 1); 
			$b = substr($color, 2, 1); 
			$rgb = array( 
				'r' => hexdec($r.$r), 
				'g' => hexdec($g.$g), 
				'b' => hexdec($b.$b) 
				); 
		} 
		
		if ( (strlen($color) < 3) || (strlen($color) > 6) ){
			$rgb = array( 'r'=>'255','g'=>'255','b'=>'255' );
		}
		
		return $rgb; 
	} 

	public static function CheckWebAddr($url){ 
		if (!ereg("^http://[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*$", $url)) { 
			return false; 
		} 
		return true; 
	} 

	public static function check_remote($url){
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		$result = curl_exec($curl);
		$exists = false;
		if ($result !== false){
			$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if ($statusCode == 200){$exists = true;}
		}
		curl_close($curl);
		return $exists;
	}
	
	public static function remote_filesize($url){
		ob_start();
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
/*	2014/1/10 12:50:58
		if (!empty($user) && !empty($pw)){m
			$headers = array('Authorization: Basic ' . base64_encode($user.':'.$pw));
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
*/
		$okay = curl_exec($ch);
		curl_close($ch);
		$head = ob_get_contents();
		ob_end_clean();

		$regex = '/Content-Length: (\d+)/si'; //2014/1/10 13:22:28
		preg_match($regex, $head, $matches);
		
		if (isset($matches[1])){
			$size = $matches[1];
		}else{
			$size = '0';
		}
		return $size;
	}

}

