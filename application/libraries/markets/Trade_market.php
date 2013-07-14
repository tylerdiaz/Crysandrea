<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once 'IMarket.php';

class Illegal_Trade_State_Exception extends Exception {
	const EXCEPTION_FMT = 'Illegal state between %s and %s';

	public $sender_id;
	public $receiver_id;

	public function __construct($sender_id, $receiver_id) {
		parent::__construct(sprintf(static::EXCEPTION_FMT, $sender_id, $receiver_id));
		$this->sender_id = $sender_id;
		$this->receiver_id = $receiver_id;
	}
}

class Trade_Exists_Exception extends Illegal_Trade_State_Exception {
	public function __construct($sender_id, $receiver_id) {
		parent::__construct($sender_id, $receiver_id);
	}
}

class Trade_Complete_Exception extends Illegal_Trade_State_Exception {
	public function __construct($sender_id, $receiver_id) {
		parent::__construct($sender_id, $receiver_id);
	}
}

class No_Such_Trade_Exception extends Illegal_Trade_State_Exception {
	const EXCEPTION_FMT = 'No such trade between %s and %s';

	public function __construct($sender_id, $receiver_id) {
		parent::__construct($sender_id, $receiver_id);
	}
}

interface Trade_Market_State {
	public function __construct(&$instance);

	// Pending state
	public function addCurrency($owner_id=0, $amount=0);
	public function addItem($owner_id, $item_id=0, $amount=1);
	public function removeCurrency($owner_id=0, $amount=0);
	public function removeItem($owner_id=0, $item_id=0, $amount=1);

	// Accept state
	public function accept($accepter_id);
}

class Trade_Market_Pending_State implements Trade_Market_State {
	private $trade_market;

	public function __construct(&$instance) {
		$this->trade_market = &$instance;
	}

	// Pending State Actions
	public function addCurrency($owner_id=0, $amount=0) {
		if (!$this->trade_market->currency_type)
			show_error('Currency type cannot be empty');
		$param = $this->trade_market->prefix($owner_id) . $this->trade_market->currency_type;
		$this->trade_market->trade->{$param} += $amount;
		$this->trade_market->CI->trades->updateRelation($this->trade_market->trade);
	}

	public function addItem($owner_id, $item_id=0, $amount=1) {
		$this->trade_market->trade_items->addRelation($owner_id, $item_id, array(
			'trader_sender' => $this->trade_market->trade->trade_sender,
			'trade_receiver' => $this->trade_market->trade->trade_receiver
		));
		$this->trade_market->CI->trades->updateRelation($this->trade_market->trade);
	}

	public function removeCurrency($owner_id=0, $amount=0) {
		$param = $this->trade_market->prefix($owner_id) . $this->trade_market->currency_type;
		$this->trade_market->trade->{$param} -= $amount;
		$this->trade_market->CI->trades->updateRelation($this->trade_market->trade);
	}

	public function removeItem($owner_id=0, $item_id=0, $amount=1) {
		$this->trade_market->trade_items->removeRelation($owner_id, $item_id, array(
			'trader_sender' => $this->trade_market->trade->trade_sender,
			'trade_receiver' => $this->trade_market->trade->trade_receiver
		));
		$this->trade_market->CI->trades->updateRelation($this->trade_market->trade);
	}

	// Accepting State Actions
	public function accept($accepter_id) {
		$accepter_status = $this->trade_market->prefix($accepter_id).'status';
		$this->trade_market->trade->{$accepter_status} = 1;
		$this->trade_market->CI->trades->updateRelation($this->trade_market->trade);
		if ($this->trade_market->trade->sender_status && $this->trade_market->trade->receiver_status)
			$this->trade_market->commit($this->trade_market->trade->trade_sender, $this->trade_market->trade->trade_receiver);
	}

	public function commit() {
		show_error('Invalid state');
	}
}

class Trade_Market_Accepting_State implements Trade_Market_State {
	private $trade_market;

	public function __construct(&$instance) {
		$this->trade_market = &$instance;
	}

	// Pending State Actions
	public function addCurrency($owner_id=0, $amount=0) {
		if (!$this->trade_market->currency_type)
			show_error('Currency type cannot be empty');
		$param = $this->trade_market->prefix($owner_id) . $this->trade_market->currency_type;
		$this->trade_market->trade->{$param} += $amount;
		$this->trade_market->invalidate();
		$this->trade_market->CI->trades->updateRelation($this->trade_market->trade);
	}

	public function addItem($owner_id, $item_id=0, $amount=1) {
		$this->trade_market->trade_items->addRelation($owner_id, $item_id, array(
			'trader_sender' => $this->trade_market->trade->trade_sender,
			'trade_receiver' => $this->trade_market->trade->trade_receiver
		));
		$this->trade_market->invalidate();
		$this->trade_market->CI->trades->updateRelation($this->trade_market->trade);
	}

	public function removeCurrency($owner_id=0, $amount=0) {
		$param = $this->trade_market->prefix($owner_id) . $this->trade_market->currency_type;
		$this->trade_market->trade->{$param} -= $amount;
		$this->trade_market->invalidate();
		$this->trade_market->CI->trades->updateRelation($this->trade_market->trade);
	}

	public function removeItem($owner_id=0, $item_id=0, $amount=1) {
		$this->trade_market->trade_items->removeRelation($owner_id, $item_id, array(
			'trader_sender' => $this->trade_market->trade->trade_sender,
			'trade_receiver' => $this->trade_market->trade->trade_receiver
		));
		$this->trade_market->invalidate();
		$this->trade_market->CI->trades->updateRelation($this->trade_market->trade);
	}

	// Accepting State Actions
	public function accept($accepter_id) {
		$accepter_status = $this->trade_market->prefix($accepter_id).'status';
		$this->trade_market->trade->{$accepter_status} = 1;
		$this->trade_market->CI->trades->updateRelation($this->trade_market->trade);
		if ($this->trade_market->trade->sender_status && $this->trade_market->trade->receiver_status)
			$this->trade_market->commit($this->trade_market->trade->trade_sender, $this->trade_market->trade->trade_receiver);
	}

	public function commit() {
		$this->trade_market->CI->load->driver('Accountant');

		// $this->trade_market->CI->load->driver('Inventory', array(
		// 	'item_type' => $this->item_type
		// ));

		$this->trade_market->CI->db->trans_start();
		$this->trade_market->CI->accountant->setCurrencyType('palladium');
		if ($this->trade_market->trade->sender_palladium > 0):
			$this->trade_market->CI->accountant->setOwner($this->trade_market->trade->trade_sender)->withdraw($this->trade_market->trade->sender_palladium);
			$this->trade_market->CI->accountant->setOwner($this->trade_market->trade->trade_receiver)->deposit($this->trade_market->trade->sender_palladium);
		endif;
		if ($this->trade_market->trade->receiver_palladium > 0):
			$this->trade_market->CI->accountant->setOwner($this->trade_market->trade->trade_receiver)->withdraw($this->trade_market->trade->receiver_palladium);
			$this->trade_market->CI->accountant->setOwner($this->trade_market->trade->trade_sender)->deposit($this->trade_market->trade->receiver_palladium);
		endif;

		$this->trade_market->CI->accountant->setCurrencyType('berries');
		if ($this->trade_market->trade->sender_berries > 0):
			$this->trade_market->CI->accountant->setOwner($this->trade_market->trade->trade_sender)->withdraw($this->trade_market->trade->sender_berries);
			$this->trade_market->CI->accountant->setOwner($this->trade_market->trade->trade_receiver)->deposit($this->trade_market->trade->sender_berries);
		endif;
		if ($this->trade_market->trade->receiver_berries > 0):
			$this->trade_market->CI->accountant->setOwner($this->trade_market->trade->trade_receiver)->withdraw($this->trade_market->trade->receiver_berries);
			$this->trade_market->CI->accountant->setOwner($this->trade_market->trade->trade_sender)->deposit($this->trade_market->trade->receiver_berries);
		endif;

		// $sender_items = $this->trade_market->CI->trade_items->findFrom($this->trade_market->trade->trade_sender, 'item_id, item_type');
		// $receiver_items = $this->trade_market->CI->trade_items->findFrom($this->trade_market->trade->trade_receiver, 'item_id, item_type');

		// foreach ($sender_items as $sender_item):
		// 	$this->trade_market->CI->inventory->setOwner($this->trade_market->trade->trade_receiver)
		// 		->setItemType($sender_item->item_type)
		// 		->addItem($sender_item->item_id);
		// 	$this->trade_market->CI->inventory->setOwner($this->trade_sender)
		// 		->setItemType($sender_item->item_type)
		// 		->removeItem($sender_item->item_id);
		// endforeach;

		// foreach ($receiver_items as $receiver_item):
		// 	$this->trade_market->CI->inventory->setOwner($this->trade_market->trade->trade_sender)
		// 		->setItemType($receiver_item->item_type)
		// 		->addItem($receiver_item->item_id);
		// 	$this->trade_market->CI->inventory->setOwner($this->trade_receiver)
		// 		->setItemType($receiver_item->item_type)
		// 		->removeItem($receiver_item->item_id);
		// endforeach;

		$this->trade_market->CI->db->trans_complete();
	}
}

/**
 * The Trade Market implements the IMarket interface where users are able to
 * trade items and currency without bounds.
 *
 * @package CRYS\Libraries\Markets
 * @author Gio Carlo Cielo <gio@crysandrea.com>
 */
class Trade_Market {
	protected $state;

	public $CI;
	public $trade;
	public $currency_type;
	public $item_type;

	public function __construct($config=array()) {
		$adapter = 'palladium';
		// $config['currency_adapter']

		$this->CI = &get_instance();
		$this->CI->load->model('trades_model', 'trades');
		$this->CI->load->model('trade_items_model', 'trade_items');
	}

	public function listing($seller_id=0, $item_id=0) {
		if (!empty($seller_id) && !empty($item_id))
			return $this->CI->trades->findRelation($seller_id, $item_id);
		return $this->CI->trades->findFrom($seller_id);
	}

	public function setTrade($sender_id=0, $receiver_id=0, $constraints = array()) {
		try {
			$this->trade = $this->CI->trades->findRelation($sender_id, $receiver_id, '*', $constraints);
		} catch (Exception $e) {
			throw new No_Such_Trade_Exception($sender_id, $receiver_id);
		}

		if ($this->trade->sender_status && $this->trade->receiver_status):
			throw new Trade_Complete_Exception($sender_id, $receiver_id);
		elseif ($this->trade->sender_status || $this->trade->receiver_status):
			$this->state = new Trade_Market_Accepting_State($this);
		else:
			$this->state = new Trade_Market_Pending_State($this);
		endif;

		return $this;
	}

	public function create($sender_id, $receiver_id) {
		if (empty($sender_id) || empty($receiver_id))
			show_error('Invalid sender ID or receiver ID');
		try {
			$trade = $this->CI->trades->findRelation($sender_id, $receiver_id, 'trade_sender, trade_receiver', array('sender_status' => 0, 'receiver_status' => 0));
			throw new Trade_Exists_Exception($sender_id, $receiver_id);
		} catch (No_Result_Exception $e) {
			$trade = Trades_Model::createRelation($sender_id, $receiver_id);
			$this->CI->trades->saveRelation($trade);
		}
	}

	public function addCurrency($owner_id=0, $amount=0) {
		if (!$this->trade)
			show_error('Trade must first be set');
		$this->state->addCurrency($owner_id, $amount);
		return $this;
	}

	public function addItem($owner_id, $item_id=0, $amount=1) {
		if (!$this->trade)
			show_error('Trade must first be set');
		$this->state->addItem($owner_id, $item_id, $amount);
		return $this;
	}

	public function removeCurrency($owner_id=0, $amount=0) {
		if (!$this->trade)
			show_error('Trade must first be set');
		$this->state->removeCurrency($owner_id, $amount);
		return $this;
	}

	public function removeItem($owner_id=0, $item_id, $amount=1) {
		if (!$this->trade)
			show_error('Trade must first be set');
		$this->state->removeItem($owner_id, $item_id, $amount);
		return $this;
	}

	public function accept($owner_id) {
		if (!$this->trade)
			show_error('Trade must first be set');
		$this->state->accept($owner_id);
		return $this;
	}

	// Complete State Actions
	public function commit() {
		if (!$this->trade)
			show_error('Trade must first be set');
		if ($this->trade->sender_status || $this->trade->receiver_status)
			$this->state = new Trade_Market_Accepting_State($this);
		else
			$this->state = new Trade_Market_Pending_State($this);
		$this->state->commit();
	}

	public function prefix($owner_id) {
		if (!$this->trade)
			show_error('Trade must first be set');
		return ($this->trade->trade_sender == $owner_id) ? 'sender_' : 'receiver_';
	}

	public function cancel() {
		if (!$this->trade)
			show_error('Trade must first be set');
		$this->CI->trades->removeRelation($this->trade->trade_sender, $this->trade->trade_receiver);
	}

	public function invalidate() {
		if (!$this->trade)
			show_error('Trade must first be set');
		$this->trade->sender_status = 0;
		$this->trade->receiver_status = 0;
	}

	public function setCurrencyType($currency_type) {
		$this->currency_type = $currency_type;
		return $this;
	}

	public function setItemType($item_type) {
		$this->item_type = $item_type;
		return $this;
	}
}

