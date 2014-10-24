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
		$res = $this->_glueNumArgs($res);
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
		$comment = M('Comment')->alias('c')->field('c.uid uid, u.name, c.ctime, c.content')->
				   join('inner join ' . C('DB_PREFIX') . 'user u on u.id = c.uid')->
				   where('wid = %d', $wid)->page($page, $limit)->order('ctime desc')->select();
		if(!$comment)  return;
		// json back
		echo json_encode($comment);
	}	
	
	/**
	 * push three type nums into res
	 * @param unknown $res
	 * @return unknown
	 */
	private function _glueNumArgs($res){
		// 查询 赞 评论 转发 的数量
		$having = '';
		foreach($res as $row){
			$having .= $row['wid'] . ',';
		}
		$having = rtrim($having, ',');
		// 查询三种数量
		$praise_res = M('Praise')->field('wid, count(*) c')->group('wid')->having("wid in ($having)")->select();
		$comment_res = M('Comment')->field('wid, count(*) c')->group('wid')->having("wid in ($having)")->select();
		$forward_res = M('Forward')->field('wid, count(*) c')->group('wid')->having("wid in ($having)")->select();
		// 转化格式
		$praise = $this->_formatNumArr($praise_res);
		$comment = $this->_formatNumArr($comment_res);
		$forward = $this->_formatNumArr($forward_res);
		//
		return $this->_glueCount($res, $praise, $comment, $forward);
	}
	
	/**
	 * 将三种数量的数组转化成好看的格式
	 */
	private function _formatNumArr($arr){
		$map = array();
		if($arr){
			foreach($arr as $row){
				$map[$row['wid']] = $row['c'];
			}		
		}
		return $map;
	}
	
	/**
	 * what's the best name of me ?
	 */
	private function _glueCount($res, $praise, $comment, $forward){
		$map = array();
		foreach($res as $row){
			if($praise && array_key_exists($row['wid'], $praise) ){
				$row['praise'] = $praise[$row['wid'] ];
			}
			if($comment && array_key_exists($row['wid'], $comment) ){
				$row['comment'] = $comment[$row['wid'] ];
			}
			if($forward && array_key_exists($row['wid'], $forward) ){
				$row['forward'] = $forward[$row['wid'] ];
			}
			$map[] = $row;
		}
		return $map;
	}
}