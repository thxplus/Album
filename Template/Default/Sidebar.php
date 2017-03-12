<div class="col-md-4 visible-md visible-lg" >

	<?php if (isset($sidebar['Manage'])) : ?>
    <section class="border margin-top-26">
		<h5 class="nomargin padding padding-left border-bottom"><?php _e('管理相册'); ?></h5>
			<ul class="nav nav-pills nav-stacked margin-top margin-bottom">
				<?php for($i=0;$i<count($sidebar['Manage']['name']);$i++ ){ ?>
					<li class="<?php echo $sidebar['Manage']['current'][$i]; ?>">
						<a href="<?php echo $sidebar['Manage']['url'][$i]; ?>"><?php echo $sidebar['Manage']['name'][$i]; ?></a>
					</li>
				<?php } ?>
			</ul>
			<?php if (isset($sidebar['Manage']['url_login_referer']['0'])) : ?>
 				<script>
 					$(document).ready(function(){
						$("section .login").click(function(){ $("#user_login").slideToggle(100,function(){}); });
 					});
 				</script>
 						<div id="user_login" style="display:none;">
 							<form action="<?php echo $sidebar['Manage']['url_login']['0']; ?>index.php/action/login" method="post" name="login" role="form">
 								<div class="form-group margin-left margin-right">
									<input type="text" id="name" name="name" value="" placeholder="用户名" class="form-control" autofocus="">
								</div>
								<div class="form-group margin-left margin-right">
									<input type="password" id="password" name="password" class="form-control" placeholder="密码">
								</div>
								<div class="form-group margin-left margin-right">
 									<button type="submit" class="form-control btn btn-primary">登录</button>
 									<input type="hidden" name="referer" value="<?php echo $sidebar['Manage']['url_login_referer']['0']; ?>">
								</div>
							</form>
 						</div>
 			<?php endif; ?>
    </section>
	<?php endif; ?>

	<?php if( isset($sidebar['Category']) ) : ?>
    <section class="border margin-top-26">
			<h5 class="nomargin padding padding-left border-bottom"><?php _e('分类检索'); ?></h5>
			<ul class="nav nav-pills nav-stacked margin-top margin-bottom">
				<?php for($i=0;$i<count($sidebar['Category']['name']);$i++ ){ ?>
					<li class="<?php echo $sidebar['Category']['current'][$i]; ?>">
						<a href="<?php echo $sidebar['Category']['url'][$i]; ?>" title="<?php echo $sidebar['Category']['description'][$i]; ?>">
							<?php echo $sidebar['Category']['name'][$i]; ?>
							<span class="badge pull-right"><?php echo $sidebar['Category']['count'][$i]; ?></span>
						</a>
					</li>
				<?php } ?>
			</ul>
    </section>
	<?php endif; ?>

		
	<?php if( isset($sidebar['From']) ) : ?>	
    <section class="border margin-top-26">
			<h5 class="nomargin padding padding-left border-bottom"><?php _e('来源归档'); ?></h5>
			<ul class="nav nav-pills nav-stacked margin-top margin-bottom">
				<?php for($i=0;$i<count($sidebar['From']['name']);$i++ ){ ?>
					<li class="<?php echo $sidebar['From']['current'][$i]; ?>">
						<a href="<?php echo $sidebar['From']['url'][$i]; ?>" title="<?php echo $sidebar['From']['title'][$i]; ?>">
							<?php echo $sidebar['From']['name'][$i]; ?>
							<span class="badge pull-right"><?php echo $sidebar['From']['total'][$i]; ?></span>
						</a>
					</li>
				<?php } ?>
			</ul>
    </section>
	<?php endif; ?>
    
    
	<?php if (isset($sidebar['Calendar'])) : ?> 
    <section class="border margin-top-26">
		<h5 class="nomargin padding padding-left border-bottom"><?php _e('日历检索'); ?></h5>
		<div class="widget-list">
			<?php echo $sidebar['Calendar'];?>
		</div>
		</section>
	<?php endif; ?>
	
	<?php if (isset($sidebar['Ajax_Calendar_url'])) : ?> 
	<section class="border margin-top-26">
		<h5 class="nomargin padding padding-left border-bottom"><div class="alert alert-warning nomargin">模板不支持 AJAX 模式</div></h5>
		<div class="padding">
			<div class="alert alert-info nomargin">
				<p>Ajax提交地址：$sidebar['Ajax_Calendar_url'] </p>
				<p>提交参数格式：?date=Date.parse()</p>
				<p>返回数据格式：json{"url":{"当月某日":"链接地址"},"sum":{"当月某日":"图片数量"}}</p>
			</div>
		</div>
	</section>
	<?php endif; ?>
	
	

	
</div><!-- end #sidebar -->