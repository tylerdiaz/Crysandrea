<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends CI_Controller
{
	// --------------------------------------------------------------------

	/**
	 * Home page
	 *
	 * Profile main function
	 *
	 * @access  public
	 * @param   none
	 * @return  view
	 * @route   n/a
	 */

	public function index()
	{
		if(isset($this->system->userdata['user_id'])):
			$this->view_user($this->system->userdata['username']);
		else:
			$this->view_user('Pixeltweak');
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

	public function view_user($username = '')
	{
		if( ! isset($this->system->userdata['user_id'])):
			$this->system->userdata['user_id'] = 0;
			$this->system->userdata['user_level'] = 'user';
		endif;

		$username = str_replace("_", " ", urldecode($username));
		$user_query = $this->db->get_where('users', array('username' => $username));

		if($user_query->num_rows() == 0) show_error('user could not be found.');
		$user_data = $user_query->row_array();

		$profile_data = array(
			'likes'       => '',
			'dislikes'    => '',
			'profile_bio' => '',
			'profile_css' => '',
			'hobbies'     => ''
		);

		$this->load->library('parser');
		$this->load->helper('forum');

		$friends = $this->db->select('username, users.user_id, user_email, friend_id, timestamp, last_activity')
							->join('users', 'users.user_id = friends.friend_id')
							->join('sessions', 'sessions.user_id = friends.friend_id', 'LEFT')
							->where('friends.user_id', $user_data['user_id'])
							->where('active', 1)
							->group_by('username')
							->order_by('last_activity', 'DESC')
							->order_by('friendship_id', 'ASC')
							->limit(16)
							->get('friends')
							->result_array();

		$total_friends = $this->db->select('COUNT(1) as total')->get_where('friends', array('user_id' => $user_data['user_id']))->row()->total;
		$profile_query = $this->db->get_where('user_preferences', array('user_id' => $user_data['user_id']));

		if ($profile_query->num_rows() > 0):
			$profile_data = array_merge($profile_data, $profile_query->row_array());
		endif;

		$profile_comments = $this->db->select('profile_comments.*, users.username, users.user_id, users.user_level')
									 ->select('IF(profile_comments.comment_author = '.$this->system->userdata['user_id'].', 1, 0) as modify', FALSE)
									 ->join('users', 'users.user_id = profile_comments.comment_author')
									 ->limit(6)
									 ->order_by('profile_comments.comment_id', 'DESC')
									 ->get_where('profile_comments', array('comment_profile' => $user_data['user_id']))
									 ->result_array();

		if (isset($this->system->userdata['user_level']) && $this->system->userdata['user_level'] != 'user'):
			foreach ($profile_comments as $key => $comment):
				$profile_comments[$key]['modify'] = 1;
			endforeach;
		endif;

		$total_wished_items = $this->count_rows('wishlist', 'user_id', $user_data['user_id']);
		$total_comments = $this->count_rows('profile_comments', 'comment_profile', $user_data['user_id']);
		$total_topics = $this->count_rows('topics', 'topic_author', $user_data['user_id']);
		$total_posts = $this->count_rows('topic_posts', 'author_id', $user_data['user_id']);

		$this->parser->parse('profile/view_profile', array(
			'page_title'     => $user_data['username']."'s Profile",
			'username'       => $user_data['username'],
			'user_id'        => $user_data['user_id'],
			'total_posts'    => $total_posts,
			'total_topics'   => $total_topics,
			'total_wishes'   => $total_wished_items,
			'profile_data'   => $profile_data,
			'comments'       => $profile_comments,
			'friends'        => $friends,
			'total_comments' => $total_comments,
			'viewer_user_id' => ($this->session->userdata('user_id') ? $this->system->userdata['user_id'] : 0)
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

	public function count_rows($table = 0, $key = '', $user_id = 0)
	{
	    return $this->db->select('COUNT(1) as total')->get_where($table, array($key => $user_id))->row()->total;
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

	public function comment($action = 0, $comment_id = 0)
	{
		if( ! $this->session->userdata('user_id')) redirect('signin');

		$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

		if($action == "add"):
			$this->load->library('form_validation');
			$this->form_validation->set_rules('comment-text', 'Comment', 'required|htmlentities|xss_clean|addslashes');

			$user_query = $this->db->get_where('users', array('user_id' => $comment_id));

			if($user_query->num_rows() > 0):
				$user_data = $user_query->row_array();
			else:
				show_error('user could not be found.');
			endif;

			if ($this->form_validation->run() == FALSE):
				show_error("comment failed to send");
			endif;

			$new_profile_comment_id = $this->db->set('comment_time', 'NOW()', FALSE)->insert('profile_comments', array(
				'comment_author'  => $this->session->userdata("user_id"),
				'comment_profile' => $comment_id,
				'comment_text'    => $this->input->post('comment-text')
			));

			if($this->input->is_ajax_request()):
				$json_response = array(
					'author_id'       => $this->session->userdata("user_id"),
					'author_profile'  => site_url('profile/view/'.urlencode($this->session->userdata("username"))),
					'author_color'    => user_color($this->session->userdata('user_level')),
					'author_username' => $this->session->userdata("username"),
					'message'         => stripslashes(nl2br($this->input->post("comment-text")))
				);
			endif;

			$string = $this->input->post('comment-text');

			$this->notification->broadcast(array(
				'receiver'          => $user_data['username'],
				'receiver_id'       => $comment_id,
				'notification_text' => $this->system->userdata['username'].' just commented on your profile: '.((strlen($string) > 24) ? substr($string, 0, 24).'...' : $string),
				'attachment_id'     => $new_profile_comment_id,
				'attatchment_type'  => 'comment',
				'attatchment_url'   => '/user/'.urlencode($user_data['username']),
			), FALSE);

			if($this->input->is_ajax_request()):
			    $this->system->parse_json($json_response);
			else:
				redirect('profile/view/'.urlencode($user_data['username']));
			endif;
		elseif($action == "delete" && strpos($referer,'crysandrea')):
			if($this->input->is_ajax_request()):
				$this->db->delete('profile_comments', array('comment_id' => $comment_id));
				return TRUE;
			else:
				return FALSE;
			endif;
		else:
			show_error('No action specified');
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

	public function load_more_comments($user_id = 0, $messages_loaded = 0)
	{
		// if( ! $this->input->if_ajax()) die("Error: AJAX REQUEST MISSING");

		if( ! isset($this->system->userdata['user_id'])):
			$this->system->userdata['user_id'] = 0;
			$this->system->userdata['user_level'] = 'user';
		endif;

		$profile_comments = $this->db->select('profile_comments.*, users.username, users.user_id, users.user_level')
									 ->select('IF(profile_comments.comment_author = '.$this->system->userdata['user_id'].', 1, 0) as modify', FALSE)
									 ->join('users', 'users.user_id = profile_comments.comment_author')
									 ->limit(6, $messages_loaded)
									 ->order_by('profile_comments.comment_id', 'DESC')
									 ->get_where('profile_comments', array('comment_profile' => $user_id))
									 ->result_array();

		if ($this->system->userdata['user_level'] != 'user'):
			foreach ($profile_comments as $key => $comment):
				$profile_comments[$key]['modify'] = 1;
			endforeach;
		endif;

		foreach ($profile_comments as $comment): ?>
			<li id="comment-<?php echo $comment['comment_id']?>" class="<?php echo $comment['comment_id']?>">
			<img src="/images/avatars/<?php echo $comment['user_id']?>_headshot.png" width="64" height="64" alt="" class="avatar_thumb" />
			<a href="<?php echo site_url('profile/view/'.urlencode($comment['username']))?>" style="color:<?php echo user_color($comment['user_level'])?>; font-weight:bold;" ><?php echo $comment['username']?></a> said: <br />
			<p>
				<?php echo stripslashes(nl2br($comment['comment_text']))?>
				<small>(<?php echo _datadate($comment['comment_time'])?>)</small>
			</p>
			<?php if($comment['modify']): ?>
				<a href="/profile/comment/delete/<?php echo $comment['comment_id'] ?>" style="color:#ff7c7c" class="delete small"> (Delete)</a>
			<?php endif; ?>
			</li>
		<?php endforeach;
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
	/*
	public function delete_comment($comment_id)
	{
		if( ! isset($this->system->userdata['user_id'])):
			$this->system->userdata['user_id'] = 0;
			$this->system->userdata['user_level'] = 'user';
		endif;

		//Select all from profile_comments where user_id = comment_profile AND comment_id = $comment_id
		$query = $this->db->get_where('profile_comments',
					  array(
					  		'comment_profile' =>  $this->system->userdata['user_id'],
					  		'comment_id' => $comment_id
					  	));
		if($this->query->num_rows() > 0){
			show_error("I own comment!");
		}
	}
	*/


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

	public function view_posts($user_id = 0)
	{
		if( ! is_numeric($user_id)) show_error('user_id must be valid');

		$user_data = $this->db->select('username')->get_where('users', array('user_id' => $user_id))->row_array();

		if(count($user_data) == 0):
			show_error('User posts could not be found');
		endif;

		$this->load->model('forum_engine');
		$this->load->helper('forum_helper');

		if ( ! $total_posts = $this->cache->get('total_posts_'.$user_id)):
			$total_posts = $this->db->select('COUNT(1) as total')->get_where('topic_posts', array('author_id' => $user_id))->row()->total;
			$this->cache->save('total_posts_'.$user_id, $total_posts, 60);
		endif;

		$this->load->library('pagination');
		$config['base_url'] = '/profile/view_posts/'.$user_id;
		$config['total_rows'] = $total_posts;
		$config['per_page'] = 14;
		$config['uri_segment'] = 4;

		$this->pagination->initialize($config);

		$user_posts = $this->db->query("SELECT topics.topic_id as tp_id,
								forums.staff,
								topics.topic_title,
								tp_new.post_id as ps_id,
								tp_new.topic_post_id as tps_id,
								tp_new.topic_id,
								tp_new.author_id,
								tp_new.post_time,
								topic_post_text.text as post_body
						FROM (SELECT post_id FROM topic_posts WHERE topic_posts.author_id = ".$user_id." ORDER BY post_id DESC LIMIT ".$this->uri->segment(4, 0).", ".$config['per_page'].") tp_old
						JOIN topic_posts tp_new ON tp_new.post_id = tp_old.post_id
						JOIN `topic_post_text` ON `topic_post_text`.`post_id` = `tp_new`.`post_id`
						JOIN `topics` ON `tp_new`.`topic_id` = `topics`.`topic_id`
						JOIN `forums` ON `forums`.`forum_id` = `topics`.`forum_id`
						ORDER BY tp_new.post_id DESC")->result_array();

		$data = array(
			'posts'          => $user_posts,
			'user_id'        => $user_id,
			'username'       => $user_data['username'],
			'user_data'      => $user_data,
			'total_posts'    => $total_posts,
			'page_body'      => 'forums',
			'page_title'     => $user_data['username'].'\'s posts',
			'posts_per_page' => $config['per_page']
		);

		$this->system->quick_parse('profile/view_posts', $data);
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

	public function view_topics($user_id = 0)
	{
	    if( ! is_numeric($user_id)) show_error('user_id must be valid');

	    $user_data = $this->db->select('username')->get_where('users', array('user_id' => $user_id))->row_array();

	    if(count($user_data) == 0):
	    	show_error('User posts could not be found');
	    endif;

	    if ( ! $total_topics = $this->cache->get('total_topics_'.$user_id)):
	    	$total_topics = $this->db->select('COUNT(1) as total')->get_where('topics', array('topic_author' => $user_id))->row()->total;
	    	$this->cache->save('total_topics_'.$user_id, $total_topics, 60);
	    endif;

	    $this->load->library('pagination');
	    $config['base_url'] = '/profile/view_topics/'.$user_id;
	    $config['total_rows'] = $total_topics;
	    $config['per_page'] = 24;
	    $config['uri_segment'] = 4;

		$this->pagination->initialize($config);

		$user_topics = $this->db->select('*')
								->from('topics')
								->where('topics.topic_author', $user_id)
								->where('forums.staff', 0)
								->join('forums', 'forums.forum_id = topics.forum_id')
								->order_by('topics.topic_id', 'desc')
								->limit($config['per_page'], $this->uri->segment(4, 0))
								->get()
								->result_array();

	    $view_data = array(
	    	'page_title' => $user_data['username'].'\'s topics',
	    	'page_body' => 'forums profile',
	    	'topics' => $user_topics,
	    	'user_data' => $user_data,
	    	'username' => $user_data['username'],
	    	'user_id' => $user_id
	    );

	    $this->system->quick_parse('profile/view_topics', $view_data);
	}


}

/* End of file Avatar.php */
/* Location: ./system/application/controllers/Avatar.php */