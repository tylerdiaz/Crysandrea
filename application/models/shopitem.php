<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ShopItem extends CI_Model {

	const TABLE = 'shop_items';
	const LIMIT = 7;

	public $shop_item_id;
	public $item_id;
	public $shop_id;
	public $item_type;
	public $item_parent;
	public $price;
	public $item_currency;
	public $insect_id;
	public $second_price;

	public function __construct() {
		parent::__construct();
	}

	public function findByItem($key, $value) {
		$this->db->select(static::TABLE.'.shop_id');
		$this->db->select($this->AvatarItem->getTable().'.thumb');
		$this->db->select($this->AvatarItem->getTable().'.name');
		$this->db->from(static::TABLE);
		$this->db->join($this->AvatarItem->getTable(), $this->AvatarItem->getTable().'.item_id='.static::TABLE.'.item_id');
		$this->db->like($this->AvatarItem->getTable().$key, $value);
		$result = $this->db->get();

		return $result;
	}

	public function getTable() {
		return static::TABLE;
	}
}

