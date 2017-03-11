<?php
	
 /**
 * Album For Typecho, base on 1.0/14.10.10
 * @copyright  Copyright (c) 2014 我本奈何 (https://plsYou.com)
 * @license    GNU General Public License 2.0
 * @version    $Id: Sidebar.php 2014/1/10 14:37:00
 */
	
Class Sidebar{
		
	public static function get($siteUrl,$url,$uri,$language){
		require 'usr/plugins/Album/Data/Baseconfig.php';
		$k = explode(',',$uri);
		$sidebar = array();
		$sidebar['Category'] 	= self::getCategory( $url,$uri,$k,$language['Category'] );	
		$sidebar['From'] = self::getFrom( $url,$uri,$k,$language['From']);
		if ( $ajax_Calendar == 'normal' ){
			$sidebar['Calendar'] = self::getCalendar( $url,$uri,substr($k['2'],1),$language['Calender'] );
		}else if ( $ajax_Calendar == 'ajax' ){
			$sidebar['Ajax_Calendar_url'] = $siteUrl.'usr/plugins/Album/Service/AjaxCalendar.php';
		}
		$sidebar['Manage'] = self::getManage( $siteUrl,$url,$uri,$language['Manage'],$k);
		return $sidebar;
			
	}

	public static function getCategory( $url,$uri,$k,$lang = array() ){
  	$db = Typecho_Db::get();
  	$query = $db->select()->from('table.album_category');
  	
  	if ( Common::admin() == false ){ 
  		$query = $query->where('public = ?', 1 );
  	}
  	
  	$category = $db->fetchAll($query);
  	
  	if ( substr($k['3'],0,1) == '2' ) $k['3'] = '0' ;

		$k['0'] = 0 ;
		$result['count']['0'] = '0';
		$result['name']['0'] = $lang['AllCategory'];
		$result['description']['0'] = $lang['AllCategory'] ;
		$result['url']['0'] = $url.implode(',',$k);
		$result['current']['0'] = Common::current_uri($uri, implode(',',$k), '1');
		
		for($i=1,$p=0;$i<count($category)+1;$i++,$p++){
			$result['name'][$i] = $category[$p]['name'];
			$result['description'][$i] = $category[$p]['description'];
			$result['count'][$i] = $category[$p]['count'];
			$result['count']['0'] += $category[$p]['count'];
			$k['0'] = $category[$p]['id'] ;
			$result['url'][$i] = $url.implode(',',$k);
			$result['current'][$i] = Common::current_uri($uri, implode(',',$k), '1');
			
		}
		return $result;
	}
	
	public static function getFrom( $url, $uri, $k, $lang = array() ){
		$db = Typecho_Db::get();
		$count = $db->fetchAll($db->select()->from('table.album_count')->limit('1'));
		$total['0'] = $count['0']['total'] ;
		$total['1'] = $count['0']['local'];
		$total['2'] = $count['0']['shoot'];
		$total['3'] = $count['0']['network'];
		
		$result = array();
		$result['total'] = $total;
		$result['name'] = explode(',',$lang['name']);
		$result['title'] = explode(',',$lang['title']);
		
  	if ( substr($k['3'],0,1) == '2' ) $k['3'] = '0' ;			
  	for ($i = 0; $i < count($result['name']); $i++){
  		$k['1'] = $i;
  		$result['url'][$i] = $url.implode(',',$k);
  		$result['current'][$i] = Common::current_uri($uri, implode(',',$k), '2');
  	}
		return $result;
	}
	
	public static function getCalendar( $url, $uri, $starttime = 0 , $lang = array() ) {
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
		if ( !isset($last_time['0']['created']) ){$last_time['0']['created']='0';}
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
				$h[$d] = mktime(0,0,0,$m,$d,$y);
				$h['sum'][$d] = $have[$key] + 1 ;
			}
		}
		
		$date = gmdate("Y-m-d H:i:s", $starttime + $timeoffset * 3600);
		
		$last_year 	= '<a href="' . self::urlCalendar($url,$uri,'LastYear',$date) .'" title="'.$lang['LastYear'].'"> '.$lang['LastTag'].' </a>';
		$next_year 	= '<a href="' . self::urlCalendar($url,$uri,'NextYear',$date) .'" title="'.$lang['NextYear'].'"> '.$lang['NextTag'].' </a>';
		
		$cal = '';
		$cal .= '<table class="table text-center nomargin">'."\n".
							'<tr class="">
								<td class="noborder border-bottom" colspan="7">'.$last_year.$year.' 年 '.$mon.' 月'.$next_year.'</td>
							</tr>'."\n";
		
		$cal .= '<tr class="week">
							<td class="noborder">'.$lang['Sunday'].'</td>
							<td class="noborder">'.$lang['Monday'].'</td>
							<td class="noborder">'.$lang['Tuesday'].'</td>
							<td class="noborder">'.$lang['Wednesday'].'</td>
							<td class="noborder">'.$lang['Thursday'].'</td>
							<td class="noborder">'.$lang['Friday'].'</td>
							<td class="noborder">'.$lang['Saturday'].'</td>
						</tr>'."\n";
		
		for($row = 0; $row < $rows; $row++) {
			$cal .= '<tr class="day">'."\n";
			for($col = 0; $col < 7; $col++) {
				$cur = $row * 7 + $col - $skip + 1;
				$curtd = $row * 7 + $col < $skip || $cur > $dim ? '&nbsp;' : $cur;
				if(!isset($h[$curtd])) {
					$cal .= '<td class="none noborder">'.$curtd.'</td>'."\n";
				} else {
					$cal .= '<td class="noborder"><a class="badge" href="' . self::urlCalendar($url,$uri,'Day',$h[$curtd]) . '" title="'.$lang['Total'].$h['sum'][$curtd].$lang['Pictures'].'">'.$cur.'</a></td>'."\n";
				}
			}
			$cal .= "</tr>\n";
		}
		
		$cal .= "</table>\n";
		$cal .= '<table class="table text-center nomargin"><tr>';
		
		for ( $i = 1; $i < 13; $i ++ ){
			$month = $i - $mon;
			if ( $i == $mon ){
				$cal .= '<td class="current"><a class="badge" href="' . self::urlCalendar($url,$uri,'Month',$date,$month) .'" title="'.$i.'月">'.$i.'</a></td>';
			}else{
				$cal .= '<td><a href="' . self::urlCalendar($url,$uri,'Month',$date,$month) .'" title="'.$i.'月">'.$i.'</a></td>';
			}
		}
		
		$cal .= "</tr>\n</table>\n";
		
		return $cal;
	}
	
	public static function getManage( $siteUrl,$url, $uri, $lang = array(), $k ){

  	$req = array();
  	for ( $i=0;$i < 3;$i++ ){
  		if ( $i == 2 ){ $k['0'] = $k['1'] = $k['2'] = 0;}
  		$k['3'] = $i;
  		$req[$i] = implode(',',$k);
  	}
  	
		$options = Typecho_Widget::widget('Widget_Options');
		$options->rewrite == 0 ? $index = 'index.php/' : $index = '';
		
		if ( Common::admin() == false ){
			$result['url_login']['0'] = $siteUrl.$index;
			$result['url_login_referer']['0'] = $url;
			$result['url']['0'] = '#';
			$result['name']['0'] = $lang['Login'];
			$result['current']['0'] = 'login';
		}else{
			
			if ( Common::current_uri($uri , $req['1'] , '4') ){ 
				$result['url']['0'] = $url.$req['0'];
				$result['name']['0'] = $lang['Manage_end'];
				$result['current']['0'] = 'active';
			}else{
				$result['url']['0'] = $url.$req['1'];
				$result['name']['0'] = $lang['Manage_start'];
				$result['current']['0'] = '';
			}
			
			$current = explode(',',$uri);
			if ( substr($current['3'],0,1) !== '2' ){ 
				$result['url']['1'] = $url.$req['2'];
				$result['name']['1'] = $lang['Config_start'];
				$result['current']['1'] = '' ;
			}else{
				$result['url']['1'] = $url.$req['0'];
				$result['name']['1'] = $lang['Config_end'];
				$result['current']['1'] = 'active';
			}
			
			$result['url']['2'] = $siteUrl.$index.'action/logout';
			$result['name']['2'] = $lang['Logout'];
			$result['current']['2'] = '';
			
		}
		
		return $result;
		
	}
		
	private function urlCalendar( $url, $uri, $m, $date = NULL,$month = NULL ){
		
  	$k = explode(',',$uri);
		if ( substr($k['3'],0,1) == '2' ) $k['3'] = '0' ;
		
		switch ($m){
			
			case 'LastYear':
			$k['2'] = '1'.strtotime( "-1 year", strtotime($date));
  		break;
  		
			case 'CurrYear':
			$k['2'] = '1'.strtotime($date);
  		break;
  		
			case 'NextYear':
  		$k['2'] = '1'.strtotime( "+1 year", strtotime($date));
  		break;
  		
			case 'LastMonth':
			$k['2'] = '2'.strtotime( "-1 month", strtotime($date));
  		break;
  		
			case 'CurrMonth':
			$k['2'] = '2'.strtotime($date);
  		break;
  		
			case 'NextMonth':
  		$k['2'] = '2'.strtotime( "+1 month", strtotime($date));
  		break;
  		
  		case 'Month':
  		if ( $month > 0 ){ $p = '+'.$month.' month'; }else{ $p = $month.' month'; }
  		$k['2'] = '2'.strtotime( $p, strtotime($date));
  		break;
  		
  		case 'All':
  		$k['2'] = '0';
  		break;
  		
  		case 'Day':
  		$k['2'] = '3'.$date;
  		break;
  		
			default:
  		$k['2'] = '0';
		}
		
		return $url.implode(',',$k);
		
	}	
		
		
		
		
}
	