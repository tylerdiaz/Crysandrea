<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Friends extends CI_Controller
{
	var $route_navigation = array(
		'index' => 'View friendlist',
		'add_friend' => 'Add friends',
		// 'invite' => 'Invite friends'
	);


	function __construct(){
		parent::__construct();
		if( ! isset($this->system->userdata['user_id'])):
			redirect('auth/signin?r=friends');
		endif;
	}

	// --------------------------------------------------------------------

	/**
	 * Home page
	 *
	 * Friends main function
	 *
	 * @access  public
	 * @param   none
	 * @return  view
	 * @route   n/a
	 */

	public function index()
	{
		$this->system->view_data['scripts'][] = '/global/js/friends/index.js';

		$requests = $this->db->select('users.user_id, username, user_email, friend_id, timestamp, friendship_id')
							->join('users', 'users.user_id = friends.friend_id')
							->where('friends.user_id', $this->system->userdata['user_id'])
							->where('active', 2)
							->where('initiator !=', $this->system->userdata['user_id'])
							->order_by('timestamp', 'ASC')
							->get('friends')
							->result_array();

		$friends = $this->db->select('username, user_email, friend_id, timestamp, last_activity')
							->join('users', 'users.user_id = friends.friend_id')
							->join('sessions', 'sessions.user_id = friends.friend_id', 'LEFT')
							->where('friends.user_id', $this->system->userdata['user_id'])
							->where('active', 1)
							->group_by('username')
							->order_by('last_activity', 'DESC')
							->order_by('friendship_id', 'ASC')
							->get('friends')
							->result_array();

		$this->system->quick_parse('friends/index', array(
			'page_title' => 'Your friends',
			'page_body'  => 'friends',
			'friends'    => $friends,
			'routes'     => $this->route_navigation,
			'active_url' => $this->uri->rsegment(2, 0),
			'requests'   => $requests
		));
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

	public function add_friend()
	{
		$this->system->view_data['scripts'][] = '/global/js/friends/add_friend.js';

	    if($_SERVER['REQUEST_METHOD'] == "POST"):
	    	$username = $this->input->post('friend_username');
	    	$user_query = $this->db->get_where('users', array('username' => $username));

	    	if($user_query->num_rows() == 0):
	    		redirect('friends/add_friend?missing&username='.urlencode($this->input->post('friend_username')));
	    	endif;
	    	$user_data = $user_query->row_array();

	    	$friendship_id = $this->input->ip_address();
	    	$friend_query = $this->db->get_where('friends', array(
	    		'user_id' => $this->system->userdata['user_id'],
	    		'friend_id' => $user_data['user_id']
	    	));

	    	// Future TODO: make sure they're not blocking each other

	    	if($friend_query->num_rows() == 0):
		    	// if not, let's create a new friendship for them
		    	$new_friend_data = array(
		    		'user_id' => $this->system->userdata['user_id'],
		    		'friend_id' => $user_data['user_id'],
		    		'active' => 2,
		    		'unfriend_count' => 0,
		    		'initiator' => $this->system->userdata['user_id']
		    	);

		    	$this->db->insert('friends', $new_friend_data);

		    	// This friendlist uses a bi-directional friendship. (2 rows are created per friendship)
		    	$new_friend_data = array(
		    		'user_id' => $user_data['user_id'],
		    		'friend_id' => $this->system->userdata['user_id'],
		    		'active' => 2,
		    		'unfriend_count' => 0,
		    		'initiator' => $this->system->userdata['user_id']
		    	);

		    	$this->db->insert('friends', $new_friend_data);
	    	else:
	    		// if so, simply re-use the old friendship
	    		$friend_data = $friend_query->row_array();
	    		if($friend_data['active'] == 1) show_error('You two are already friends!');

	    		if($friend_data['active'] != 2):
	    			$where_friendship_yin = array('user_id' => $friend_data['user_id'], 'friend_id' => $friend_data['friend_id']);
	    			$where_friendship_yan = array('user_id' => $friend_data['friend_id'], 'friend_id' => $friend_data['user_id']);

	    			$update_friend_data = array(
	    				'active' => 2, // 2 = request, 1 = active, 0 = ignored
	    				'initiator' => $this->system->userdata['user_id']
	    			);

	    			$this->db->where($where_friendship_yin)->update('friends', $update_friend_data);
	    			$this->db->where($where_friendship_yan)->update('friends', $update_friend_data);
    			endif;
    		endif;

    		$this->notification->broadcast(array(
    			'receiver' => $user_data['username'],
    			'receiver_id' => $user_data['user_id'],
    			'notification_text' => $this->system->userdata['username'].' has sent you a friend request',
    			'attachment_id' => $this->db->insert_id(),
    			'attatchment_type' => 'friend_request',
    			'attatchment_url' => '/friends',
    		), FALSE);

    		redirect('friends/add_friend?sent');

	    	// Index their recent communication to give a value ratio to the friendship. Do you guys have a lot of friends in common?
	    	// Give it a "friend score" based on that index
	    else:
	    	// Users you've recently interacted with (This will be very cool) Offer a "I don't know this user" Maybe show where you met?
	    		// Be careful not to show it to people who you don't get along with
	    	// Posted in one topic page together, exchanged PM's, share a lot of common friends, purchase from the marketplace often (3 or more),

	    	$this->system->quick_parse('friends/add_friend', array(
	    		'page_title' => 'Add a friend',
	    		'page_body' => 'friends',
	    		'routes'     => $this->route_navigation,
	    		'active_url' => $this->uri->rsegment(2, 0),
	    	));
	    endif;
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

	public function get_user_data($user_id = 0)
	{
		if( ! is_numeric($user_id)) show_error('User ID must be a number!');

	    $user_query = $this->db->select('users.user_id, users.username, sessions.last_activity, users.user_last_login, users.user_level, friends.friendship_id')
	    						->join('sessions',  'users.user_id = sessions.user_id', 'LEFT')
	    						->join('friends',  'friends.friend_id = users.user_id', 'LEFT')
	    						->limit(1)
	    						->where(array('users.user_id' => $user_id))
	    						->get('users');

	    if($user_query->num_rows() > 0):
	    	$user_data = $user_query->row_array();
	    	$user_data['user_last_login'] = human_time($user_data['user_last_login']);
	    	$user_data['username_clean'] = urlencode($user_data['username']);
	    	$this->output->set_content_type('application/json')->set_output(json_encode($user_data, JSON_NUMERIC_CHECK));
	    else:
	    	show_error('user could not be found.');
	    endif;
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

	public function accept_request()
	{
	    if($_SERVER['REQUEST_METHOD'] == "POST"):
	    	$friendship_id = $this->input->post('friendship_id');
	    	$user_id = $this->input->post('user_id');

	    	if( ! is_numeric($user_id)) show_error('user_id must be valid');
	    	if( ! is_numeric($friendship_id)) show_error('user_id must be valid');

	    	$friend_query = $this->db->select('users.username, friends.*')->join('users', 'users.user_id = friends.friend_id')->get_where('friends', array('friend_id' => $user_id, 'friendship_id' => $friendship_id));

	    	if($friend_query->num_rows() == 0):
	    		show_error('Friendship could not be found.');
	    	endif;

	    	$friend_data = $friend_query->row_array();

			$where_friendship_yin = array('user_id' => $friend_data['user_id'], 'friend_id' => $friend_data['friend_id']);
			$where_friendship_yan = array('user_id' => $friend_data['friend_id'], 'friend_id' => $friend_data['user_id']);

	    	$update_friend_data = array('active' => 1);

	    	$this->db->where($where_friendship_yin)->update('friends', $update_friend_data);
	    	$this->db->where($where_friendship_yan)->update('friends', $update_friend_data);

	    	redirect('friends/index?acceped&u='.urlencode($friend_data['username']));
	    else:
	    	redirect('friends');
	    endif;
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

	public function ignore_request($friendship_id = 0)
	{
    	if( ! is_numeric($friendship_id)) show_error('user_id must be valid');

    	$friend_query = $this->db->get_where('friends', array('friendship_id' => $friendship_id));

    	if($friend_query->num_rows() == 0):
    		show_error('Friendship could not be found.');
    	endif;

    	$friend_data = $friend_query->row_array();

		$where_friendship_yin = array('user_id' => $friend_data['user_id'], 'friend_id' => $friend_data['friend_id']);
		$where_friendship_yan = array('user_id' => $friend_data['friend_id'], 'friend_id' => $friend_data['user_id']);

    	$update_friend_data = array('active' => 0, 'unfriend_count' => ($friend_data['friend_id']+1));

    	$this->db->where($where_friendship_yin)->update('friends', $update_friend_data);
    	$this->db->where($where_friendship_yan)->update('friends', $update_friend_data);

    	redirect('friends/index?ignored');
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

	public function remove_friend()
	{
		$friend_id = $this->input->post('friend_id');
		if( ! is_numeric($friend_id)) show_error('friend_id must be valid');

		if($_SERVER['REQUEST_METHOD'] == "POST"):
			$friend_query = $this->db->get_where('friends', array('friend_id' => $friend_id, 'user_id' => $this->system->userdata['user_id']));

			if($friend_query->num_rows() == 0):
				show_error('Friendship could not be found.');
			endif;

			$friend_data = $friend_query->row_array();

			$where_friendship_yin = array('user_id' => $friend_data['user_id'], 'friend_id' => $friend_data['friend_id']);
			$where_friendship_yan = array('user_id' => $friend_data['friend_id'], 'friend_id' => $friend_data['user_id']);

			$update_friend_data = array('active' => 0, 'unfriend_count' => ($friend_data['friend_id']+1));

			$this->db->where($where_friendship_yin)->update('friends', $update_friend_data);
			$this->db->where($where_friendship_yan)->update('friends', $update_friend_data);

			if($this->input->is_ajax_request()):
			    $response = array('success' => 1);
			    $this->output->set_content_type('application/json')->set_output(json_encode($response, JSON_NUMERIC_CHECK));
			else:
			    redirect('friends/index?removed');
			endif;
		endif;
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

	public function get_friends($return = 'username')
	{
	    $friends = $this->db->select('username, user_email, friend_id, timestamp, last_activity')
	    					->join('users', 'users.user_id = friends.friend_id')
	    					->join('sessions', 'sessions.user_id = friends.friend_id', 'LEFT')
	    					->where('friends.user_id', $this->system->userdata['user_id'])
	    					->where('active', 1)
	    					->group_by('username')
	    					->order_by('last_activity', 'DESC')
	    					->order_by('friendship_id', 'ASC')
	    					->get('friends')
	    					->result_array();

	   	$friend_json = array();
	    $rows_to_return = explode(',', $return);

	    foreach ($friends as $friend_key => $friend):
	    	if(count($rows_to_return) > 1):
	    		foreach ($rows_to_return as $row_key):
	    			$friend_json[$friend_key][$row_key] = $friend[$row_key];
	    		endforeach;
	    	else:
	    		if(isset($friend[$rows_to_return[0]])):
	    			$friend_json[] = $friend[$rows_to_return[0]];
	    		endif;
    		endif;
	    endforeach;

	    $this->output->set_content_type('application/json')->set_output(json_encode($friend_json, JSON_NUMERIC_CHECK));
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

	public function reformat_schema()
	{
		$friends = $this->db->get('old_friends')->result_array();

		foreach ($friends as $friend):
			$new_friend_data = array(
				'user_id' => $friend['user'],
				'friend_id' => $friend['friend'],
				'active' => 1,
				'unfriend_count' => 0,
				'initiator' => $friend['user']
			);

			$new_friend_id = $this->db->insert('friends', $new_friend_data);

			$new_friend_data = array(
				'user_id' => $friend['friend'],
				'friend_id' => $friend['user'],
				'active' => 1,
				'unfriend_count' => 0,
				'initiator' => $friend['friend']
			);

			$new_friend_id = $this->db->insert('friends', $new_friend_data);
		endforeach;
	}

}

/* End of file Friends.php */
/* Location: ./system/application/controllers/Friends.php */