<?php
 /**
 * Album For Typecho, base on 1.0/14.10.10 
 *
 * @package Album
 * @author 我本奈何
 * @version 1.2
 * @link https://plsYou.com
 */
 
	ob_start();
	$te_config = '../../../../config.inc.php';
	require_once $te_config;
	$ops = Typecho_Widget::widget('Widget_Options');
	$siteUrl = $ops->siteUrl;
	if ( file_exists('install.lock') ) {header('Location: '.$siteUrl.'');exit;}//安装检测
	$options = new stdClass();
	$options->generator = 'Typecho ' . Typecho_Common::VERSION;
	list($soft, $currentVersion) = explode(' ', $options->generator);
	$options->software = $soft;
	$options->version = $currentVersion;
	function _r($name, $default = NULL){return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;}		
?>
<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<head lang="zh-CN">
	<meta charset="<?php _e('UTF-8'); ?>" />
	<title><?php _e('Album 安装程序'); ?></title>
	<link rel="stylesheet" type="text/css" href="../../../../admin/css/normalize.css" />
	<link rel="stylesheet" type="text/css" href="../../../../admin/css/grid.css" />
	<link rel="stylesheet" type="text/css" href="../../../../admin/css/style.css" />
	<style type="text/css"> 
		*{font-family: Tahoma,Arial,Verdana,'\5FAE\8F6F\96C5\9ED1','\534E\6587\7EC6\9ED1',MingLiu,"Segoe UI Light","Segoe UI";}
		.typecho-install-patch li.current {color: #FFF;font-weight: 400;}
		.typecho-install-patch {margin-bottom: 0;}
	</style>
</head>
<body>
<div class="typecho-install-patch">
    <h1>Album For <?php echo $options->software ." ". $options->version; ?></h1>
    <ol class="path">
        <li<?php if ( !isset($_GET['start']) && !isset($_GET['finish']) && !isset($_GET['config'])) : ?> class="current"<?php endif; ?>><span>1</span><?php _e('欢迎使用'); ?></li>
        <li<?php if (isset($_GET['config'])) : ?> class="current"<?php endif; ?>><span>2</span><?php _e('初始化配置'); ?></li>
        <li<?php if (isset($_GET['start'])) : ?> class="current"<?php endif; ?>><span>3</span><?php _e('开始安装'); ?></li>
        <li<?php if (isset($_GET['finish'])) : ?> class="current"<?php endif; ?>><span>4</span><?php _e('安装成功'); ?></li>
    </ol>
</div>
<div class="container">
	<div class="colgroup">
		<div class="col-mb-12 col-tb-8 col-tb-offset-2">
			<div class="column-14 start-06 typecho-install">
        <?php if (isset($_GET['finish'])) : //安装成功
        	touch("install.lock");
        ?>    	
					<?php if (!@file_exists('Baseconfig.php')) : ?>
                <h1 class="typecho-install-title"><?php _e('安装失败!'); ?></h1>
                <div class="typecho-install-body">
                    <form method="post" action="?config" name="config">
                    <p class="message error"><?php _e('没有成功创建 Baseconfig.php 文件，请您重新安装！'); ?> <button type="submit"><?php _e('重新安装 &raquo;'); ?></button></p>
                    </form>
                </div>
           <?php else : ?>
                <h1 class="typecho-install-title"><?php _e('安装成功!'); ?></h1>
                <div class="typecho-install-body">
                    <div class="message success">恭喜！插件已经安装完成！</div>
										<p>源码：<a href="http://way.so" target="_bank">我本奈何</a> <br>前端：<a href="http://" target="_bank">章萧醇</a> </p>
                    <div class="p message success"><?php _e('作者是非职业写手，也是业余中的业余选手，仅仅是为了折腾。'); ?></div>
                <h1 class="typecho-install-title"><?php _e('注意事项!'); ?></h1>
                    <div class="p message notice">
                    请设置如下文件或文件夹可写：<br/>
                     Album/Data <br/>
                     Album/Data/Baseconfig.php ... 当前状态：<?php if ( is_writable('Baseconfig.php')){echo '<span style="color:green;">可写</span>';}else{echo '<span style="color:red;">不可写</span>';} ?><br/>
                     Album/Data/Config.inc.php .... 当前状态：<?php if ( is_writable('Config.inc.php')){echo '<span style="color:green;">可写</span>';}else{echo '<span style="color:red;">不可写</span>';} ?><br/>
                     Album/Data/Language.ini ....... 当前状态：<?php if ( is_writable('Language.ini')){echo '<span style="color:green;">可写</span>';}else{echo '<span style="color:red;">不可写</span>';} ?><br/>
                     程序会生成一个 Album/Data/install.lock 文件，如果需要重新安装请删除该文件，刷新任意页面进入安装。
                    </div>
                    <div class="p message success"><a href="<?php echo $siteUrl; ?>"><?php _e('点击这里返回您的 Blog'); ?></a></div>
                </div>
           <?php endif;?>
        
        <?php elseif (isset($_GET['start'])): //开始安装
					//获取参数
					include 'Baseconfig.php';

					$db = Typecho_Db::get();	
					$result = $db->fetchAll($db->select('value')->from('table.options')->where('`name` = ?', 'charset'));
					if ( $result['0']['value'] = 'UTF-8' ) { $charset = 'utf8'; }else{$charset = 'gbk';}
					$prefix = $db->getPrefix();
					//初始化数据库结构 
					$scripts = file_get_contents ('Data.sql');
					$scripts = str_replace('typecho_', $prefix, $scripts);
					$scripts = str_replace('%charset%', $charset, $scripts);
					$scripts = explode(';', $scripts);
						foreach ($scripts as $script) {
							$script = trim($script);
							if ($script) {
								$db->query($script, Typecho_Db::WRITE);
							}
						}

					//默认分类导入
					$db->query($db->insert('table.album_category')->rows(array('id' => '1', 'name' => '默认分类', 'public' => '1', 'description' => '这是个默认分类','count'=>'0' )));
					//统计表导入
					$db->query($db->insert('table.album_count')->rows(array( 'total'=>'0','local'=>'0','shoot'=>'0','network'=>'0' )));
        	
        	header('Location: Install.php?finish');
        
        ?>    	
        
        <?php elseif (isset($_GET['config'])): //初始化配置
        if ('config' == _r('action')) {
        	$err = '';
					$success = true ;
					$table_exist = array() ;
					if( file_exists('Baseconfig.php')) unlink('Baseconfig.php');
/*				$db = Typecho_Db::get();	
					$result = $db->fetchAll($db->select('slug')->from('table.contents')->where('`slug` = ?', $_POST['app_path']));

					if ( isset($result['0']) ){
						$success = false ;
						$err .= '您设定的 相册路径 '.$_POST['app_path'].' 已经被占用！<br/> 你可以改变路径名或者删除该命名的页面。<br/>';
					}else{
						$db->query($db->insert('table.contents')->rows(array( 'title'=>$_POST['app_name'], 'slug'=>$_POST['app_path'], 'created'=>time(), 'modified'=>time(), 'text'=>"[album]\r\n请不要单独变更此页的路径名！\r\n如果需要，请先到相册前台基础设置里更改相册路径名\r\n然后再来这里变更\r\n请务必保持二者一致！", 'authorId'=>'1', 'order'=>'100', 'template'=>NULL, 'type'=>'page', 'status'=>'publish', 'password'=>NULL, 'commentsNum'=>'0', 'allowComment'=>'0', 'allowPing'=>'0', 'allowFeed'=>'0', 'parent'=>'0', )));
					}
			*/		
					$db = Typecho_Db::get();	
					$prefix = $db->getPrefix();
					$album_table = array('album','album_local','album_shoot','album_network','album_category','album_count');

					for ($i=0;$i<count($album_table);$i++){

						if(!function_exists('mysql_connect')){  

							if( mysqli_num_rows( mysqli_query("SHOW TABLES LIKE '".$prefix.$album_table[$i]."'" ) )==1){
								$table_exist[] = $prefix.$album_table[$i] ;
								$err .= '->数据表 '.$prefix.$album_table[$i].' 已经存在!<br/>';
							}

						}else{
						
							if( mysql_num_rows( mysql_query("SHOW TABLES LIKE '".$prefix.$album_table[$i]."'" ) )==1){
								$table_exist[] = $prefix.$album_table[$i] ;
								$err .= '->数据表 '.$prefix.$album_table[$i].' 已经存在!<br/>';
							}

						}
					}
					
					if ( count($table_exist) > 0  ){
						$success = false ;
						if ( isset($_POST['table']) ){
							if ( $_POST['table'] == 1 ){
								for ( $i=0; $i<count($table_exist); $i++){
									$db->query("DROP TABLE IF EXISTS {$table_exist[$i]}");
								}
							}
							
							if ( $_POST['table'] == 2 ){
						//		if ( isset($result['0']) ){
									$complete = true ;
						/*		}else{
									$db->query($db->insert('table.contents')->rows(array( 'title'=>$_POST['app_name'], 'slug'=>$_POST['app_path'], 'created'=>time(), 'modified'=>time(), 'text'=>"[album]\r\n请不要单独变更此页的路径名！\r\n如果需要，请先到相册前台基础设置里更改相册路径名\r\n然后再来这里变更\r\n请务必保持二者一致！", 'authorId'=>'1', 'order'=>'100', 'template'=>NULL, 'type'=>'page', 'status'=>'publish', 'password'=>NULL, 'commentsNum'=>'0', 'allowComment'=>'0', 'allowPing'=>'0', 'allowFeed'=>'0', 'parent'=>'0', )));
									$complete = true ;
								}
								*/
							}
							
							$success = true ;
						}
					}
					
					
					/* 构造配置文件 */
					
					$lines[] = '<?php
/**
 * Album For Typecho, base on 1.0/14.10.10
 *
 * @package Album Base Config
 * @author 我本奈何
 * @version 1.2 beta
 * @link https://plsYou.com
 */';
 					$lines[] = "\n\n";
					//$lines[] = "\$app_name = '".$_POST['app_name']."';\n";			//相册名称
					//$lines[] = "\$app_path = '".$_POST['app_path']."';\n";			//相册路径名
					$lines[] = "\$app_welcome = '".$_POST['app_welcome']."';\n";									//欢迎语
					$lines[] = "\$read_order = '".$_POST['read_order']."';\n";		//读取顺序 
					/* 二个模板体系 */
					//$lines[] = "\$theme = 'album';\n";													//相册模板类型 album or typecho 
					/* 检测目录 确定模板名称*/
					$lines[] = "\$template = '/Default/';\n";										//相册模板选择 
					$lines[] = "\$category_show = 'no';\n";											//菜单是否显示分类
					$lines[] = "\$per_page_num = '".$_POST['per_page_num']."';\n";										//每页图片显示数量
					$lines[] = "\$page_nav_show = 'yes';\n";										//是否显示页码导航
					$lines[] = "\$ajax_Calendar = 'normal';\n";
					$contents = implode('', $lines);
					@file_put_contents('Baseconfig.php', $contents);
					$_SESSION['typecho_ablum'] = 1;
					
					if($success != true && file_exists('Baseconfig.php')) {
						unlink('Baseconfig.php');
						echo '<br/><div class="emessage error" style="padding:5px;">'.$err.'</div>';
					}
					
					if (!file_exists('Baseconfig.php')) {
						$success = false ;
					} else if ( isset($complete) ){
						header('Location: Install.php?finish');
						exit;
					}else if ($success == true) {
						header('Location: Install.php?start');
						exit;
					}
					
				}
        ?> 
				<form method="post" action="?config" name="config">
					<div class="typecho-install-body">  
						<h2><?php _e('基础设置'); ?></h2> 	
						<ul class="typecho-option">	
							<li>
								<label class="typecho-label" for=""><?php _e('欢迎标语'); ?></label>
								<input class="form-control" type="text" name="app_welcome" value="当前版本 1.2" />
								<p class="description"><?php _e('当然也可以做广告'); ?></p>
							</li>
							<li>
								<label class="typecho-label" for=""><?php _e('显示顺序'); ?></label>
								<select class="form-control" name="read_order">
									<option value="ASC">升序</option>
									<option value="DESC">降序</option>
								</select>
								<p class="description"><?php _e('图片的显示顺序。升序先发的图在前，降序反之。'); ?></p>
							</li>
							<li>
								<label class="typecho-label" for=""><?php _e('每页数量'); ?></label>
								<input class="form-control" type="text" name="per_page_num" value="16" />
								<p class="description"><?php _e('每页显示多少张图片'); ?></p>
							</li>
							
					<?php if ( isset($table_exist) ){	?>
					<?php if ( count($table_exist) > 0 ){	?>
							<li style="background:#FBE3E4;padding:8px;">
								<label class="typecho-label" for="album table"><?php _e('数据表设置'); ?></label>
								<input style="width:30px;height:10px;" type="radio" name="table" value="1" checked class="input-radio"/><?php _e('覆盖现有数据'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input style="width:30px;height:10px;" type="radio" name="table" value="2" class="input-radio"/><?php _e('使用现有数据'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<!--
								<input style="width:30px;height:10px;" type="radio" name="table" value="3" class="input-radio"/><?php _e('改变默认表头'); ?> 
								-->
								<p class="description"><?php _e('请注意: 覆盖操作将删除原有数据表后重新建立!'); ?></p>
							</li>
					<?php }} ?>
						</ul>
						<h2><?php _e('备注说明'); ?></h2>
						<ul class="typecho-option">
							<li>
								<p class="description"><?php _e('更加详细的设置，可以安装完成后在前台配置选项里更改。'); ?></p>
							</li>
						</ul>
					</div>
      		<input type="hidden" name="action" value="config" />
      		<p class="submit"><button type="submit" class="primary"><?php _e('确认, 开始安装 &raquo;'); ?></button></p>
        </form>
        <script>
					var input = document.createElement('input'),
					description = document.getElementsByClassName('description')[2],
					input_radio = document.getElementsByClassName('input-radio'),
					i = 0,
					n = input_radio.length;

					input.type = 'text';
					input.name = 'table_name';
	
					for(; i<n; i++){
						input_radio[i].addEventListener('change', function(){
							var val = this.value;
		
							if(val == 3){
								this.parentNode.insertBefore(input, description)
							}else{
								this.parentNode.removeChild(input)
							}
		
						},false);
					};
        </script>
				<?php  	else: //欢迎使用
								$enable = true ;
								if ( function_exists('gd_info') == false ){
								  $gd_res = "<span style=\"color:red;\"> 未检测到 GD 库函数，缩略图功能无法使用！</span>";
								  $enable = false ;
								}else{
									$gd_info = @gd_info();
								  $gd_res = "<span style=\"color:green;\"> 检测到图像压缩函数 GD Library，版本：".$gd_info["GD Version"]."</span>";
								  
								  if ( function_exists('imagecreatefromgif') == false ){
								  	$gd_res_gif = "<span style=\"color:red;\"> 未检测到支持 GIF </span>";
								  }else{
								  	$gd_res_gif = "<span style=\"color:green;\"> 检测到支持 GIF </span>";
								  }
								  
								  if ( function_exists('imagecreatefromjpeg') == false ){
								  	$gd_res_jpg = "<span style=\"color:red;\"> 未检测到支持 JPEG </span>";
								  }else{
								  	$gd_res_jpg = "<span style=\"color:green;\"> 检测到支持 JPEG </span>";
								  }
								  
								  if ( function_exists('imagecreatefrompng') == false ){
								  	$gd_res_png = "<span style=\"color:red;\"> 未检测到支持 JPEG </span>";
								  }else{
								  	$gd_res_png = "<span style=\"color:green;\"> 检测到支持 PNG </span>";
								  }
								  
								}
								
								if( function_exists("mysql_get_server_info") || function_exists("mysqli_get_server_info") ) {
									$mysql_res = "<span style=\"color:green;\"> 检测到支持 MySQL！</span>";
								}else{
									$mysql_res = "<span style=\"color:red;\">未检测到 MySQL！</span>";
									$enable = false ;
								}
								
								if ( function_exists('curl_init') == false ){
									$curl_res = "<span style=\"color:red;\"> CURL 未检测到，远程图片功能无法使用。</span>";
								}else{
									$curl_res = "<span style=\"color:green;\"> 成功检测到 CURL 支持 </span>";
								}
								
        				if (false === ($handle = @fopen(dirname(__FILE__) . "/Data.php", 'w'))) {
        					$Data_res = "<span style=\"color:red;\">Data 目录不可写（".dirname(__FILE__)."/）</span>";
        					$enable = false ;
            		} else {
									fclose($handle);
									unlink(dirname(__FILE__) . "/Data.php");
									$Data_res = "<span style=\"color:green;\">Data 目录检测通过！（".dirname(__FILE__)."/）</span>";
								}
								
								if (!is_dir( dirname(__FILE__) . "/Attachment/" )){
									$tmp = "<span style=\"color:red;\">附件目录不存在！</span>";
									if (@mkdir ( dirname(__FILE__) . "/Attachment/" , 0777)){
										$tmp .= "<span style=\"color:green;\"> --> 附件目录已经建立！</span>";
									}else{
										$tmp .= "<span style=\"color:red;\"> --> 附件目录建立失败，请手工建立！（".dirname(__FILE__)."/Attachment/）</span>";
										$enable = false ;
									}
          				$att_res = $tmp;
        				}else{
        					if (false === ($handle = @fopen(dirname(__FILE__) . "/Attachment/Attachment.php", 'w'))) {
        						$att_res = "<span style=\"color:red;\">附件目录不可写（".dirname(__FILE__)."/Attachment/）</span>";
        						$enable = false ;
            			} else {
										fclose($handle);
										unlink(dirname(__FILE__) . "/Attachment/Attachment.php");
										$att_res = "<span style=\"color:green;\">附件目录检测通过！（".dirname(__FILE__)."/Attachment/）</span>";
									}
        				}
        				
								if (!is_dir( dirname(__FILE__) . "/Cache/" )){
									$tmp = "<span style=\"color:red;\">缓存目录不存在！</span>";
									if (@mkdir ( dirname(__FILE__) . "/Cache/" , 0777)){
										$tmp .= "<span style=\"color:green;\"> --> 缓存目录已经建立！</span>";
									}else{
										$tmp .= "<span style=\"color:red;\"> --> 缓存目录建立失败，请手工建立！（".dirname(__FILE__)."/Cache/）</span>";
										$enable = false ;
									}
          				$cache_res = $tmp;
        				}else{
        					if (false === ($handle = @fopen(dirname(__FILE__) . "/Cache/cache.php", 'w'))) {
        						$cache_res = "<span style=\"color:red;\">缓存目录不可写（".dirname(__FILE__)."/Cache/）</span>";
        						$enable = false ;
            			} else {
										fclose($handle);
										unlink(dirname(__FILE__) . "/Cache/cache.php");
										$cache_res = "<span style=\"color:green;\">缓存目录检测通过！（".dirname(__FILE__)."/Cache/）</span>";
									}
        				} 				
        				
				?>
					<form method="post" action="?config">
							<div class="typecho-install-body">
								<h1><?php _e('关于相册'); ?></h1>
								<p><?php _e('这是一个 Typecho 的相册插件，安装程序模仿的 Typecho 0.9 。'); ?></p>
								<p><?php _e("感谢<span style=\"font-weight:700\"> <a href=\"http://\" target=\"_bank\">章萧醇</a> </span>设计模板样式。"); ?></p>
								<h1><?php _e('配置检测'); ?></h1>
									<h3><?php _e("一、数据库检测");?></h3> 
									<div class="p message success">
										<?php echo "&nbsp; ".$mysql_res;?>
									</div>
									<h3><?php _e("二、图像压缩组件检测");?></h3> 
									<div class="p message success">
										<?php echo "&nbsp; ".$gd_res; ?><br />
										<?php echo "&nbsp; ".$gd_res_gif; ?><br />
										<?php echo "&nbsp; ".$gd_res_jpg; ?><br />
										<?php echo "&nbsp; ".$gd_res_png; ?>
									</div>
									<h3><?php _e("三、CURL 组件检测");?></h3>
									<div class="p message success">
										<?php echo "&nbsp; ".$curl_res; ?>
									</div>
									<h3><?php _e("四、目录检测");?></h3>
									<div class="p message success">
										<?php echo "&nbsp; ".$Data_res;	?><br />
										<?php echo "&nbsp; ".$cache_res; ?><br />
										<?php echo "&nbsp; ".$att_res; ?>
									</div>
							</div>
						<p class="submit">
							<button type="submit" class="primary" <?php if ($enable==false){echo 'style="color:#FFF;" Disabled';} ?>>
								<?php if ($enable==false){echo '非常遗憾 请检查配置情况 ';}else{echo '我准备好了, 开始下一步 &raquo;';} ?>
							</button>
						</p>
					</form>
				<?php endif; ?> 	
            	
			</div>
		</div>
	</div>	
</div>

<div class="typecho-foot" role="contentinfo">
    <div class="copyright">
        <p>由 <a href="http://typecho.org">Typecho</a> 强力驱动, 版本 <?php echo  $options->version; ?></p>
    </div>
    <nav class="resource">&bull;
       源码：<a href="http://way.so" target="_bank">我本奈何</a> &bull;
       前端：<a href="http://" target="_bank">章萧醇</a> 
    </nav>
</div>
</body>
</html>