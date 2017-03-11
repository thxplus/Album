<!DOCTYPE HTML>
<html>
<head>
    <meta charset="<?php _e($charset); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php _e('相册 - '.$title);  ?></title>

    <!-- 使用url函数转换相关路径 -->
    <link rel="stylesheet" href="<?php _e($Base_path); ?>css/bootstrap.css">
    <link rel="stylesheet" href="<?php _e($Base_path); ?>css/way.css">
    <script src="<?php _e($Base_path); ?>js/jquery-2.1.0.min.js"></script>
    <script src="<?php _e($Base_path); ?>js/album.js"></script>
    <!--[if lt IE 9]>
    <script src="<?php _e($Base_path .'js/html5shiv.js'); ?>"></script>
    <script src="<?php _e($Base_path .'js/respond.js'); ?>"></script>
    <![endif]-->
</head>
<body>
<!--[if lt IE 8]>
    <div class="browsehappy"><?php _e('当前网页 <strong>不支持</strong> 你正在使用的浏览器. 为了正常的访问, 请 <a href="http://browsehappy.com/">升级你的浏览器</a>'); ?>.</div>
<![endif]-->

<nav class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container">
  <div class="row">
  	
  	<div class="navbar-header">
  	  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
  	    <span class="sr-only">Toggle navigation</span>
  	    <span class="icon-bar"></span>
  	    <span class="icon-bar"></span>
  	    <span class="icon-bar"></span>
  	  </button>
          <a class="navbar-brand" href="<?php _e($siteUrl); ?>"><?php _e($title); ?></a>
    </div>
		<div class="collapse navbar-collapse col-md-5 margin-left">
			<ul class="nav navbar-nav">
          	<li><a href="<?php _e($siteUrl); ?>"><?php _e('首页'); ?></a></li>
						<?php _e($menu);?>
			</ul>
		</div>

		<div class="col-md-4 navbar-right nopadding visible-md visible-lg">
			<form class="navbar-form margin-top" method="post" action="./" role="search">
				<div class="input-group">
					<label for="s" class="sr-only"><?php _e('搜索关键字'); ?></label>
      		<input type="text" name="s" class="form-control" placeholder="<?php _e('输入关键字搜索'); ?>" />
      		<span class="input-group-btn">
        		<button class="btn btn-default" type="submit"><?php _e('搜'); ?></button>
      		</span>
    		</div>
			</form>
		</div>
		
	</div>
	</div>
</nav>

<div class="container" id="body">
	<div class="row">