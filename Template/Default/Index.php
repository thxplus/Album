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
	<!-- 信息反馈 示例 -->
	<?php if ( isset($DATA['ERR']['KEY']) ) : ?>
	<article class="border margin-top-26 padding-top padding-left padding-right">
		<?php for( $i = 0; $i < count( $DATA['ERR']['KEY'] ); $i++ ) { ?>
			<?php if ( $DATA['ERR']['KEY'][$i] == 0 ) : ?>
				<div class="alert alert-success"> <?php echo $DATA['ERR']['SUM'][$i];?> Files <?php echo  $DATA['ERR']['MSG'][$i]; ?> </div>
			<?php else: ?>
				<div class="alert alert-warning"> <?php echo $DATA['ERR']['SUM'][$i];?> Files <?php echo  $DATA['ERR']['MSG'][$i]; ?> </div>
			<?php endif; ?>
		<?php } ?>
	</article>
	<?php endif; ?>
	
	<article class="border margin-top-26">
	<?php if ( isset( $DATA['ACTION']['POST'] ) ) : ?>
		<script>
			$(document).ready(function(){ 
				$("#imgAdd").click(function(){ $("#pic-add").slideToggle(100,function(){}); });
				$(".close").click(function(){ $("#pic-add").hide(); });  
			}); 
		</script>
		<div class="border-bottom padding"><span class="btn btn-info noborder">相册管理面板</span><span id="imgAdd" class="btn btn-info noborder pull-right">添加图片</span></div>
	<?php else: ?>
		<h5 class="nomargin padding padding-left border-bottom"><?php echo $DATA['ACTION']['WELCOME'] ; ?></h5>
	<?php endif; ?>

	<!-- /* 图片 输出示例 */ -->
	<?php if ( isset( $DATA['IMG'] ) ) : ?>
		<?php if ( isset( $DATA['ACTION']['POST'] ) ) : ?>
		
		<div class="post-action">
		<script language=javascript>
			function selectAll(){ //实例JS ，用于全选/反选
				var a = document.getElementsByClassName("img_check");
				if(a[0].checked){
					for(var i = 0;i<a.length;i++){
						if(a[i].type == "checkbox") a[i].checked = false;
					}
					document.getElementById('select_all').value='全选';
				}else{
					for(var i = 0;i<a.length;i++){
						if(a[i].type == "checkbox") a[i].checked = true;
					}
					document.getElementById('select_all').value='取消';
				}
			}
			
			$(document).ready(function(){ 
				$('.photo').each(function(){
					$(this).click(function(){
						var photo_checkID = $(this).attr('id'); 
						if($("#"+photo_checkID+" input:checkbox").is(":checked")){
							$("#"+photo_checkID+" input:checkbox").removeAttr("checked");
							$("#"+photo_checkID).removeClass("imgfilter");
						}else{
							$("#"+photo_checkID+" input:checkbox").prop("checked","checked");
							$("#"+photo_checkID).addClass("imgfilter");
						}
					});
				});

  			$('#method_1_add').click(function () {
  				var input = $('#method_1 > form > div.addbase > .row:last-child input'),
  						id = parseInt(input[1].id.split('_')[1])+1 ,
  						html = 	'<div class="row">' +
  										'	<div class="col-xs-6 form-group">' +
											'		<input class="form-control" type="text" name="description[]" placeholder="图片描述，空缺将以文件名代替">' +
											'	</div>' +
											'	<div class="col-xs-4 nopadding form-group">' +
											'		<input class="form-control pull-left" type="text" name="file_' + id + '" id="upload_' + id + '" onclick = "file[].click();" placeholder="浏览文件"/>' +
											'		<input class="form-control" type="file" name="file[]" class="form-control" onchange="file_' + id + '.value=this.value;" style="position: absolute;filter: alpha(opacity:0);opacity: 0;" />' +
											'	</div>' +
											'	<div class="col-xs-2 form-group">' +
											'		<input class="form-control cancel btn btn-warning noborder" type="button" value="取消" />' +
											'	</div>' +
											'</div>',
  			      el = $(html).hide().appendTo('#method_1 .addbase').fadeIn();
  			  attachDeleteEvent(el);
  			  document.getElementById("method_1").scrollTop = 10000;
  			});
  			
  			$('#method_2_add').click(function () {
  				var html = 	'<div class="row">' +
											'	<div class="col-xs-6 form-group">' +
											'		<input class="form-control" type="text" name="description[]" placeholder="图片描述，空缺将以文件名代替">' +
											'	</div>' +
											'	<div class="col-xs-4 nopadding form-group">' +
											'		<input class="form-control" name="path[]" type="text" value="" placeholder="请输入完整的图片地址！" />' +
											'	</div>' +
											'	<div class="col-xs-2 form-group">' +
											'		<input class="form-control cancel btn btn-warning noborder" type="button" value="取消" />' +
											'	</div>' +
											'</div>',
  						el = $(html).hide().appendTo('#method_2 .addbase').fadeIn();
  				attachDeleteEvent(el);
  				document.getElementById("method_2").scrollTop = 10000;
  			});	
  			
  			function attachDeleteEvent (el) {
  			    $('input.cancel', el).click(function () {
  			            $(this).closest('div.row').fadeOut(function () {
  			                $(this).remove();
  			            });
  			    });
  			}
  			
			});
		</script>	
		
		<form action="<?php echo $DATA['ACTION']['POST']['action']; ?>" method="post" class="form-inline " rol="form"> 
			<div class="border-bottom padding ">
				<div class="form-group">
					<!-- js辅助：点击全选 OR 全取消  -->
					<input type="button" class="form-control" onclick="selectAll()" id="select_all" value="全选"/>
				</div>
				<div class="form-group">
					<select name="method" class="form-control">
						<option selected>操作</option>
						<option value="del">删除</option>
						<option value="move">转移</option>
					</select>
				</div>
				<div class="form-group">
					<select name="move" class="form-control"><?php echo $DATA['ACTION']['POST']['category']; ?></select><!-- js辅助：当选择 转移 的时候才出现 ?  -->
				</div>
				<div class="form-group">
					<input class="form-control" type="submit" name="submit" value="确定"><!-- js辅助: 为选操作 无法点确定 ? -->
				</div>
			</div>
		<?php endif; ?>
		
	<!-- 无数据 示例 -->
	<?php if ( isset($DATA['ERR']['IMG']) ) : ?> 
		<!-- 	$DATA['ERR']['IMG'] 如果存在即表示 '无内容' , 即 当前页/当前分类 等 无内容 -->
		<div class="padding">
			<div class="alert alert-info">未获得数据！</div>  
		</div>
	<?php endif; ?>

		<div class="main album">
			<ul class="list-unstyled" id="album">
			<?php foreach ( $DATA['IMG'] as $key=>$value ){
							foreach ( $value as $k=>$v ){$DATA['IMG'][$key][$k] = $v;} ?>
				<li class="photo pull-left" id="photo_<?php echo $DATA['IMG'][$key]['id']; ?>">
					<?php if ( isset( $DATA['ACTION']['POST'] ) ) : ?>
					<a>
					<?php else: 
					$info = explode(',',$DATA['IMG'][$key]['pixel']);
					$DATA['IMG'][$key]['info']['width'] = $info['0'];
					$DATA['IMG'][$key]['info']['height'] = $info['1'];
					
					if ( $DATA['IMG'][$key]['size'] > 1024 ){
						$DATA['IMG'][$key]['info']['size'] = round($DATA['IMG'][$key]['size'] / 1024,2) . 'KB';
					}else if ( $DATA['IMG'][$key]['size'] > 1048576 ){
						$DATA['IMG'][$key]['info']['size'] = round($DATA['IMG'][$key]['size'] / 1048576,2) . 'MB';
					}else{
						$DATA['IMG'][$key]['info']['size'] = $DATA['IMG'][$key]['size'] . 'B';
					}
					
					$class='photourl';
					
					if ( isset($DATA['IMG'][$key]['info']['pid']) && $DATA['IMG'][$key]['info']['pid'] != NULL ){
						$db = Typecho_Db::get();
						$art_tid = $db->fetchRow($db->select()->from('table.contents')->where( 'cid = ?', $DATA['IMG'][$key]['info']['pid'] ));
						if ( isset($art_tid['parent']) ){
							$art_id = $db->fetchRow($db->select()->from('table.contents')->where( 'cid = ?', $art_tid['parent'] ));
							$DATA['IMG'][$key]['description'] = '来自文章：'.$art_id['title'];
							$DATA['IMG'][$key]['info']['title'] = $art_id['title'];
							$DATA['IMG'][$key]['info']['url'] = Album_Plugin::url($art_id);
						}else{
							$DATA['IMG'][$key]['description'] = '原始图片已经被编辑或删除！';
							$DATA['IMG'][$key]['url'] = '';
							$class='photourl-none';
						}
						
					}
					
					?>
					<a href="<?php echo $DATA['IMG'][$key]['url']; ?>" class="<?php echo $class;?>" title="<?php echo $DATA['IMG'][$key]['description']; ?>" id='<?php echo json_encode($DATA['IMG'][$key]['info']); ?>' >
					<?php endif; ?>
						<img src="<?php echo $DATA['IMG'][$key]['thumb']; ?>" title="" width="238" height="<?php $info = explode(',',$DATA['IMG'][$key]['pixel']); echo  238 / $info['0'] * $info['1']; ?>"/>
					</a>
					
						<input type="checkbox" class="img_check" name="img_id[]" value='<?php echo $DATA['IMG'][$key]['id']; ?>' style="display:none;"/> 
					
						<span>
							<?php if ( isset($DATA['IMG'][$key]['info']['url']) ) : ?>
								<a href="<?php echo $DATA['IMG'][$key]['info']['url']; ?>" target="_bank"><?php echo $DATA['IMG'][$key]['description']; ?></a>
							<?php else: ?>
								<?php echo $DATA['IMG'][$key]['description']; ?>
							<?php endif; ?>
						</span>
					
				</li>
			<?php } ?>
			</ul>
			
			<script language="javascript">
				new Waterfall({"container":"album","colWidth":248,"colCount":3});
				var iv = new imgshow( 'pages' );
			</script>
		</div>
		<?php if ( isset( $DATA['ACTION']['POST'] ) ) : ?> </form></div><?php endif; ?>
	<?php endif; ?>
	</article>
	<!-- /* 分页类 */ -->
	<ol class="page-navigator">
		<?php 
			if ( isset($DATA['ACTION']['pages']) ){
				echo $DATA['ACTION']['pages']->render();
			}
		?>
	</ol>
</div><!-- end #main-->

<!-- /* 上传管理 */ -->
<div class="pic-add" id="pic-add" style="display:none;" >
	<div class="h4 border-bottom padding">上传图片<span class="pull-right close">X</span></div>
	
	<script>
		function Method(num){ //实例JS 用于切换三种上传方式,使用JQ可以获得更好的视觉效果。
			for(var i=1;i<=3;i++){
				document.getElementById("method_"+i).style.display="none";
				document.getElementById("title_"+i).style.background="";
			}
			document.getElementById("method_"+num).style.display="block";
			document.getElementById("title_"+num).style.background="#5cb85c";
		}
	</script>
	
	<div class="h4 border-bottom padding text-center">
		<span class="btn btn-info margin-right margin-left noborder" onclick="return Method(1)" id="title_1" style="background:#5cb85c;">本地上传</span>
		<span class="btn btn-info margin-right margin-left noborder" onclick="return Method(2)" id="title_2">链接贴图</span>
		<span class="btn btn-info margin-right margin-left noborder" onclick="return Method(3)" id="title_3">路径扫描</span>
	</div>
		
	<div class="padding" id="method_1">
		<form action="<?php echo $DATA['ACTION']['POST']['action']; ?>" encType="multipart/form-data" method="post" rol="form">
			
			<div class="form-group">
				<select name="category" class="form-control"><?php echo $DATA['ACTION']['POST']['category']; ?></select>
			</div>
			
			<div class="addbase">
				<div class="row">
				  <div class="col-xs-6 form-group">
				    <input class="form-control" type='text' name="description[]" placeholder="图片描述，空缺将以文件名代替">
				  </div>
				  <div class="col-xs-6 nopadding padding-right form-group">
				    <input class="form-control  pull-left" type='text' name="file_1" id='upload_1' onclick = "file[].click();" placeholder="浏览文件"/>
				    <input class="form-control" type="file" name="file[]" class="form-control" onchange="file_1.value=this.value;" style="position: absolute;filter: alpha(opacity:0);opacity: 0;" />
				  </div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-xs-6 form-group">
					<input type="button" class="form-control btn btn-info" id="method_1_add" value="+ 继续添加图片" />
				</div>
				<div class="col-xs-6 nopadding padding-right form-group">
					<input class="form-control" name="method" type="hidden" value="local" />
					<input class="form-control btn btn-info" name="submit" type="submit" value="提交">
				</div>
			</div>
			
		</form>
	</div>
			
	<div class="padding" id="method_2" style="display:none;">
		<form action="<?php echo $DATA['ACTION']['POST']['action']; ?>" encType="multipart/form-data" method="post" rol="form">
			
			<div class="row">
				<div class="col-xs-6 form-group">
					<select class="form-control" name="category"><?php echo $DATA['ACTION']['POST']['category']; ?></select>
				</div>
				<div class="col-xs-6 nopadding padding-right form-group">
					<select class="form-control" name="from"><option value="shoot" selected>根据 Exif() 归类</option><option value="network">全部归类为网络</option></select>
				</div>
			</div>
			
			<div class="addbase">
				<div class="row">
				  <div class="col-xs-6 form-group">
				    <input class="form-control" type='text' name="description[]" placeholder="图片描述，空缺将以文件名代替">
				  </div>
				  <div class="col-xs-6 nopadding padding-right form-group">
						<input class="form-control" name="path[]" type="text" value="" placeholder="请输入完整的图片地址！" />
					</div>
				</div>
			</div>
					
			<div class="row">
				<div class="col-xs-6 form-group">
					<input type="button" class="form-control btn btn-info" id="method_2_add" value="+ 继续添加图片" />
				</div>
				<div class="col-xs-6 nopadding padding-right form-group">
					<input class="form-control" name="method" type="hidden" value="website" />
					<input class="form-control btn btn-info" name="submit" type="submit" value="提交">
				</div>
			</div>

		</form>
	</div>
			
	<div class="padding" id="method_3" style="display:none;">
		<form action="<?php echo $DATA['ACTION']['POST']['action']; ?>" encType="multipart/form-data" method="post" rol="form">
			
			<div class="row">
				<div class="col-xs-6 form-group">
					<select class="form-control" name="category"><?php echo $DATA['ACTION']['POST']['category']; ?></select>
				</div>
				<div class="col-xs-6 nopadding padding-right form-group">
					<select class="form-control" name="from"><option value="shoot" selected>根据 Exif() 归类</option><option value="local">全部归类为本地</option></select>
				</div>
			</div>
			
			<div class="form-group">
				<input class="form-control" type="text" name="path" value="" placeholder="博客根目录的相对路径 例如：usr/uploads" />
			</div>
			
			<div class="form-group">
				<input class="form-control" name="method" type="hidden" value="scan" />
				<input class="form-control btn btn-info" name="submit" type="submit" value="提交">
			</div>

		</form>
	</div>
			
</div>

<?php include $theme_dir.'Sidebar.php';?>
<?php include $theme_dir.'Footer.php';?>