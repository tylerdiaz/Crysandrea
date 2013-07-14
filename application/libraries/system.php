<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * System Library - Used for general purpose functions
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 * @copyright Origamee - May 18, 2011
 * @last_update: May 18, 2011 by Tyler Diaz
 *
 * NOTE: I know this is for ManaHaven, I'm enjoy seeing code traces from projects I never released
 **/

class System
{
	private $CI;
	private $production = FALSE;
	var $userdata = array();
	var $view_data = array(
		'styles' => array('/global/css/main.css?9902831'),
		'scripts' => array(
			'/global/js/jquery.min.js',
			'/global/js/main.js'
		),
	);

	function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->load->driver('cache', array('adapter' => 'apc'));

		if ($this->CI->session->userdata('user_id')):
			$this->userdata = $this->CI->db->get_where('users', array('users.user_id' => $this->CI->session->userdata('user_id')))->row_array();

			if ( ! $this->userdata['online_friends'] = $this->CI->cache->get('online_friends:'.$this->userdata['user_id'])):
				$this->userdata['online_friends'] = $this->get_friends();
				$this->CI->cache->save('online_friends:'.$this->userdata['user_id'], $this->userdata['online_friends'], 30);
			endif;
		else:
			// Can we auto-authenticate them?
			$this->userdata['user_id'] = 0;
			$this->userdata['banned'] = 0;
			$this->userdata['username'] = 'Anonymous';
			$this->userdata['online_friends'] = array();
		endif;
	}

	// --------------------------------------------------------------------

	/**
	 * Quick Parse
	 *
	 * A robust way to call the header & footer along with the template.
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	output
	 */

	public function quick_parse($template, $data = array())
	{
		$this->CI->load->library('parser');
		if($this->CI->input->is_ajax_request()):
			$ajax = true;
		else:
			$ajax = false;
		endif;

		$data = array_merge($this->view_data, $data);
		$data['user'] = $this->userdata;
		$data['item_collected'] = 0;

		$data['total_queries'] = count($this->CI->db->queries);
		if ( ! $daily_signins = $this->CI->cache->get('daily_signins')):
			$daily_signins = $this->CI->db->select('count(1) as total')->get_where('login_logs', array('log_time >=' => time()-86400))->row()->total;
			$this->CI->cache->save('daily_signins', $daily_signins, 200);
		endif;

		if ( ! $daily_posts = $this->CI->cache->get('daily_posts')):
			$daily_posts = $this->CI->db->select('count(1) as total')->get_where('topic_posts', array('post_time >=' => date("Y-m-d H:i:s", time()-86400)))->row()->total;
			$this->CI->cache->save('daily_posts', $daily_posts, 200);
		endif;

		$data['login_today'] = number_format($daily_signins);
		$data['daily_posts'] = number_format($daily_posts);

		if (isset($this->userdata['user_id'])):
			$data['unread_mail'] = $this->CI->db->get_where('mail', array('receiver' => $this->userdata['user_id'], 'status' => 0, 'saved' => 0))->num_rows();
			// if ($this->is_staff()):
			// endif;

			// $enable_scavenger = FALSE;

			// if ($this->userdata['user_id'] == 14 || $enable_scavenger):
			// 	if ( ! $set_location = $this->CI->cache->get('winter_location_'.$this->userdata['user_id'])):
			// 		$possible_locations = array('/snowball/shop', '/shops/view/8/40', '/forum/view/9', '/mailbox/outbox', '/forum/your_topics');
			// 		$set_location = $possible_locations[array_rand($possible_locations)];
			// 		$this->CI->cache->save('winter_location_'.$this->userdata['user_id'], $set_location, 86400);
			// 	endif;
			// endif;

			// if ($this->userdata['user_id'] == 14 || $enable_scavenger):
			// 	if ( ! $this->CI->cache->get('item_found_'.$this->userdata['user_id'])):
			// 		if ($set_location == '/'.$this->CI->uri->uri_string()):
			// 			$data['item_collected'] = 1;
			// 		endif;
			// 	endif;
			// endif;
		endif;

		if( ! $ajax) $this->CI->load->view('layout/header', $data);

		if (isset($this->userdata['banned']) && $this->userdata['banned'] == 1):
			$this->CI->parser->parse('general/banned', $data);
		else:
			$this->CI->parser->parse($template, $data);
		endif;

		if( ! $ajax) $this->CI->load->view('layout/footer', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Parse JSON
	 *
	 * A clean and proper way to output JSON to the client
	 *
	 * @access  public
	 * @param   json_array
	 * @return  append output (JSON)
	 */

	public function parse_json($json_data = array())
	{
		$this->CI->output->set_content_type('application/json')->set_output(json_encode($json_data, JSON_NUMERIC_CHECK));
	}


	// --------------------------------------------------------------------

	/**
	 * Is the user a staff member?
	 *
	 * Check and return a boolean value if the user is a staff member
	 *
	 * @access  public
	 * @param   (int) user_id
	 * @return  boolean
	 */

	public function is_staff($user_id = 0)
	{
		// Non-logged in fallback
		if($user_id == 0) $user_id = (isset($this->userdata['user_id']) ? $this->userdata['user_id'] : 0);

		if( ! isset($this->userdata['user_level'])) return FALSE;

		if($user_id == $this->CI->session->userdata('user_id') && $this->userdata['user_level'] != "user"):
			return TRUE;
		else:
			$target_userdata = $this->CI->db->get_where('users', array('users.user_id' => $user_id))->row_array();

			if($target_userdata['user_level'] != "user"):
				return TRUE;
			else:
				return FALSE;
			endif;
		endif;
	}

	// --------------------------------------------------------------------

	/**
	 * Create a new notification
	 *
	 * Send a notification to the recipient about a certain action
	 *
	 * @access  public
	 * @param   array (notification data)
	 * @return  boolean/int
	 */

	public function create_notificiation($notification_data = array())
	{
		// Just incase they forgot something, let's do some fault tolerance!
		if(isset($notification_data['to_user_id']) && ! isset($notification_data['to_username'])):
			$to_userdata = $this->retrieve_user_data($notification_data['to_user_id']);
			if(count($to_userdata) < 1) return FALSE;
			$notification_data['to_username'] = $to_userdata['username'];
		elseif(isset($notification_data['to_username']) && ! isset($notification_data['to_user_id'])):
			$to_userdata = $this->retrieve_user_data($notification_data['to_username'], 'username');
			if(count($to_userdata) < 1) return FALSE;
			$notification_data['to_user_id'] = $to_userdata['user_id'];
		endif;

		$this->CI->db->where('user_id', $notification_data['to_user_id'])->set('notifications', '(notifications+1)', FALSE)->update('users');
		$insert_boolean = $this->CI->db->set('notification_timestamp', 'NOW()', false)->insert('user_notifications', array(
			'notification_type'          => $notification_data['type'],
			'notification_from_user_id'  => (isset($notification_data['user_id']) ? $notification_data['user_id'] : $this->userdata['user_id']),
			'notification_from_username' => (isset($notification_data['username']) ? $notification_data['username'] : $this->userdata['username']),
			'notification_to_user_id'    => $notification_data['to_user_id'],
			'notification_to_username'   => $notification_data['to_username'],
			'notification_message'       => $notification_data['message'],
			'notification_active'        => 1,
			'notification_data_id' 		 => (isset($notification_data['data_id']) ? $notification_data['data_id'] : 0)
		));

		$notification_data['id'] = $this->CI->db->insert_id();

		return ($insert_boolean ? $notification_data : $insert_boolean);
	}

	// --------------------------------------------------------------------

	/**
	 * New function
	 *
	 * Description of new function
	 *
	 * @access  public
	 * @param   none
	 * @return  output
	 */

	public function create_batch_notifications($user_data = array(), $notification_data = array())
	{
		$user_notifications = array();
		$user_ids = array();

		foreach ($user_data as $user):
			$user_ids[] = $user['user_id'];
			$user_notifications[] = array(
				'notification_type'          => $notification_data['type'],
				'notification_from_user_id'  => (isset($notification_data['user_id']) ? $notification_data['user_id'] : $this->userdata['user_id']),
				'notification_from_username' => (isset($notification_data['username']) ? $notification_data['username'] : $this->userdata['username']),
				'notification_to_user_id'    => $user['user_id'],
				'notification_to_username'   => $user['username'],
				'notification_message'       => $notification_data['message'],
				'notification_active'        => 1,
				'notification_data_id' 		 => (isset($notification_data['data_id']) ? $notification_data['data_id'] : 0)
			);
		endforeach;

		$this->CI->db->where_in('user_id', $user_ids)->set('notifications', '(notifications+1)', FALSE)->update('users');
		return $this->CI->db->insert_batch('user_notifications', $user_notifications);
	}

	// --------------------------------------------------------------------

	/**
	 * Clear Notifications
	 *
	 * Clear a certain type of notifications once the user has accessed the page
	 *
	 * @access  public
	 * @param   notification type
	 * @return  boolean
	 */

	public function clear_notifications($type = '', $extra_data_id = 0)
	{
		$this->CI->db->where('notification_to_user_id', $this->userdata['user_id'])
				 	 ->where('notification_active', 1)
				 	 ->where("notification_type LIKE '".$type."%'")
				 	 ->where('notification_data_id', $extra_data_id)
				 	 ->set('notification_cleared_time', 'NOW()', false)
				 	 ->update('user_notifications', array('notification_active' => 0, 'notification_visited' => 1));

		$this->userdata['notifications'] -= $this->CI->db->affected_rows();

		return $this->CI->db->where('user_id', $this->userdata['user_id'])->update('users', array('notifications' => max(0, $this->userdata['notifications']))); // cap it at 0
	}

	// --------------------------------------------------------------------

	/**
	 * Broadcast event
	 *
	 * Broadcast to your friends an event related to you
	 *
	 * @access  public
	 * @param   (char 190) message
	 * @param   (int) target_user_id
	 * @return  id
	 */

	public function broadcast($message = "", $target_user_id = 0)
	{
		$this->CI->db->insert('activity_logs', array(
			'username' => $this->userdata['username'],
			'user_id' => $this->userdata['user_id'],
			'target_user_id' => $target_user_id,
			'message' => $message
		));

		return $this->CI->db->insert_id();
	}

	// --------------------------------------------------------------------

	/**
	 * New function
	 *
	 * Description of new function
	 *
	 * @access  public
	 * @param   none
	 * @return  output
	 */

	public function get_feature_config($feature_name = '', $cache = TRUE)
	{
		if( ! isset($this->userdata['user_id'])) $this->userdata['user_id'] = 0;
		if (( ! $configuration_array = $this->CI->cache->get($feature_name.'_config')) || $cache === FALSE):
			$config_query = $this->CI->db->get_where('feature_config', array('feature' => $feature_name));

			foreach($config_query->result_array() as $row):
				$configuration_array[$row['key']] = str_replace('<key>', $this->userdata['user_id'], $row['value']);
			endforeach;

			if($cache):
				$this->CI->cache->save($feature_name.'_config', $configuration_array, 3600); // one hour
			endif;
		endif;

		return $configuration_array;
	}

	// --------------------------------------------------------------------

	/**
	 * New function
	 *
	 * Description of new function
	 *
	 * @access  public
	 * @param   none
	 * @return  output
	 */

	public function get_friends()
	{
		return $this->CI->db->select('username, friend_id, last_activity')
							->join('users', 'users.user_id = friends.friend_id')
							->join('sessions', 'sessions.user_id = friends.friend_id', 'LEFT')
							->where('friends.user_id', $this->userdata['user_id'])
							->where('active', 1)
							->where('last_activity >=', (time()-1200))
							->order_by('friendship_id', 'ASC')
							->group_by('friend_id')
							->get('friends')
							->result_array();
	}

}


/* End of file system.php */
/* Location: ./system/application/library/system.php */