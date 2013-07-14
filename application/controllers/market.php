<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Market extends CI_Controller
{
	var $tyi_items = array(20579, 20565, 2330, 2329, 3340, 3339, 4722, 4723, 3483, 4739, 4740, 5229, 5228, 5233, 5232, 5398, 5399, 6080, 6081, 7118, 7117, 7269, 7268, 7907, 7921, 7932, 7938, 8545, 8554, 9865, 9875, 9881, 9891, 10400, 10403, 10530, 10539, 12164, 12174, 12177, 12183, 15516, 15520, 16473, 16479, 16496, 16498, 16536, 16533, 18720, 18728, 20438, 20442, 20478, 20486, 20506, 20507, 20542, 20536, 20560, 20549);
	var $milestone_items = array(2058, 2059, 2063, 2064, 2060, 2061, 2062, 3321, 3320, 3319, 3318, 3317, 3322, 3478, 3479, 3481, 3480, 3482, 4733, 4730, 4731, 4732, 5781, 5782, 5783, 5784, 5785, 7265, 7264, 7266, 7263, 7267, 20446, 20443, 20447, 20444, 20445);
	var $event_items = array(2328, 2234, 2235, 3475, 3477, 3472, 3473, 3474, 4705, 4706, 4709, 4707, 4708, 4704, 4724, 4725, 4726, 4727, 4728, 4729, 5230, 5392, 5396, 5397, 6059, 6060, 6061, 6062, 6063, 7906, 7905, 8431, 8430, 8432, 8433, 8434, 8435, 9188, 9194, 9187, 9178, 9197, 9200, 9204, 9233, 9181, 9898, 9896, 9893, 9894, 4703, 9895, 10428, 10438, 10415, 10412, 10425, 10422, 10429, 10435, 10525, 12175, 16490, 16487, 16482, 16488, 16485, 16484, 16525, 16509, 16501, 16513, 16512, 16502, 16517, 16503, 16504, 16507, 16516, 16527, 16523, 18730, 18731, 18732, 18733, 18734, 18735, 18736, 18737, 18738, 18739, 18740, 18741, 18742, 18743, 18744, 20490, 20491, 20492, 20493, 20494, 20495, 20496, 20497, 20498, 20499, 20500, 20471, 20472, 20470, 20464, 20474, 20466, 20456, 20517, 20518, 20519, 20512, 20514, 20520, 20531, 20526, 20533, 20530, 20528, 20532);
	var $default_hours = 24;
	var $listing_fee = 0.045;

	function __construct()
	{
	    parent::__construct();
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

	public function index()
	{
		if( ! $this->session->userdata('user_id')) redirect('auth/signin');

		$this->_refund_finished_items($this->system->userdata['user_id']);

		// if ($this->input->get('your_auctions')):
		// 	$marketplace_items = $this->db->order_by('id', 'DESC')->get_where('marketplace_items', array(
		// 		'finishes_at >' => date('Y-m-d H:i:s', time()),
		// 		'purchased' => 0,
		// 		'cancled' => 0,
		// 		'user_id' => $this->system->userdata['user_id']
		// 	));

		// 	$view_data = array(
		// 		'page_title' => 'Your marketplace auctions',
		// 		'page_body' => 'marketplace your_auctions',
		// 		'items' => $marketplace_items->result_array()
		// 	);

		// 	$this->system->quick_parse('market/your_auctions', $view_data);
		// else:
		// 	$total_rows = $this->db->get_where('marketplace_items', array(
		// 		'finishes_at >' => date('Y-m-d H:i:s', time()),
		// 		'purchased' => 0,
		// 		'cancled' => 0
		// 	))->num_rows();

		// 	$this->load->library('pagination');
		// 	$config['base_url'] = site_url('marketplace/index/');
		// 	$config['total_rows'] = $total_rows;
		// 	$config['per_page'] = 18;
		// 	$config['uri_segment'] = 3;
		// 	$this->pagination->initialize($config);

		// 	$marketplace_items = $this->db->limit($config['per_page'], $this->uri->segment(3, 0))->order_by('id', 'DESC')->get_where('marketplace_items', array(
		// 		'finishes_at >' => date('Y-m-d H:i:s', time()),
		// 		'purchased' => 0,
		// 		'cancled' => 0
		// 	));

			$view_data = array(
				'page_title' => 'The Market',
				'page_body' => 'marketplace all_auctions',
				// 'items' => $marketplace_items->result_array(),
				// 'pagination_configuration' => $config
			);

			$this->system->quick_parse('layout/uc', $view_data);

			// $this->system->quick_parse('market/index', $view_data);
		// endif;
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

	public function view_item($item_id = 0)
	{
	    // What will you fill me with?! :D
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

	public function view_seller($user_id = 0)
	{
	    // What will you fill me with?! :D
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

	public function purchase_item()
	{
		if( ! $this->session->userdata('user_id')) redirect('signin');

	    if($_SERVER['REQUEST_METHOD'] == "POST"):
	    	if( ! is_numeric($auction_id = $this->input->post('auction_id'))) show_error('auction id must be a valid number');
	    	if ($this->cache->get('recently_closed_'.$auction_id)):
	    		$this->session->set_flashdata('notice', 'You were just a second late! A more fortunate user managed to purchase that auction right before you.');
	    		redirect('marketplace/?just_missed_it=1');
	    	endif;

	    	$marketplace_item = $this->db->where(array('id' => $auction_id))->get('marketplace_items');

	    	if($marketplace_item->num_rows() > 0):
	    		$auction_data = $marketplace_item->row_array();
	    	else:
	    		show_error('This item could not be found!');
	    	endif;

	    	if($auction_data['user_id'] == $this->system->userdata['user_id']) show_error('You cannot purchase your own items!');
	    	if($auction_data['price'] > $this->system->userdata['user_palladium']) show_error('You do not have enough palladiun to purchase this item!');
	    	if($auction_data['purchased'] == 1 || $auction_data['cancled'] == 1) redirect('marketplace/?just_missed_it=1');

				$this->cache->save('recently_closed_'.$auction_id, TRUE, 60);

	    	// remove the item from the market and set the purchased data
	    	$marketplace_items = array(
					'purchased'    => 1,
					'purchased_by' => $this->system->userdata['user_id'],
	    	);

	    	$this->db->set('completed_at', 'NOW()', false)->where('id', $auction_id)->update('marketplace_items', $marketplace_items);

	    	$this->load->model('user_engine');
	    	$this->user_engine->add_item($auction_data['item_id'], 1, $this->system->userdata['user_id']);

	    	// Give the user their gold and remove the tax!
	    	$received_total = round($auction_data['price']-round($auction_data['price']*$this->listing_fee));
	    	$new_palladium = $this->user_engine->add_palladium($received_total, $auction_data['user_id']);
	    	$removed_palladium = $this->user_engine->remove_palladium($received_total, $this->system->userdata['user_id']);

	    	$this->system->notification($auction_data['user_id'], 'Hello, '.$auction_data['username'].'!

The "'.$auction_data['item_name'].'" you had on auction for '.number_format($auction_data['price']).' palladium has been bought by [url=/user/'.urlencode($this->system->userdata['username']).']'.$this->system->userdata['username'].'[/url].

You previously had '.number_format($new_palladium-$received_total).' palladium, you now have [b]'.number_format($new_palladium).'[/b]! (A total earning of '.number_format($received_total).')');

	    	$random_quotes = array(
	    		'bask in it\'s glory',
	    		'sport it out',
	    		'enjoy it',
	    		'try it on',
	    		'add it to your stylish avatar',
	    		'give it a try',
	    		'have fun with it',
	    	);

	    	// $this->session->set_flashdata('success', 'You have purchased the '.$auction_data['item_name'].' for a total of '.$auction_data['price'].'. You should check out your inventory and '.$random_quotes[array_rand($random_quotes)].'!');
	    	redirect('marketplace/index/'.$this->input->post('page'));
	    	// redirect to success message!
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

	public function remove_item()
	{
		if( ! $this->session->userdata('user_id')) redirect('signin');

		if($_SERVER['REQUEST_METHOD'] == "POST"):
			if( ! is_numeric($auction_id = $this->input->post('auction_id'))) show_error('auction id must be a valid number');
			if ($this->cache->get('recently_closed_'.$auction_id)):
				$this->session->set_flashdata('notice', 'You were just a second late! A more fortunate user managed to purchase that auction right before you.');
				redirect('marketplace/?just_missed_it=1');
			endif;

			$marketplace_item = $this->db->where(array('id' => $auction_id))->get('marketplace_items');

			if($marketplace_item->num_rows() > 0):
				$auction_data = $marketplace_item->row_array();
			else:
				show_error('This item could not be found!');
			endif;

			if($auction_data['user_id'] != $this->system->userdata['user_id']) show_error('You cannot cancel other auctions!');
			if($auction_data['purchased'] == 1 || $auction_data['cancled'] == 1) show_error('You cannot cancel completed auctions!');

			$this->cache->save('recently_closed_'.$auction_id, TRUE, 60);

			// remove the item from the market and set the purchased data
			$this->db->set('completed_at', 'NOW()', false)->where('id', $auction_id)->update('marketplace_items', array('cancled' => 1));

			$this->load->model('user_engine');
			$this->user_engine->add_item($auction_data['item_id'], 1, $auction_data['user_id']);

			$this->system->notification($auction_data['user_id'], 'Hi, '.$auction_data['username'].'

This is a confirmation of your "'.$auction_data['item_name'].'" auction being removed. Although we did not charge the listing fee, we do plan on charging the '.($this->listing_fee*100).'% fee for cancled items in the near future.

Be wise before removing items quickly in the future!');

			// $this->session->set_flashdata('success', 'You have have cancled the '.$auction_data['item_name'].' auction');
			redirect('marketplace');
			// redirect to success message!
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

	public function sell_item()
	{
		if( ! $this->session->userdata('user_id')) redirect('signin');

		if($_SERVER['REQUEST_METHOD'] == "POST"):
			$item_id = $this->input->post('item_id');
			if( ! is_numeric($item_id)) show_error('item_id must be a valid number');
			if( ! is_numeric($this->input->post('price'))) show_error('price must be a valid number');

			$nr_in_hour = $this->db->select('username')
								   ->where('published_at >=', 'DATE_SUB(NOW(),INTERVAL 1 HOUR)')
								   ->where('user_id =', $this->system->userdata['user_id'])
								   ->where('cancled =','0')
								   ->get('marketplace_items');

			if($nr_in_hour->num_rows() >= 4) show_error('You are limited to auctioning 4 items per hour!');

			$avatar_item_query = $this->db->select('name, thumb, item_id')->where(array('item_id' => $item_id))->get('avatar_items');

			if($avatar_item_query->num_rows() > 0):
				$item_data = $avatar_item_query->row_array();
			else:
				show_error('Item could not be found!');
			endif;

			$user_item = $this->db->where(array('soft_deleted' => 0, 'item_id' => $item_id, 'user_id' => $this->system->userdata['user_id']))->get('user_items');

			if($user_item->num_rows() > 0):
				$user_item_data = $user_item->row_array();
			else:
				show_error('Item owned could not be found!');
			endif;

			$this->load->model('user_engine');
			$items_removed = $this->user_engine->remove_item($item_id, 1, $this->system->userdata['user_id']); // The returned value is for debugging purposes, we already know they own it

			$item_type = $this->_process_item_type($item_id);

			$avatar_items_relation = $this->db->where(array('parent_id' => $this->input->post('item_id')))->get('avatar_items_relations');

			if($avatar_items_relation->num_rows() > 0):
				// $item_data['name'] = trim(preg_replace('/\(.*\)/', '(full set)', $item_data['name']));
			// elseif($avatar_items_relation->num_rows() > 0 && $item_type == 'common'):
				$item_data['name'] = trim(preg_replace('/\(.*\)/', '', $item_data['name']));
			endif;

			$marketplace_items = array(
				'user_id' => $this->system->userdata['user_id'],
				'username' => $this->system->userdata['username'],
				'item_id' => $this->input->post('item_id'),
				'item_thumbnail' => $item_data['thumb'],
				'item_name' => $item_data['name'],
				'price' => $this->input->post('price'),
				'item_type' => $item_type,
				'finishes_at' => date('Y-m-d H:i:s', (time()+($this->default_hours*60*60))),
			);

			$this->db->set('published_at', 'NOW()', false)->insert('marketplace_items', $marketplace_items);

			$this->cache->save('marketplace_last_item', date('Y-m-d H:i:s', time()));

			redirect('marketplace/?your_auctions=1');
		else:
			$this->load->model('trade_engine');

			$view_data = array(
				'page_title' => 'Auction an item',
				'page_body' => 'marketplace sell_item',
				'inventory' => $this->trade_engine->get_user_inventory($this->system->userdata['user_id'])
			);

			$this->system->quick_parse('market/sell_item', $view_data);
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

	public function search()
	{
	  	$order_by = 'published_at';

    	if( ! preg_match("/^([a-zA-Z0-9:\s\(\)\'])+$/i", $query = $this->input->get('q'))) show_error('Invalid datatype');

    	if($val = preg_match('/(by:)+([a-zA-Z]){2,10}/i', $query, $match)):
    	   $query = trim(preg_replace('/(by:)+([a-zA-Z]){2,10}/i', '', $query));
    	   $search_types = array('price' => 'price', 'ending' => 'finishes_at', 'time' => 'finishes_at', 'started' => 'published_at');
    	   $term = explode(':', $match[0]);
    	   $term = $term[1];
    	  if(in_array($term, $search_types)):
    	       $order_by = $search_types[$term];
    	   else:

    	  endif;
    	endif;

    	// Know what would be cool? To have it list your friend's items first!
    	$items = $this->db->select('id, username, item_thumbnail, item_name, price, item_type, item_id, finishes_at, published_at')
					  					  ->where('LOWER(item_name) LIKE LOWER("%'.$query.'%")')
											  ->where('user_id !=', $this->system->userdata['user_id'])
											  ->order_by($order_by, 'desc')
											  ->limit(32)
											  ->get_where('marketplace_items', array(
													'finishes_at >' => date('Y-m-d H:i:s', time()),
													'purchased' => 0,
													'cancled' => 0
											  ))->result_array();

			$formatted_items = array();
			foreach ($items as $key => $item):
				$formatted_items[$key] = array(
					'button_class'   => (($this->system->userdata['user_palladium'] >= $item['price']) ? 'buy_now_button' : 'inactive_buy_now_button'),
					'time_left'      => human_time($item['finishes_at'], TRUE),
					'time_published' => human_time($item['published_at']),
					'url_username'   => urlencode($item['username'])
				);

				$formatted_items[$key] = array_merge($formatted_items[$key], $item);
			endforeach;

    	$this->output->set_content_type('application/json')->set_output(json_encode($formatted_items, JSON_NUMERIC_CHECK));
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

	public function _process_item_type($item_id = 0)
	{
	    if(in_array($item_id, $this->tyi_items)) return 'tyi';
	    if(in_array($item_id, $this->milestone_items)) return 'milestone';
	    if(in_array($item_id, $this->event_items)) return 'event';

	    $shop_item = $this->db->where(array('item_id' => $item_id))->limit(1)->get('shop_items');

	    if($shop_item->num_rows() > 0):
		    return 'common';
	    else:
		    return 'other';
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

	public function _refund_finished_items($user_id = 0)
	{
	    $expired_items = $this->db->get_where('marketplace_items', array(
				'finishes_at <=' => date('Y-m-d H:i:s', time()),
				'purchased'  => 0,
				'cancled'    => 0,
				'user_id'    => $this->system->userdata['user_id']
	    ))->result_array();

	    foreach ($expired_items as $auction_data):
			$this->cache->save('recently_closed_'.$auction_data['id'], TRUE, 60);

		    $this->system->userdata['new_mail'] += 1;

			// remove the item from the market and set the purchased data
			$this->db->set('completed_at', 'NOW()', false)->where('id', $auction_data['id'])->update('marketplace_items', array('cancled' => 1));

			$this->load->model('user_engine');
			$this->user_engine->add_item($auction_data['item_id'], 1, $auction_data['user_id']);

			$this->system->notification($auction_data['user_id'], 'Hi, '.$auction_data['username'].'

This is a notice of your "'.$auction_data['item_name'].'" auction being expired from the marketplace. We\'ve put the item gently into your inventory, should you consider to keep it.

Happy auctioning!');

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

	public function get_price_suggestion()
	{
		if( ! is_numeric($item_id = $this->input->post('id'))) show_error('item id must be valid');

		$item_data = $this->db->select('price, published_at')->order_by('price', 'desc')->limit(4)->get_where('marketplace_items', array('purchased' => 1, 'item_id' => $item_id))->result_array();

		$item_type = $this->_process_item_type($item_id);

		foreach ($item_data as $key => $data):
			$item_data[$key]['published'] = human_time($data['published_at']);
			// $item_data[$key]['price'] = number_format($data['price']);
			$item_data[$key]['type'] = 'marketplace_log';
			unset($item_data[$key]['published_at']);
		endforeach;

		if(count($item_data) < 1 && $item_type == 'common'):
			$shop_item = $this->db->where(array('item_id' => $item_id))->limit(1)->get('shop_items')->row_array();
			$item_data[0]['type'] = 'shop_suggesiton';
			$item_data[0]['price'] = $shop_item['price'];
			$item_data[0]['published'] = 'Shop price';
		endif;

		$this->output->set_content_type('application/json')->set_output(json_encode($item_data, JSON_NUMERIC_CHECK));
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

	public function poll_updates()
	{
		$json_response = array();

		if(strtotime($this->cache->get('marketplace_last_item')) > strtotime($this->input->post('timestamp'))):
			$json_response['timestamp'] = date('Y-m-d H:i:s', time());
			$marketplace_items = $this->db->select('id, username, item_thumbnail, item_name, price, item_type, item_id, finishes_at, published_at')->limit(6)->order_by('id', 'DESC')->get_where('marketplace_items', array(
				'finishes_at >'  => date('Y-m-d H:i:s', time()),
				'published_at >' => date('Y-m-d H:i:s', strtotime($this->input->post('timestamp'))),
				'purchased'      => 0,
				'cancled'        => 0
			))->result_array();

			$formatted_items = array();
			foreach ($marketplace_items as $key => $item):
				$formatted_items[$key] = array(
					'button_class'   => (($this->system->userdata['user_palladium'] >= $item['price']) ? 'buy_now_button' : 'inactive_buy_now_button'),
					'time_left'      => human_time($item['finishes_at'], TRUE),
					'time_published' => human_time($item['published_at']),
					'url_username'   => urlencode($item['username'])
				);
				$formatted_items[$key] = array_merge($formatted_items[$key], $item);
			endforeach;

			$json_response['items'] = $formatted_items;
		endif;

	    $this->output->set_content_type('application/json')->set_output(json_encode($json_response, JSON_NUMERIC_CHECK));
	}
}

/* End of file market.php */
/* Location: ./system/application/controllers/market.php */