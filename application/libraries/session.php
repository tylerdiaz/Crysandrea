<?php
/**
 * Crysandrea's new Session class
 *
 * @author
 * @version 1.0
 * @package Session
 **/

/**
 * Session Class
 * Store session data in database, create, set, remove, destroy
 * @package Session
 **/
class CI_Session
{
	public $userdata = FALSE;
	private $session_key = FALSE;
	private $session_temp = FALSE;
	private $cookie_name = 'session';
	private $cookie_ttl  = 87200;
	private $table_name  = 'sessions';
	private $cache_userdata = FALSE;
	private $cache_userdata_ttl = 30; // in seconds - only works if $cache_userdata is TRUE
	private $sess_time_to_update = 1200;
	private $check_by_ip = FALSE;
	private $flashdata = FALSE;
	private $key_ttl = 87200;
	private $CI;

	function __construct() {
		$this->CI =& get_instance();

		if(get_cookie($this->cookie_name)) $this->session_key = get_cookie($this->cookie_name);

		if( ! $this->session_key) $this->session_key = NULL;

		if($this->session_key && (time() % 10) >= 7):
			$this->_session_update($this->session_key);
		endif;
	}

	private function remove_key() {
		$args  = func_get_args();
		return array_diff_key($args[0],array_flip(array_slice($args,1)));
	}

	public function create() {
		if(get_cookie($this->cookie_name)):

			// let's look and find to make sure there is no table, if not, let's delete the cookie.
			$row_validation = $this->CI->db->get_where($this->table_name, array('key' => $this->session_key))->num_rows();

			if($row_validation < 1):
				$this->destroy();
				return $this->create();
			endif;

			return FALSE;
		else:
			$key = sha1(uniqid(sha1(uniqid(null, TRUE)), TRUE));

			$this->session_key = $key;
			delete_cookie($this->cookie_name);
			$this->CI->input->set_cookie($this->cookie_name, $key, $this->cookie_ttl);

			$this->CI->db->insert($this->table_name, array(
				'data' => json_encode(array('_ttl' => (time()+$this->key_ttl))),
				'key' => $key,
				'browser' => $_SERVER['HTTP_USER_AGENT'],
				'ip' => ip2long($this->CI->input->ip_address()),
				'last_activity' => time()
			));

			return TRUE;
		endif;
	}

	private function read() {
		if($this->userdata !== FALSE) return $this->userdata;

		$cache_label = 'sess:'.$this->session_key;

		if($this->cache_userdata && ($this->userdata = $this->cache->get($cache_label)) !== FALSE):
			$this->userdata = json_decode($this->userdata, TRUE);
			return $this->userdata;
		endif;

		$result = $this->CI->db->get_where($this->table_name, array(
			'key' => $this->session_key,
			'browser' => $_SERVER['HTTP_USER_AGENT']
		))->row_array();

		if(count($result) == 0):
			return FALSE;
		else:
			if($this->cache_userdata):
				$this->cache->save($cache_label, $result['data'], $this->cache_userdata_ttl);
			endif;

			$this->userdata = json_decode($result['data'], TRUE);
			return $this->userdata;
		endif;
	}

	private function save($session_data = array()) {
		$this->userdata = $session_data;
		if($this->cache_userdata) $this->cache->save('sess:'.$this->session_key, json_encode($session_data), $this->cache_userdata_ttl);
		return $this->CI->db->update($this->table_name, array('data' => json_encode($session_data)), array('key' => $this->session_key));
	}

	public function all_userdata() {
		return $this->read();
	}

	public function active() {
		if(isset($_COOKIE[$this->cookie_name]) || $this->session_key):
			return TRUE;
		else:
			return FALSE;
		endif;
	}

	public function destroy() {
		$this->CI->db->where('key', $this->session_key)->delete($this->table_name);
		$this->session_key = FALSE;
		$this->userdata = FALSE;
		if($this->cache_userdata) apc_delete('sess:'.$this->session_key);
		$_COOKIE[$this->cookie_name] = NULL;

		delete_cookie($this->cookie_name);

		// setcookie($this->cookie_name, '', time()-10);
	}

	public function set($key = '', $value = '', $overwrite = TRUE) {
		$session = $this->read();

		if(is_array($key)):
			foreach ($key as $new_key => $new_value):
				if($overwrite === FALSE && isset($session[$new_key])) continue;
				$session[$new_key] = $new_value;
			endforeach;
		else:
			if($overwrite === FALSE && isset($session[$new_key])) return FALSE;
			$session[$key] = $value;
		endif;

		$this->save($session);
		return TRUE;
	}

	public function set_userdata($key = '', $value = '', $overwrite = TRUE) {
		return $this->set($key, $value, $overwrite);
	}

	public function get($key) {
		$session = $this->read();

		if(isset($session[$key])):
			return $session[$key];
		else:
			return FALSE;
		endif;
	}

	public function userdata($key) {
		return $this->get($key);
	}

	public function drop($key) {
		$session = $this->read();
		$dropped = FALSE;

		if(is_array($key)):
			foreach ($key as $data):
				if(isset($session[$key])):
					$session_data = $this->remove_key($session, $data);
					$dropped = TRUE;
				endif;
			endforeach;
		else:
			if(isset($session[$key])):
				$session_data = $this->remove_key($session, $key);
				$dropped = TRUE;
			endif;
		endif;

		if($dropped):
			$this->save($session_data);
			return TRUE;
		else:
			return FALSE;
		endif;
	}

	public function unset_userdata($key) {
		return $this->drop($key);
	}

	public function _session_update($key = ''){
		$time_to_live = ($this->get('_ttl') ? $this->get('_ttl') : NULL);
		$user_id = ($this->get('user_id') ? $this->get('user_id') : NULL);

		$update_data = array('last_activity' => time(), 'user_id' => $user_id);
		// $this->CI->db->where(array('user_id' => $user_id, 'key !=' => $key))->delete($this->table_name);

		// if(time() > $time_to_live):
		// 	$new_key = sha1(uniqid(sha1(uniqid(null, TRUE)), TRUE));

		// 	if($this->cache_userdata) apc_delete('sess:'.$this->session_key);

		// 	$this->set('_ttl', (time()+$this->key_ttl));
		// 	$update_data['key'] = $new_key;
		// 	$this->session_key = $new_key;
		// 	$this->CI->input->set_cookie($this->cookie_name, $new_key, $this->cookie_ttl);
		// endif;

		$this->CI->db->update($this->table_name, $update_data, array('key' => $key));
	}
}

