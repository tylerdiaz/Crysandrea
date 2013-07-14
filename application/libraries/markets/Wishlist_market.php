<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wishlist_Market {
	private $CI;

	public function__construct($config=array()) {
		$this->CI->load->driver('Inventory', array(
			'item_type' => 'wishlist')
		);
	}

	public function requestItem($owner_id, $item_id) {
		$this->CI->inventory->setOwner($owner_id)->addItem($item_id);
	}

	public function giveItem($owner_id, $giver_id, $item_id) {
		$this->CI->inventory
			->setItemType('avatar_items')
			->setOwner($owner_id)
			->removeItem($item_id);
		$this->CI->inventory
			->setOwner($owner_id)
			->addItem($item_id);
		$this->CI->inventory
			->setItemType('wishlist')
			->setOwner($owner_id)
			->removeItem($item_id);
	}
}

