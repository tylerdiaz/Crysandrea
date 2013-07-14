<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forest_User_Catch extends CRYS_Data_Model {
	public $id;
	public $insect_id;
	public $user_id;
	public $timestamp;

	protected static $REQUIRED = array('insect_id', 'user_id');
	protected static $DEFAULTS = array('insect_id' => 0);

	public function __construct($attributes=array()) {
		parent::__construct($attributes);
	}
}

class Forest_User_Catches extends CRYS_Composite_Model {
	protected static $TABLE = 'forest_user_catches';
	protected static $PRIMARY_KEY = 'id';

	protected static $INNER_MODEL = 'Forest_User_Catch';

	protected static $FK_FROM = 'user_id';
	protected static $FK_TO = 'insect_id';

	public function __construct() {
		parent::__construct();
	}

	public function removeRelation($fk_from, $fk_to) {
		if ($this->db->get_where(static::$TABLE, array(
				static::$FK_FROM => $fk_from,
				static::$FK_TO => $fk_to))->num_rows() < 1):
			throw new Exception('User does not own this bug');
		endif;

		parent::removeRelation($fk_from, $fk_to);
	}
}
