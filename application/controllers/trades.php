<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trades extends CI_Controller
{
	var $under_construction = FALSE;
	var $tradeable_currencies = array('Palladium', 'Berries');
	var $item_names = array();
	var $ignore_layers = array();
	var $ignore_items = array(
		'1',    // Starter shirt
		'5',    // Base (pale)
		'3',    // Base (normal)
		'8',    // Base (palest)
		'9',    // Base (tan)
		'10',   // Base (dark)
		'12',   // Base (darktan)
		'13',   // Base (darkest)
		'36',   // Standard eyes (grey)
		'37',   // Standard eyes (black)
		'38',   // Standard eyes (blue)
		'39',   // Standard eyes (brown)
		'40',   // Standard eyes (green)
		'46',   // Standard eyes (red)
		'81',   // Mouth (1 dark)
		'82',   // Mouth (1 light)
		'83',   // Mouth (2)
		'84',   // Mouth (3 dark)
		'85',   // Mouth (3 light)
		'86',   // Mouth (4)
		'87',   // Starter Jeans
		'88',   // Starter Jeans (Grey)
		'89',   // Starter Jeans (Black)
		'90',   // Starter Jeans (Blue)
		'113',  // Puffy Hair (black)
		'115',  // Puffy Hair (brown)
		'134',  // Puffy Hair (yellow)
		'136',  // Military Hair (black)
		'138',  // Military Hair (brown)
		'157',  // Military Hair (yellow)
		'159',  // Parted long Hair (black)
		'161',  // Parted long Hair (brown)
		'180',  // Parted long Hair (yellow)
		'368',  // Sports Bra
		'683',  // Basic undies (black)
		'2187', // Nose (small)
		'2331', // De-equip mouth
		'2332', // De-equip nose
		'2333', // De-equip hair
		'7900', //remove eyes
	);

	public function __construct() {
		parent::__construct();
		$this->load->library('markets/trade_market');
		$this->load->model(array('users', 'forest_users'));
		$this->load->model('trade_items_model', 'model');

		$this->system->view_data['styles'][] = 'http://fonts.googleapis.com/css?family=Nunito:400,300';

		if ( ! $this->session->userdata('user_id')):
			redirect('/auth/signin?r=trades');
		endif;

		if ($this->under_construction && ! $this->system->is_staff() && $this->system->userdata['user_id'] != 19 && $this->system->userdata['user_id'] != 598):
			show_error('Trades are under construction');
		endif;

		if($this->system->userdata['user_id'] == 14):
			log_message('error', 'Tyler was in the trades!');
		endif;

	}

	// --------------------------------------------------------------------

	/**
	 * Home page
	 *
	 * Trades main function
	 *
	 * @access  public
	 * @param   none
	 * @return  view
	 * @route   n/a
	 */

	public function index()
	{
		$this->system->view_data['scripts'][] = '/global/js/trades/index.js';

		$user_id = $this->session->userdata['user_id'];

		$trades = $this->db->where('(trade_receiver = '.$user_id.' OR trade_sender = '.$user_id.')')
						   ->where('(sender_status = 0 OR receiver_status = 0)')
						   ->get('trades')
						   ->result_array();

		$view_data = array(
			'page_title' => 'Your Trades',
			'page_body' => 'trades',
			'trades' => $trades
		);

		$this->system->quick_parse('trades/index', $view_data);
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

	public function create_trade()
	{
		if( ! preg_match("/^([a-z0-9\s])+$/i", $this->input->post('username'))) show_error('Invalid datatype');

		$this->load->model('user_engine');
		$user_data = $this->user_engine->get($this->input->post('username'), 'username');

		if (strtolower($this->input->post('username')) == strtolower($this->system->userdata['username'])):
			show_error('You cannot trade with yourself');
		endif;

		if( ! $user_data) show_error('User could not be found');

		try {
			$trade = $this->trade_market->create($this->system->userdata['user_id'], $user_data['user_id']);
		} catch (Trade_Exists_Exception $e) {
			show_error('You are already trading with that user.');
		}

		$new_trade_id = $this->db->insert_id();

		$this->notification->broadcast(array(
			'receiver' => $user_data['username'],
			'receiver_id' => $user_data['user_id'],
			'notification_text' => $this->system->userdata['username'].' has sent you a trade request',
			'attachment_id' => $new_trade_id,
			'attatchment_type' => 'trade_request',
			'attatchment_url' => '/trades/view_trade/'.$new_trade_id,
		), FALSE);

		$this->db->where(array('trade_id' => $new_trade_id))->update('trades', array('trade_title' => 'Trading: '.$this->system->userdata['username'].' and '.$user_data['username']));

		redirect('/trades/view_trade/'.$new_trade_id);
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

	public function view_trade($trade_id = 0)
	{
		// BUG: if a user has no forest account, they cannot trade

		$this->load->model(array('avatar_engine'));
		$this->system->view_data['scripts'][] = '/global/js/trades/view_trade.js';

		$user_id = $this->system->userdata['user_id'];
		$this->trade_id = $trade_id;

		$trade = $this->trades->find($trade_id);

		$trade->sender->user = $this->users->find($trade->trade_sender);
		$trade->sender->forest = $this->_load_forester($trade->trade_sender);
		$trade->sender->items = $this->_get_trade_items($trade->trade_sender);

		$trade->receiver->user = $this->users->find($trade->trade_receiver);
		$trade->receiver->forest = $this->_load_forester($trade->trade_receiver);
		$trade->receiver->items = $this->_get_trade_items($trade->trade_receiver);

		$items = $this->_get_inventory($user_id);

		$trade->receiver_status_text = ($trade->receiver_status == 2 ? 'Canceled' : ($trade->receiver_status == 1 ? 'Accepted' : 'Pending'));
		$trade->sender_status_text = ($trade->sender_status == 2 ? 'Canceled' : ($trade->sender_status == 1 ? 'Accepted' : 'Pending'));

	    $view_data = array(
			'page_title' => 'Viewing Trade',
			'page_body'  => 'trades',
			'items'      => $items,
			'trade_id'   => $trade_id,
			'currencies' => $this->tradeable_currencies,
			'trade'      => get_object_vars($trade),
			'role'       => ($user_id == $trade->trade_sender ? 'sender' : 'receiver'),
			'item_names' => json_encode(array_keys($this->item_names)),
			'item_data'  => json_encode($this->item_names),
	    );

	    $this->system->quick_parse('trades/view_trade', $view_data);
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

	public function add_item($trade_id = 0, $item_key = 0)
	{
		if($_SERVER['REQUEST_METHOD'] != "POST") show_error('Invalid post format');

		if(($type = $this->input->post('type')) != 'bug') $type = 'item';
		if(($amount = $this->input->post('amount')) == FALSE) $amount = 1;

		$user_id = $this->system->userdata['user_id'];
		$trade_data = $this->_pre_check($trade_id, $user_id);
		$trading_with = ($trade_data->trade_sender == $user_id ? $trade_data->trade_receiver : $trade_data->trade_sender);

		$item_key = substr($item_key, 2, -2);

		$this->load->driver('inventory');
		$this->inventory->avatar_items->setOwner($user_id);

		if ($type == 'bug'):
			$this->inventory->setItemType('bug_items');
		else:
			$this->inventory->setItemType('avatar_items');
		endif;

		$this->inventory->setOwner($user_id);

		if ($type == 'bug'):
			$total_catches = $this->db->select('COUNT(1) as total')
									  ->group_by('insect_id')
									  ->get_where('forest_user_catches', array('user_id' => $user_id, 'insect_id' => $item_key))
									  ->row()
									  ->total;

			if($total_catches < $amount):
				show_error('You do not have enough insects for this');
			endif;
		else:
			$total_items = $this->db->select('COUNT(1) as total')
									  ->group_by('item_id')
									  ->get_where('user_items', array('user_id' => $user_id, 'item_id' => $item_key, 'soft_deleted' => 0, 'equipped' => 0))
									  ->row()
									  ->total;

			if($total_items < $amount):
				show_error('You do not have enough items for this');
			endif;
		endif;

		try {
			if ($type == 'bug'):
				$this->db->limit($amount)->where(array('user_id' => $user_id, 'insect_id' => $item_key))->delete('forest_user_catches');
			else:
				for ($i=0; $i < $amount; $i++) {
					$this->inventory->removeItem($item_key);

					if($this->system->userdata['user_id'] == 14):
						log_message('error', 'User '.$user_id.' adding '.$item_key.' ('.($i+1).' of '.$amount.') to trade id '.$trade_id);
					endif;

				}
			endif;
		} catch (No_Such_Item_Exception $e) {
			show_error('You do not own enough of this item.');
		} catch (Exception $e) {
			show_error($e);
		}

		try {
			$trade_item = $this->trade_items->findRelation($trade_id, $user_id, '*', array('item_id' => $item_key, 'item_type' => $type));
			$exists = TRUE;
		} catch (No_Such_Item_Exception $e) {
			$exists = FALSE;
		} catch (No_Result_Exception $e) {
			$exists = FALSE;
		}

		try {
			if ($exists):
				$trade_item->amount += $amount;
				$this->trade_items->updateRelation($trade_item, array('item_id' => $item_key, 'item_type' => $type));
			else:
				$new_trade_item = $this->trade_items->createRelation($trade_id, $user_id, array(
					'item_id' => $item_key,
					'item_type' => $type, // bug
					'amount' => $amount
				));

				$this->trade_items->saveRelation($new_trade_item);
			endif;

			$this->db->update('trades', array('sender_status' => 0, 'receiver_status' => 0), array('trade_id' => $trade_id));

		} catch (Exception $e) {
			show_error($e);
		}

		$this->system->parse_json(array('success' => 1));
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

	public function remove_item($trade_id = 0, $item_key = 0)
	{
		if($_SERVER['REQUEST_METHOD'] != "POST") show_error('Invalid post format');

		if(($type = $this->input->post('type')) != 'bug') $type = 'item';
		if(($amount = $this->input->post('amount')) == FALSE) $amount = 1;

		$user_id = $this->system->userdata['user_id'];
		$trade_data = $this->_pre_check($trade_id, $user_id);
		$trading_with = ($trade_data->trade_sender == $user_id ? $trade_data->trade_receiver : $trade_data->trade_sender);

		$item_key = substr($item_key, 2, -2);

		$this->load->driver('inventory');
		$this->inventory->avatar_items->setOwner($user_id);

		if ($type == 'bug'):
			$this->inventory->setItemType('bug_items');
		else:
			$this->inventory->setItemType('avatar_items');
		endif;

		$this->inventory->setOwner($user_id);

		try {
			$trade_item = $this->trade_items->findRelation($trade_id, $user_id, '*', array('item_id' => $item_key, 'item_type' => $type));
			$exists = TRUE;
		} catch (No_Such_Item_Exception $e) {
			$exists = FALSE;
		} catch (No_Result_Exception $e) {
			$exists = FALSE;
		}

		try {
			if ($exists):
				if($trade_item->amount-$amount < 0) show_error('Not enough items in the trade to be removed');
				if ($trade_item->amount-$amount > 0):
					$trade_item->amount -= $amount;
					$this->trade_items->updateRelation($trade_item, array('item_id' => $item_key, 'item_type' => $type));
				else:
					$this->trade_items->removeRelation($trade_id, $user_id, array('item_id' => $item_key, 'item_type' => $type));
				endif;

				try {
					for ($i=0; $i < $amount; $i++) {
						$this->inventory->addItem($item_key);

						if($this->system->userdata['user_id'] == 14):
							log_message('error', 'User '.$user_id.' removing '.$item_key.' ('.($i+1).' of '.$amount.') to trade id '.$trade_id);
						endif;
					}
					$this->db->update('trades', array('sender_status' => 0, 'receiver_status' => 0), array('trade_id' => $trade_id));
				} catch (No_Such_Item_Exception $e) {
					show_error('An error occured when adding the item');
				} catch (Exception $e) {
					show_error($e);
				}

			endif;
		} catch (Exception $e) {
			show_error($e);
		}

		$this->system->parse_json(array('success' => 1));
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

	public function add_currency($trade_id = 0)
	{
	    if($_SERVER['REQUEST_METHOD'] != "POST") show_error('Invalid post format');

	    $user_id = $this->system->userdata['user_id'];
	    $trade_data = $this->_pre_check($trade_id, $user_id);

	    if( ! in_array(ucfirst($this->input->post('currency_type')), $this->tradeable_currencies)) show_error('non allowed currency');

	    $currency = strtolower($this->input->post('currency_type'));

	    if($currency == 'berries'):
	    	$new_forester = $this->_load_forester($user_id);
	    	if( ! $new_forester) redirect('/trades/view_trade/'.$trade_id);
    	endif;

	    $trading_with = ($trade_data->trade_sender == $user_id ? $trade_data->trade_receiver : $trade_data->trade_sender);
	    $amount = abs($this->input->post('total_amount'));

	    $role = 'sender_'.$currency;
	    if ($trade_data->trade_receiver == $user_id):
	    	$role = 'receiver_'.$currency;
	    endif;

	    $this->load->driver('accountant');
	    $this->accountant->setCurrencyType($currency);
	    $this->accountant->setOwner($user_id);

	    if ($this->input->post('modify_method') == 'remove'):

	    	if($trade_data->{$role} < $amount):
	    		show_error('Not enough funds in the trade');
    		endif;

			try {
				$this->trade_market->setTrade($trade_data->trade_sender, $trade_data->trade_receiver)
								   ->setCurrencyType($currency)
								   ->removeCurrency($user_id, $amount);

			} catch (Insufficient_Currency_Exception $e) {
			    show_error('Not enough funds in the trade');
			}

			$this->accountant->deposit($amount);
		else:
			try {
				$this->accountant->withdraw($amount);
			} catch (Insufficient_Currency_Exception $e) {
			    show_error('You do not have enough funds');
			}

			$this->trade_market->setTrade($trade_data->trade_sender, $trade_data->trade_receiver, array('trade_sender !=' => 1, 'trade_receiver !=' => 1))
							   ->setCurrencyType($currency)
							   ->addCurrency($user_id, $amount);
	    endif;

	    $this->db->update('trades', array('sender_status' => 0, 'receiver_status' => 0), array('trade_id' => $trade_id));

		redirect('/trades/view_trade/'.$trade_id);
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

	public function remove_currency($trade_id = 0)
	{
	    if($_SERVER['REQUEST_METHOD'] != "POST") show_error('Invalid post format');

	    $user_id = $this->system->userdata['user_id'];
	    $trade_data = $this->_pre_check($trade_id, $user_id);

	    if( ! in_array(ucfirst($this->input->post('currency_type')), $this->tradeable_currencies)) show_error('non allowed currency');

	    $currency = strtolower($this->input->post('currency_type'));

	    $trading_with = ($trade_data->trade_sender == $user_id ? $trade_data->trade_receiver : $trade_data->trade_sender);
	    $amount = $this->input->post('total_amount');

	    $this->load->driver('accountant');
	    $this->accountant->setCurrencyType($currency);
	    $this->accountant->setOwner($user_id);

	    try {
	    	$this->trade_market->setTrade($trade_data->trade_sender, $trade_data->trade_receiver)
	    					   ->setCurrencyType($currency)
	    					   ->removeCurrency($user_id, $amount);
	    } catch (Insufficient_Currency_Exception $e) {
		    show_error('Not enough funds in the trade');
	    }

    	$this->accountant->deposit($amount);

    	$this->db->update('trades', array('sender_status' => 0, 'receiver_status' => 0), array('trade_id' => $trade_id));

		redirect('/trades/view_trade/'.$trade_id);
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

	public function accept_trade($trade_id = 0, $method = 'accept')
	{
	    if($_SERVER['REQUEST_METHOD'] != "POST") show_error('Invalid post format');

	    if($method != 'accept' && $method != 'cancel') show_error('undefined method');

	    $this->load->model('user_engine');

	    $user_id = $this->system->userdata['user_id'];
	    $trade_data = $this->_pre_check($trade_id, $user_id);
	    $trading_with = ($trade_data->trade_sender == $user_id ? $trade_data->trade_receiver : $trade_data->trade_sender);

	    $trade_link = $this->trades->findRelation($trade_data->trade_sender, $trade_data->trade_receiver, '*', array('trade_sender !=' => 1, 'trade_receiver !=' => 1, 'trade_id' => $trade_id));

	    $trading_with_data = $this->user_engine->get($trading_with);

	    $role = 'sender_status';
	    if ($trade_data->trade_receiver == $user_id):
	    	$role = 'receiver_status';
	    endif;

	    if($method == 'accept'):
	    	$trade_link->{$role} = 1;
	    else:
	    	$trade_link->receiver_status = 2;
	    	$trade_link->sender_status = 2;
    	endif;

	    $this->trades->updateRelation($trade_link);

		$this->load->driver('accountant');

	    if ($trade_link->sender_status == 1 && $trade_link->receiver_status == 1 && $method == 'accept'):
			$this->db->trans_start();
			$this->_process_trade($trade_id, $trade_data);
			$this->db->trans_complete();

			$message = $this->system->userdata['username'].' has accepted and completed the trade.';
		elseif($method == 'cancel'):
			$this->db->trans_start();
			$this->_revert_trade($trade_id, $trade_data);
			$this->db->trans_complete();

			$message = $this->system->userdata['username'].' has canceled their trade with you.';
	    endif;

	    if (isset($message)):
	    	$this->notification->broadcast(array(
	    		'receiver' => $trading_with_data['username'],
	    		'receiver_id' => $trading_with_data['user_id'],
	    		'notification_text' => $message,
	    		'attachment_id' => $trade_id,
	    		'attatchment_type' => 'trade_completed',
	    		'attatchment_url' => '/trades/view_trade/'.$trade_id,
	    	), FALSE);
	    endif;

	    redirect('/trades/view_trade/'.$trade_id);
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

	public function rename_trade($trade_id = 0)
	{
	    if($_SERVER['REQUEST_METHOD'] != "POST") show_error('Invalid post format');

	    if($method != 'accept' && $method != 'cancel') show_error('undefined method');

	    $this->load->model('user_engine');

	    $user_id = $this->system->userdata['user_id'];
	    $trade_data = $this->_pre_check($trade_id, $user_id);
	    $trading_with = ($trade_data->trade_sender == $user_id ? $trade_data->trade_receiver : $trade_data->trade_sender);

	    $trade_link = $this->trades->findRelation($trade_data->trade_sender, $trade_data->trade_receiver, '*', array('trade_sender !=' => 1, 'trade_receiver !=' => 1, 'trade_id' => $trade_id));

	    $trading_with_data = $this->user_engine->get($trading_with);

	    $new_title = $this->input->post('title');

	    // -
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

	public function _process_trade($trade_id = 0, $trade_data = array(), $reverting = FALSE)
	{
	    $this->accountant->setCurrencyType('palladium');

	    if ($trade_data->sender_palladium > 0):
	    	$this->accountant->setOwner($trade_data->trade_receiver)->deposit($trade_data->sender_palladium);
	    endif;
	    if ($trade_data->receiver_palladium > 0):
	    	$this->accountant->setOwner($trade_data->trade_sender)->deposit($trade_data->receiver_palladium);
	    endif;

	    $this->accountant->setCurrencyType('berries');

	    if ($trade_data->sender_berries > 0):
	    	$this->accountant->setOwner($trade_data->trade_receiver)->deposit($trade_data->sender_berries);
	    endif;
	    if ($trade_data->receiver_berries > 0):
	    	$this->accountant->setOwner($trade_data->trade_sender)->deposit($trade_data->receiver_berries);
	    endif;

	    try {
		    if ($reverting):
		    	$receiver_items = $this->trade_items->findRelation($trade_id, $trade_data->trade_sender, '*', array(), 1000);
		    else:
		    	$receiver_items = $this->trade_items->findRelation($trade_id, $trade_data->trade_receiver, '*', array(), 1000);
		    endif;
	    } catch (Exception $e) {
	    	$receiver_items = array();
	    }

	    try {
	    	if ($reverting):
	    		$sender_items = $this->trade_items->findRelation($trade_id, $trade_data->trade_receiver, '*', array(), 1000);
	    	else:
	    		$sender_items = $this->trade_items->findRelation($trade_id, $trade_data->trade_sender, '*', array(), 1000);
	    	endif;
	    } catch (Exception $e) {
	    	$sender_items = array();
	    }

	    $this->load->driver('inventory');

	    foreach ($sender_items as $sender_item):
	    	$type = 'avatar_items';
	    	if ($sender_item->item_type == 'bug'):
	    		$type = 'bug_items';
	    	endif;

	    	$this->inventory->{$type}->setOwner($trade_data->trade_receiver);
	    	$this->inventory->setOwner($trade_data->trade_receiver);
	    	$this->inventory->setItemType($type);

	    	for ($i=0; $i < $sender_item->amount; $i++):
	    		$this->inventory->addItem($sender_item->item_id);
	    	endfor;
	    endforeach;

	    foreach ($receiver_items as $receiver_item):
	    	$type = 'avatar_items';
	    	if ($receiver_item->item_type == 'bug'):
	    		$type = 'bug_items';
	    	endif;

	    	$this->inventory->{$type}->setOwner($trade_data->trade_sender);
	    	$this->inventory->setOwner($trade_data->trade_sender);
	    	$this->inventory->setItemType($type);

	    	for ($i=0; $i < $receiver_item->amount; $i++):
	    		$this->inventory->addItem($receiver_item->item_id);
	    	endfor;
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

	public function _revert_trade($trade_id = 0, $trade_data = array())
	{
		$new_sender = $trade_data->trade_sender;
		$new_receiver = $trade_data->trade_receiver;

		$trade_data->trade_receiver = $new_sender;
		$trade_data->trade_sender = $new_receiver;

		return $this->_process_trade($trade_id, $trade_data, TRUE);
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

	public function _pre_check($trade_id = 0, $user_id = 0)
	{
		try {
			$trade_data = $this->trades->find($trade_id);
		} catch (No_Result_Exception $e) {
			show_error('This trade does not exist');
		}

	    if($trade_data->trade_receiver != $user_id && $trade_data->trade_sender != $user_id):
	    	show_error('You are not a part of this trade');
	    endif;

	    if($trade_data->receiver_status == 1 && $trade_data->sender_status == 1):
	    	show_error('This trade is already completed');
	    endif;

	    if($trade_data->receiver_status == 2 && $trade_data->sender_status == 2):
	    	show_error('This trade was already canceled');
	    endif;

	    return $trade_data;
	}


	// --------------------------------------------------------------------

	/**
	 * New function
	 *
	 * Description of new function
	 *
	 * @access  private
	 * @param   none
	 * @return  output
	 */

	private function _get_inventory($user_id = 0)
	{
		$this->load->model('avatar_engine');

		$equipped_items = array_keys($this->avatar_engine->get_equiped_items($user_id));
		$inventory_query = $this->avatar_engine->get_user_inventory($user_id);
		$hash_salt = substr(time(), 0, 4);
		$attributes = 'width="42" height="42" alt=""';

		foreach($inventory_query as $row):
			if(in_array($row['layer_id'], $this->ignore_layers)) continue;
			if(in_array($row['item_id'], $this->ignore_items)) continue;
			if(in_array($row['main_id'], $equipped_items)) continue;

			if(isset($items[$row['main_tab']][$row['tab_name']][$row['item_id']])):
				$items[$row['main_tab']][$row['tab_name']][$row['item_id']]['amount']++;
				$items[$row['main_tab']][$row['tab_name']][$row['item_id']]['total']++;
				continue;
			endif;

			$item_hash_key = substr($hash_salt, 0, 2).$row['item_id'].substr($hash_salt, 2); // Small security fence to confuse exploiters, no relevance to devs - Just strip 2 chars off both sides

			$href_attributes = '';

			$attr_array = array(
				'data-key' => $item_hash_key,
				'data-tab' => strtolower($row['tab_name']),
				'data-type' => 'inventory',
				'data-format' => 'item',
				'title' => $row['itemname'],
				'class' => 'magicTip',
			);

			foreach ($attr_array as $key => $value):
				$href_attributes .= ' '.$key.'="'.$value.'"';
			endforeach;

			$this->item_names[$row['itemname']] = $item_hash_key;

			$items[$row['main_tab']][$row['tab_name']][$row['item_id']] = array(
				'name'     => $row['itemname'],
				'item_key' => $item_hash_key,
				'item_id'  => $row['item_id'],
				'thumb'    => $row['thumb'],
				'amount'   => $row['num'],
				'total'    => $row['num'],
				'type'     => 'item',
				'element'  => anchor('#', image('/images/items/'.$row['thumb'], $attributes), $href_attributes)
			);
		endforeach;

	    $hunter_query = $this->db->get_where('forest_users', array('user_id' => $user_id));

	    if($hunter_query->num_rows() > 0):
	    	$hunter_data = $hunter_query->row_array();
	    	$insect_list = $this->db->select('uc.user_id, uc.insect_id, fi.*, COUNT(uc.insect_id) as amount')
	    							->from('forest_user_catches uc')
	    							->join('forest_insects fi', 'uc.insect_id = fi.id')
	    							->where('uc.user_id', $user_id)
	    							->group_by('uc.insect_id')
	    							->order_by('fi.exp', 'ASC')
	    							->order_by('amount', 'DESC')
	    							->get()
	    							->result_array();

	    	foreach ($insect_list as $insect):
	    		$href_attributes = '';

		    	$item_hash_key = substr($hash_salt, 0, 2).$insect['insect_id'].substr($hash_salt, 2);  // Small security fence to confuse exploiters, no relevance to devs - Just strip 2 chars off both sides
	    		$attr_array = array(
					'data-key'    => $item_hash_key,
					'data-tab'    => strtolower('Bugs'),
					'data-type'   => 'inventory',
					'data-format' => 'bug',
					'data-amount' => $insect['amount'],
					'title'       => $insect['name'].' (x'.$insect['amount'].')',
					'class'       => 'magicTip',
	    		);

	    		foreach ($attr_array as $key => $value):
	    			$href_attributes .= ' '.$key.'="'.$value.'"';
	    		endforeach;

		    	$this->item_names[$insect['name']] = $item_hash_key;

	    		$items[99]['Bugs'][$insect['id']] = array(
					'name'     => $insect['name'],
					'item_key' => $item_hash_key,
					'item_id'  => $insect['insect_id'],
					'thumb'    => $insect['image'],
					'amount'   => 1,
					'total'    => $insect['amount'],
					'type'     => 'insect',
					'element'  => anchor('#', image($insect['image'], $attributes), $href_attributes)
				);
	    	endforeach;
	    else:
	    	return array();
    	endif;

		return $items;
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

	private function _get_trade_items($user_id = 0)
	{
	    try {
	    	$hash_salt = substr(time(), 0, 4);
	    	$item_query = $this->trade_items->findRelation($this->trade_id, $user_id, '*', array(), 200);
	    	$final_items = array();

			foreach ($item_query as $item_data):
				$item_basket[$item_data->item_type][$item_data->trade_item_id] = $item_data->item_id;
				$final_items[$item_data->item_id] = get_object_vars($item_data);
			endforeach;

			$attributes = 'width="42" height="42" alt=""';

			foreach ($item_basket as $item_type => $type_array):
				if ($item_type == 'bug'):
					$forest_insect_query = $this->db->where_in('id', array_values($type_array))->get('forest_insects')->result_array();
					foreach ($forest_insect_query as $insect_data):
						$insect_data['thumb'] = $insect_data['image'];
						$final_items[$insect_data['id']] = array_merge($final_items[$insect_data['id']], $insect_data);
					endforeach;
				elseif ($item_type == 'item'):
					$avatar_item_query = $this->db->select('avatar_items.*, avatar_layers.main_tab')->where_in('item_id', array_values($type_array))->join('avatar_layers', 'avatar_layers.id = avatar_items.layer')->get('avatar_items')->result_array();
					foreach ($avatar_item_query as $item_data):
						$final_items[$item_data['item_id']] = array_merge($final_items[$item_data['item_id']], $item_data);
					endforeach;
				endif;
			endforeach;

			// This is not proper, it should load it from the database. But, for now...
			$tab_format = array(
				1 => 'Tops',
				2 => 'Head',
				3 => 'Bottom',
				4 => 'Feet',
				5 => 'Accessories',
				6 => 'Items',
				7 => 'Appearance',
				8 => 'Hair',
			);

			foreach ($final_items as $item_key => $item):
				$item_hash_key = substr($hash_salt, 0, 2).$item['item_id'].substr($hash_salt, 2);  // Small security fence to confuse exploiters, no relevance to devs - Just strip 2 chars off both sides
				$attr_array = array(
					'data-key'    => $item_hash_key,
					'data-tab'    => strtolower($item['item_type'] == 'bug' ? 'Bugs' : $tab_format[$item['main_tab']]),
					'data-type'   => 'trade_item',
					'data-format' => $item['item_type'],
					'data-amount' => $item['amount'],
					'title'       => $item['name'].($item['item_type'] == 'bug' ? ' (x'.$item['amount'].')' : ''),
					'class'       => 'magicTip '.($user_id == $this->system->userdata['user_id'] ? 'modify_item' : ''),
				);

				$href_attributes = '';
				foreach ($attr_array as $key => $value):
					$href_attributes .= ' '.$key.'="'.$value.'"';
				endforeach;

				if($item['item_type'] == 'item'):
					$item['thumb'] = '/images/items/'.$item['thumb'];
				endif;

				$final_items[$item_key]['element'] = anchor('#', image($item['thumb'], $attributes), $href_attributes);
			endforeach;

			return $final_items;
	    } catch (No_Result_Exception $e) {
	    	return array();
	    }
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

	public function _load_forester($user_id = 0)
	{
		try {
			return $this->forest_users->find($user_id);
		} catch (Exception $e) {
			return FALSE;
		}
	}

}

/* End of file Trades.php */
/* Location: ./system/application/controllers/Trades.php */