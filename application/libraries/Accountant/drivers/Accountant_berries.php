<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accountant_berries extends CI_Driver {
	private $CI;

	private $owner_id;

	public function __construct() {
		$this->CI = &get_instance();
		$this->CI->load->model('forest_users');
	}

	public function setOwner($owner_id) {
		$this->owner_id = $owner_id;
	}

	public function balance() {
		$owner = $this->forest_users->find($this->owner_id, 'user_id, berries');
		return $owner->berries;
	}

	public function withdraw($amount) {
		$owner = $this->CI->forest_users->find($this->owner_id, 'user_id, berries');
		if ($owner->berries < $amount):
			$this->CI->db->trans_rollback();
			throw new Insufficient_Currency_Exception($this->owner_id, $amount);
		endif;
		$owner->berries -= $amount;
		$this->CI->forest_users->update($owner);
	}

	public function deposit($amount) {
		$owner = $this->CI->forest_users->find($this->owner_id, 'user_id, berries');
		$owner->berries += $amount;
		$this->CI->forest_users->update($owner);
	}
}

