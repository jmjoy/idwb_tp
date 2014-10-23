<?php

class RegisterAction extends Action{
	
	/**
	 * 注册处理
	 */
	public function index(){
		// 获取输入
		$name = I("post.name");
		$password = I("post.password");
		$email = I("post.email");
		// 验证输入
		if(!preg_match('/^[\x{4e00}-\x{9fa5}\w\-]{3,8}$/iu', $name)){
			echo '-4';
			return; 
		}
		if(!preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/i', $email)){
			return;
		} 
		if(!preg_match('/^\S{4,12}$/i', $password)){
			return;
		}
		// 验证重复
		if(M('User')->where(array('name' => $name))->count()){
			echo '-1';
			return;
		}
		if(M('User')->where(array('email' => $name))->count()){
			echo '-2';
			return;
		}
		// 插入数据
		$data = array(
				'name' => $name, 
				'password' => md5($password), 
				'email' => $email, 'ctime' => time()
		);
		if(!M('User')->data($data)->add()){
			echo '-3';
			return;
		}
		echo '1';
	}
	
}
