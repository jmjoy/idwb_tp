<?php

class ImageAction extends Action{
	
	/**
	 * 获取头像
	 */
	public function avatar(){
		//
		if(!$id = I('get.id', 0, 'intval'))  return;
		$raw = I('get.raw', '', '');
		$ctime = M('User')->where('id = %d', $id)->getField('ctime');
		if($ctime === false)  return;
		// 判断是不是要原图
		if($raw){
			$path = C('AVATAR_PATH') . date('y/m/d/', $ctime) . $id . '.jpg';			
		}
		else{
			$path = C('AVATAR_PATH') . date('y/m/d/', $ctime) . $id . '_.jpg';			
		}
		//
		header('Content-Type: image/jpeg');
		if(file_exists($path)){
			echo file_get_contents($path);
		}
		else{
			echo file_get_contents(C('AVATAR_PATH') . '0.jpg' );
		}
		
	}
	
}
