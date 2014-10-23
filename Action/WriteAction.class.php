<?php

class WriteAction extends CommonAction{
	
	/**
	 * 发布微博处理
	 */
	public function index(){
		// 获取微博内容
		if(!$content = I('post.content'))  return;
		$content = mb_substr($content, 0, 200, 'utf-8');
		// 插入内容
		$data = array(
				'uid'		=> $this->id,
				'content'	=> $content,
				'ctime'		=> time()
		);
		// 插入数据库
		if(!M('Weibo')->data($data)->add())  return;
		// 成功
		echo '1';
	} 
	
}
