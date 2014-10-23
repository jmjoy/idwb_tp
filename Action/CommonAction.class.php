<?php

class CommonAction extends Action{
	/**
	 * 用户ID
	 */
	protected $id;
	
	/**
	 * 
	 */
	public function _initialize(){
		// 检验输入
		if(!$id = I('post.id', 0, 'intval'))  die('');
		if(!$password = I('post.password'))  die('');
		// 查询数据库
		if($corrPwd = M('User')->where('id = %d', $id)->getField('password')){
			if($corrPwd == $password){
				// 检验通过
				$this->id = $id;
				return;
			}
		}
		// 检验不通过
		die('');
	}
	
}
