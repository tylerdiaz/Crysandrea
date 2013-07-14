<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once 'IMarket.php';

/**
 * Defines the shop market from the IMarket interface. This market loads
 * CRYS\Libraries\Accountant driver and standard models for shops, users and
 * items.
 *
 * @inheritdoc IMarket
 * 
 * @package CRYS\Libraries\Markets
 * @author Gio Carlo Cielo <gio@crysandrea.com>
 */
class Shops_Market implements IMarket {
	private $CI;

	private $currency_type;
	private $item_type;

	public function __construct($config=array()) {
		$this->currency_type = 'palladium';
		$this->item_type = 'avatar_items';
		if (!empty($config) && !empty($config['currency_type']))
			$this->currency_type = $config['currency_type'];
		if (!empty($config) && !empty($config['item_type']))
			$this->item_type = $config['item_type'];

		$this->CI = &get_instance();
		$this->CI->load->driver('accountant', array('currency_type' => $this->currency_type));
		$this->CI->load->driver('inventory', array('item_type' => $this->item_type));
		$this->CI->load->model('shop_items');
	}

	public function setCurrencyType($currency_type) {
		$this->currency_type = $currency_type;
		return $this;
	}

	public function setItemType($item_type) {
		$this->item_type = $item_type;
		return $this;
	}

	public function listing($seller_id=0, $item_id=0) {
		if (empty($seller_id))
			show_error('Invalid shop');
		if (!empty($seller_id) && !empty($item_id))
			return $this->CI->shop_items->findRelation($seller_id, $item_id, 'item_id');
		return $this->CI->shop_items->findFrom($seller_id);
	}

	public function sell($seller_id=0, $item_id=0, $attributes=array()) {
		if (empty($seller_id) || empty($item_id))
			show_error('Invalid seller or invalid item');
		$this->CI->inventory->setItemType($this->item_type)
			->setOwner($seller_id)
			->removeItem($item_id);
		$shop_item = $this->CI->shop_items->create($seller_id, $item_id, $attributes);
		$this->CI->shop_items->save($shop_item);
		return $this;
	}

	public function purchase($seller_id=0, $buyer_id=0, $item_id=0, $attributes=array()) {
		if (empty($seller_id) || empty($buyer_id) || empty($item_id))
			show_error('Invalid seller, buyer or item');
		// Perform validation
		try {
			$shop_item = $this->CI->shop_items->findRelation($seller_id, $item_id, 'price');
		} catch (No_Result_Exception $e) {
			throw new No_Such_Item_Exception($seller_id, $item_id);
		}

		$this->CI->db->trans_start();
		// Remove currency from user
		$value = $shop_item->price;
		$this->CI->accountant
			->setCurrencyType($this->currency_type)
			->setOwner($buyer_id)
			->withdraw($value);

		// Add to user items
		$this->CI->inventory->setItemType($this->item_type)
			->setOwner($buyer_id)
			->addItem($item_id);
		$this->CI->db->trans_complete();
		return $this;
	}

	/**
	 * An optional feature of shops to sell an item back to the seller for a
	 * depreciated value.
	 *
	 * @param int $seller_id The ID of the seller
	 * @param int $buyer_id The ID of the buyer
	 * @param array $attributes Array of attributes for selling back the item
	 * @throws No_Such_Item_Exception If the seller does not sell the specified
	 *		item
	 */
	public function sellback($seller_id=0, $buyer_id=0, $item_id=0, $attributes=array()) {
		if ($seller_id === 0 || $item_id === 0 || $item_id === 0)
			show_error('Invalid seller, buyer or item');
		// Perform validation
		try {
			$shop_item = $this->CI->shop_items->findRelation($seller_id, $item_id, 'price');
		} catch (No_Result_Exception $e) {
			throw new No_Such_Item_Exception($seller_id, $item_id);
		}

		$this->CI->db->trans_start();
		// Remove item from user
		$this->CI->inventory
			->setItemType($this->item_type)
			->setOwner($buyer_id)
			->removeItem($item_id);

		// Add currency to the user
		$value = $shop_item->price;
		$current_value = $this->_sellback_depreciation($value);
		$this->CI->accountant
			->setCurrencyType($this->currency_type)
			->setOwner($buyer_id)
			->deposit($current_value);
		$this->CI->db->trans_complete();
		return $this;
	}

	/**
	 * Calculates the depreciated value of selling an item back to a shop.
	 * @param int $value The original value
	 * @return int The depreciated value
	 */
	protected function _sellback_depreciation($value) {
		assert($value > 0);
		return ($value >> 1) / 5 * 5;
	}
}
