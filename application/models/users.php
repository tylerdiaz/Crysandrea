<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CRYS_Data_Model {
	public $user_id;
	public $avatar_gender;
	public $username;
	public $user_ip;
	public $user_email;
	public $user_pass;
	public $user_last_login;
	public $register_date;
	public $user_level;
	public $user_palladium;
	public $user_gems;
	public $user_signature;
	public $last_action;
	public $donated;
	public $special_currency;
	public $second_special_currency;
	public $banned;
	public $reffered;
	public $new_mail;
	public $new_trades;
	public $new_friends;
	public $previously_donated;
	public $auto_login_token;
	public $last_saved_avatar;
	public $verified_email;
	public $timezone;

	public function __construct($attributes=array()) {
		parent::__construct($attributes);
	}
}

class Users extends CRYS_Model {
	protected static $TABLE = 'users';
	protected static $PRIMARY_KEY = 'user_id';

	protected static $INNER_MODEL = 'User';

	public function __construct() {
		parent::__construct();
	}
}

