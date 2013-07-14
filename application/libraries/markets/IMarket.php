<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Interface for implementing market-based logic such as establishing item
 * listings and purchasing items.
 * 
 * @package CRYS\Libraries\Markets
 * @author Gio Carlo Cielo <gio@crysandrea.com>
 */
interface IMarket {
	/**
	 * Retrieves the market listing
	 *
	 * @param int $seller_id ID of seller to aggregate listings from
	 * @param int $item_id ID of an item to purchase
	 * @return array|object The item for sale if the seller and item are
	 *		defined; else, an array of items
	 */
	public function listing($seller_id=0, $item_id=0);

	/**
	 * Appends an item to the market listing
	 *
	 * @param int $seller_id ID of the seller
	 * @param int $item_id ID of the item to sell
	 * @param array $attributes Extra attributes for this listing
	 * @return bool TRUE if the item has been successfully entered into the market
	 *		listing
	 * @throws No_Such_Item_Exception if the seller does not own the item
	 */
	public function sell($seller_id=0, $item_id=0, $attributes=array());

	/**
	 * Purchases an item from the market listing
	 *
	 * @param int $seller_id ID of the seller
	 * @param int $buyer_id ID of the buyer
	 * @param int $item_id ID of the item to purchase
	 * @param array $attributes Extra attributes for this listing
	 * @return bool TRUE if the item has been succesfully purchased
	 * @throws No_Such_Item_Exception if the seller does not own the item
	 * @throws Insufficient_Currency_Exception if the buyer has insufficient
	 *		currency to purchase the item
	 */
	public function purchase($seller_id=0, $buyer_id=0, $item_id=0, $attributes=array());
}

