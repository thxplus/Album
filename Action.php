<?php
/**
 * Album For Typecho, base on 0.9 bulid 
 *
 * @package Album
 * @author 我本奈何
 * @version 1.2
 * @link https://plsYou.com
 */

class Album_Action extends Typecho_Widget implements Widget_Interface_Do
{
	
	public function action(){
		
		$time = self::runtime();
		$options = Typecho_Widget::widget('Widget_Options');
		$siteUrl = $options->siteUrl;
		
		if ( !@file_exists( 'usr/plugins/Album/Data/install.lock' ) ) {
			header('Location: '. $siteUrl. 'usr/plugins/Album/Data/Install.php');
		}else{
			require_once 'Data/Baseconfig.php';
		}
		
		include_once 'Service/Common.php';
		
		$url = Common::request_path();
		$uri = Common::request_uri($_SERVER["REQUEST_URI"]);
		$language = parse_ini_file('Data/Language.ini', 'ture');
		$theme_dir = __TYPECHO_ROOT_DIR__ . __TYPECHO_PLUGIN_DIR__ . '/Album/Template' . $template ;
		$charset = $options->charset;
		$title = $options->title;
		$Base_path = $siteUrl . __TYPECHO_PLUGIN_DIR__ . '/Album/Template' . $template ;
		$logoUrl = $options->logoUrl;
		$description = $options->description;
		
		$Page = $Category = array();
		$Page = Typecho_Widget::widget('Widget_Contents_Page_List')->stack;
		if ( $category_show == 'yes' ){
			$Category = Typecho_Widget::widget('Widget_Metas_Category_List@album_Index')->stack ;
		}
		$i = 0 ; $menu = '' ;$n = count($Page);$m = count($Category);
		$nav = array_merge($Category,$Page);
		
		foreach ( $nav as $v ){
			$current = '' ;  $p = '' ; $i ++ ;
			$n + $m > $i ? $p =  "\n                    " : '' ;
			if ( strpos($v['permalink'], $app_path) ) $current = ' class="active" ';
			if ( empty($v['title']) ) $v['title'] = $v['name'];
			$menu .=  '<li'.$current.'><a href="' . $v['permalink'] . '" title="' . $v['title'] . '">' . $v['title'] . '</a></li> ' . $p ;
		}
		$k = explode(',',$uri);

		if ( substr($k['3'],0,1) == '2' ){
			include_once 'Service/Manage.php';
			$res = new Manage();
			$DATA = $res->setManage();
			$theme_include =  $theme_dir.'Manage.php';
		}else{
			include 'Service/Index.php';
			$res = new Index();
			$DATA = $res->show($uri);
			$theme_include =  $theme_dir.'Index.php';
		}

		include_once 'Service/Sidebar.php';
		$sidebar = Sidebar::get($siteUrl,$url,$uri,$language);
		$run_time = number_format( self::runtime() - $time,6);
		include $theme_include;
		
	}
	
	public static function runtime(){
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}
	
}
?>
