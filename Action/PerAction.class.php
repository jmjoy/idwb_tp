<?php

/**
 * 废弃了?
 */
class PerAction extends CommonAction{
	
	/**
	 * 查询粉丝数, 关注数和微博数
	 */
	public function index(){
		$fans_count = M('Attention')->where('aid = %d', $this->id)->count();
		$att_count = M('Attention')->where('uid = %d', $this->id)->count();
		$weibo_count = M('Weibo')->where('uid = %d', $this->id)->count();
		echo "$fans_count|$att_count|$weibo_count";
	}
	
}
