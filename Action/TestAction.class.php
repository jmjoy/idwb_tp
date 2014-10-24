<?php

class TestAction extends Action {
	
	public function index() {
		echo json_encode(I('server.'));
	}
	
	public function post() {
		$this->display();
	}
}
