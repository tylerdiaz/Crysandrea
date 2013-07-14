<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Crysandria Engine Model (C.E.M)
 *
 * @package		Crysandria
 * @subpackage	Models
 * @categories		Database
 * @author		Tyler Diaz
 * @link			http://www.crysandria.com
 */

class Account_engine extends CI_Model {

	function Account_engine(){
		parent::__construct();
	}
	
	function email_check($_email, $_user_id){
	
		$query = $this->db->query('SELECT user_email FROM users WHERE user_id = '.$_user_id)->row();
		
		if($_email == $query->user_email){
			return TRUE;
		} else {
			return FALSE;
		}
	
	}
	
	function get_profile_data($user_id){
		return $this->db->query('SELECT users.username,
										users.user_email
									FROM users 
									WHERE users.user_id = '.$user_id.'
									LIMIT 1')->result_array(); 
	}

	function get_preferences($user_id){
		return $this->db->query('SELECT user_preferences.hobbies ,
											user_preferences.likes,
											user_preferences.dislikes,
											user_preferences.profile_bio
									FROM user_preferences 
									WHERE user_preferences.user_id = '.$user_id.'
									LIMIT 1'); 
	}
	
	function get_signature($user_id){
		return $this->db->query('SELECT users.user_signature
									FROM users 
									WHERE users.user_id = '.$user_id.'
									LIMIT 1')->result_array(); 
	}

	function get_css($user_id){
		return $this->db->query('SELECT user_preferences.profile_css
									FROM user_preferences 
									WHERE user_preferences.user_id = '.$user_id.'
									LIMIT 1'); 
	}
	
}


?>