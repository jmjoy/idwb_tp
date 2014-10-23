<?php

class MeAction extends CommonAction{
	
	/**
	 * 我 界面
	 */
	public function index(){
		$id = $this->id;
		$pre = C('DB_PREFIX');
		// 获取用户信息
		$user = M('User')->field('name, mood')->where('id = %d', $id)->find();
		if(!$user)  return;
		// 获取 粉丝, 关注, 微博 数目
		$sql = "select count(*) c from {$pre}attention where aid = $id union all " . 
			   "select count(*) c from {$pre}attention where uid = $id union all " .
			   "select count(*) c from {$pre}weibo where uid = $id";
		$res = M()->query($sql);
		// 判断
		if(count($res) != 3)  return;
		// 返回  格式 是           name|fans_num|attention_num|weibo_num
		echo "{$user['name']}|{$user['mood']}|{$res[0]['c']}|{$res[1]['c']}|{$res[2]['c']}";
	}
	
}
 