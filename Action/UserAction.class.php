<?php

class UserAction extends CommonAction{
	
	public function index(){
		// 输入验证
		if(!$search = I('post.search'))  return;
		if(!$limit = I('post.limit', 0, 'intval'))  return;		
		$search = addcslashes($search, '_%');
		$page = I('post.page', 0, 'intval');
		// 数据库查询微博数量 
		$where['name'] = array('like', '%' . $search . '%');
		$where['id'] = array('neq', $this->id);
		$user = M('User')->field('id, name, mood')->where($where)->page($page, $limit)->order('id desc')->select();
		if(!$user)  return;
		// 查询关注状态
		$att = M('Attention')->where('uid = %d', $this->id)->getField('aid', true);
		if($att){
			for($i = 0; $i < count($user); $i++){ 
				if(in_array($user[$i]['id'], $att)){
					$user[$i]['attend'] = '1';
				}
			}
		}
		// 返回
		echo json_encode($user);
	}
	
}
