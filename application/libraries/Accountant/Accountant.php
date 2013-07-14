<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Thrown when an owner has insufficienct currency for a purchase.
 *
 * @package CRYS\Libraries\Markets
 * @author Gio Carlo Cielo <gio@crysandrea.com> @package
 */
class Insufficient_Currency_Exception extends Exception {
	const EXCEPTION_FMT = '%d has insufficient funds. You need at least %d.';

	public $owner_id;
	public $amount;

	public function __construct($owner_id, $amount) {
		parent::__construct(sprintf(self::EXCEPTION_FMT, $owner_id, $amount));
		$this->owner_id = $owner_id;
		$this->amount = $amount;
	}
}

/**
 * Accountant manages every type of currency for a specified owner. Valid
 * drivers include 'palladium' and 'berries'.
 *
 * @package CRYS\Libraries\Accountant
 * @author Gio Carlo Cielo <gio@crysandrea.com>
 */
class Accountant extends CI_Driver_Library {
	protected $valid_drivers = array(
		'accountant_palladium',
		'accountant_berries'
	);

	protected $adapter = 'palladium';
	protected $owner_id;

	public function __construct($config=array()) {
		if (empty($config))
			return;
		if (empty($config['currency_type']))
			show_error('No currency type for Accountant');
		$this->adapter = $config['currency_type'];
	}
	
	public function setCurrencyType($adapter) {
		assert(!empty($adapter));
		$this->adapter = $adapter;
		return $this;
	}

	public function setOwner($owner_id) {
		assert(!empty($owner_id));
		$this->{$this->adapter}->setOwner($owner_id);
		return $this;
	}

	/**
	 * Retrieves the current balance on the specified owner's account.
	 *
	 * @param int $owner_id THe id of the owner's account
	 * @return The current balance on the specified owner's account
	 */
	public function balance() {
		return $this->{$this->adapter}->balance();
	}

	/**
	 * Withdraws a specified amount from a specified owner's account.
	 *
	 * @param int $owner_id The ID of the owner's account
	 * @param int $amount The amount to withdraw
	 * @return The new balance on the account
	 */
	public function withdraw($amount) {
		assert($amount > 0);
		$this->{$this->adapter}->withdraw($amount);
		return $this;
	}

	/**
	 * Deposites a specified amount into the specified owner's account.
	 *
	 * @param int $owner_id, The ID of the owner's account
	 * @param int $amount The amount to deposit
	 * @return The new balance on the account
	 */
	public function deposit($amount) {
		assert($amount > 0);
		$this->{$this->adapter}->deposit($amount);
		return $this;
	}
}

