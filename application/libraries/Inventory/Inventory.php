<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Thrown when an owner does not own a specified item.
 *
 * @package CRYS\Libraries\Markets
 * @author Gio Carlo Cielo <gio@crysandrea.com> @package
 */
class No_Such_Item_Exception extends Exception {
	const EXCEPTION_FMT = '%d does not own item %d.';

	public $owner_id;
	public $item_id;

	public function __construct($owner_id, $item_id) {
		parent::__construct(sprintf(self::EXCEPTION_FMT, $owner_id, $item_id));
		$this->owner_id = $owner_id;
		$this->item_id = $item_id;
	}
}

/**
 * Inventory manages every type type of item for a specified owner. Valid
 * drivers include 'avatar_items' and 'bug_items'.
 *
 * @package CRYS\Libraries\Inventory
 * @author Gio Carlo Cielo <gio@crysandrea.com>
 */
class Inventory extends CI_Driver_Library {
	protected $valid_drivers = array(
		'inventory_avatar_items', 'inventory_bug_items'
	);

	protected $_adapter = 'avatar_items';

	public function __construct($config = array()) {
		$item_type = 'avatar_items';
		if (!empty($config['item_type']))
			$item_type = $config['item_type'];
		$this->_adapter = $item_type;
	}

	public function setItemType($item_type) {
		assert(!empty($item_type));
		$this->_adapter = $item_type;
		return $this;
	}
	
	public function setOwner($owner_id) {
		assert(!empty($owner_id));
		$this->{$this->_adapter}->setOwner($owner_id);
		return $this;
	}
	
	public function getItem($item_id) {
		return $this->{$this->_adapter}->getItem($item_id);
	}

	public function getItems() {
		return $this->{$this->_adapter}->getItems();
	}

	public function addItem($item_id) {
		assert(!empty($item_id));
		$this->{$this->_adapter}->addItem($item_id);
		return $this;
	}

	public function removeItem($item_id) {
		assert(!empty($item_id));
		$this->{$this->_adapter}->removeItem($item_id);
		return $this;
	}
}

