<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Item extends CRYS_Data_Model {
	public $id;
	public $item_id;
	public $user_id;
	public $equipped;
	public $parent_id;
	public $trying_on;
	public $soft_deleted;
	public $soft_deleted_by_user_id;
	public $sent_by;

	protected static $REQUIRED = array('item_id', 'user_id');
	protected static $DEFAULTS = array(
		'equipped' => false,
		'parent_id' => 0,
		'trying_on' => 0,
		'soft_deleted' => 0,
	);

	public function __construct($attributes=array()) {
		parent::__construct($attributes);
	}
}

class User_Items extends CRYS_Composite_Model {
	protected static $TABLE = 'user_items';
	protected static $PRIMARY_KEY = 'id';

	protected static $INNER_MODEL = 'User_Item';

	protected static $FK_FROM = 'user_id';
	protected static $FK_TO = 'item_id';

	public function __construct() {
		parent::__construct();
	}

	public function removeRelation($fk_from, $fk_to) {
		if ($this->db->get_where(static::$TABLE, array(
				static::$FK_FROM => $fk_from,
				static::$FK_TO => $fk_to))->num_rows() < 1):
			throw new Exception('User does not own this item');
		endif;

		parent::removeRelation($fk_from, $fk_to);
	}
}
