<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trade extends CRYS_Data_Model {
	public $trade_title;
	public $trade_sender;
	public $trade_receiver;
	public $sender_palladium;
	public $receiver_palladium;
	public $sender_berries;
	public $receiver_berries;
	public $trade_date;
	public $sender_status;
	public $receiver_status;

	// ignore
	public $trade_id;
	public $trade_cap;
	public $trade_notification;

	protected static $REQUIRED = array('trade_sender', 'trade_receiver');
	protected static $DEFAULTS = array(
		'sender_palladium' => 0,
		'receiver_palladium' => 0,
		'sender_status' => 0,
		'receiver_status' => 0
	);

	public function __construct($attributes=array()) {
		parent::__construct($attributes);
	}
}

class Trades_Model extends CRYS_Composite_Model {
	protected static $TABLE = 'trades';
	protected static $PRIMARY_KEY = 'trade_id';

	protected static $INNER_MODEL = 'Trade';

	protected static $FK_FROM = 'trade_sender';
	protected static $FK_TO = 'trade_receiver';

	public function __construct() {
		parent::__construct();
	}
}

