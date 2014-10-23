<?php

class UploadAction extends CommonAction {

	/**
	 * 上传头像处理
	 */
	public function avatar() {
		// 校验文件
		// 2 : 图片过大
		if($_FILES["file"]["size"] > 50000){
			echo '2';
			return;
		}
		// 3 : 未知错误
		if($_FILES["file"]["error"] > 0){
			echo '3';
			return;
		}
		//
		$ctime = M('User')->where(array('id' => $this->id))->getField('ctime');
		if($ctime === false){
			echo '3';
			return;
		}
		//
		$path = C('AVATAR_PATH') . date('y/m/d', $ctime);
		if(!file_exists($path)){
			mkdir($path, '0777', true);
		}
		$filename = $path . '/' . $this->id;
		$b = move_uploaded_file($_FILES["file"]["tmp_name"], $filename . '.jpg');
		if(!$b){
			echo '3';
			return;
		}
		// 生成缩略图
		import('ORG.Util.Image');
		if(Image::thumb($filename . '.jpg', $filename . '_.jpg', '', 60, 60) === false){
			echo '3';
			return;			
		}
		echo '1';
	}

}
