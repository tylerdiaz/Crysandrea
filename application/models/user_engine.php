<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * User Engine Model
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 * @copyright Tyler Diaz - August 6, 2010
 * @last_update: May 2, 2011 by Tyler Diaz
 **/

class User_engine extends CI_Model
{
	var $update_queue = array();

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

	public function get($value = 0, $key = 'user_id')
	{
	    $user_query = $this->db->get_where('users', array($key => $value));
	    if($user_query->num_rows() > 0):
	    	return $user_query->row_array();
	    else:
	    	return FALSE;
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

	public function set($key = '', $value = '', $user_id = 0)
	{
		if($user_id == 0):
			$user_id = $this->system->userdata['user_id'];
			$this->system->userdata[$key] = $value;
			$this->session->userdata[$key] = $value;
		endif;

		$this->update_queue[$user_id]['user_id'] = $user_id;
		$this->update_queue[$user_id][$key] = $value;
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

	public function remove($key = '', $value = '', $user_id = 0)
	{
		if($user_id == 0):
			$user_id = $this->system->userdata['user_id'];
			if(isset($this->session->userdata[$key])) $this->session->userdata[$key] -= $value;
		endif;

		$this->update_queue[$user_id]['user_id'] = $user_id;
		$this->update_queue[$user_id][$key] = ($this->system->userdata[$key]-$value);
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

	public function add($key = '', $value = '', $user_id = 0)
	{
		if($user_id == 0):
			$user_id = $this->system->userdata['user_id'];
			if(isset($this->session->userdata[$key])) $this->session->userdata[$key] += $value;
		endif;

		$this->update_queue[$user_id]['user_id'] = $user_id;
		$this->update_queue[$user_id][$key] = ($this->system->userdata[$key]+$value);
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

	public function add_item($item_id = 0, $amount = 1, $user_id = 0, $equipped = 0)
	{
		if($user_id === 0) $user_id = $this->system->userdata['user_id'];

		$new_user_item_data = array(
			'item_id'      => $item_id,
			'user_id'      => $user_id,
			'equipped'     => $equipped,
			'trying_on'    => $equipped,
			'soft_deleted' => '0',
			'sent_by'      => $this->system->userdata['user_id']
		);

		for ($i=0; $i < $amount; $i++):
			$new_user_item_id = $this->db->insert('user_items', $new_user_item_data);
		endfor;

		return $new_user_item_id;
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

	public function remove_item($item_id = 0, $amount = 0, $user_id = 0, $equipped = 0)
	{
		if($user_id === 0) $user_id = $this->system->userdata['user_id'];

		$this->db->limit($amount)->delete('user_items', array('item_id' => $item_id, 'user_id' => $user_id, 'soft_deleted' => 0, 'equipped' => $equipped));

		return $this->db->affected_rows();
	}

	// --------------------------------------------------------------------

	function __destruct()
	{
		if(count($this->update_queue) > 0) $this->db->update_batch('users', $this->update_queue, 'user_id');
	}

}


/* End of file system.php */
/* Location: ./system/application/models/user_engine.php */