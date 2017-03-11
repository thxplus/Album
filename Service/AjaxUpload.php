<?php
 /**
 * Album For Typecho, base on 1.0/14.10.10
 * @copyright  Copyright (c) 2014 我本奈何 (https://plsYou.com)
 * @license    GNU General Public License 2.0
 * @version    $Id: AjaxUpload.php 2014/2/27 19:07:05
 */
 
	include_once '../config.inc.php';
	include_once 'Common.php';

  		if ( isset($_POST['submit']) ){
  			
				$key = array
						(
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
								'14' => 'Failed To Get Image [curl]',
								'99' => 'Unauthorized'
						);

  			if ( Common::admin() == false ) { 
					$data['ERR']['KEY']['0'] = '99';
					$data['ERR']['NAME']['0'] = '';
					$data['ERR']['MSG']['0'] = $key['99'];
  				echo json_encode($data);
  				exit(); 
  			}
  			
				include_once 'usr/plugins/Album/Service/Post.php';
				$post = new Post();
				$post = $post->route($_POST,$_FILES);


				
				for ($i=0; $i<count($post);$i++){
					$data['ERR']['KEY'][$i] = $post[$i]['err'];
					$data['ERR']['NAME'][$i] = $post[$i]['msg'];
					$data['ERR']['MSG'][$i] = $key[$post[$i]['err']];
				}
				
				echo json_encode($data);
				
  		}
  		
  		
  		
  		
  		
?>