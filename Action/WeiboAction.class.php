<?php

class WeiboAction extends CommonAction{
	
	public function index(){
	}
	
	/**
	 * get one pre's weibo
	 */
	public function one(){
		// 输入验证
		if(!$uid = I('post.id', 0, 'intval'))  return;
		if(!$limit = I('post.limit', 0, 'intval'))  return;
		$page = I('post.page', 0, 'intval');
		// 数据库查询微博数量
		if(!$count = M('Weibo')->where('uid = %d', $uid)->count()){
			return;
		}
		// 联合查询微博
		$res = M('Weibo')->alias('w')->field('u.id, u.name, w.content, w.ctime')->
				join('inner join ' . C('DB_PREFIX') . 'user u on u.id = w.uid')->
				where('w.uid = %d', $uid)->page($page, $limit)->order('w.ctime desc')->select();
		// 检测res是否查询到
		if(!$res)  return; 
		// 输出数据到客户端
		echo json_encode($res);
	}
	
	/**
	 * get one per's and his/her attentions' weibo
	 */
	public function all(){
		// 输入验证
		if(!$id = I('post.id', 0, 'intval'))  return;
		if(!$limit = I('post.limit', 0, 'intval'))  return;
		$page = I('post.page', 0, 'intval');
		// 数据库查询微博
		$res = M('Weibo')->alias('w')->field('u.id uid, u.name, w.id wid, w.content, w.ctime')->join('inner join ' . C('DB_PREFIX') . 'user u on u.id = w.uid')->
			   where('u.id = %d or u.id in (select aid from ' . C('DB_PREFIX') . 'attention where uid = %d)', $this->id, $this->id)->page($page, $limit)->order('w.ctime desc')->select();
		// 检测res是否查询到
		if(!$res)  return; 
		// 查询 赞 评论 转发 的数量
		$having = '';
		foreach($res as $row){
			$having .= $row['wid'] . ',';
		}
		$having = rtrim($having, ',');
		$praise = M('Praise')->field('wid, count(*) c')->group('wid')->having("wid in ($having)")->select();
		// 组装数组
		if(is_array($praise)){
			$res = $this->_glueArray($res, $praise, 'wid', 'praise');
		}
		// 看我是不是关注了
		$praised = M('Praise')->where('uid = %d', $this->id)->getField('wid', true);
		if($praised){
			$tmp_arr = array();
			foreach($res as $row){
				if(in_array($row['wid'], $praised)){
					$row['praised'] = 1;
				}
				$tmp_arr[] = $row;
			}
			$res = $tmp_arr;
		}
		// 输出数据到客户端
		echo json_encode($res);
	}

	/**
	 * handle praise
	 */
	public function praise(){
		if(!$wid = I('post.wid', 0, 'intval'))  return;
		// 检测操作
		$count = M('Praise')->where('wid = %d and uid = %d', $wid, $this->id)->count();
		// 这是增加赞
		if(!$count){
			if(M('Praise')->data(array('wid' => $wid, 'uid' => $this->id))->add()){
				echo '1';
			}
		}
		// 这是删除赞
		else{
			if(M('Praise')->where('wid = %d and uid = %d', $wid, $this->id)->delete()){
				echo '2';
			}
		}
	}

	/**
	 * handle comment
	 */
	public function comment(){
		// validate args
		if(!$wid = I('post.wid', 0, 'intval'))  return;
		if(!$comment = I('post.comment', '', ''))  return;
		$comment = mb_substr($comment, 0, 50, 'utf-8');
		$data = array(
				'wid'		=>	$wid,
				'uid'		=>	$this->id,
				'content'	=>	$comment,
				'ctime'		=>	time()
		);
		if(!M('Comment')->data($data)->add())  return;
		echo '1';
	}
	
	/**
	 * list Comment of a weibo
	 */
	public function listComment(){
		// validate args
		if(!$wid = I('post.wid', 0, 'intval'))  return;
		if(!$limit = I('post.limit', 0, 'intval'))  return;
		$page = I('post.page', 0, 'intval');
		// query database
		$comment = M('Comment')->where('wid = %d', $wid)->page($page, $limit)->order('ctime desc')->select();
		if(!$comment)  return;
		// json back
		echo json_encode($comment);
	}	
	
	/**
	 * 组装数组
	 */
	private function _glueArray($res, $arr, $key, $key_name, $count_name = 'c'){
		$tmp_arr = array();
		foreach($arr as $row){
			$tmp_arr[$row[$key]] = $row[$count_name];
		}
		$arr = $tmp_arr;
		$tmp_arr = array();
		foreach($res as $row){
			if(in_array($row[$key], array_keys($arr))){
				$row[$key_name] = $arr[$row[$key]];
			}
			$tmp_arr[] = $row;
		}
		return $tmp_arr;
	} 
	
	
}