<?php

class InfoAction extends CommonAction{
	
	/**
	 * 获取个人信息
	 */
	public function index(){
		if(!$uid = I('post.uid', 0, 'intval'))  return;
		// 获取基础信息
		$user = M('User')->field('name, ctime, mood, sex, birth, residence, intro')->where(array('id' => $uid))->find();
		if(!$user)  return;
		// 获取 三种 数量
		$sql = 'select count(*) c from %1$sattention where aid = %2$u union all ' . 
			   'select count(*) c from %1$sattention where uid = %2$u union all ' . 
			   'select count(*) c from %1$sweibo where uid = %2$u';
		$sql = sprintf($sql, C('DB_PREFIX'), $uid);
		$res = M()->query($sql);
		$counts = array();
		foreach($res as $row){
			$counts[] = $row['c'];
		}
		// 获取关注状态
		$attend = 0;
		if($this->id != $uid){
			$attend = M('Attention')->where('uid = %d and aid = %d', $this->id, $uid)->count();
		}
		// name,ctime,mood,sex,birth,residence,intro,fas_num,attention_num,weibo_num,attend
		$gule = '!@*&^%';
		echo implode($gule, $user) . $gule . implode($gule, $counts) . $gule . $attend;
	}
	
	/**
	 * 修改个人信息
	 */
	public function modify(){
		// 获取
		$mood = I('post.mood', '', '');
		$sex = I('post.sex', 0, 'intval');
		$birth = I('post.birth', 0, 'intval');
		$residence = I('post.residence', '', '');
		$intro = I('post.intro', '', '');
//		// 转换特殊字符
//		$mood = $this->_replaceSpecialChars($mood);
//		$residence = $this->_replaceSpecialChars($residence);
//		$intro = $this->_replaceSpecialChars($intro);
		// 
		$data['mood'] = $mood;
		$data['sex'] = $sex;
		$data['birth'] = $birth;
		$data['residence'] = $residence;
		$data['intro'] = $intro;
		//
		$res = M('User')->where(array('id' => $this->id))->save($data);
		if(!$res)  return;
		echo '1';	
	}
	
//	private function _replaceSpecialChars($str){
//		$str = str_replace('%aI#@', '&', $str);
//		$str = str_replace('#JJ#@a', '=', $str);
//		return $str;
//	}
	
}
 