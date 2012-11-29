<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Groups extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('user_perm');
	}

	function index($group_id = false) {
		// Grab list of current groups
		$groups = $this->db->select('*')->from('groups')->get()->result();
		$data['groups'] = '';
		foreach($groups as $group) {
			$data['groups'] .= '<a href="'.site_url('groups/index/'.$group->id).'"><span style="color: orange; padding-right: 5px;">edit</span></a> ';
			$data['groups'] .= '<a href="'.site_url('groups/delete/'.$group->id).'"><span style="color: red; padding-right: 5px;">delete</span></a> ';
			$data['groups'] .= $group->name.'<br />';
		}

		// Grab list of current users
		$users = $this->db->select('*')->from('users')->get()->result();
		$data['users'] = '';
		foreach($users as $user) {
			$data['users'] .= '<input type="checkbox" name="users[]" value="'.$user->id.'" />'.$user->name.'<br />';
		}

		$data['group_id'] = $group_id;
		$data['name'] = '';
		if($group_id) {
			// Grab group info
			$data['name'] = $this->db->select('name')->from('groups')->where('id', $group_id)->limit(1)->get()->row()->name;

			// Grab users
			$set_user_array = array();
			$set_users = $this->db->select('*')->from('groups_users')->where('group_id', $group_id)->get()->result();
			foreach($set_users as $info) {
				array_push($set_user_array, $info->user_id);
			}

			// Set users
			$data['users'] = '';
			foreach($users as $user) {
				$data['users'] .= '<input type="checkbox" '.(in_array($user->id, $set_user_array) ? 'checked="checked"': '').' name="users[]" value="'.$user->id.'" />'.$user->name.'<br />';
			}

			// Populate list with 
			$group_permissions = $this->db->select('*')->from('permissions')->where('group_id', $group_id)->get()->result();

			$data['permissions'] = $this->user_perm->list_locations($group_permissions);
		} else {
			$data['permissions'] = $this->user_perm->list_locations();
		}

		$this->load->view('groups', $data);
	}

	function add() {
		$name = $this->input->post('name');
		$users = $this->input->post('users');
		$perms = $this->input->post('perms');

		if($name && $users && $perms) {
			// Add group
			$this->db->insert('groups', array('name' => $name));
			$group_id = $this->db->insert_id();

			// Add users to group
			foreach($users as $user) {
				$this->db->insert('groups_users', array('group_id' => $group_id, 'user_id' => $user));
			}

			// Add permissions to group
			$this->user_perm->add_permissions($group_id, $perms);
		}
		redirect('groups');
	}

	function edit() {
		$group_id = $this->input->post('group_id');
		$name = $this->input->post('name');
		$users = $this->input->post('users');
		$perms = $this->input->post('perms');

		if($group_id && $name && $users && $perms) {
			// Delete group stuff before insert
			$this->db->delete('permissions', array('group_id' => $group_id));
			$this->db->delete('groups_users', array('group_id' => $group_id));

			// Update group
			$this->db->where('id', $group_id);
			$this->db->update('groups', array('name' => $name));

			// Add users to group
			foreach($users as $user) {
				$this->db->insert('groups_users', array('group_id' => $group_id, 'user_id' => $user));
			}

			// Add permissions to group
			$this->user_perm->add_permissions($group_id, $perms);
		}
		redirect('groups');
	}

	function delete($group_id) {
		// Delete group permissions
		$this->db->delete('permissions', array('group_id' => $group_id));

		// Delete group users
		$this->db->delete('groups_users', array('group_id' => $group_id));

		// Delete group
		$this->db->delete('groups', array('id' => $group_id));
		redirect('groups'); 
	}
}