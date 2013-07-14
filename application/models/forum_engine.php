<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Crysandrea Forum Engine
 *
 * @author(s) 	Tyler Diaz
 * @version 	1.1
 * @copyright 	Crysandrea - July 30, 2010
 * @last_update July 30, 2010 by Tyler Diaz
 **/

class Forum_engine extends CI_Model
{
	var $cache_prefix;
	var $memcached = TRUE;
	var $data_response = 'array';
	var $cache_durations = array(
		'total_registered_users'	=> 300, 	// 5 minutes
		'users_online'				=> 60, 		// 1 minutes
		'non_registered_users_online' => 60, 		// 1 minutes
		'forums_and_categories'		=> 3600, 	// 1 hour
		'forum_activity' 			=> 180,		// 3 minutes
		'get_forum_data' 			=> 360,
    	'get_topic_data' 			=> 3600,
	);
    var $offline_time = 1080;
	var $forum_id = 0;
	var $topic_id = 0;

	function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Engine configuration
	 *
	 * Allows the developer to arrange the global class variables
	 *
	 * @access	public
	 * @param	array
	 * @return	n/a
	 */

	function configure($preferences = array())
	{
		foreach($preferences as $config => $value):
			$this->{$config} = strtolower($value);
		endforeach;
	}

	// --------------------------------------------------------------------

	/**
	 * Get forum statistics
	 *
	 * Obtain the current forum statistics, no cache.
	 *
	 * @access	public
	 * @param	n/a
	 * @return	data
	 */

	function get_statistics()
	{
		$response = FALSE;

		if($this->memcached == TRUE):
			$this->cache->useMemcache('11211', 'localhost');
			$memcache = $this->cache->get($this->cache_prefix.'registered_users');

			if($memcache):
				$users = $memcache;
			else:
				$users = $this->db->select('user_id')->order_by('user_id', 'desc')->limit(1)->get('users')->row()->user_id;
				$this->cache->save($this->cache_prefix.'registered_users', $users, NULL, $this->cache_durations['total_registered_users']);
			endif;

		else:
			$users = $this->db->select('MAX(user_id) as user_id')->limit(1)->get('users')->row()->user_id;
		endif;

		//
		// We don't cache posts because it's a very live/active statistic
		//
		$posts = $this->db->select('MAX(post_id) as post_id')->limit(1)->get('topic_posts')->row()->post_id;

		switch($this->data_response)
		{
			case 'array':
				$response['total_posts'] = $posts;
				$response['registered_users'] = $users;
			break;
			case 'object':
				$response->total_posts = $posts;
				$response->registered_users = $users;
				$response->total_topics = $topics;
			break;
			case 'json':
				$response['total_posts'] = $posts;
				$response['registered_users'] = $users;
				$response['total_topics'] = $topics;
				$response = json_encode($response);
			break;
		}

		return $response;
	}

	// --------------------------------------------------------------------

	/**
	 * Get users online
	 *
	 * Obtain all users who are recently active
	 *
	 * @access	public
	 * @param	n/a
	 * @return	data
	 */

	function get_users_online()
	{
		$response = FALSE;

		if($this->memcached == TRUE):
			$this->cache->useMemcache('11211', 'localhost');
			$memcache = $this->cache->get($this->cache_prefix.'users_online');

			if($memcache):
				$response = $memcache;
			else:
				$online_users = $this->db->select('username, user_level, last_action')
										 ->order_by('username', 'asc')
										 ->where('last_action >='. (time()-$this->offline_time).'')
										 ->get('users');
			endif;
		else:
			$online_users = $this->db->select('username, user_level, last_action')
									 ->order_by('username', 'asc')
									 ->where('last_action >='. (time()-$this->offline_time).'')
									 ->get('users');
		endif;

		if($this->memcached == TRUE && $memcache == FALSE):

			switch($this->data_response)
			{
				case 'array':
					$response = $online_users->result_array();
				break;
				case 'object':
					$response = $online_users->result();
				break;
				case 'json':
					$response = json_encode($online_users->result_array());
				break;
			}

			$this->cache->save($this->cache_prefix.'users_online', $response, NULL, $this->cache_durations['users_online']);

		elseif($this->memcached == FALSE):

			switch($this->data_response)
			{
				case 'array':
					$response = $online_users->result_array();
				break;
				case 'object':
					$response = $online_users->result();
				break;
				case 'json':
					$response = json_encode($online_users->result_array());
				break;
			}

		endif;

		return $response;
	}


	// --------------------------------------------------------------------

	/**
	 * Get none registed users who are online
	 *
	 * Obtain all users who are recently active and not signed in
	 *
	 * @access	public
	 * @param	n/a
	 * @return	data
	 */

	function get_non_registered_online_users()
	{
		$response = FALSE;

		if($this->memcached == TRUE):
			$this->cache->useMemcache('11211', 'localhost');
			$memcache = $this->cache->get($this->cache_prefix.'non_registered_users_online');

			if($memcache):
				$response = $memcache;
			else:
				$online_users = $this->db->select('user_ip')
										 ->order_by('timestamp', 'DESC')
										 ->where('timestamp >='. (time()-1800).'')
										 ->get('online_users');
				$this->db->query("DELETE FROM online_users WHERE timestamp < NOW()-600");
			endif;
		else:
			$online_users = $this->db->select('user_ip')
										 ->order_by('timestamp', 'DESC')
										 ->where('timestamp >='. (time()-1800).'')
										 ->get('online_users');
			$this->db->query("DELETE FROM online_users WHERE timestamp < NOW()-600");
		endif;
		if($this->memcached == TRUE && $memcache == FALSE):

			switch($this->data_response)
			{
				case 'array':
					$response = $online_users->result_array();
				break;
				case 'object':
					$response = $online_users->result();
				break;
				case 'json':
					$response = json_encode($online_users->result_array());
				break;
			}

			$this->cache->save($this->cache_prefix.'non_registered_users_online', $response, NULL, $this->cache_durations['non_registered_users_online']);

		elseif($this->memcached == FALSE):

			switch($this->data_response)
			{
				case 'array':
					$response = $online_users->result_array();
				break;
				case 'object':
					$response = $online_users->result();
				break;
				case 'json':
					$response = json_encode($online_users->result_array());
				break;
			}

		endif;

		return $response;
	}




	// --------------------------------------------------------------------

	/**
	 * Get forums and categories
	 *
	 * Function used to retrieve all forum and categories
	 * in a clean format and pack it up to our users.
	 * The memcache part makes this function so long*
	 *
	 * @access	public
	 * @param	n/a
	 * @return	data
	 */

	function get_forums_and_categories()
	{
		$categories = array();

		if($this->memcached == TRUE):
			$this->cache->useMemcache('11211', 'localhost');
			$memcache = $this->cache->get($this->cache_prefix.'forums_and_categories');

			if($memcache):
				$categories = $memcache;
	    	else:
				$data = $this->db->select('categories.name, forums.forum_id, forums.forum_name, forums.forum_description')
						  		 ->join('forums', 'forums.parent_id = categories.id', 'left')
						  		 ->get_where('categories', array('categories.staff' => 0))->result_array();
			endif;
		else:
			$data = $this->db->select('categories.name, forums.forum_id, forums.forum_name, forums.forum_description')
					  		 ->join('forums', 'forums.parent_id = categories.id', 'left')
					  		 ->get_where('categories', array('categories.staff' => 0))->result_array();
		endif;

		if($this->memcached == FALSE || $memcache == FALSE):

			foreach($data as $forum):
				$forum_name = strtolower(trim($forum['name']));

				$categories[$forum_name][] = array(
					'forum_id' => $forum['forum_id'],
					'forum_name' => $forum['forum_name'],
					'forum_description' => $forum['forum_description']
				);
			endforeach;

			switch($this->data_response)
			{
		    	case 'object':
		    		$categories = $this->_array_to_object($categories);
		    	break;
		    	case 'json':
		    		$categories = json_encode($categories);
		    	break;
				default:
					$categories;
				break;
		    }

		endif;

		if($this->memcached == TRUE && $memcache == FALSE)
		{
			$this->cache->save($this->cache_prefix.'forums_and_categories', $categories, NULL, $this->cache_durations['forums_and_categories']);
		}

		return $categories;
	}

	// --------------------------------------------------------------------

	/**
	 * Get forum activity
	 *
	 * Obtains all the recent forum posts
	 *
	 * @access	public
	 * @param	n/a
	 * @return	data
	 */

	function get_forum_activity()
	{
		$return_forums = FALSE;

		if($this->memcached == TRUE):
			$this->cache->useMemcache('11211', 'localhost');
			$memcache_key = $this->cache->get($this->cache_prefix.'forum_activity');

			if($memcache_key):
				$return_forums = $memcache_key;
			else:
				$forums = $this->db->select('max(topics.last_post) as last_post, topics.forum_id')
									->group_by('forum_id')
									->get('topics');
			endif;

		else:
			$forums = $this->db->select('max(topics.last_post) as last_post, topics.forum_id')
								->group_by('forum_id')
								->get('topics');
		endif;

		if($this->memcached == FALSE || $memcache_key == FALSE)
		{
			switch($this->data_response)
			{
		    	case 'object':
		    		$forums = $forums->result();

					foreach($forums as $forum):
						$return_forums->{$forum->forum_id} = $forum->last_post;
					endforeach;
		    	break;
		    	case 'json':
		    		$forums = $forums->result_array();

					foreach($forums as $forum):
						$return_forums[$forum['forum_id']] = $forum['last_post'];
					endforeach;
					$return_forums = json_encode($return_forums);
		    	break;
				default:
					$forums = $forums->result_array();

					foreach($forums as $forum):
						$return_forums[$forum['forum_id']] = $forum['last_post'];
					endforeach;
				break;
			}
		}

		if($this->memcached == TRUE && $memcache_key == FALSE)
		{
			$this->cache->save($this->cache_prefix.'forum_activity', $return_forums, NULL, $this->cache_durations['forum_activity']);
		}

		return $return_forums;
	}

	// --------------------------------------------------------------------

	/**
	 * Get forum data
	 *
	 * Obtain total topics and basic information about a specific forum
	 *
	 * @access	private
	 * @param	n/a
	 * @return	n/a
	 */
	function get_forum_data($id)
	{
		// Quick security add to see if the forum id is a number *it must be!*
		if( ! is_numeric($id)) return false;

		$this->forum_id = $id; // Now the engine knows which ID you mean.

		$total_topics = $this->db->select('COUNT(1) as total_topics')->get_where('topics', array('forum_id' => $id));
		$forum_data = $this->db->select('forum_name, forum_description, palladium_enabled, user_post, forums.staff, parent_id, categories.name')
								->from('forums')
								->join('categories', 'categories.id = forums.parent_id')
								->where(array('forum_id' => $id))
								->get();

		// Quick security add to see if the forum exists
		if($forum_data->num_rows() == 0) return false;

		$forum_data = $forum_data->row_array();
		$forum_data['total_topics'] = $total_topics->row()->total_topics;

		return $forum_data;
	}

	// --------------------------------------------------------------------

	/**
	 * Get forum data
	 *
	 * Obtain total topics and basic information about a specific forum
	 *
	 * @access	private
	 * @param	n/a
	 * @return	n/a
	 */
	function get_forum_topics($id, $limit, $start)
	{
		if(empty($id)) $id = $this->forum_id($id); // ID assumption

		$forum_topics = $this->db->select('topics.topic_author,
							topics.topic_type,
							topics.topic_id,
							topics.topic_title,
							topics.topic_status,
							topics.total_posts,
							topics.last_post_username,
							topics.last_post,
							users.username')
							->join('users', 'users.user_id = topics.topic_author')
							->order_by('topics.topic_type', 'desc')
							->order_by('topics.last_post', 'desc')
							->limit($start, $limit)
							->get_where('topics', array('forum_id' => $id));

		switch($this->data_response)
		{
			case 'object':
    			$forum_topics = $forum_topics->result();
    		break;
    		case 'json':
    			$forum_topics = $forum_topics->result_array();
				$forum_topics = json_encode($forum_topics);
    		break;
			default:
				$forum_topics = $forum_topics->result_array();
			break;
		}
		return $forum_topics;
	}

	// --------------------------------------------------------------------

	/**
	 * Create a topic
	 *
	 * Generic function to create a new topic
	 *
	 * @access	private
	 * @param	n/a
	 * @return	object
	 */

	function create_topic($topic_data = array())
	{
		if(isset($topic_data['forum_id'])):
			$topic_data['forum_id'] = $this->forum_id;
		endif;

		$this->db->set('last_post', 'NOW()', false) // 2 MySQL NOW() functions
				 ->set('topic_time', 'NOW()', false)
				 ->insert('topics', $topic_data);

		return $this->db->insert_id();
	}


	// --------------------------------------------------------------------

	/**
	 * Get topic data
	 *
	 * Generic function to create a new post
	 *
	 * @access	private
	 * @param	n/a
	 * @return	object
	 */

	function get_topic_data($id = 0, $count_posts = TRUE)
	{
	    if($id == 0):
			$id = $this->topic_id;
		else:
			$this->topic_id = $id;
		endif;

		$topic_data = $this->db->select('topics.*, forums.staff as staff, forums.forum_name, forums.short_name, forums.palladium_enabled')
								->join('forums', 'topics.forum_id = forums.forum_id')
								->get_where('topics', array('topic_id' => $id));

		return $topic_data->row_array();
	}


    // --------------------------------------------------------------------

    /**
     * New page
     *
     * New page description
     *
     * @access  public
     * @param   none
     * @return  redirect
     * @route   n/a
     */

    function count_posts($id = 0)
    {
        if($id == 0):
			$id = $this->topic_id;
		else:
			$this->topic_id = $id;
		endif;

        $topic_posts = $this->db->select('COUNT(1) as total_topics')
                                ->get_where('topic_posts', array('topic_id' => $id));

        switch($this->data_response)
		{
			case 'object':
				$topic_posts = $topic_posts->row()->total_topics;
			break;
			default:
				$topic_posts = $topic_posts->result_array();
				$topic_posts = $topic_posts[0]['total_topics'];
			break;
		}

		return $topic_posts;
    }

    // --------------------------------------------------------------------

    /**
     * New page
     *
     * New page description
     *
     * @access  public
     * @param   none
     * @return  redirect
     * @route   n/a
     */

    function get_topic_posts($topic_data = array(), $start = 0, $limit = 0)
    {
        $total_page_percentage = floor(($start/$topic_data['total_posts'])*100);

        $topic_posts_id = $this->db->select('topic_posts.post_id, topic_posts.topic_post_id, topic_posts.topic_id')
                                    ->where('topic_id', $topic_data['topic_id'])
                                    ->where('topic_post_id >', $start)
                                    ->order_by('topic_posts.post_id', 'asc')
                                    ->limit($limit)
                                    ->get('topic_posts')
                                    ->result_array();

        $posts_id = array();
        foreach($topic_posts_id as $post):
            $posts_id[$post['topic_post_id']] = $post['post_id'];
        endforeach;

        ksort($posts_id);

        $post_data = $this->db->select('topics.topic_id,
										 topic_posts.post_id,
										 topic_posts.topic_id,
										 topic_posts.author_id,
										 topic_posts.author_ip,
										 topic_posts.post_time,
										 topic_posts.number_of_edits,
										 topic_posts.lock_edits,
										 topic_posts.updated_by,
										 users.username,
										 users.user_signature,
										 users.user_level,
										 users.last_action,
										 users.donated,
										 users.user_id,
										 topic_post_text.text as post_body')
         						->where_in('topic_posts.post_id', array_values($posts_id))
								->join('topics', 'topics.topic_id = topic_posts.topic_id')
								->join('users', 'users.user_id = topic_posts.author_id')
								->join('topic_post_text', 'topic_post_text.post_id = topic_posts.post_id')
								->get('topic_posts');

		return $post_data->result_array();
    }

    // --------------------------------------------------------------------

    /**
     * New page
     *
     * New page description
     *
     * @access  public
     * @param   none
     * @return  redirect
     * @route   n/a
     */

    function create_post($message = '', $topic_data = array(), $user_id = 0)
    {
        if(isset($topic_data[0])){
            $topic_data = $topic_data[0];
        }

        $post_data = array(
			'topic_id' => $topic_data['topic_id'],
			'topic_post_id' => ($topic_data['total_posts']+1),
			'author_id' => $user_id,
       	    'author_ip' => $this->input->ip_address(),
			'forum_id' => $topic_data['forum_id']
		);

        $this->db->set('post_time', 'NOW()', false);
        $this->db->insert('topic_posts', $post_data);

        $post_id = $this->db->insert_id();

        if((time()-strtotime($this->system->userdata['register_date'])) < 86400 ):
            $message = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", '', $message);
            $message = preg_replace("(\[IMG\](.+?)\[\/IMG\])is",'',$message);
            $message = preg_replace("(\[img\](.+?)\[\/img\])is",'',$message);
        	$message = preg_replace("(\[imgleft\](.+?)\[\/imgleft\])is",'',$message);
        	$message = preg_replace("(\[imgright\](.+?)\[\/imgright\])is",'',$message);
        	$message = preg_replace("(\[imgcenter\](.+?)\[\/imgcenter\])is",'',$message);
        endif;

		$post_message_data = array(
		    'post_id' => $post_id,
		    'text' => $message
		);

		$this->db->insert('topic_post_text', $post_message_data);

        $new_topic_data = array(
            'last_post_username' => $this->system->userdata['username'],
            'total_posts' => $topic_data['total_posts']+1
        );

        $this->db->set('last_post', 'NOW()', false);
        $this->db->where('topic_id', $topic_data['topic_id']);
        $this->db->update('topics', $new_topic_data);

        return $post_id;
    }


    // --------------------------------------------------------------------

    /**
     * New page
     *
     * New page description
     *
     * @access  public
     * @param   none
     * @return  redirect
     * @route   n/a
     */

    function get_post_data($post_id = 0)
    {
        if($post_id == 0):
			$post_id = $this->post_id;
		else:
			$this->post_id = $post_id;
		endif;

		$post_data = $this->db->select('topic_posts.*, topic_post_text.text as post_body, users.username')
							  ->join('topic_post_text', 'topic_posts.post_id = topic_post_text.post_id')
							  ->join('users', 'topic_posts.author_id = users.user_id')
							  ->get_where('topic_posts', array('topic_posts.post_id' => $post_id));

		switch($this->data_response)
		{
			case 'object':
				$post_data = $post_data->result();
			break;
			case 'json':
				$post_data = json_encode($post_data->result_array());
			break;
			default:
				$post_data = $post_data->result_array();
			break;
		}

		return $post_data;

    }

    // --------------------------------------------------------------------

    /**
     * Count posts before
     *
     * Mostly used to find which page the post belongs to.
     *
     * @access  public
     * @param   array
     * @return  int
     * @route   n/a
     */

    function count_posts_before($post_data = array(), $post_id = 0)
    {
        if(isset($post_data[0]))
        {
            $post_data = $post_data[0];
        }

        $query = $this->db->select('COUNT(1) as total_posts')
                          ->where('topic_id', $post_data['topic_id'])
                          ->where('post_id', $post_data['post_id'])
                          ->get('topic_posts')
                          ->row();

        return $query->total_posts;
    }


    // --------------------------------------------------------------------

    /**
     * New page
     *
     * New page description
     *
     * @access  public
     * @param   none
     * @return  redirect
     * @route   n/a
     */

    function update_post($update_data = array(), $post_id = 0, $topic_id = 0, $update_change = true)
    {
        if($post_id == 0):
			$post_id = $this->post_id;
		else:
			$this->post_id = $post_id;
		endif;

        $update_where = array('post_id' => $post_id);

        if(isset($update_data['post_title']))
        {
        	$update_topic_where = array('topic_id' => $topic_id);
            $this->db->update('topics', array('topic_title' => $update_data['post_title']), $update_topic_where);
        }

        $this->db->update('topic_post_text', array('text' => $update_data['post_body']), $update_where);

		$topic_post_to_update = array();

	   if($update_change == true){
	  		$topic_post_to_update = array(
	        							'author_ip' => $update_data['author_ip'],
	        							'updated_by' => $update_data['updated_by'],
	        							'number_of_edits' => $update_data['number_of_edits']
	        						);
			$this->db->set('update_time', 'NOW()', false);
			$this->db->update('topic_posts', $topic_post_to_update ,$update_where);
	   }

    }

    // --------------------------------------------------------------------

	/**
	 * Log previous edit
	 *
	 * Logs previous edits for a post
	 *
	 * @access	public
	 * @param	none
	 * @return	redirect
	 * @route	n/a
	 */

	function log_prevous_edit($post_data = array())
	{

		$insert_array = array(
								'post_body' => $post_data['post_body'],
								'post_id' => $post_data['post_id'],
								'post_author_ip' => $post_data['author_ip']
							);
		if(isset($post_data['updated_by']) && $post_data['updated_by'] != NULL){
			$insert_array['author_id'] =  $post_data['updated_by'];
		}else{
			$insert_array['author_id'] =  $post_data['author_id'];
		}

		if(isset($post_data['updated_time']) && $post_data['updated_time'] != NULL){
			$insert_array['post_time'] = $post_data['updated_time'];
		}else{
			$insert_array['post_time'] = $post_data['post_time'];
		}

		$this->db->insert('topic_post_previous_edits', $insert_array);

	}


    // --------------------------------------------------------------------

    /**
     * New page
     *
     * New page description
     *
     * @access  public
     * @param   none
     * @return  redirect
     * @route   n/a
     */

    function first_topic_post($post_id = 0, $topic_data = 0)
    {
        $get_last_post = $this->db->select('post_id')
                                  ->where('topic_id', $topic_data)
                                  ->order_by('post_id', 'ASC')
                                  ->limit(1)
                                  ->get('topic_posts')
                                  ->row()
                                  ->post_id;

        if($get_last_post == $post_id)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }


	// --------------------------------------------------------------------

	/**
	 * New page
	 *
	 * New page description
	 *
	 * @access	public
	 * @param	none
	 * @return	redirect
	 * @route	n/a
	 */

	function get_posts_since($since = '', $topic_id = 0, $user_id = 0)
	{
		$query = $this->db->select('topic_posts.post_id,
									topic_posts.topic_id,
								 	topic_posts.author_id,
								 	topic_post_text.text as post_body,
								 	topic_posts.post_time,
								 	users.username,
								 	users.user_signature,
								 	users.user_level,
								 	users.donated,
								 	users.last_action,
								 	users.user_id')
						  ->from('topic_posts')
						  ->join('users', 'topic_posts.author_id = users.user_id')
						  ->join('topic_post_text', 'topic_post_text.post_id = topic_posts.post_id')
						  ->where('topic_posts.topic_id', $topic_id)
					  	  ->where('topic_posts.author_id !=', $user_id)
					  	  ->where('topic_posts.post_time >', $since)
						  ->order_by('topic_posts.post_id', 'asc')
						  ->get();

		return $query->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * ID to username
	 *
	 * Converts a user ID into their username
	 *
	 * @param	n/a
	 * @return	n/a
	 */

	function user_id_to_username($user_id)
	{
		return 	$this->db->select('username')
						->from('users')
						->where('user_id', $user_id);
	}





	// --------------------------------------------------------------------

	/**
	 * Topic users online
	 *
	 * Checks if the user is online
	 *
	 * @param	n/a
	 * @return	n/a
	 */

	function topic_user_online($user_id)
	{
		$user = $this->db->query('SELECT user_id, last_action FROM users WHERE user_id = '.$user_id)->row();
	//	$user =	$this->db->select('user_id', 'last_action')
	//					->where('user_id', $user_id)
	//					->from('users')
	//					->row();

		if($user->last_action > time()-2400 ){
			return TRUE;
		} else {
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Format Topic URI
	 *
	 * Creates a safe URI
	 *
	 * @param	strings
	 * @return	string
	 */


	function format_topic_uri($to_format){

		return preg_replace('/ /', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', htmlspecialchars_decode(trim(strtolower($to_format)))));
	}

	// --------------------------------------------------------------------

	/**
	 * Lock or Unlock Topic
	 *
	 * Will lock/unlock a topic
	 *
	 * @param	n/a
	 * @return	n/a
	 */


	function lock_or_unlock_topic($topic_id)
	{

		$topic_status = $this->db->select('topic_status')
                                  ->where('topic_id', $topic_id)
                                  ->limit(1)
                                  ->get('topics')
                                  ->row()
                                  ->topic_status;
		if($topic_status == 'unlocked')
		{
			$this->lock_topic($topic_id);
		}

		if($topic_status == 'locked')
		{
			$this->unlock_topic($topic_id);
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Lock topic
	 *
	 * Will lock a topic -- Only lock!
	 *
	 * @param	Topic ID
	 */




	function lock_topic($topic_id)
	{

		$this->db->update('topics', array('topic_status' => 'locked'), array('topic_id' => $topic_id));

	}


	// --------------------------------------------------------------------

	/**
	 * Unlock topic
	 *
	 * Will lock a UNLOCK -- Only unlock!
	 *
	 * @param	Topic ID
	 */




	function unlock_topic($topic_id)
	{

		$this->db->update('topics', array('topic_status' => 'unlocked'), array('topic_id' => $topic_id));

	}
	// --------------------------------------------------------------------

	/**
	 * Move topic
	 *
	 * Move a topic to a new thread
	 *
	 * @param	Topic ID, Move to topic ID
	 */




	function move_topic($topic_id, $move_to_id)
	{
		$this->db->update('topics', array('forum_id' => $move_to_id), array('topic_id' => $topic_id));
		$this->db->update('topic_posts', array('forum_id' => $move_to_id), array('topic_id' => $topic_id));

	}



	// --------------------------------------------------------------------

	/**
	 * Sticky
	 *
	 * Will make toggle sticky-ness
	 *
	 * @param	n/a
	 * @return	n/a
	 */


	function sticky($topic_id)
	{

		$topic_type = $this->db->select('topic_type')
                                  ->where('topic_id', $topic_id)
                                  ->limit(1)
                                  ->get('topics')
                                  ->row()
                                  ->topic_type;
		if($topic_type == NULL)
		{
			$this->make_sticky($topic_id);
		}

		if($topic_type == 'sticky')
		{
			$this->make_unsticky($topic_id);
		}

	}

	// --------------------------------------------------------------------

	/**
	 * New Function
	 *
	 * Function Description
	 *
	 * @access	public
	 * @param	none
	 * @return	redirect
	 * @route	n/a
	 */

	function make_sticky($topic_id)
	{

		$this->db->update('topics', array('topic_type' => 'sticky'), array('topic_id' => $topic_id));

	}

	// --------------------------------------------------------------------

	/**
	 * New Function
	 *
	 * Function Description
	 *
	 * @access	public
	 * @param	none
	 * @return	redirect
	 * @route	n/a
	 */

	function make_unsticky($topic_id)
	{
		$this->db->update('topics', array('topic_type' => ''), array('topic_id' => $topic_id));
	}





	// --------------------------------------------------------------------

	/**
	 * Delete Post
	 *
	 * Will lock/unlock a topic
	 *
	 * @param	n/a
	 * @return	n/a
	 */


	function delete_post($post_data, $topic_data)
	{

		$tables = array('topic_posts', 'topic_post_text');
		$this->db->where('post_id', $post_data['post_id']);
		$this->db->limit(1);
		$this->db->delete($tables);



      $new_topic_data = array(
		'total_posts' => $topic_data['total_posts']-1
		);
        $this->db->where('topic_id', $topic_data['topic_id']);
        $this->db->update('topics', $new_topic_data);

  		return;

	}

	// --------------------------------------------------------------------

	/**
	 * Get Previous Edits
	 *
	 * Function Description
	 *
	 * @access	public
	 * @param	none
	 * @return	redirect
	 * @route	n/a
	 */

	 function get_previous_edits($post_id = 0)
	 {
	   $edits = $this->db->get_where('topic_post_previous_edits', array('post_id' => $post_id))->result_array();
	   if(isset($edits[0])){
	   		return $edits;
	   }else{
	   		return false;
	   }
	 }



	// --------------------------------------------------------------------

	/**
	 * Convert array to an object
	 *
	 * Alright, I'll admit this completely kills one of the main
	 * purposes of using different formats in the first place, *speed*
	 * but on very special cases, it's the simplest solution.
	 *
	 * @access	private
	 * @param	n/a
	 * @return	object
	 */

	function _array_to_object($array = array())
	{
		if (!isset($array[0])) // isset() has proven to be the fastest to me.
		{
			$data = false;
			foreach ($array as $key => $val)
			{
				$data->{$key} = $val;
			}
			return $data;
		}
		return false;
	}




	// --------------------------------------------------------------------

	/**
	 * Purge Cache
	 *
	 * Sometimes cache's go cold, and they need to be purged for their own good
	 *
	 * @access	private
	 * @param	n/a
	 * @return	n/a
	 */

	function _purge_cache()
	{
		if($this->memcached == TRUE):
			foreach($this->cache_durations as $cache => $time):
				$this->cache->remove($this->cache_prefix.$cache);
			endforeach;
		endif;
	}

}

/* End of file forum_engine.php */
/* Location: ./system/application/models/forum_engine.php */