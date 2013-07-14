<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_Avatar_Items extends CI_Driver {
	private $CI;

	private $owner_id;

	public function __construct() {
		$this->CI = &get_instance();
		$this->CI->load->model('user_items');
	}

	public function setOwner($owner_id) {
		$this->owner_id = $owner_id;
	}

	public function getItem($item_id) {
		try {
			return $this->CI->user_items->findRelation($this->owner_id, $item_id);
		} catch (No_Result_Exception $e) {
			throw new No_Such_Item_Exception($this->owner_id, $item_id);
		}
	}

	public function getItems() {
		return $this->CI->user_items->findFrom($this->owner_id);
	}

	public function addItem($item_id) {
		$user_item = $this->CI->user_items->createRelation($this->owner_id, $item_id);
		$this->CI->user_items->saveRelation($user_item);
	}

	public function removeItem($item_id) {
		try {
			$this->CI->user_items->findRelation($this->owner_id, $item_id, 'item_id');
		} catch (No_Result_Exception $e) {
			throw new No_Such_Item_Exception($this->owner_id, $item_id);
		}
		$this->CI->user_items->removeRelation($this->owner_id, $item_id);
	}
}

