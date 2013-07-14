<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forest_User extends CRYS_Data_Model {
	public $forest_user_id;
	public $user_id;
	public $level;
	public $exp;
	public $next_level_exp;
	public $bugs_caught;
	public $catch_attempts;
	public $energy;
	public $max_energy;
	public $energize_at;
	public $berries;
	public $catches_till_snap;
	public $speed;
	public $sneak;
	public $power;
	public $luck;
	public $net_snapped_at;
	public $swings_till_snap;
	public $last_hunt;

	protected static $DEFAULTS = array(
		'bugs_caught'       => 0,
		'catch_attempts'    => 0,
		'energy'            => 100,
		'max_energy'        => 100,
		'energize_at'       => 0,
		'berries'           => 0,
		'catches_till_snap' => 0,
		'speed'             => 0,
		'sneak'             => 0,
		'power'             => 0,
		'luck'              => 0,
		'net_snapped_at'    => 0,
		'swings_till_snap'  => 0
	);

	public function __construct($attributes=array()) {
		parent::__construct($attributes);
	}
}

class Forest_Users extends CRYS_Model {
	protected static $TABLE = 'forest_users';
	protected static $PRIMARY_KEY = 'user_id';

	protected static $INNER_MODEL = 'Forest_User';

	public function __construct() {
		parent::__construct();
	}
}


