<?php

 /**
 * Album For Typecho, base on 1.0/14.10.10
 * @copyright  Copyright (c) 2014 我本奈何 (https://plsYou.com)
 * @license    GNU General Public License 2.0
 * @version    $Id: Manage.php 2014/1/13 20:18:50
 */

class Manage{
	
	public function setManage(){
		
		if ( Common::admin() == false ) {
			$msg['ERR']['KEY']['0'] = 51 ;
			$msg['ERR']['MSG']['0'] = '请先登录';
			return $msg;
		}else{
			$msg['SUC']['KEY']['0'] = 0 ;
		}

		$uri = Common::request_uri($_SERVER["REQUEST_URI"]);
		$url = Common::request_path();
		$k = $kk = explode(',',$uri); 
		$Nav_name = array('基础设置','高级设置','语言设置','分类管理','重建统计');
		
		for ($i=0;$i<count($Nav_name);$i++){
			$kk['0'] = $kk['1'] = $kk['2'] = '0';
			$kk['3'] = '2'.$i;
			$req[$i] = implode(',',$kk);
		}
		
		for ( $i = 0; $i < count($Nav_name); $i++ ){
			if ( Common::current_uri($uri , $req[$i] , '4') ){
				$msg['NAV']['current'][$i] = 'btn-success';
			}else{
				$msg['NAV']['current'][$i] = 'btn-info';
			}
			
			$msg['NAV']['url'][$i] = $url.$req[$i];
			$msg['NAV']['name'][$i] = $Nav_name[$i];
		}
		
		if ( !strpos( implode($msg['NAV']['current']) ,'success' ) ){ 
			$msg['NAV']['current']['0'] = 'btn-success';
		}

		Switch ($k['3']){
			
			case 20 :
				$msg['CONTENT'] = self::baseconfig($url,$req);
				break;
			case 21 :
				$msg['CONTENT'] = self::config_inc($url,$req);
				break;
			case 22 :
				$msg['CONTENT'] = self::language($url,$req);
				break;
			case 23 :
				$msg['CONTENT'] = self::category($url,$req);
				break;
			case 24 :
				$msg['CONTENT'] = self::total($url,$req);
				break;
			default :
				$msg['CONTENT'] = self::baseconfig($url,$req);	
				
		}
		
		if ( isset( $msg['CONTENT']['ERR'] ) ){
			$msg['ERR'] = $msg['CONTENT']['ERR'];
		}

		return $msg;
		
	}
	
	private function config_inc($url,$req){
		
		$result['action'] = $url.$req['1'];
		if(isset($_POST['submit'] )){
			$config_inc = $_POST['textarea'];
			if (! @file_put_contents('usr/plugins/Album/Data/Config.inc.php', $config_inc) ){
				$result['ERR']['KEY']['0'] = 52 ;
				$result['ERR']['MSG']['0'] = '写入配置文件失败，请检查配置文件属性。<br/>配置文件位于插件目录 Data/Config.inc.php , 请设置属性为 777 ！';
			}else{
				$result['ERR']['KEY']['0'] = 50 ;
				$result['ERR']['MSG']['0'] = '保存成功！';
			}
		}

		$config_inc = @file_get_contents('usr/plugins/Album/Data/Config.inc.php');
		
		if ( $config_inc == false ){ 
			$result['ERR']['KEY']['0'] = 53 ;
			$result['ERR']['MSG']['0'] = '读取配置文件失败，请检查配置文件属性。<br/>配置文件位于插件目录 Data/Config.inc.php , 请设置属性为 777 ！';
			return $result;
		}
		
		$result['CONFIG'] = $config_inc;
		return $result;	
	}
	
	private function language($url,$req){
		
		$result['action'] = $url.$req['2'];
			
		if(isset($_POST['submit'] )){
			
			foreach ( $_POST as $key=>$value ){
				$k = explode('-',$key);
				if ( isset($k['1']) ){
					$data[$k['0']][$k['1']] = $value;
				}
			}
				
			if ( self::write_ini_file($data, 'usr/plugins/Album/Data/Language.ini', true) === true ){
				$result['ERR']['KEY']['0'] = 50 ;
				$result['ERR']['MSG']['0'] = '保存成功！';
			}else{
				$result['ERR']['KEY']['0'] = 54 ;
				$result['ERR']['MSG']['0'] = '操作失败,请检测文件属性！配置文件位于插件目录 Data/Language.ini , 请设置属性为 777 ！';
			}
				
		}
				
		$language = @parse_ini_file('usr/plugins/Album/Data/Language.ini', 'ture');
		
		if ( $language === false ){ 
			$result['ERR']['KEY']['0'] = 53 ;
			$result['ERR']['MSG']['0'] = '读取配置文件失败，请检查配置文件属性。<br/>配置文件位于插件目录 Data/Language.ini , 请设置属性为 777 ！';
			return $result;
		}

		$result['LANGUAGE'] = $language ;
		return $result;	
	}
	
	private function category($url,$req){
		$db = Typecho_Db::get();
		$result['action'] = $url.$req['3'];

		if( isset($_POST['submit'] )){
			
			$_POST = array_map('Common::form' , $_POST);
			
			for ( $i = 0; $i < count($_POST['name']); $i++ ){
				
				if ( empty($_POST['name'][$i]) ){ continue;}
				
					$result['ERR']['KEY']['0'] = 50 ;
					$result['ERR']['MSG']['0'] = '操作完成 ！';
					
				if ( $_POST['add'][$i] == 'add' ){
					$db->query($db->insert('table.album_category')->rows(array('id' => NULL, 'name' => $_POST['name'][$i], 'public' => $_POST['public'][$i], 'description' => $_POST['description'][$i],'count'=>'0' )));
				}
				
				if ( $_POST['add'][$i] == 'del' && $_POST['id'][$i] != '1' ){
					$db->query($db->delete('table.album_category')->where('id = ?', $_POST['id'][$i]));
					$db->query($db->update('table.album')->where('category = ?', $_POST['id'][$i])->rows(array( 'public'=>$_POST['public']['0'],'category' => '1' )));
					$_POST['count']['0'] = $_POST['count']['0'] + $_POST['count'][$i] ;
					$db->query($db->update('table.album_category')->where('id = ?', '1')->rows(array( 'count' => $_POST['count']['0'] )));
				}else if ( $_POST['add'][$i] == 'del' && $_POST['id'][$i] == '1' ){
					$result['ERR']['KEY']['0'] = 55 ;
					$result['ERR']['MSG']['0'] = '默认分类无法删除 ！';
				}
				
				if ( $_POST['add'][$i] == 'Modify' ){
					$db->query($db->update('table.album_category')->where('id = ?', $_POST['id'][$i])->rows(array( 'name' => $_POST['name'][$i], 'public' => $_POST['public'][$i], 'description' => $_POST['description'][$i] )));
				}
			}
		}
	
  	$res = $db->fetchAll($db->select()->from('table.album_category'));
		$result['CATEGORY'] = $res;
		return $result;	
	}
	
	private function total($url,$req){
		$msg['action'] = $url.$req['4'];
		if(isset($_POST['submit'] )){
			$db = Typecho_Db::get();
			$prefix = $db->getPrefix();
		
			$result = $db->fetchAll($db->select('id')->from('table.album_category'));
		
			for( $i = 0; $i < count($result); $i++ ){
				$db->query($db->update('table.album_category')->where('id = ?', $result[$i]['id'])->rows(array('count' => count($db->fetchAll($db->select()->from('table.album')->where('category = ?', $result[$i]['id'] ))))));
			}
		
			$shoot = $db->fetchAll($db->query( "SELECT COUNT(*) AS shoot FROM {$prefix}album_shoot ;"));
			$local = $db->fetchAll($db->query( "SELECT COUNT(*) AS local FROM {$prefix}album_local ;"));
			$network = $db->fetchAll($db->query( "SELECT COUNT(*) AS network FROM {$prefix}album_network ;"));
			$total = $shoot['0']['shoot']+$local['0']['local']+$network['0']['network'];
			$db->query( "UPDATE {$prefix}album_count SET total={$total},shoot={$shoot['0']['shoot']},local={$local['0']['local']},network={$network['0']['network']} WHERE id=1 ;");
		
			$msg['ERR']['KEY']['0'] = 50 ;
			$msg['ERR']['MSG']['0'] = '重建统计完成！';
		}
		$msg['COUNT'] = '';
		return $msg;	
	}
	
	private function baseconfig($url,$req){
		
		if(isset($_POST['submit'] )){
			$_POST = array_map('Common::form' , $_POST);
			$lines[] = '<?php
/**
 * Album For Typecho, base on 0.9 bulid
 *
 * @package Album Base Config
 * @author 我本奈何
 * @version 1.2
 * @link http://way.so
 */';
 			$lines[] = "\n\n";
		//	$lines[] = "\$app_name = '".$_POST['app_name']."';\n";												//相册名称
		//	$lines[] = "\$app_path = '".$_POST['app_path']."';\n";												//相册路径名
			$lines[] = "\$app_welcome = '".$_POST['app_welcome']."';\n";									//欢迎语
			$lines[] = "\$read_order = '".$_POST['read_order']."';\n";										//读取顺序
			/* 二个模板体系 */
			//$lines[] = "\$theme = '".$_POST['theme']."';\n";															//相册模板类型 album or typecho 
			/* 检测目录 确定模板名称*/
			$lines[] = "\$template = '/".$_POST['template']."/';\n";											//相册模板选择 
			$lines[] = "\$category_show = '".$_POST['category_show']."';\n";							//菜单是否显示分类
			$lines[] = "\$per_page_num = '".$_POST['per_page_num']."';\n";										//每页图片显示数量
			$lines[] = "\$page_nav_show = '".$_POST['page_nav_show']."';\n";										//是否显示页码导航
			$lines[] = "\$ajax_Calendar = '".$_POST['ajax_Calendar']."';\n";							//是否使用ajax_Calendar
			$contents = implode('', $lines);
			
			if (! @file_put_contents('usr/plugins/Album/Data/Baseconfig.php', $contents) ){
				$result['ERR']['KEY']['0'] = 52 ;
				$result['ERR']['MSG']['0'] = '配置文件失败，请检查配置文件属性。<br/>配置文件位于插件目录 Data/Baseconfig.php , 请设置属性为 777 ！';
			}else{
				$result['ERR']['KEY']['0'] = 50 ;
				$result['ERR']['MSG']['0'] = '保存成功！';
			}

		}
			
		include 'usr/plugins/Album/Data/Baseconfig.php';
		$dir = 'usr/plugins/Album/Template/';
		
			if ($tpl = opendir($dir)){
				$tpl_dir = '';
				while (($file = readdir($tpl)) !== false){
					if ( $file != '.' && $file != '..' && is_dir( $dir.$file ) ){
						if ( $template == '/'.$file.'/' ){ 
							$select = 'selected';
						}else{
							$select = '';
						}
         		$tpl_dir .= '<option value="'.$file.'" '.$select.'>'.$file.'</option>' ;
         		$select = '';
         	}
				}
				closedir($tpl);
			}
			
		$result['BASE'] = array
								( 
										//'app_name' => $app_name,
										//'app_path' => $app_path,
										'app_welcome' => $app_welcome,
										'read_order' => $read_order,
										//'theme' => $theme,
										'template' => $tpl_dir,
										'category_show' => $category_show,
										'per_page_num' => $per_page_num,
										'page_nav_show' => $page_nav_show,
										'ajax_Calendar' => $ajax_Calendar,
								);	
																											
		$result['action'] = $url.$req['0'];
		return $result;	
	}
	
	private static function write_ini_file($assoc_arr, $path, $has_sections = true) {
		$content = "";
		if ($has_sections) {
			foreach ( $assoc_arr as $key => $elem ) {
				$content .= "[" . $key . "]\n";
				foreach ( $elem as $key2 => $elem2 ) {
					if (is_array ( $elem2 )) {
						for($i = 0; $i < count ( $elem2 ); $i ++) {
							$content .= $key2 . "[] = " . $elem2 [$i] . "\n";
						}
					} else if ($elem2 == ""){
						$content .= $key2 . " = \n";
        	}else{
          	$content .= $key2 . " = " . $elem2 . "\n";
        	}
				}
			}
    } else {
			foreach ( $assoc_arr as $key => $elem ) {
				if (is_array ( $elem )) {
					for($i = 0; $i < count ( $elem ); $i ++) {
						$content .= $key2 . "[] = " . $elem [$i] . "\n";
					}
				} else if ($elem == ""){
					$content .= $key2 . " = \n";
				}else{
					$content .= $key2 . " = " . $elem . "\n";
				}
			}
    }
		if (! $handle = @fopen ( $path, 'w' )) {
			return false;
		}
		if (! @fwrite ( $handle, $content )) {
			return false;
		}
		fclose ( $handle );
		return true;
  }
  
}
