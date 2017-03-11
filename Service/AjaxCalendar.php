<?php

 /**
  * Album For Typecho, base on 1.0/14.10.10
 * @copyright  Copyright (c) 2014 我本奈何 (https://plsYou.com)
 * @license    GNU General Public License 2.0
 * @version    $Id: AjaxCalendar.php 2014/1/10 14:34:19
 */
	
	include_once '../config.inc.php';
	include_once 'Common.php';
	
	$url = Common::request_path();
	$uri = Common::request_uri($_SERVER["REQUEST_URI"]);
	$k = explode(',',$uri);
	isset($_GET['date']) ?  $starttime = $GET['date'] : $starttime = 0 ;

	$timeoffset = 8 ; 
	$starttime == 0 ? $starttime = time() : $starttime ;
		
	$pendtime = $starttime - (gmdate('j', $starttime + $timeoffset * 3600) - 1) * 86400 - ($starttime + $timeoffset * 3600) % 86400;
	$pstarttime = $pendtime - gmdate('t', $pendtime + $timeoffset * 3600 - 1) * 86400;
	$nstarttime = $pendtime + gmdate('t', $pendtime + $timeoffset * 3600 + 1) * 86400;
	$nendtime = $nstarttime + gmdate('t', $nstarttime + $timeoffset * 3600 + 1) * 86400;

	list($skip, $dim) = explode('-', gmdate('w-t', $pendtime + $timeoffset * 3600 + 1));
	$rows = ceil(($skip + $dim) / 7);

		$db = Typecho_Db::get();
		$prefix = $db->getPrefix();
		$query_time = $db->query( "SELECT created FROM {$prefix}album ORDER BY id DESC LIMIT 1 ;");
		$last_time = $db->fetchAll($query_time);
		
		$cache = 'usr/plugins/Album/Data/Cache/Calendar.c';
		
		if ( false === @filemtime($cache) || @filemtime($cache) < $last_time['0']['created'] ){
		
			$query = $db->select('created')->from('table.album');
			$data = $db->fetchAll($query );

			$have = array();
		
			foreach ( $data as $cur ){
				$day = gmdate("Y m d", $cur['created'] + $timeoffset * 3600);
				if ( isset($have[$day]) ){ $have[$day] ++ ; }else{ $have[$day] = 0 ; }
			}
			
			if(!$fso=fopen($cache,'w')){return false;}
			if(!flock($fso,LOCK_EX)){return false;}
			if(!fwrite($fso,serialize( $have ))){return false;}
			flock($fso,LOCK_UN);
			fclose($fso);
			
		}else{
			$fso = fopen($cache, 'r');
			$have = unserialize(fread($fso, filesize($cache)));
			fclose($fso);
		}

		$year = gmdate("Y", $starttime + $timeoffset * 3600);
		$mon = gmdate("m", $starttime + $timeoffset * 3600);
		
		foreach ( $have as $key=>$value ){
			list($y,$m,$d) = explode(' ',$key);
			if ( $y == $year && $m == $mon ){
				if ( substr($d,0,1) == '0' ){  $d =  substr($d,1); } 
				$k['2'] = '3'.mktime(0,0,0,$m,$d,$y);
				$h['url'][$d] = $url.implode(',',$k);
				$h['sum'][$d] = $have[$key];
			}
		}
		
	echo json_encode($h);

?>