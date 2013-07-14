<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trade_Item extends CRYS_Data_Model {
	public $user_id;
	public $item_id;
	public $bug_id;
	public $trade_id;
	public $item_type;
	public $amount;

	// ignore
	public $trade_item_id;

	protected static $REQUIRED = array('trade_id', 'user_id');
	protected static $DEFAULTS = array(
		'item_type' => 'item',
		'amount' => 'amount'
	);

	public function __construct($attributes=array()) {
		parent::__construct($attributes);
	}
}

class Trade_Items_Model extends CRYS_Composite_Model {
	protected static $TABLE = 'trade_items';
	protected static $PRIMARY_KEY = 'trade_item_id';

	protected static $INNER_MODEL = 'Trade_Item';

	protected static $FK_FROM = 'trade_id';
	protected static $FK_TO = 'trade_sender';

	public function __construct() {
		parent::__construct();
	}
}
