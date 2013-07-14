<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_wishlist extends CI_Driver {
	private $CI;
	private $owner_id;

	public function __construct() {
		$this->CI = &get_instance();
		$this->CI->load->model('wishlist');
	}

	public function setOwner($owner_id) {
		$this->owner_id = $owner_id;
	}

	public function getItem($item_id) {
		try {
			$this->wishlist->findRelation($this->owner_id, $item_id);
		} catch (No_Such_Result_Exception $e) {
			throw new No_Such_Item_Exception($this->owner_id, $item_id);
		}
	}

	public function getItems() {
		return $this->CI->wishlist->findFrom($this->owner_id);
	}

	public function addItem($item_id) {
		$wishlist_item = $this->wishlist->createRelation($this->owner_id, $item_id);
		$this->wihslist->saveRelation($wishlist_item);
	}

	public function removeItem($item_id) {
		try {
			$this->wishlist->findRelation($this->owner_id, $item_id, '1');
		} catch (No_Such_Item_Exception $e) {
			throw new No_Such_Item_Exception($this->owner_id, $item_id);
		}
		$this->wishlist->removeRelation($this->owner_id, $item_id);
	}
}

