<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	public function index()
	{
		if ($this->session->userdata['user_id']):

			$notification_query = $this->db->limit(5)->order_by('timestamp', 'DESC')->get_where('notifications', array('receiver_id' => $this->system->userdata['user_id']))->result_array();

			if ( ! $latest_topics = $this->cache->get('dashboard_topics')):
				$latest_topics = $this->load_recent_topics();
				$this->cache->save('dashboard_topics', $latest_topics, 3);
			endif;

			$latest_announcement = $this->db->query('SELECT topics.last_post,
													  topics.topic_title,
													  topics.topic_id,
													  topics.forum_id,
													  topics.topic_author,
													  topics.topic_status,
													  topics.topic_time,
													  forums.forum_name
												FROM  topics
												JOIN forums ON topics.forum_id = forums.forum_id
												AND forums.forum_id = 1
												ORDER BY topics.topic_id DESC
												LIMIT 1')->row_array();

			$this->cache->delete('spotlight_topic');
			if ( ! $spotlight_topic = $this->cache->get('spotlight_topic')):
				$spotlight_topic = $this->db->limit(1)
											->join('topics', 'topics.topic_id = spotlight_topics.topic_id')
											->join('users', 'topics.topic_author = users.user_id')
											->order_by('spotlight_topics.timestamp', 'DESC')
											->get('spotlight_topics')
											->row_array();

				$this->cache->save('spotlight_topic', $spotlight_topic, 900);
			endif;

			$view_data = array(
				'page_title' => 'Homepage',
				'page_body' => 'home',
				'notifications' => $notification_query,
				'latest_topics' => $latest_topics,
				'latest_announcement' => $latest_announcement,
				'spotlight_topic' => $spotlight_topic
			);

			$this->system->view_data['scripts'][] = '/global/js/home/index.js';
			$this->system->quick_parse('home/index', $view_data);
		else:
			foreach ($_COOKIE as $name => $value):
			    delete_cookie($name);
			endforeach;

			$this->load->view('home/landing_page');
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

	public function debug()
	{
		$contestants = array(14586, 12040, 343, 15219, 6083, 3001, 111, 13412, 346, 1639, 12143, 14684, 12191, 2576, 13089, 9956, 10217, 12407, 7788, 12640, 12733, 2672, 12335, 13133, 13980, 5654, 10506, 6155, 12361, 14954, 5489, 13116, 13611, 1427, 13601, 3250, 5004, 11725, 3790, 3419, 1547, 12051, 10520, 11672, 35, 54, 15028, 4804, 10949, 13627, 4842, 14308, 10998, 14206, 1249, 12000, 13167, 8183, 15022, 4750, 12650, 11904, 43, 12999, 1818, 5033, 7907, 60, 10150, 5566, 5514);

		$users = $this->db->where_in('user_id', $contestants)->get('users')->result_array();
		foreach ($users as $user_data):
			$this->notification->broadcast(array(
				'receiver' => $user_data['user_id'],
				'receiver_id' => $user_data['username'],
				'notification_text' => 'You have been refunded for your Avatar of the Month entry.',
				'attachment_id' => 0,
				'attatchment_type' => 'announcement',
				'attatchment_url' => '/topic/view/29011/10',
			), FALSE);
		endforeach;
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

	public function load_recent_topics()
	{
		if ( ! $latest_topics = $this->cache->get('dashboard_topics')):
			$latest_topics = $this->db->select('topics.last_post,
												topics.topic_title,
												topics.topic_id,
												topics.forum_id,
												topics.topic_author,
												topics.topic_status,
												forums.forum_name')
										->from('topics')
										->join('forums', 'topics.forum_id = forums.forum_id')
										->where(array('forums.forum_id !=' => 7, 'forums.staff !=' => 1))
										->order_by('topics.last_post', 'DESC')
										->limit(6)
										->get()
										->result_array();

			$this->cache->save('dashboard_topics', $latest_topics, 2);
		endif;

	    if( ! $this->input->is_ajax_request()):
	    	return $latest_topics;
	    else:
	    	$html = $this->load->view('partials/dashboard_topics', array('latest_topics' => $latest_topics), TRUE);
	    	$this->system->parse_json(array('html' => $html));
	    endif;
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */