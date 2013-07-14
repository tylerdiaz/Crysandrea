<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MarketplaceItem extends CI_Model {

	const TABLE = 'marketplace_items';

	public $id;
	public $user_id;
	public $username;
	public $item_id;
	public $item_thumbnail;
	public $item_name;
	public $price;
	public $purchased;
	public $cancled;
	public $purchased_by;
	public $published_at;
	public $finishes_at;
	public $completed_at;
	public $item_type;

	public function __construct() {
		parent::__construct();
	}

	public function getTable() {
		return static::TABLE;
	}
}

