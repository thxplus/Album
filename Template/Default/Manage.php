<?php
/**
 * 这是相册系统的一套默认皮肤
 * 
 * @author 我本奈何
 * @version 1.2
 * @link https://plsYou.com
 */
 
 include $theme_dir.'Header.php';
 ?>

<div class="col-md-8" id="main" role="main">

	<!-- /* 反馈信息 输出 */ -->
	<?php if ( isset($DATA['ERR']) ) : ?>
	<article class="border margin-top-26 padding-top padding-left padding-right">
		<?php for( $i = 0; $i < count( $DATA['ERR']['KEY'] ); $i++ ) { ?>
			<?php if ( $DATA['ERR']['KEY'][$i] == 50 ) : ?>
				<div class="alert alert-success"> <?php echo  $DATA['ERR']['MSG'][$i]; ?> </div>
			<?php else: ?>
				<div class="alert alert-warning"> <?php echo  $DATA['ERR']['MSG'][$i]; ?> </div>
			<?php endif; ?>
		<?php } ?>
	</article>
	<?php endif; ?>

	<?php if ( isset($DATA['SUC']) ) : ?>
	<article class="border margin-top-26">
		<h5 class="nomargin padding padding-left border-bottom"><span class="">参数配置面板</span></h5>
		<!-- /* 设置菜单 输出 */ -->
		<div class="h4 border-bottom padding text-center">
			<?php for( $i = 0; $i < count( $DATA['NAV']['name'] ); $i++ ) { ?>
				<input class="btn margin-right margin-left noborder <?php echo $DATA['NAV']['current'][$i]; ?>" type="button" value="<?php echo $DATA['NAV']['name'][$i]; ?>" onclick="location.href='<?php echo $DATA['NAV']['url'][$i]; ?>'"/>
			<?php } ?>
		</div>
	
		<!-- /* 设置内容 输出 */ -->
		<div class="padding">
			<form method="post" action="<?php echo $DATA['CONTENT']['action']; ?>" rol="form">
			
			<!-- /* 基础设置 输出 */ -->
			<?php if ( isset($DATA['CONTENT']['BASE']) ) : $op = $DATA['CONTENT']['BASE']; ?>
					
					<div class="form-group">
						<div class="input-group">
  						<span class="input-group-addon">欢迎标语</span>
							<input class="form-control" type="text" name="app_welcome" value="<?php echo $op['app_welcome']; ?>" />
							<span class="input-group-addon" style="width:60%;text-align: left;">当然也可以做广告</span>
						</div>
					</div>
					
					<div class="form-group">
						<div class="input-group">
  						<span class="input-group-addon">显示顺序</span>
  						<?php if ( $op['read_order'] == 'ASC' ) { $read_order_ASC = ' selected';$read_order_DESC = ''; }else{ $read_order_ASC = '';$read_order_DESC = ' selected'; } ?>
							<select class="form-control" name="read_order">
								<option value="ASC"<?php echo $read_order_ASC;?>>升序</option>
								<option value="DESC"<?php echo $read_order_DESC;?>>降序</option>
							</select>
							<span class="input-group-addon" style="width:60%;text-align: left;">图片的显示顺序。升序先发的图在前，降序反之。</span>
						</div>
					</div>
			<!--
					<div class="form-group">
						<div class="input-group">
  						<span class="input-group-addon">模板体系</span>
							<select class="form-control" name="theme"><option value="<?php echo $op['theme']; ?>"><?php echo $op['theme']; ?></option></select>
							<span class="input-group-addon" style="width:60%;text-align: left;">选择自带模板体系还是使用TE的模板体系（对此版本无意义）</span>
						</div>
					</div>-->
			
					<div class="form-group">
						<div class="input-group">
  						<span class="input-group-addon">模板名称</span>
							<select class="form-control" name="template"><?php echo $op['template']; ?></select>
							<span class="input-group-addon" style="width:60%;text-align: left;">选择要使用的模板</span>
						</div>
					</div>
			
					<div class="form-group">
						<div class="input-group">
  						<span class="input-group-addon">每页数量</span>
							<input class="form-control" type="text" name="per_page_num" value="<?php echo $op['per_page_num']; ?>" />
							<span class="input-group-addon" style="width:60%;text-align: left;">每页显示多少张图片</span>
						</div>
					</div>
			
					<div class="form-group">
						<div class="input-group">
  						<span class="input-group-addon">显示分类</span>
  						<?php if ( $op['category_show'] == 'yes' ) { $category_show_yes = ' selected';$category_show_no = ''; }else{ $category_show_yes = '';$category_show_no = ' selected'; } ?>
							<select class="form-control" name="category_show"><option value="yes"<?php echo $category_show_yes;?>>显示</option><option value="no"<?php echo $category_show_no;?>>不显示</option></select>
							<span class="input-group-addon" style="width:60%;text-align: left;">是否整合博客分类到顶部菜单导航</span>
						</div>
					</div>
					
					<div class="form-group">
						<div class="input-group">
  						<span class="input-group-addon">显示分页</span>
  						<?php if ( $op['page_nav_show'] == 'yes' ) { $page_nav_show_yes = ' selected';$page_nav_show_no = ''; }else{ $page_nav_show_yes = '';$page_nav_show_no = ' selected'; } ?>
							<select class="form-control" name="page_nav_show"><option value="yes"<?php echo $page_nav_show_yes;?>>显示</option><option value="no"<?php echo $page_nav_show_no;?>>不显示</option></select>
							<span class="input-group-addon" style="width:60%;text-align: left;">为纯瀑布模板预留的接口（暂时无用），默认模板不支持。</span>
						</div>
					</div>
					
					<div class="form-group">
						<div class="input-group">
  						<span class="input-group-addon">日历接口</span>
  						<?php if ( $op['ajax_Calendar'] == 'normal' ) { $ajax_Calendar_yes = ' selected';$ajax_Calendar_no = ''; }else{ $ajax_Calendar_yes = '';$ajax_Calendar_no = ' selected'; } ?>
  						<?php if ( $op['ajax_Calendar'] == 'disable' ) { $ajax_Calendar_dis = ' selected'; $ajax_Calendar_no = ''; }else{ $ajax_Calendar_dis = ''; } ?>
							<select class="form-control" name="ajax_Calendar">
								<option value="disable" <?php echo $ajax_Calendar_dis; ?> >关闭日历</option>
								<option value="normal" <?php echo $ajax_Calendar_yes; ?> >PHP 模式</option>
								<option value="ajax" <?php echo $ajax_Calendar_no; ?> >AJAX 模式</option>
							</select>
							<span class="input-group-addon" style="width:60%;text-align: left;">根据模板选择，默认模板支持 PHP 模式</span>
						</div>
					</div>
					
			<?php endif; ?>
			
			<!-- /* 高级设置 输出 */ -->
			<?php if ( isset($DATA['CONTENT']['CONFIG']) ) : $op = $DATA['CONTENT']['CONFIG']; ?>
					<div class="form-group">
						<textarea class="form-control" name="textarea" id="textarea" rows="35"><?php echo $op; ?></textarea>
					</div>
					<div class="alert alert-danger">
						<p>可以更改变量值，但请不要更改变量名，否则将导致插件崩溃。</p>
						<p>修改缩略图部分请一定三思，因为只对修改后的缩略图生效！</p>
					</div>
			<?php endif; ?>
			
			<!-- /* 语言设置 输出 */ -->
			<?php if ( isset($DATA['CONTENT']['LANGUAGE']) ) : $op = $DATA['CONTENT']['LANGUAGE']; ?>
				<?php foreach ( $op as $key=>$lang ){ ?>
					<div class="btn"><?php echo $key; ?> 部分</div>
					<?php foreach ( $lang as $v=>$set ){ ?>
						<div class="form-group">
							<div class="input-group">
  							<span class="input-group-addon"><div style="width:150px;text-align: left;"><?php echo $v; ?></div></span>
								<input class="form-control" type="text" name="<?php echo $key; ?>-<?php echo $v; ?>" value="<?php echo $set; ?>"/>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
				
					<div class="alert alert-danger">
						<p>未做严格表单过滤。</p>
						<p>请不要输出特殊字符，否则可能导致文件损坏。</p>
					</div>
				
			<?php endif; ?>
			
			<!-- /* 分类设置 输出 */ -->
			<?php if ( isset($DATA['CONTENT']['CATEGORY']) ) : $res = $DATA['CONTENT']['CATEGORY']; ?>

				<?php for (  $i = 0; $i < count($res); $i++  ){ ?>
					<?php	
						if ( $res[$i]['public'] == '1' ){$public = 'selected';$secret = '';}else{$public = '';$secret = 'selected';}
						if ( $res[$i]['id'] == '1' ){$add = '<option value="Modify">修改</option>';}else{$add = '<option value="Modify">修改</option><option value="del">删除</option>';} 
					?>
					
					<div class="row">
						<div class="col-xs-4 form-group">
							<input class="form-control" type="hidden" name="id[]" value="<?php echo $res[$i]['id']; ?>" />
							<input class="form-control" type="hidden" name="count[]" value="<?php echo $res[$i]['count']; ?>" />
							<input class="form-control" type="text" name="name[]" value="<?php echo $res[$i]['name']; ?>" />
						</div>
						<div class="col-xs-2 nopadding padding-right form-group">
							<select class="form-control" name="public[]"><option value="1" <?php echo $public; ?>>公开</option><option value="0" <?php echo $secret; ?>>私密</option></select>
						</div>
						<div class="col-xs-4 nopadding form-group">
							<input class="form-control" type="text" name="description[]" value="<?php echo $res[$i]['description']; ?>"/>
						</div>	
						<div class="col-xs-2 form-group">
							<select class="form-control" name="add[]"><?php echo $add; ?></select>
						</div>	
					</div>
					
				<?php } ?>
				
					<div class="row">
						<div class="col-xs-4 form-group">
							<input class="form-control" type="text" name="name[]" value="" />
						</div>
						<div class="col-xs-2 nopadding padding-right form-group">
							<select class="form-control" name="public[]"><option value="1">公开</option><option value="0">私密</option></select>
						</div>
						<div class="col-xs-4 nopadding form-group">
							<input class="form-control" type="text" name="description[]" value=""/>
						</div>	
						<div class="col-xs-2 form-group">
							<input type="hidden" name="add[]" value="add" >
							<input class="form-control" type="button" value="新建分类" />
						</div>	
					</div>

					<div class="alert alert-info">
						<p>私密分类仅博主可见。</p>
						<p>删除分类后该类目下的图片会转移到默认分类里。</p>
						<p>如果想指定转移，请到图片管理里先转移再删除分类。</p>
					</div>
					
			<?php endif; ?>

			<!-- /* 重建统计 输出 */ -->
			<?php if ( isset($DATA['CONTENT']['COUNT']) ) :  ?>
				<div class="alert alert-info">
					<p>根据数据量，耗费资源有所不同，斟酌使用。</p>
					<p>无法矫正手工增删数据库条目造成的数据不准确。</p>
				</div>
			<?php endif; ?>
			
			<input class="form-control btn btn-info" name="submit" type="submit" value="确认提交"/>
			
		</form>
		
		</div>
	</article>

<?php endif; ?>
</div><!-- end #main-->

<?php include $theme_dir.'Sidebar.php';?>
<?php include $theme_dir.'Footer.php';?>