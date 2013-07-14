<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accountant_palladium extends CI_Driver {
	private $CI;

	private $owner_id;

	public function __construct() {
		$this->CI = &get_instance();
		$this->CI->load->model('users');
	}

	public function setOwner($owner_id) {
		$this->owner_id = $owner_id;
	}

	public function balance() {
		$owner = $this->CI->users->find($this->owner_id, 'user_id, user_palladium');
		return $owner->palladium;
	}

	public function withdraw($amount) {
		$owner = $this->CI->users->find($this->owner_id, 'user_id, user_palladium');
		if ($owner->user_palladium < $amount):
			$this->CI->db->trans_rollback();
			throw new Insufficient_Currency_Exception($this->owner_id, $amount);
		endif;
		$owner->user_palladium -= $amount;
		$this->CI->users->update($owner);
	}

	public function deposit($amount) {
		$owner = $this->CI->users->find($this->owner_id, 'user_id, user_palladium');
		$owner->user_palladium += $amount;
		$this->CI->users->update($owner);
	}
}
