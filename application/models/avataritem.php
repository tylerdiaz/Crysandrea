<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AvatarItem extends CI_Model {

	const TABLE = 'avatar_items';
	const LIMIT = 8;

	public $item_id;
	public $name;
	public $gender;
	public $thumb;

	public function __construct() {
		parent::__construct();
	}

	public function findAvailable($case_sensitive = FALSE)  {
		assert(!empty($this->item_id) || !empty($this->name));

		/*
		* EXPLAIN EXTENDED SELECT ai.name, ai.thumb, MIN(mi.id) AS marketplace_id,
		* MIN(si.shop_id) as shop_id FROM avatar_items AS ai LEFT JOIN shop_items
		* AS si ON si.item_id=ai.item_id LEFT JOIN marketplace_items AS mi ON
		* mi.item_id=ai.item_id WHERE ai.name LIKE '%Start%' GROUP BY ai.item_id LIMIT 7;
		* (0.00sec)
		*/

		$this->db->select(static::TABLE.'.item_id');
		$this->db->select(static::TABLE.'.name');
		$this->db->select(static::TABLE.'.thumb');
		$this->db->select(static::TABLE.'.layer');
		// $this->db->select('MIN('.$this->MarketplaceItem->getTable().'.id) AS `marketplace_item_id`');
		$this->db->select('MIN('.$this->ShopItem->getTable().'.shop_id) AS `shop_id`');
		$this->db->from(static::TABLE);
		// shop_items is indexed on item_id; so this must be joined first

		$this->db->join($this->ShopItem->getTable(), $this->ShopItem->getTable().'.item_id='.static::TABLE.'.item_id', 'left');
		// $this->db->join($this->MarketplaceItem->getTable(), $this->MarketplaceItem->getTable().'.item_id='.static::TABLE.'.item_id', 'left');

		if ($this->item_id):
			$this->db->where(static::TABLE.'.name', $this->item_id);
		elseif ($this->name):
			if ($case_sensitive):
				$this->db->like(static::TABLE.'.name', $this->name);
			else:
				$this->db->like('LOWER('.static::TABLE.'.name)', strtolower($this->name));
			endif;
		endif;

		$this->db->group_by(static::TABLE.'.item_id');
		$this->db->limit(static::LIMIT);
		$result = $this->db->get()->result_array();
		return $result;
	}

	public function getTable() {
		return static::TABLE;
	}
}

