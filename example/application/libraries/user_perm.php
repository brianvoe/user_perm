<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User_perm {

	// Array list of locations
	public $locations = array(
		'section' => array(
			'products' => array('add','edit','delete'),
			'stats' => array('view','export')
		),
		'area' => array(
			'dashboard' => array('promo_codes','user_count','search'),
			'countries' => array('united_states','canada','europe','asia','japan')
		)
	);

	// Db table naming
	public $db_perm = 'permissions'; // Permissions table name - permissions are assigned to groups
	public $db_users = 'users'; // Users table name
	public $db_groups = 'groups'; // Groups table name
	public $db_assoc = 'groups_users'; // Associate user to a group table name

	// Input name and variable settings
	public $name_locations = 'perms[]';
	public $val_delimiter = '--';
	public $text_locations = '';

	function __construct() {
		$this->ci = & get_instance();
	}

	///////////////////
	// Html displays //
	///////////////////
	function list_locations($select = false, $array = false, $input_val = '', $num = 0) { // Get list of locations in ul, li layout with checkbox
		if($array) {
			$this->text_locations .= '<ul>';
			foreach($array as $key => $value) {
				if(is_array($value)) {
					$this->text_locations .= '<li class="perm_group perm_group_'.$num.'">'.$key.'</li>';
					$this->list_locations($select, $value, $this->set_list_val($input_val, $key), $num++);
				} else {
					$check_val = false;
					if($select) {
						$cur_input = explode($this->val_delimiter, $input_val);
						$cur_input_count = count($cur_input);
						foreach($select as $select_val) {
							if($select_val->{'cat_'.$cur_input_count} != '' && $select_val->{'cat_'.$cur_input_count} == $value) {
								$check_val = true;
							}
						}
					}

					$this->text_locations .= '<li class="perm_val">';
					$this->text_locations .= '	<input type="checkbox" '.($check_val ? 'checked="checked"': '').' name="'.$this->name_locations.'" value="'.$this->set_list_val($input_val, $value).'" />'.$value;
					$this->text_locations .= '</li>';
				}
			}
			$this->text_locations .= '</ul>';
		} else {
			$this->text_locations = '';
			$this->list_locations($select, $this->locations, '', $num);
			return $this->text_locations;
		}
	}

	//////////////////////
	// Add/Remove items //
	//////////////////////
	function add_permissions($group_id, $perms) { // Add Permissions to group
		foreach($perms as $perm) {
			$perm_insert = array('group_id' => $group_id);
			$perm = explode($this->val_delimiter, $perm);
			foreach($perm as $key => $value) {
				$perm_insert['cat_'.$key] = $value;
			}
			$this->ci->db->insert($this->db_perm, $perm_insert);
		}
	}
	function remove_permissions($group_id = false) { // Remove permissions from a group
		if($group_id) {
			$this->ci->db->delete($this->db_perm, array('group_id' => $group_id));
		}
	}

	//////////////
	// Get info //
	//////////////
	function get_permissions($group_id) { // Get current permissions of group(s)
		if($group_id) {
			$this->ci->db->select('*');
			$this->ci->db->from($this->db_perm);
			if(is_array($group_id)) {
				$this->ci->db->where_in('group_id', $group_id);
			} else {
				$this->ci->db->where('group_id', $group_id);
			}
			return $this->ci->db->get()->result();
		} else {
			return false;
		}
	}
	function get_groups($user_id) { // Get list of group_ids the user belongs to
		// Get groups that the user belongs to
		if($user_id) {
			$this->ci->db->select('*');
			$this->ci->db->from($this->db_assoc);
			$this->ci->db->where('user_id', $user_id);
			$results = $this->ci->db->get()->result();
			if($results) {
				$id_array = array();
				foreach($results as $result) {
					array_push($id_array, $result->group_id);
				}
				return $id_array;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/////////////////
	// Check stuff //
	/////////////////
	function check($user_id = false, $locations = false) { // Check if user has permission to view location
		// Get groups permissions
		$group_ids = $this->get_groups($user_id);
		$perms = $this->get_permissions($group_ids);
		$loc_count = count($locations);
		$valid = false;
		if($perms) {
			// Loop through permissions and see if it matches requested location
			foreach($perms as $perm){
				// Make sure db even has the length of depth of locations
				if(isset($perm->{'cat_'.($loc_count-1)}) && $perm->{'cat_'.($loc_count-1)} != '') {
					$valid_num = 0;

					// Loop through each location
					foreach($locations as $key => $location) {
						if($location == $perm->{'cat_'.$key}) {
							$valid_num++;
						}
					}
					
					if($valid_num == $loc_count) {
						$valid = true;
					}
				}
			}
			return $valid;
		} else {
			return false;
		}
	}

	//////////
	// Misc //
	//////////
	private function set_list_val($is, $add) {
		return strtolower(($is == '' ? $add: $is.$this->val_delimiter.$add));
	}
}	