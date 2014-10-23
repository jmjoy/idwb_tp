<?php

class LoginAction extends Action{
	
	/**
	 * 登录处理
	 */
	public function index(){
		// 获取输入
		$name = I('post.name');
		$password = I('post.password');
		if($name == '' || $password == '') 	return;
		// 判断name是昵称还是邮箱
		if(strpos($name, '@') === false){
			$where = array('name' => $name);
		}
		else{
			$where = array('email' => $name);
		}
		if($user = M('User')->where($where)->find()){
			if($password == $user['password']){
				echo $user['id'];
				return;
			}
		}
		echo '0';
	}
	
	
}
