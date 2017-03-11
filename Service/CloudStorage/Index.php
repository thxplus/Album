<?php

 /**
 * Album For Typecho, base on 1.0/14.10.10
 * @copyright  Copyright (c) 2014 我本奈何 (https://plsYou.com)
 * @license    GNU General Public License 2.0
 * @version    $Id: Index.php 2014/3/11 23:17:14
 */


class CloudStorage{
	
	public static function post($server,$file,$method,$path,$name){
		
		include 'usr/plugins/Album/Data/Config.inc.php';
		
		$config = array();
		$config['path'] = $path ;
		$config['method'] = $method ;
		$config['name'] = $name ;
		
		Switch ( $server ){
			
			case 'qiniu' :	
				$config['bucket'] = $qiniu_bucket;
				$config['domain'] = $qiniu_domain;		
				$config['AccessKey'] = $qiniu_AccessKey;
				$config['SecretKey'] = $qiniu_SecretKey; 
				$config['public'] = $qiniu_public ;		
				$config['time'] = $qiniu_time ;
				break;
			
			case 'upyun' :
				$config['bucket'] = $upyun_bucket;
				$config['domain'] = $upyun_domain;
				$config['user'] = $upyun_user;
				$config['psw'] = $upyun_psw;
				$config['token'] = $upyun_token;
				$config['time'] = $upyun_time;
				$config['key'] = $upyun_key;
				break;

			case 'baidupcs' :
				//$config[''] = '';
				break;
				
			case 'baidubcs' :
				$config['APIKey'] = $bcs_APIKey; 
				$config['SecretKey'] = $bcs_SecretKey;
				$config['bucket'] = $bcs_bucket;
				$config['domain'] = $bcs_domain;
				$config['public'] = $bcs_public;
				$config['time'] = $bcs_time;
				break;
				
		}
		
		$result = self::$server($file,$config);
		
		return $result;
		
	}
	
	private function baidubcs($file,$config){
		
		require_once("BaiduBCS/bcs.class.php");
		$baidu_bcs = new BaiduBCS ( $config['APIKey'], $config['SecretKey'], str_replace('http://','',$config['domain']) );
		
		if ( $config['method'] == 'upload' ){
			
			$config['public'] == true ? $opt = array("acl" => "public-read") : $opt = array("acl" => "private") ;
			$response = $baidu_bcs->create_object($config['bucket'], DIRECTORY_SEPARATOR.$config['path'].DIRECTORY_SEPARATOR.$config['name'], $file, $opt);
			
			if ($response->isOK()){
				return $config['domain'].DIRECTORY_SEPARATOR.$config['bucket'].DIRECTORY_SEPARATOR.$config['path'].DIRECTORY_SEPARATOR.$config['name'];
			}
			
			return false;
			
		}else if ( $config['method'] == 'del' ){
			 
			$url = str_replace($config['domain'].DIRECTORY_SEPARATOR,'',$config['path']) ;
			$baidu_bcs->delete_object ( substr($url,0,strpos($url,'/')), substr($url,strpos($url,'/')) );
			return true;
			
		}else if ( $config['method'] == 'mkurl' ){
			
			if ( $config['public'] == true ) return $config['path'];
			$url = str_replace($config['domain'].DIRECTORY_SEPARATOR,'',$config['path']) ;
			$data = "MBOT\nMethod=GET\nBucket=".substr($url,0,strpos($url,'/'))."\nObject=".substr($url,strpos($url,'/'))."\nTime=".(time()+$config['time'])."\n";
			$sign = urlencode(base64_encode(hash_hmac('sha1', $data, $config['SecretKey'], true)));
			return $config['path'].'?sign=MBOT:'.$config['APIKey'].':'.$sign.'&time='.(time()+$config['time']).'&response-cache-control=private' ;
			
			
		}else if ( $config['method'] == 'curl' ){
			
			$config['public'] == true ? $opt = array("acl" => "public-read") : $opt = array("acl" => "private") ;
			$response = $baidu_bcs->create_object_by_content($config['bucket'], DIRECTORY_SEPARATOR.$config['path'].DIRECTORY_SEPARATOR.$config['name'], file_get_contents($file), $opt);
			
			if ($response->isOK()){
				return $config['domain'].DIRECTORY_SEPARATOR.$config['bucket'].DIRECTORY_SEPARATOR.$config['path'].DIRECTORY_SEPARATOR.$config['name'];
			}
			
		}
		
		return false;
		
	}
	
	private function baidupcs($file,$config){
		// 我还在申请API。。 无语
		return ;
	}
	
	private function qiniu($file,$config){
		
		require_once("Qiniu/io.php");
		require_once("Qiniu/rs.php");
		Qiniu_setKeys($config['AccessKey'], $config['SecretKey']);
		
		if ( $config['method'] == 'upload' ){
			$policy = new Qiniu_RS_PutPolicy($config['bucket']);
    	$token = $policy->Token(null);
    	$extra = new Qiniu_PutExtra();
    	$extra->Crc32 = 1;
    
    	list($result, $error) = Qiniu_PutFile($token, $config['path'].DIRECTORY_SEPARATOR.$config['name'], $file, $extra);
    
			if ($error == null){
      	return $config['domain'].DIRECTORY_SEPARATOR.$config['path'].DIRECTORY_SEPARATOR.$config['name'];
			}else{
				return false;
			}
		
		}else if ( $config['method'] == 'del' ){
			
			$client = new Qiniu_MacHttpClient(null);
			$path = str_replace($config['domain'].DIRECTORY_SEPARATOR,'',$config['path']);
    	Qiniu_RS_Delete($client, $config['bucket'], $path);
    
			return true;
			
		}else if ( $config['method'] == 'mkurl' ){
			
			if ( $config['public'] == true ) return $config['path'];
			$getPolicy = new Qiniu_RS_GetPolicy();
			$getPolicy->Expires = $config['time'];
			return $getPolicy->MakeRequest($config['path'], NULL);
			
		}else if ( $config['method'] == 'curl' ){
			
			$policy = new Qiniu_RS_PutPolicy($config['bucket']);
    	$token = $policy->Token(null);
    	$extra = new Qiniu_PutExtra();
    	$extra->Crc32 = 1;
    	$file = file_get_contents( $file );
    	$_img_file = __TYPECHO_ROOT_DIR__.'/usr/plugins/Album/Data/Cache/tmp'.rand(0,100) ;
    	$fp = fopen ( $_img_file, 'w+' ); 
      fwrite ( $fp, $file );  
      fclose ( $fp ); 
    	list($result, $error) = Qiniu_PutFile($token, $config['path'].DIRECTORY_SEPARATOR.$config['name'], $_img_file, $extra);
    	unlink($_img_file);
    	
			if ($error == null){
      	return $config['domain'].DIRECTORY_SEPARATOR.$config['path'].DIRECTORY_SEPARATOR.$config['name'];
			}else{
				return false;
			}
			
		}
		
		return false;
		
	}
	
	private function upyun($file,$config){
		
		require_once('Upyun/upyun.class.php');
		$upyun = new UpYun($config['bucket'], $config['user'], $config['psw']);
		
		if ( $config['method'] == 'upload' || $config['method'] == 'curl' ){

			$result = $upyun->writeFile("/".$config['path'].DIRECTORY_SEPARATOR.$config['name']."/", file_get_contents($file) ,TRUE);
			
			return $config['domain'].DIRECTORY_SEPARATOR.$config['path'].DIRECTORY_SEPARATOR.$config['name'];
			
		}else if ( $config['method'] == 'del' ){
			
			$path = str_replace($config['domain'].DIRECTORY_SEPARATOR,'',$config['path']);
			$upyun->delete("/{$path}/");
			
			return true;
			
		}else if ( $config['method'] == 'mkurl' ){
			
			if ( $config['token'] == false ) return $config['path'];
			$sign = substr(md5($config['key'].'&'.( time() + $config['time'] ).'&'.str_replace($config['domain'],'',$config['path'])), 12,8).( time() + $config['time'] );
			return $config['path'].'?_upt='.$sign;
			
		}
		
		return false;
		
	}
	
}