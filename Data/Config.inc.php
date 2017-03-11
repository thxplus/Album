<?php
/**
 * Album For Typecho, base on 1.0/14.10.10
 *
 * @package Album Config.inc
 * @author 我本奈何
 * @version 1.2
 * @link https://plsYou.com
 */

/*如果你不知道这是什么，请不要随意改动！*/
/*你可以改变变量的值，但请不要改变变量名！ 后果很严重！ */

/**
 * 设置 主题发布图片收集
 *  开启 true
 *  关闭 false
 *  开启后 发布主题时 发布的图片会自动导入相册
 */

	$imgSyn = true;   //开
	//$imgSyn = false;  //关
	//注意，相册不会接管博客本身的存储。
	
/**
 * 设置 收集方式
 * 全部收集 all
 * 指定字符文件名 自定义字符 比如 $imgTag = '-album' 则表示 xxxxx-album.jpg/png/gif 将被收集到相册
 * 注意，自定义字符不能用于文件名开头 否则无效！ 比如 -albumxxxxx.jpg 是无效的！
 * 开启主题发布图片收集后 有效
 */

	$imgTag = '-album';
	//$imgTag = 'all';
	
	
/**
 * 设置 根据文件名 识别 分类
 * 指定收集分类 分类链接第一个数字即为 分类ID
 * 开启主题发布图片收集后 有效
 * 后台没有验证ID的准确写 请不要填错。
 */
 
	$imgCateID = '1';

/**
 * 设置 缩略图引擎
 * 默认值 GD
 * 支持的值
 * GD 
 * ImageMagick
 * 当前仅支持 GD 库模式
 */

	$thumb_Engine = 'GD';
	//$thumb_Engine = 'ImageMagick';

/**
 * 设置 缩略图生成方法
 * 指定宽度 1
 * 指定高度 2
 * 指定高度和宽度 3
 */
 
	$thumb_Method = '1';

/**
 * 设置 缩略图填充模式
 * 仅对同时指定宽度和高度时生效
 * 拉伸填充	1
 * 按比例填充 2
 * 填充颜色 仅对 比例填充生效
 * 透明图片底色会被填充
 * 安装初期可以尝试合适的值
 * 仅对修改后的缩略图有效
 * 请慎重考虑！
 */
	
	$thumb_createMethod = '2';
	$thumb_createColor = '#000'; //仅支持 #EEE #EEEEEE  二种写法 （3位或者6位）
	
/**
 * 设置 缩略图尺寸
 * 安装初期可以尝试合适的值
 * 仅对修改后的缩略图有效
 * 请慎重考虑！
 */
 
	$thumb_width = '241';		//缩略图宽	在指定宽度时有效
	$thumb_height = '361';		//缩略图高	在指定高度时有效
	
/**
 * 设置 缩略图最大值
 * 仅单一指定高度或宽度时生效
 * 仅对修改后的缩略图有效
 * 请慎重考虑！
 */
 
	$thumb_MaxWidth = '200';	//缩略图宽 最大值	仅在指定高度时有效
	$thumb_MaxHeight = '400';	//缩略图高 最大值 仅在指定宽度时有效
	
/**
 * 设置 JPEG 缩略图质量
 * 支持的值 0-100
 * 数字越大 质量越好 文件越大
 */
 
	$thumb_Jpeg = '90';

/**
 * 设置 上附件位置
 * 默认上传到本地 local
 * 暂时只支持本地上传
 */
 
	$upload_Server = 'local'; 				//本地存储
	//$upload_Server = 'qiniu'; 			//七牛云存储
	//$upload_Server = 'upyun'; 			//又拍云存储
	//$upload_Server = 'baidubcs';		//百度BCS云存储
	//$upload_Server = 'baidupcs';		//百度PCS网盘
	
/**
 * 设置 七牛云存储
 * 相册中删除图片 存储数据将同时被删除
 * 仅对 $upload_Server = 'qiniu' 方式生效
 */
 
	$qiniu_bucket = '';	//在七牛云中建立的空间名
	$qiniu_domain = ''; //上面空间名的域名 必须带http://
	$qiniu_AccessKey = ''; //七牛 AccessKey
	$qiniu_SecretKey = ''; //七牛 SecretKey
	//$qiniu_public = true ; // 七牛空间性质，公开
	$qiniu_public = false ; // 七牛空间性质，私有
	$qiniu_time = '3600'; // 授权有效期，当 $qiniu_public = false 有效
	
/**
 * 设置 又拍云存储
 * 相册中删除图片 存储数据将同时被删除
 * 仅对 $upload_Server = 'upyun' 方式生效
 */
 
	$upyun_bucket = ''; //在又拍云中建立的空间名
	$upyun_domain = ''; //上面空间名绑定的域名 必须带http://
	$upyun_user = ''; // 操作员
	$upyun_psw = ''; // 密码
	$upyun_token = true; // Token 仿盗链 开启 
	//$upyun_token = false; // Token 仿盗链 关闭
	$upyun_time = '3600' ; // 有效期 单位 秒 
	$upyun_key = '' ; // 密钥,必须保持与又拍云设置一直。

/**
 * 设置 百度BCS存储
 * 相册中删除图片 存储数据将同时被删除
 * 仅对 $upload_Server = 'baidubcs' 方式生效
 */
	
	$bcs_APIKey = '';	// 百度 API Key
	$bcs_SecretKey = '';	// 百度 Secret Key
	$bcs_bucket = 'baidubcs';	// 百度云存储的 bucket 名
	$bcs_domain = 'http://bcs.duapp.com'; 	// 现行版本为此固定值，不要更改。
	//$bcs_public = true ; //文件性质 公开
	$bcs_public = false ; // 文件性质 私有
	$bcs_time = '3600'; // 有效期 单位 秒
	
/**
 * 设置 百度PCS存储
 * 相册中删除图片 存储数据将同时被删除
 * 仅对 $upload_Server = 'baidupcs' 方式生效
 */
	
	
	
	
	
/**
 * 设置网络图片存储
 * 抓取到存储	true
 * 不抓取直接显示	false
 * 无论是否抓取 都在本地生产缩略图
 */

	$remote_Curl = true;
	//$remote_Curl = false;

/**
 * 设置网络图片获取方式
 * 方式	readfile
 * 方式	curl
 * curl 方式一般可以无视对方仿盗链
 * readfile 一般给不支持 curl 的主机使用
 */

	$remote_Method = 'curl';
	//$remote_Method = 'readfile';

/**
 * 设置本站图片防盗链
 * 
 * 
 * 计划中
 * 
 */
