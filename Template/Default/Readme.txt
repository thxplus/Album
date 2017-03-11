<!-- /* 反馈信息 输出 */ -->
<!--
模板中可以获取 '信息标识' 然后根据标识 自定义 '信息内容' 也可以直接获取 '信息内容' 直接输出

反馈信息标识 => 反馈信息内容：
  $DATA['ERR']['KEY'][$i] = $DATA['ERR']['MSG'][$i]
'0' => 'Upload Complete',
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
'14' => 'Failed To Get Image [curl]'

备注：如果提交大量图片或者含图片较多的文件夹 会产生相应相应数据，可以判断 count($DATA['ERR']['MSG'][$i]) 后一次输出
-->




<!-- /* 图片数据  输出 */ -->
<!--
$DATA['IMG'][$key][id]    图片序号 唯一 	返回值例示：'1'
$DATA['IMG'][$key][name]   	文件名   返回值例示：'1.jpg'
$DATA['IMG'][$key][mime]   	文件类型   	返回值例示:	'image/jpeg'
$DATA['IMG'][$key][pixel]   文件尺寸   	返回值例示:	'90,90,2,width="90" height="90",8,3,image/jpeg' 
  依次为 宽度,高度,类型ID（其值1为GIF格式、2为JPEG/JPG格式、3为PNG格式）,HTML宽高,颜色位数（8bits）,通道(3是RGB4是CMYK),图片类型 
  中间 ',' 隔开 。可以用JS或者PHP分割函数取值，x = x(',',$DATA['IMG'][$key][pixel]) ; x[4] = width="90" height="90" ;
$DATA['IMG'][$key][size]   	文件大小   	返回值例示:	'6767'	外链图片有几率无法获取大小 此时值为 0
$DATA['IMG'][$key][created]  	创建时间   	返回值例示:	'1389344534'
$DATA['IMG'][$key][description] 	描述说明   	返回值例示:	'示例图片' 当无描述的时候 此值为 原始文件名
$DATA['IMG'][$key][url]   	图片地址  	返回值例示:	'http://xxx/usr/plugins/Album/Data/Attachment/2014/01/1389344534895.jpg'
$DATA['IMG'][$key][thumb]   缩略图地址  返回值例示:	'http://xxx/usr/plugins/Album/Data/Attachment/2014/01/thumb/1389344534895.jpg'
$DATA['IMG'][$key][from]   	图片来源  	返回值例示:	'local' 'shoot'	'network'
$DATA['IMG'][$key][category]  	分类序号  	返回值例示:	'1'
$DATA['IMG'][$key][info]   	附加信息  	根据 $DATA['IMG'][$key][from] 的值分三种情况：
   
当 $DATA['IMG'][$key][from] == 'local'
$DATA['IMG'][$key][info][id]      无意义
$DATA['IMG'][$key][info][iid]     图片序号    返回值 = $DATA['IMG'][$key][id]
$DATA['IMG'][$key][info][tid]     来源主题ID  	NULL / 0
$DATA['IMG'][$key][info][pid]     来源附件ID  	NULL / 0
$DATA['IMG'][$key][info][title]    	来源主题标题  NULL / 文件名
$DATA['IMG'][$key][info][category]    	所属分类序号  返回值示例:	'1'

当 $DATA['IMG'][$key][from] == 'network'
$DATA['IMG'][$key][info][id]      无意义
$DATA['IMG'][$key][info][iid]     图片序号    返回值 = $DATA['IMG'][$key][id]
$DATA['IMG'][$key][info][description]   	来源网址    返回值示例: 'www.funme.net'
$DATA['IMG'][$key][info][category]    	所属分类序号  返回值示例:	'1'
   
当 $DATA['IMG'][$key][from] == 'shoot'	
$DATA['IMG'][$key][info][id]      无意义
$DATA['IMG'][$key][info][iid]     	图片序号    返回值 = $DATA['IMG'][$key][id]
$DATA['IMG'][$key][info][camera]     设备品牌型号  返回值示例: 'NIKON CORPORATION / NIKON D90'
$DATA['IMG'][$key][info][lens]     	镜头信息   返回值示例: '24.0-120.0 mm f/4.0'
$DATA['IMG'][$key][info][aperture]    	拍摄光圈   返回值示例: 'f/4.0'
$DATA['IMG'][$key][info][shutterSpeed]   	拍摄快门    返回值示例: '1/1000'
$DATA['IMG'][$key][info][focalLength]   	拍摄焦距   返回值示例:	'110' 	建议附加 mm 变为 110mm
$DATA['IMG'][$key][info][focalLength35mmFilm] 	等效焦距   返回值示例: '165' 	建议附加 mm 变为 165mm
$DATA['IMG'][$key][info][ISO]     	感光度   	返回值示例: '100'
$DATA['IMG'][$key][info][time]     	拍摄时间   返回值示例: '2013:08:10 22:33:29'
$DATA['IMG'][$key][info][category]    	所属分类序号  返回值示例:	'1'

备注：你可以 $img = array() ; $img = $DATA['IMG'][$key]; 接下来变量会短很多。比如 $img[id] 就等效为：  $DATA['IMG'][$key][id] 。 
-->


