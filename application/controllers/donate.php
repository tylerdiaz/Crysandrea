<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Donate extends CI_Controller
{
	var $donation_email = 'payments@crysandrea.com';
	var $currency_value = 0.075;
	var $total_tyis = 2;
	var $currency_name = 'gems';
	var $donation_currency = 'user_gems';
	var $prices = array(
		'tyi'         => 35,
		'special_net' => 50
	);
	var $discounts = array(
		'2.50' => 1.07,
		'2.5'  => 1.07,
		5      => 1.15,
		10     => 1.18,
		25     => 1.25,
		50     => 1.3
	);
	var $discount_bundles = array(
		'2.50' => 35,
		'2.5'  => 35,
		5      => 75,
		10     => 160,
		25     => 415,
		50     => 865
	);

	var $tyi_names = array();
	var $route_navigation = array(
		'index' => 'Thank you items'
	);

	public function __construct()
	{
		parent::__construct();
		$this->load->library('paypal_lib');

		if ( ! $this->tyis = $this->cache->get('tyi_cache')):
			$this->tyis = $this->db->order_by('item_id', 'DESC')->limit($this->total_tyis)->get_where('avatar_items', array('type' => 'tyi'))->result_array();

			foreach ($this->tyis as $key => $tyi):
				$this->tyis[$key]['children'] = $this->db->select('child_id as item_id, layer, parent_id, name, thumb')
									 					 ->where('parent_id', $tyi['item_id'])
									 					 ->join('avatar_items', 'avatar_items.item_id = avatar_items_relations.child_id')
									 					 ->order_by('avatar_items_relations.id', 'desc')
									 					 ->get('avatar_items_relations')
									 					 ->result_array();
			endforeach;

			$this->cache->save('tyi_cache', $this->tyis, 200);
		endif;

		foreach ($this->tyis as $tyi):
			$this->tyi_ids[] = $tyi['item_id'];
			$this->tyi_names[] = $tyi['name'];
		endforeach;
	}

	// --------------------------------------------------------------------

	/**
	 * Home page
	 *
	 * Donate main function
	 *
	 * @access  public
	 * @param   none
	 * @return  view
	 */

	public function index()
	{
		$this->system->view_data['scripts'][] = '/global/js/donate/index.js';
		$this->paypal_lib->add_field('business', $this->donation_email);
		$this->paypal_lib->add_field('return', site_url('donate/success'));
		$this->paypal_lib->add_field('cancel_return', site_url('donate/?cancel=1'));
		$this->paypal_lib->add_field('notify_url', site_url('donate/ipn')); // <-- IPN url
		$this->paypal_lib->add_field('custom', urlencode(json_encode(array('user_id' => $this->system->userdata['user_id'])))); // <-- Verify return
		$this->paypal_lib->add_field('item_name', 'Donate to Crysandrea');
		$this->paypal_lib->add_field('amount', 0);

		$view_data = array(
			'page_title'  => 'February 2013 Donation items',
			'page_body'   => 'donate',
			'paypal_form' => $this->paypal_lib->paypal_form(),
			'discounts'   => $this->discounts,
			'bundles'     => $this->discount_bundles,
			'items'       => $this->tyis,
			'routes'      => $this->route_navigation,
			'prices'      => $this->prices,
			'active_url'  => $this->uri->rsegment(2, 0),
		);

		$this->system->quick_parse('donate/index', $view_data);
	}


	// --------------------------------------------------------------------

	/**
	 * Instant Payment Notification
	 *
	 * This method gets pinged, along with POST data, when a new donation is made.
	 *
	 * @access  public
	 * @param   none
	 * @return  JSON
	 */

	public function ipn()
	{
		if ($this->input->post('test_ipn')):
			return false;
		endif;

		if( ! isset($_POST['custom'])) show_error('Must have full post data');

		$user_data = json_decode(urldecode($_POST['custom']), TRUE);
		$money = ($_POST['mc_gross'] > 1 ? $_POST['mc_gross'] : round($_POST['mc_gross'], 1));
		$currency = round($money/$this->currency_value);

		$multiplier = 1;

		if(isset($this->discount_bundles[$money])):
			$currency = $this->discount_bundles[$money];
		endif;

		$this->db->insert('donation_logs', array(
			'payment_date'         => $this->input->post('payment_date'),
			'payment_status'       => $this->input->post('payment_status'),
			'first_name'           => $this->input->post('first_name'),
			'last_name'            => $this->input->post('last_name'),
			'payer_email'          => $this->input->post('payer_email'),
			'payer_id'             => $this->input->post('payer_id'),
			'address_name'         => $this->input->post('address_name'),
			'address_country'      => $this->input->post('address_country'),
			'address_country_code' => $this->input->post('address_country_code'),
			'address_zip'          => $this->input->post('address_zip'),
			'address_state'        => $this->input->post('address_state'),
			'address_city'         => $this->input->post('address_city'),
			'address_street'       => $this->input->post('address_street'),
			'receiver_email'       => $this->input->post('receiver_email'),
			'residence_country'    => $this->input->post('residence_country'),
			'mc_currency'          => $this->input->post('mc_currency'),
			'mc_fee'               => $this->input->post('mc_fee'),
			'mc_gross'             => $this->input->post('mc_gross'),
			'user_id'              => (isset($user_data['user_id']) ? $user_data['user_id'] : $this->system->userdata['user_id']),
			'currency_granted'     => $currency
		));

		if (strtolower($this->input->post('payment_status')) == 'completed'):
			$this->db->where('user_id', (isset($user_data['user_id']) ? $user_data['user_id'] : $this->system->userdata['user_id']))->set('user_'.$this->currency_name, '(user_'.$this->currency_name.'+'.$currency.')', FALSE)->set('donated', 1)->update('users');
		else:
			$this->load->helper('string');

  			$this->db->set('date', 'NOW()', false)->insert('mail', array(
				'sender'           => 14,
				'receiver'         => (isset($user_data['user_id']) ? $user_data['user_id'] : $this->system->userdata['user_id']),
				'subject'          => 'Pending donation',
				'message'					 => "Hello there!

The donation for your ".$this->currency_name." hasn't been fully processed just yet. Be sure to PM me or reply to this message as soon as the payment clears to grant you the ".$this->currency_name."!

If you need any help or have any questions, let me know! (or email me at tyler@crysandrea.com if you're into email)",
				'status'           => 0,
				'conversation_key' => random_string('alnum', 42),
				'unique_mail_id'   => random_string('alnum', 42)
  			));
		endif;

		$this->output->set_content_type('application/json')->set_output(json_encode(array('success'), JSON_NUMERIC_CHECK));
	}
	// --------------------------------------------------------------------

	/**
	 * Success
	 *
	 * A layout thanking a user for donating
	 *
	 * @access  public
	 * @param   none
	 * @return  output
	 */

	public function success()
	{
	    $view_data = array(
				'page_title' => 'Thank you for donating',
				'page_body'  => 'donate'
	    );

	    $this->system->quick_parse('donate/success', $view_data);
	}

	// --------------------------------------------------------------------

	/**
	 * Purchase donation item
	 *
	 * Purchase a donation item from the donation page for the currency
	 *
	 * @access  public
	 * @param   none
	 * @return  redirect
	 */

	public function purchase_item()
	{
		if ( ! isset($this->system->userdata['user_id'])):
			redirect('/auth/signin?r=donate');
		endif;

		$item_id = $this->input->post('item_id');
		$total = $this->input->post('total');

		if( ! is_numeric($total)) show_error('total must be valid');
		if( ! is_numeric($item_id)) show_error('total must be valid');

		$avatar_item_query = $this->db->get_where('avatar_items', array('item_id' => $item_id));
		if($avatar_item_query->num_rows() > 0):
			$avatar_item_data = $avatar_item_query->row_array();
			if($avatar_item_data['type'] == 'tyi'):
				if (($this->prices['tyi']*$total) <= $this->system->userdata['user_'.$this->currency_name]):
					$this->load->model('user_engine');
					$this->user_engine->remove('user_'.$this->currency_name, ($this->prices['tyi']*$total));
					$this->user_engine->add_item($item_id, $total);
					redirect('donate?success=1');
				endif;
			else:
				show_error('You cannot purchase this item');
			endif;
		else:
			show_error('avatar_item could not be found.');
		endif;
	}
}

/* End of file Donate.php */
/* Location: ./system/application/controllers/donate.php */