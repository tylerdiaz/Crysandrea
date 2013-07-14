<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_Bug_Items extends CI_Driver {
	private $CI;

	private $owner_id;

	public function __construct() {
		$this->CI = &get_instance();
		$this->CI->load->model('forest_user_catches');
	}

	public function setOwner($owner_id) {
		$this->owner_id = $owner_id;
	}

	public function getItem($item_id) {
		try {
			return $this->CI->forest_user_catches->findRelation($this->owner_id, $item_id);
		} catch (No_Result_Exception $e) {
			throw new No_Such_Item_Exception($this->owner_id, $item_id);
		}
	}

	public function getItems() {
		return $this->CI->forest_user_catches->findRelation($this->owner_id);
	}

	public function addItem($item_id) {
		$forest_user_catch = $this->CI->forest_user_catches->createRelation($this->owner_id, $item_id);
		$this->CI->forest_user_catches->saveRelation($forest_user_catch);
	}

	public function removeItem($item_id) {
		try {
			$this->CI->forest_user_catches->findRelation($this->owner_id, $item_id, 'id');
		} catch (No_Result_Exception $e) {
			throw new No_Such_Item_Exception($this->owner_id, $item_id);
		}
			$this->CI->forest_user_catches->removeRelation($this->owner_id, $item_id);
	}
}

