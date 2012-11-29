<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User_perm {

	public $db_table = 'permissions';
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

	private $name_locations = 'perms[]';
	private $val_delimiter = '--';
	private $text_locations = '';

	function __construct() {
		$this->ci = & get_instance();
	}

	function list_locations($select = false, $array = false, $input_val = '', $num = 0) {
		if($array) {
			$this->text_locations .= '<ul>';
			foreach($array as $key => $value) {
				if(is_array($value)) {
					$this->text_locations .= '<li class="perm_group perm_group_'.$num.'">'.$key.'</li>';
					$this->list_locations($select, $value, $this->set_val($input_val, $key), $num++);
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
					$this->text_locations .= '	<input type="checkbox" '.($check_val ? 'checked="checked"': '').' name="'.$this->name_locations.'" value="'.$this->set_val($input_val, $value).'" />'.$value;
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

	private function set_val($is, $add) {
		return strtolower(($is == '' ? $add: $is.$this->val_delimiter.$add));
	}

	function add_permissions($group_id, $perms) {
		foreach($perms as $perm) {
			$perm_insert = array('group_id' => $group_id);
			$perm = explode($this->val_delimiter, $perm);
			foreach($perm as $key => $value) {
				$perm_insert['cat_'.$key] = $value;
			}
			$this->ci->db->insert($this->db_table, $perm_insert);
		}
	}

	function remove_permissions($group_id = false) {
		if($group_id) {
			$this->ci->db->delete($this->db_table, array('group_id' => $group_id));
		}
	}

	function check($group_id = false, $locations = false) {
		if($group_id && $locations) {
			$this->ci->db->select('id');
			$this->ci->db->from($this->db_table);
			if(is_array($group_id)) {
				$this->ci->db->where_in('group_id', $group_id);
			} else {
				$this->ci->db->where('group_id', $group_id);
			}
			foreach($locations as $key => $value) {
				$this->ci->db->where('cat_'.$key, $value);
			}
			return $this->ci->db->get()->row();
		} else {
			return false;
		}
	}

}	