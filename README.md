# Album For Typecho 1.0/14.10.10

 * @package Album
 * @author 我本奈何
 * @version 1.2 beta
 * @link https://plsYou.com


## 安装方法
+ 上传文件夹到 plugins 目录 插件目录名称必须是 Album
+ 设置 Data 目录 可写 ， 设置 Config.inc.php，Language.ini 文件可以写
+ 后台插件管理开启应用
+ 从 yourDomain/Album 进入安装程序
+ 前台登录 进行个性设置

### 2017/03/10
- 时隔2年多 上传到 GitHub
- 更新 1.0/14.10.10 兼容
- 更新 PHP7 兼容

### 2014/08/15 
* 放弃路由劫持，使用 Helper::addRoute 方式
* 取消自定义页面
* 取消部分选项，增加图片浏览顺序选项
* 定义新版本号 1.2

### 2014/03/15
重新整理反馈输出
模板统一成 Bootstrap , 砍掉大部分冗余 CSS
修正已知BUG
定义新版本号 1.1

### 2014/03/12 
add 七牛云私有空间访问支持
add 又拍云存储支持
add 又拍云TOKEN仿盗链
add 百度云存储BCS支持 / 支持访问私有图片

### 2014/03/09
add 七牛云存储支持，又拍云无免费额度 暂时不支持。
add 文章同步的EXIF信息

### 2014/03/06
add 文章图片同步更新相册 完成 同时更新部分模板细节

### 2014/03/04
fix 缩略图不清晰
fix 外链图片未检测图片是否存在
fix 安装文件警告错误（by 混蛋70）
add 完善部分模板显示