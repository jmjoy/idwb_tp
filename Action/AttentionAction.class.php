<?php

class AttentionAction extends CommonAction{
	
	/**
	 * 关注申请
	 */
	public function index(){
		if(!$aid = I('post.aid', 0, 'intval'))  return;
		if(M('Attention')->where('uid = %d and aid = %d', $this->id, $aid)->count())  return;
		if(!M('Attention')->data(array('uid' => $this->id, 'aid' => $aid))->add())  return;
		echo '1';
	}
	
	
	/**
	 * 取消关注
	 */
	public function cancel(){
		if(!$aid = I('post.aid', 0, 'intval'))  return;
		if(!M('Attention')->where('uid = %d and aid = %d', $this->id, $aid)->count())  return;
		if(!M('Attention')->where('uid = %d and aid = %d', $this->id, $aid)->delete())  return;
		echo '1';
	}
	
	/**
	 * 获取粉丝
	 */
	public function getFans(){
		// 查询res
		$res = $this->_getRelation('aid', 'uid');
		// 获取我关注的人
		$att = M('Attention')->where('uid = %d', $this->id)->getField('aid', true);
		// 检测我有没有关注
		if($att){
			$map = array();
			foreach($res as $row){
				if(in_array($row['id'], $att)){
					$row['attend'] = 1;
				}
				$map[] = $row;			
			}
			$res = $map;
		}
		//
		echo json_encode($res);
	} 
	
	/**
	 * 获取关注
	 */
	public function getAttention(){
		// 查询res
		$res = $this->_getRelation('uid', 'aid');
		// 都跟一个attend为1
		$map = array();
		foreach($res as $row){
			$row['attend'] = 1;
			$map[] = $row;
		}
		// return
		echo json_encode($map);
	}
	
	/**
	 * 联合查询User表和那个Attention表
	 */
	private function _getRelation($whereKey, $onKey){
		if(!$limit = I('post.limit', 0, 'intval'))  return;
		$page = I('post.page', 1, 'intval');
		$search = I('post.search', '');
		$search = addcslashes($search, '_%');
		// 总数
		$where[$whereKey] = $this->id;
		$count = M('Attention')->where($where)->count();
		if(!$count)  return;
		// 分有搜索词和没有两种情况
		if($search){
			$where['u.name'] = array('like', "%$search%");
		}
		// 结果
		return M('Attention')->alias('a')->field('u.id, u.name, u.mood')->join('inner join ' . C('DB_PREFIX') . 'user u on u.id = a.' . $onKey)->
				where($where)->page($page, $limit)->order('u.id desc')->select();	
	}
	
}
