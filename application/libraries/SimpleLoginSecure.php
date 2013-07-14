<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('extentions/passwordhash.php');

define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', false);

class SimpleLoginSecure
{
	var $CI;
	var $user_table = 'users';

	function __construct()

	{

		$this->CI =& get_instance();

	}


	/**
	 * Create a user account
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function create($username = '', $user_email = '', $user_pass = '', $auto_login = true)
	{
		// Make sure account info was sent
		if($username == '' OR $user_pass == '' OR $user_email == '') {
			return false;
		}

		//Check against user table
		$this->CI->db->where('username', $username);
		$query2 = $this->CI->db->get_where($this->user_table);

		if ($query2->num_rows() > 0) //Username already exists
			return false;

		$this->CI->db->where('user_email', $user_email);
		$query3 = $this->CI->db->get_where($this->user_table);

		if ($query3->num_rows() > 0) //user_email already exists
			return false;

		//Hash user_pass using phpass
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		$user_pass_hashed = $hasher->HashPassword($user_pass);

		//Insert account into the database
		$data = array(
					'username' => $username,
					'user_email' => $user_email,
					'user_pass' => $user_pass_hashed,
					'last_action' => date('c'),
				);

		$this->CI->db->set($data);

		if(!$this->CI->db->insert($this->user_table)) //There was a problem!
			return false;


		if($auto_login)
			$this->login($username, $user_pass);

		return true;
	}

	/**
	 * Login and sets session variables
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function login($username = '', $user_pass = '', $skip_password = false) {

		if($username == '' OR $user_pass == '')
			return false;


		//Check if already logged in
		if($this->CI->session->userdata('username') == $username)
			return true;


		//Check against user table
		$this->CI->db->where('username', $username);
		$query = $this->CI->db->get_where($this->user_table);


		if ($query->num_rows() > 0) {
			$user_data = $query->row_array();

			$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);

			if(!$hasher->CheckPassword($user_pass, $user_data['user_pass']) && $skip_password != TRUE) return false;

			//Destroy old session
			$this->CI->session->sess_destroy();

			//Create a fresh, brand new session
			$this->CI->session->sess_create();


			$this->CI->db->query('UPDATE ' . $this->user_table  . '
								SET user_last_login = NOW()
								WHERE user_id = ' . $user_data['user_id']);


			//Set session data
			//unset($user_data);
			$session_data['username'] = $user_data['username']; // for compatibility with Simplelogin
			$session_data['user_id'] = $user_data['user_id']; // for compatibility with Simplelogin
			$session_data['user_level'] = $user_data['user_level']; // for compatibility with Simplelogin
			$session_data['logged_in'] = true;

			$this->CI->session->set_userdata($session_data);
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Logout user
	 *
	 * @access	public
	 * @return	void
	 */
	function logout($username = '', $user_pass = '') {
		$this->CI =& get_instance();

		$this->remove_auto_login($this->CI->session->userdata('user_id'));
		$this->CI->session->sess_destroy();
	}

	function verify_password($_user_pass, $_user_id){
		$this->CI->db->where('user_id', $_user_id);
		$query = $this->CI->db->get_where($this->user_table)->row_array();

		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);

		if(!$hasher->CheckPassword($_user_pass, $query['user_pass']))
			return FALSE;
		else
			return TRUE;
	}

	function password_hash($_password){

		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		return $hasher->HashPassword($_password);

	}




	function generate_auto_login_token_value()
	{
		$this->CI->load->helper('string');
		$chars = rand(32, 45);
		return random_string('alnum', $chars);
	}


	function create_new_auto_login($user_id = 0)
	{

		$new_token = $this->generate_auto_login_token_value();

		$this->CI->db->where('user_id', $user_id)->update($this->user_table, array('last_action' => time(), 'auto_login_token' => $new_token));

		set_cookie(array(
			'name'   => 'auto_login',
			'value'  => $user_id.'...'.$new_token,
			'expire' => (time()+(60*60*24*7*30*4)) // One month in seconds
		));

	}


	function auto_login($user_id = 0, $token = '')
	{

	    if(strlen($token) < 24 || ctype_alnum($token) == FALSE) return FALSE;
	    $user_id = (int) $user_id;

	    $user = $this->CI->db->get_where($this->user_table, array('auto_login_token' => $token, 'user_id' => $user_id));


	    if($user->num_rows() > 0){
	        $user_data = $user->row_array();
	        if($this->login($user_data['username'], $user_data['user_pass'], true)):
	        	$this->create_new_auto_login($user_data['user_id']);
	        	return true;
	        else:
	        	return false;
	        endif;
		}else{
			$this->remove_auto_login($user_id);
			return false;
		}

	}

	function remove_auto_login($user_id)
	{
		$this->CI->db->where('user_id', $user_id)->update($this->user_table, array('auto_login_token' => 'null'));
	    delete_cookie("auto_login");
	}
}
?>