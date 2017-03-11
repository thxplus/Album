<?php

 /**
 * Album For Typecho, base on 1.0/14.10.10
 * @copyright  Copyright (c) 2014 我本奈何 (https://plsYou.com)
 * @license    GNU General Public License 2.0
 * @version    $Id: Index.php 2014-8-15 11:26:22
 */
 
class Index{
	public function show( $req = array() ){
		include 'usr/plugins/Album/Data/Baseconfig.php';
		$k = explode(',',$req);
		$From = array( 'all','local','shoot','network');
		$db = Typecho_Db::get();
		$cur_page = $k['4'];
		
		if ( $k['3'] == "1" ){
			$url = Common::request_path();
			$manage_Data = array();
			$manage_Data['action'] = $url.$req;
			$manage_Data['category'] = NULL;
  		$db_category = $db->fetchAll($db->select()->from('table.album_category'));

  		for ( $i=0; $i<count($db_category); $i++ ){
  			$manage_Data['category'] .= '<option value="'.$db_category[$i]['id'].'">'.$db_category[$i]['name'].'</option>';
  		}
  		
  		if ( isset($_POST['submit']) ){
  			
				$MSG = array
						(
								'0' => 'Update Complete',
								'1' => 'No File Chosen',
								'2' => 'Error File Type',
								'3' => 'Failed To upload',
								'4' => 'Error Thumb File Type! [GD METHOD]',
								'5' => 'All EXIF Message Undefined',
								'6' => 'Not Supported File Type [EXIF]',
								'7' => 'Function read_exif_data() Not Exists',
								'8' => 'None EXIF Message Found',
								'9' => 'Error Images Address',
								'10' => 'Error Directory',
								'11' => 'None Image Found ',
								'12' => 'Make Directory Error',
								'13' => 'Error File Type [curl]',
								'14' => 'Failed To Get Image [curl]',
								'99' => 'Unauthorized'
						);

  			if ( Common::admin() == false ) { 
					$data['ERR']['KEY']['0'] = '99';
					$data['ERR']['NAME']['0'] = '';
					$data['ERR']['MSG']['0'] = $key['99'];
  				return $data; 
  			}
  			
				include_once 'usr/plugins/Album/Service/Post.php';
				$post = new Post();
				$post = $post->route($_POST,$_FILES);
				
				$_Err = array();
				for ($i=0; $i<count($post);$i++){
					$_Err['KEY'][$i] = $post[$i]['err'];
					$_Err['NAME'][$i] = $post[$i]['msg'];
				}
				
				$_Err_m = array();  
				foreach($_Err['KEY'] as $v){  
					@$_Err_m[$v] ++ ;    
				} 
				
				foreach( $_Err_m as $key=>$value ){
					$data['ERR']['KEY'][] = $key ;
					$data['ERR']['SUM'][] = $value ;
					$data['ERR']['MSG'][] = $MSG[$key] ;
				}
				
				/*
				for ($i=0; $i<count($post);$i++){
					$data['ERR']['KEY'][$i] = $post[$i]['err'];
					$data['ERR']['NAME'][$i] = $post[$i]['msg'];
					$data['ERR']['MSG'][$i] = $key[$post[$i]['err']];
					
				}
			*/
				
			
				
  		}
  		
		}
		
  	$query = $db->select()->from('table.album');
  	
		if ($k['2']){ 
			$tag 	= substr($k['2'],0,1);
			$time = substr($k['2'],1) + 8*3600;
			$date = explode('-',gmdate("Y-m-d", $time));
			if ( $tag == '1' ){
				$star_time = mktime(0,0,0, 1,1,$date['0']);
				$end_time = mktime(23,59,59, 12,31,$date['0']);
			}else if( $tag == '2' ){
				$day = date('t', $time);
				$star_time = mktime(0,0,0,$date['1'],1,$date['0']);
				$end_time = mktime(23,59,59,$date['1'],$day,$date['0']);
			}else if( $tag == '3' ){
				$star_time = mktime(0,0,0,$date['1'],$date['2'],$date['0']);
				$end_time = mktime(23,59,59,$date['1'],$date['2'],$date['0']);
			}else{
				$end_time = '';
				$star_time = '';
			}
			$query = $query->where('created < ? AND created > ?', $end_time, $star_time);
		}else{
			$query = $query->where('created > ?', 0);
		}
		if ($k['0']){
			$query = $query->where('category = ?', $k['0'] );
		}else{
			$query = $query->where('category > ?', 0 );
		}
		if ($k['1']) $query = $query->where('from = ?', $From[$k['1']] );
		if ( Common::admin() == false ) {
  		$query = $query->where('public = ?', '1' );
  	}
  	if ( $read_order == 'DESC' ){
  		$query = $query->order('created', Typecho_Db::SORT_DESC); 
  	}else{
  		$query = $query->order('created', Typecho_Db::SORT_ASC); 
  	}
  	
  	
		//·ÖÒ³´¦Àí
		$total = count($db->fetchAll($query));
		$max_page = ceil($total/$per_page_num);
		$cur_page > $max_page ? $cur_page = $max_page : NULL ;
		$query = $query->page($cur_page,$per_page_num);	
		
		$data['IMG'] = $db->fetchAll($query);

		if ( $total > 0 ){
			foreach ($data['IMG'] as $key=>$res){
				$query = $db->select()->from('table.album_'.$res['from'].'')->where('iid = ?', $res['id'] )->limit('1');
				$img_data = $db->fetchAll($query);
				foreach( $img_data as $m=>$v){
					$data['IMG'][$key]['info'] = $v;
				}
				
				if ( $res['server'] !== 'local' ){
					include_once 'usr/plugins/Album/Service/CloudStorage/Index.php';
					$data['IMG'][$key]['url'] = CloudStorage::post($res['server'],NULL,'mkurl',$data['IMG'][$key]['url'],NULL);
				}
				
			}
		}else{
			$data['ERR']['IMG'] = 'No Data';
		}
		
		if ( isset($manage_Data) && Common::admin() == TRUE ){
			$data['ACTION']['POST'] = $manage_Data;
		}
		
		$data['ACTION']['WELCOME'] = $app_welcome ;

		//´¦ÀíÒ³Âë²¿·Ö
		
		if(isset($k['4'])) unset($k['4']);		//Çå³ýµ±Ç°Ò³Âë²ÎÊý
		$url = Common::request_path();
		$nav = new Typecho_Widget_Helper_PageNavigator_Box($total, $cur_page, $per_page_num,$this->pageurl($url,'{$page}',$k));
		$data['ACTION']['pages'] = $nav;
		
		return $data;
		
	}
	//Ò³ÂëurlÉú³É
	public function pageurl($url , $page , $arr = array()){
		$arr['4']  = '%7Bpage%7D';
		return  $url . implode(',',$arr) ;
	}
	
}
