<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Crysandrea Auth_engine Model
 *
 * @package		Crysandrea
 * @subpackage	Models
 * @categories	Database
 * @author(s)	Tyler Diaz / Alex Bor
 * @link
 */

class Auth_engine extends CI_Model
{

	function Auth_engine()
	{
		parent::__construct();
	}


	// --------------------------------------------------------------------

	/**
	 * Auth engine -> Log signin
	 *
	 * This function logs a signin attempt
	 *
	 * @access	private
	 * @param	string	username trying to be signed into
	 * @return	n/a
	 */
	function log_signin($username = "")
	{
		$data = array(
			'log_user' => $username,
			'log_ip' => $this->input->ip_address(),
			'log_time' => time()
		);

		$this->db->insert('login_logs', $data);
	}


	// --------------------------------------------------------------------

	/**
	 * Auth engine -> Log successful signin
	 *
	 * This function logs a successful signin
	 *
	 * @access	private
	 * @return	string
	 */
	function log_successful_signin()
	{
		$data = array(
			'username' => $this->session->userdata('username'),
			'userID' => $this->session->userdata('user_id'),
			'userIP' => $this->input->ip_address()
		);

		$this->db->insert('successful_login_logs', $data);
	}


	// --------------------------------------------------------------------

	/**
	 * Auth engine -> Get signin attempts
	 *
	 * This function get how many times the persons IP address has tried to signin
	 *
	 * @access	private
	 * @return	int
	 */
	function get_signin_attempts()
	{
		$total_attempts = $this->db->select('log_id')
								   ->from('login_logs')
								   ->where('log_ip', $this->input->ip_address())
								   ->where('log_time >', time()-300)
								   ->count_all_results();

		return $total_attempts;
	}


	// --------------------------------------------------------------------

	/**
	 * Auth engine -> Log signin
	 *
	 * This function logs a signin attempt
	 *
	 * @access	private
	 * @param	array	fields that are going to connect with the user query
	 * @return	n/a
	 */
	function get_user($where = array())
	{
		$this->db->select('username, user_id, user_email')->from('users');

		foreach($where as $key => $value)
		{
			$this->db->where($key, $value);
		}

		$user = $this->db->limit(1)->get();

		if($user->num_rows() > 0)
		{
			return $user->result_array();
		}
		else
		{
			return FALSE;
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Auth engine -> New password recover key
	 *
	 * This function creates a new key for the password recovery
	 *
	 * @access	private
	 * @param	string	Random key
	 * @param	int		ID of the user who's trying to restore their password
	 * @return	string 	Random key
	 */
	function new_password_recover_key($string = '', $user_id = '')
	{
		$data = array(
			'key' => $string,
			'user_id' => $user_id
		);

		$this->db->insert('password_recovery_keys', $data);

		return $string;
	}


	// --------------------------------------------------------------------

	/**
	 * Auth engine -> Get password key
	 *
	 * This function gets a key for the password recovery
	 *
	 * @access	private
	 * @param	string	Random key
	 * @return	array 	Key data retrieved from the database
	 */
	function get_password_key($key = '')
	{
		$key_data = $this->db->select('id, key, user_id, created_at')
							 ->from('password_recovery_keys')
							 ->where('key', $key)
							 ->get()
							 ->result_array();

		return $key_data;
	}


	// --------------------------------------------------------------------

	/**
	 * Auth engine -> Set a new password
	 *
	 * This function changes a user password to a specified one
	 *
	 * @access	private
	 * @param	int		User id
	 * @param	string	New password
	 * @return	n/a
	 */
	function set_new_password($user_id = 0, $new_password = '')
	{
		$this->db->update('users', array('user_pass' => $new_password), array('user_id' => $user_id));
	}



	// --------------------------------------------------------------------

	/**
	 * Auth engine -> Destroy key
	 *
	 * These keys are disposable. Once used, chuck 'em out.
	 *
	 * @access	private
	 * @param	string	Random key
	 * @return	n/a
	 */
	function destroy_key($key = '')
	{
		$this->db->limit(1)->delete('password_recovery_keys', array('key' => $key));
	}

}


/* End of file auth_engine.php */
/* Location: ./system/application/models/auth_engine.php */