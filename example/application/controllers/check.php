<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Check extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('user_perm');
	}

	function index() {
		$perm_check = $this->user_perm->check(7, array('section', 'stats', 'view'));

		if($perm_check) {
			echo 'Welcome to this page';
		} else {
			echo 'You do not have permission';
		}
	}
}