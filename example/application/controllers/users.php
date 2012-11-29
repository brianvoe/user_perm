<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

	function index($user_id = false) {
		// Grab list of current users
		$users = $this->db->select('*')->from('users')->get()->result();
		$data['users'] = '';
		foreach($users as $user) {
			$data['users'] .= '<a href="'.site_url('users/index/'.$user->id).'"><span style="color: orange; padding-right: 5px;">edit</span> ';
			$data['users'] .= '<a href="'.site_url('users/delete/'.$user->id).'"><span style="color: red; padding-right: 5px;">delete</span> ';
			$data['users'] .= '</a>'.$user->name.'<br />';
		}

		$data['user_id'] = $user_id;
		$data['name'] = '';
		if($user_id) {
			// Grab user name
			$data['name'] = $this->db->select('name')->from('users')->where('id', $user_id)->limit(1)->get()->row()->name;
		}

		$this->load->view('users', $data);
	}

	function add() {
		if($this->input->post('name')) {
			$this->db->insert('users', array('name' => $this->input->post('name'))); 
		}
		redirect('users');
	}

	function edit() {
		if($this->input->post('user_id')) {
			$this->db->where('id', $this->input->post('user_id'));
			$this->db->limit(1);
			$this->db->update('users', array('name' => $this->input->post('name'))); 
		}
		redirect('users');
	}

	function delete($user_id) {
		$this->db->delete('users', array('id' => $user_id));
		redirect('users'); 
	}
}