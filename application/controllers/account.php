<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller
{
	var $privacy_settings = array(
		'inbox_messages' => array(
			'value' => 2,
			'description' => 'Send messages to my inbox',
		),
		'annonymous_gits' => array(
			'value' => 2,
			'description' => 'Receive anonymous gifts',
		)
	);
	var $timezones = array(
		"-12.0" => '(GMT -12:00) Eniwetok, Kwajalein',
		"-11.0" => '(GMT -11:00) Midway Island, Samoa',
		"-10.0" => '(GMT -10:00) Hawaii',
		"-9.0"  => '(GMT -9:00) Alaska',
		"-8.0"  => '(GMT -8:00) Pacific Time (US &amp; Canada)',
		"-7.0"  => '(GMT -7:00) Mountain Time (US &amp; Canada)',
		"-6.0"  => '(GMT -6:00) Central Time (US &amp; Canada)',
		"-5.0"  => '(GMT -5:00) Eastern Time (US &amp; Canada)',
		"-4.0"  => '(GMT -4:00) Atlantic Time (Canada)',
		"-3.5"  => '(GMT -3:30) Newfoundland',
		"-3.0"  => '(GMT -3:00) Brazil, Buenos Aires, Georgetown',
		"-2.0"  => '(GMT -2:00) Mid-Atlantic',
		"-1.0"  => '(GMT -1:00 hour) Azores, Cape Verde Islands',
		"0.0"   => '(GMT) Western Europe Time, London, Casablanca',
		"1.0"   => '(GMT +1:00 hour) Copenhagen, Paris',
		"2.0"   => '(GMT +2:00) South Africa',
		"3.0"   => '(GMT +3:00) Baghdad, Moscow',
		"3.5"   => '(GMT +3:30) Tehran',
		"4.0"   => '(GMT +4:00) Abu Dhabi',
		"4.5"   => '(GMT +4:30) Kabul',
		"5.0"   => '(GMT +5:00) Ekaterinburg, Tashkent',
		"5.5"   => '(GMT +5:30) Bombay, New Delhi',
		"5.75"  => '(GMT +5:45) Kathmandu',
		"6.0"   => '(GMT +6:00) Almaty, Dhaka, Colombo',
		"7.0"   => '(GMT +7:00) Bangkok, Hanoi, Jakarta',
		"8.0"   => '(GMT +8:00) Beijing, Singapore, Hong Kong',
		"9.0"   => '(GMT +9:00) Tokyo',
		"9.5"   => '(GMT +9:30) Adelaide, Darwin',
		"10.0"  => '(GMT +10:00) Eastern Australia, Guam',
		"11.0"  => '(GMT +11:00) Magadan',
		"12.0"  => '(GMT +12:00) Wellington, Fiji'
	);

	var $route_navigation = array(
		'index' => array(
			'icon'        => '/global/css/images/icons/equalizer.png',
			'title'       => 'Your settings',
			'description' => 'Here\'s where you can edit most of your main account data',
			'enabled'     => TRUE
		),
		'profile' => array(
			'icon'        => '/global/css/images/icons/equalizer.png',
			'title'       => 'Your profile info',
			'description' => 'Here you can edit your profile style and information.',
			'enabled'     => TRUE
		),
		'signature' => array(
			'icon'        => '/global/css/images/icons/equalizer.png',
			'title'       => 'Forum signature',
			'description' => 'Your signature gives you room for something you\'d like to share.',
			'enabled'     => TRUE
		),
		'privacy' => array(
			'icon'        => '/global/css/images/icons/equalizer.png',
			'title'       => 'Privacy settings',
			'description' => 'Modify who can interact with you post on the site.',
			'enabled'     => FALSE
		),
	);

	function __construct()
	{
		parent::__construct();
		$this->load->library(array('authentication', 'form_validation'));
		$this->load->model('account_engine');
		$this->load->helper('string');

		$this->form_validation->set_error_delimiters('<li>', '</li>');

		if( ! $this->session->userdata('user_id')) redirect('auth/signin');
	}

	// --------------------------------------------------------------------

	/**
	 * Setting index
	 *
	 * A place where you can edit most of your main account data
	 *
	 * @access  public
	 * @param   none
	 */

	public function index()
	{
		$success_notices = array();
		$this->load->model('user_engine');
		$this->system->view_data['scripts'][] = '/global/js/account/index.js';

		if($_SERVER['REQUEST_METHOD'] == "POST"):
			$changed_username = ($this->input->post('username') && $this->input->post('username') != $this->system->userdata['username']);
			$changed_email = ($this->input->post('email') && $this->input->post('email') != $this->system->userdata['user_email']);
			$changed_timezone = ($this->input->post('timezone') && ($this->input->post('timezone')*3600) != $this->system->userdata['timezone']);
			$new_password = (strlen($this->input->post('new_password')) > 0);

			$this->form_validation->set_message('is_unique', 'That %s has already been taken.');

			if($changed_username) $this->form_validation->set_rules('username', 'username', 'callback_username_verification|required|is_unique[users.username]|max_length[20]');
			if($changed_email) $this->form_validation->set_rules('email', 'email', 'valid_email|required|is_unique[users.user_email]');
			if($changed_timezone) $this->form_validation->set_rules('timezone', 'timezone', 'numeric|required');
			if($changed_email || $new_password || $changed_username) $this->form_validation->set_rules('password', 'current password', 'required|min_length[6]|callback_password_confirmation');
			if($new_password):
				$this->form_validation->set_rules('new_password', 'New password', 'required|min_length[6]');
				$this->form_validation->set_rules('confirm_new_password', 'New password confirmation', 'required|matches[new_password]');
			endif;
		endif;

		if ($this->form_validation->run() === TRUE):
			if($changed_username && $this->system->userdata['user_palladium'] >= 1000):
				$success_notices[] = 'Your username has been successfully changed to "'.$this->input->post('username').'" for 1,000 palladium.';
				$this->user_engine->remove('user_palladium', 1000);
				$this->user_engine->set('username', $this->input->post('username'));
			endif;

			if($changed_email):
				$success_notices[] = 'Your new email has been saved.';
				$this->user_engine->set('user_email', $this->input->post('email'));
			endif;

			if($changed_timezone):
				$success_notices[] = 'Your new timezone has been saved.';
				$this->user_engine->set('timezone', $this->input->post('timezone')*3600);
			endif;

			if($new_password):
				$success_notices[] = 'Your new password has been saved.';
				$this->user_engine->set('user_pass', $this->authentication->hash_password($this->input->post('new_password')));
			endif;
		endif;

		$this->system->quick_parse('account/index', array(
			'page_title' => 'My account settings',
			'page_body'  => 'account sub-settings',
			'user_tz'    => ($this->system->userdata['timezone']/3600),
			'timezones'  => $this->timezones,
			'routes'     => $this->route_navigation,
			'active_url' => $this->uri->rsegment(2, 0),
			'success' 	 => $success_notices,
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Edit Profile
	 *
	 * Allow users to edit their profile information
	 *
	 * @access  public
	 * @param   none
	 */

	public function profile()
	{
		$this->system->view_data['scripts'][] = '/global/js/account/profile.js';

		$success_notices = array();
		$existing_data = FALSE;

		$this->form_validation->set_rules('likes', 'Liked', 'htmlentities|xss_clean|addslashes');
		$this->form_validation->set_rules('dislikes', 'Dislikes', 'htmlentities|xss_clean|addslashes');
		$this->form_validation->set_rules('hobbies', 'Hobbies', 'htmlentities|xss_clean|addslashes');
		$this->form_validation->set_rules('profile_bio', 'Biography', 'htmlentities|xss_clean');
		$this->form_validation->set_rules('profile_css', 'Profile CSS', 'xss_clean|addslashes');

		$preference_values = array('likes','dislikes','profile_bio','profile_css','hobbies');
		$user_preference_query = $this->db->get_where('user_preferences', array('user_id' => $this->system->userdata['user_id']));

		if($user_preference_query->num_rows() > 0):
			$user_preference_data = $user_preference_query->row_array();
			$existing_data = TRUE;
		else:
			$user_preference_data = array_fill_keys($preference_values, '');
		endif;

		if ($this->form_validation->run() === TRUE):
			$preference_data = array();
			foreach ($preference_values as $value):
				if($this->input->post($value) && $this->input->post($value) !== $user_preference_data[$value]):
					$success_notices[] = "Your ".preg_replace('/_/', ' ', $value)." ".($value == 'profile_bio' || $value == 'profile_css' ? 'has' : 'have')." been updated.";
					$preference_data[$value] = $this->input->post($value);
				endif;
			endforeach;

			if ($existing_data == FALSE):
				$preference_data['user_id'] = $this->system->userdata['user_id'];
				$this->db->insert('user_preferences', $preference_data);
			else:
				if(count($preference_data) > 0):
					$this->db->where(array('user_id' => $this->system->userdata['user_id']))->update('user_preferences', $preference_data);
				endif;
			endif;

			$user_preference_data = array_merge($user_preference_data, $preference_data);
		endif;

		$this->system->userdata = array_merge($user_preference_data, $this->system->userdata);

		$this->system->quick_parse('account/profile', array(
			'page_title' => 'My profile information',
			'page_body'  => 'account sub-profile',
			'routes'     => $this->route_navigation,
			'active_url' => $this->uri->rsegment(2, 0),
			'success' 	 => $success_notices,
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Edit Signature
	 *
	 * Allow users to edit their forum signature
	 *
	 * @access  public
	 * @param   none
	 */

	function signature()
	{
		$this->system->view_data['scripts'][] = '/global/js/account/signature.js';

		$success_notices = array();

		$this->form_validation->set_rules('user_signature', 'Signature', 'htmlentities|xss_clean|addslashes');

		if ($this->form_validation->run() === TRUE):
			$success_notices[] = "Your signature has been updated!";
			$this->system->userdata['user_signature'] = ($this->input->post('user_signature'));
			$this->db->where('user_id', $this->session->userdata('user_id'))->update('users', array('user_signature' => $this->input->post('user_signature')));
		endif;

		$data = array(
			'page_title'	=> 'My forum signature',
			'page_body'	=> 'account sub-signature',
			'routes'     => $this->route_navigation,
			'active_url' => $this->uri->rsegment(2, 0),
			'success' 	 => $success_notices,
		);

		$this->system->quick_parse('account/signature', $data);
	}

	// --------------------------------------------------------------------

	public function username_verification($username = '')
	{
	    $this->load->library('authentication');

	    if($this->authentication->username_check($username)):
			return TRUE;
    	else:
    		$this->form_validation->set_message('username_verification', 'That username was not allowed, make sure to only use letters, numbers and/or spaces in your username! (Right: Username - Wrong: u$3r_n@m5)');
    		return FALSE;
    	endif;
	}

	public function password_confirmation($password = '')
	{
		if($this->authentication->verify_password($password, $this->system->userdata['user_id'])):
			return TRUE;
		else:
			$this->form_validation->set_message('password_confirmation', 'Your password was incorrect! Did you happen to forget it while signed in? If so, you can still <a href="/auth/forgot_password">recover your password</a> while you\'re signed in.');
			return FALSE;
		endif;
	}


}

/* End of file account.php */
/* Location: ./system/application/controllers/account.php */