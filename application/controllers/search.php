<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SearchType {
	const ITEMS = 'items';
	const TOPIC_TITLES = 'topic_titles';
	const SITE_FEATURES = 'site_features';
	const USERS = 'users';
	const MAILBOX = 'maillbox';
}

class Search extends CI_Controller
{
	private static $SEARCH_LIMIT = 5;

	public function __construct() {
		parent::__construct();
		$this->load->helper('form');
	}

	// --------------------------------------------------------------------

	/**
	 * Home page
	 *
	 * Search main function
	 *
	 * @access  public
	 * @param   none
	 * @return  view
	 * @route   n/a
	 */

	public function index()
	{
		$view_data = array(
			'page_title' => 'Search',
			'page_body' => 'search'
		);

		$this->system->quick_parse('search/index', $view_data);
	}

	/**
	*
	* @return JSON array of results USERS*/
	public function get_items($search_key = '', $search_type = SearchType::ITEMS) {
		assert(!empty($search_key));
		$data = array(
			'search_type' => $search_type,
			'results' => array(),
		);
		switch ($search_type):
			case SearchType::ITEMS:
				$data['results'] = $this->_search_items($search_key);

				foreach ($data['results'] as $key => $item):
					$data['results'][$key]['name'] .= ' - '.$item['layer'].'/'.$this->db->get_where('avatar_layers', array('id' => $item['layer']))->row()->name;
				endforeach;
			break;
			case SearchType::TOPIC_TITLES:
				$data['results'] = $this->_search_topic_titles($search_key);
			break;
			case SearchType::SITE_FEATURES:
				$data['results'] = $this->_search_site_features($search_key);
			break;
			case SearchType::USERS:
				$data['results'] = $this->_search_users($search_key);
			break;
			case SearchType::MAILBOX:
				$data['results'] = $this->_search_mailbox($search_key);
			break;
		endswitch;

		if($this->input->is_ajax_request()):
			$this->system->parse_json($data);
		else:
			$this->output->enable_profiler(true);
		endif;
	}

	public function get_all_items($search_key = '', $search_type = SearchType::ITEMS) {

	}

	private function _search_items($key) {
		$this->load->model(array('ShopItem', 'MarketplaceItem', 'AvatarItem'));
		$this->AvatarItem->name = $key;
		return $this->AvatarItem->findAvailable();
	}

	private function _search_topic_titles($key) {
		return $this->db->select('topic_author, topic_title, topics.forum_id, topic_id, total_posts, forum_name, username, LOWER(name) as category_name')
			->join('forums', 'forums.forum_id = topics.forum_id')
			->join('categories', 'forums.parent_id = categories.id')
			->join('users', 'users.user_id = topics.topic_author')
			->like('topic_title', $key)
			->where('forums.staff', 0)
			->order_by('total_posts', 'desc')
			->order_by('last_post', 'desc')
			->get('topics', self::$SEARCH_LIMIT)
			->result_array();
	}

	private function _search_site_features($key) {
		return $this->db->select('nav_id, nav_url, nav_text')
			->like('nav_text', $key)
			->get('site_navigation', self::$SEARCH_LIMIT)->result_array();
	}

	private function _search_users($key) {
		return $this->db->select('user_id, username, user_level, last_action')
			->like('username', $key)
			->get('users', self::$SEARCH_LIMIT)->result_array();
	}

	private function _search_mailbox($key) {
		return $this->db->select('sender, subject, date')
			->where('id', $this->session['user_id'])
			->like('subject', $key)
			->get('mail', self::$SEARCH_LIMIT)
			->result_array();
	}
}

/* End of file Search.php */
/* Location: ./system/application/controllers/Search.php */