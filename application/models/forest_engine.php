<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Untitled Model
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 * @copyright Crysandrea - September 6, 2010
 * @last_update: September 6, 2010 by Tyler Diaz
 **/

class Forest_engine extends CI_Model
{
    var $forester_data = array();

	function __construct()
	{
		parent::__construct();
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

    function get_forester_data($user_id = 0)
    {
        if($user_id == 0):
            $user_id = $this->system->userdata['user_id'];
        endif;

        $hunter = $this->db->get_where('forest_users', array('user_id' => $user_id));
        $this->forester_data = $hunter->result_array();

        return $this->forester_data;
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

    function remove_bug($bug_id = 0, $quanity = 1, $user_id = 0)
    {
        if($user_id == 0):
            $user_id = $this->system->userdata['user_id'];
        endif;

	    $this->db->limit($quanity)->delete('forest_user_catches', array('insect_id' => $bug_id, 'user_id' => $user_id));
	    return $this->db->affected_rows();
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

    function add_bug($bug_id = 0, $quanity = 1, $user_id = 0)
    {
        if($user_id == 0):
            $user_id = $this->system->userdata['user_id'];
        endif;

	    $bug_num = 0;

	    while($quanity > $bug_num)
	    {
	        $this->db->insert('forest_user_catches', array('insect_id' => $bug_id, 'user_id' => $user_id));
	        $bug_num++;
	    }
    }

	// --------------------------------------------------------------------

	/**
	 * Index page
	 *
	 * Allows the users to view the index of the controller
	 *
	 * @access	public
	 * @param	none
	 * @return	redirect
	 * @route	n/a
	 */

	function remove_berries($berries = 0, $user_id = 0)
	{
		if($user_id == 0){
	        $user_id = $this->system->userdata['user_id'];
	        if(sizeof($this->forester_data) < 1)
	        {
	            $this->get_forester_data($user_id);
	        }
	    }
	    else
	    {
	        if(sizeof($this->forester_data) < 1)
	        {
	            $this->get_forester_data($user_id);
	        }
	    }


		$new_berries = ($this->forester_data[0]['berries']-$berries);
		$this->db->update('forest_users', array('berries' => $new_berries), array('user_id' => $user_id));
		return $new_berries;
	}

	// --------------------------------------------------------------------

	/**
	 * Index page
	 *
	 * Allows the users to view the index of the controller
	 *
	 * @access	public
	 * @param	none
	 * @return	redirect
	 * @route	n/a
	 */

	function add_berries($berries = 0, $user_id = 0)
	{
		if(!isset($this->forester_data)){
			$this->forester_data = array();
		}
		if($user_id == 0){
	        $user_id = $this->system->userdata['user_id'];
	        if(sizeof($this->forester_data) < 1)
	        {
	            $this->get_forester_data($user_id);
	        }
	    }
	    else
	    {
	        if(sizeof($this->forester_data) < 1)
	        {
	            $this->get_forester_data($user_id);
	        }
	    }

		$new_berries = ($this->forester_data[0]['berries']+$berries);
		$this->db->update('forest_users', array('berries' => $new_berries), array('user_id' => $user_id));
		return $new_berries;
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

    function get_user_bug($bug_id = 0, $id_only = false, $user_id = 0)
    {
        if($user_id == 0):
            $user_id = $this->system->userdata['user_id'];
        endif;

        if($id_only == TRUE)
        {
            $bug_query = $this->db->select("fc.id")
                                   ->from('forest_user_catches fc')
                                   ->where('fc.user_id', $user_id)
                                   ->where('fc.insect_id', $bug_id)
                                   ->limit(1)
                                   ->get()
                                   ->result_array();
        }
        else
        {
            $bug_query = $this->db->select("fc.id, COUNT(fc.id) as num, fi.thumbnail, fi.name, fi.id")
                                   ->from('forest_user_catches fc')
                                   ->join('forest_insects fi', 'fc.insect_id = fi.id')
                                   ->where('fc.user_id', $user_id)
                                   ->where('fc.insect_id', $bug_id)
                                   ->limit(1)
                                   ->get()
                                   ->result_array();
        }

        return $bug_query[0];
    }



    // --------------------------------------------------------------------
    //    FROM HERE ON IS THE OLD STUFF
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

    function forest_user($user_id){
		return $this->db->where('user_id', $user_id)->limit(1)->get('forest_users')->row_array();
	}

	// ----------------------------------------------------------------------

	function get_bugs_avalible($user_level){

		return $this->db->query('SELECT *
								FROM forest_insects
								WHERE min_level <= '.$user_level.'
								AND max_level > '.$user_level.'
								ORDER BY exp ASC')->result_array();

	}

	// ----------------------------------------------------------------------

	function get_net($_user_id){

		$net_id = $this->db->query('SELECT item_id
		FROM user_items
		WHERE user_id = '.$_user_id.'
		AND equipped = 1
		AND ( item_id = 3324 OR item_id = 3323)');

		if($net_id->num_rows() > 0){
			switch($net_id->row()->item_id){
				case 3324:
					return 'pro';
				break;
				case 3323:
					return 'basic';
				break;

			}
		} else {
			return 'none';
		}
	}

	// ----------------------------------------------------------------------

	function get_nets($_user_id){
		return $this->db->query('SELECT item_id
		FROM user_items
		WHERE user_id = '.$_user_id.'
		AND ( item_id = 3486 OR item_id = 3485)')->result_array();

	}

	// ----------------------------------------------------------------------

	function catch_attempt($_user_id){
		$catches = $this->db->query('SELECT catch_attempts
		FROM forest_users
		WHERE user_id = '.$_user_id.'
		LIMIT 1')->row()->catch_attempts;

		$this->db->update('forest_users',array('catch_attempts' => $catches+1),array('user_id' => $_user_id));
	}

	// ----------------------------------------------------------------------

	function catch_success($_user_id){
		$caught = $this->db->query('SELECT bugs_caught
		FROM forest_users
		WHERE user_id = '.$_user_id.'
		LIMIT 1')->row()->bugs_caught;

		$this->db->update('forest_users',array('bugs_caught' => $caught +1),array('user_id' => $_user_id));
	}

	// ----------------------------------------------------------------------

	function add_exp($_exp, $_user_id){
		$current_exp = $this->db->query('SELECT exp
		FROM forest_users
		WHERE user_id = '.$_user_id.'
		LIMIT 1')->row()->exp;

		$this->db->update('forest_users',array('exp' => $_exp + $current_exp),array('user_id' => $_user_id));

		return $_exp + $current_exp;
	}

	// ----------------------------------------------------------------------

	function caught_bug($_bug_id, $_user_id){

	    $logs = $this->db->query('SELECT id, insect, user_id, amount FROM forest_catch_logs WHERE user_id = '.$_user_id.' LIMIT 1');

		if($logs->num_rows() > 0){
			$this->db->update('forest_catch_logs', array('amount' => $logs->row()->amount+1), array('user_id' => $_user_id, 'insect' => $_bug_id));
		} else {
			$this->db->insert('forest_catch_logs', array('insect' => $_bug_id, 'user_id' => $_user_id, 'amount' => 1));
		}

		$this->db->insert('forest_user_catches', array('user_id' => $_user_id, 'insect_id' => $_bug_id));
	}

	// ----------------------------------------------------------------------

	function get_user_bugs($user_id){

		return $this->db->query('SELECT forest_user_catches.user_id,
        		 		                forest_user_catches.insect_id,
        		 		                forest_insects.*,
        		 		        COUNT(forest_user_catches.insect_id) as amount
                                FROM forest_user_catches
                                JOIN forest_insects
        	                        ON forest_user_catches.insect_id = forest_insects.id
                                WHERE forest_user_catches.user_id = '.$user_id.'
                                GROUP BY forest_user_catches.insect_id
                                ORDER BY forest_insects.exp DESC')->result_array();

	}

	// ----------------------------------------------------------------------

	function level_up($user_id)
	{
	    $current_exp = $this->db->where('user_id', $user_id)->get('forest_users')->row();
        $new_exp = ((floor(floor($current_exp->exp*9/5)/50))*50);
		$this->db->where('user_id', $user_id)->update('forest_users', array('next_level_exp' => $new_exp, 'level' => ($current_exp->level+1)));
        $new_bugs = $this->db->select('COUNT(1) as new_bugs')->where('min_level', $current_exp->level+1)->from('forest_insects')->get()->row()->new_bugs;

		return array('new_level' => $current_exp->level+1, 'new_exp' => $new_exp, 'new_bugs' => $new_bugs);
	}

	// ----------------------------------------------------------------------

	function forester_data($user_id){
		return $this->db->query('SELECT * FROM forest_users WHERE user_id = '.$user_id)->result_array();
	}

	// ----------------------------------------------------------------------

	function amount_owned($insect_id, $user_id){
		return $this->db->query('SELECT insect_id FROM forest_user_catches WHERE user_id = '.$user_id.' AND insect_id = '.$insect_id)->num_rows();
	}

	// ----------------------------------------------------------------------

	function forester_exists($_user_id){
		$exists = $this->db->query('SELECT user_id FROM forest_users WHERE user_id = '.$_user_id.' LIMIT 1')->num_rows();

		if($exists > 0):
			return TRUE;
		else:
			return FALSE;
	    endif;
	}

	// ----------------------------------------------------------------------

	function update_energy($_user_id){

		$forester = $this->db->query('SELECT energy, max_energy, energize_at
										FROM forest_users
										WHERE user_id = '.$_user_id.'
										LIMIT 1')->row();

		$energy = floor(minutes_ago(time(), $forester->energize_at)/5);

		if($energy > 0){
			$this->db->update('forest_users', array('energize_at' => time()), array('user_id' => $_user_id));
		}

		if($forester->energy+$energy < $forester->max_energy){
			$energy_amount = $forester->energy+$energy;
		} else {
			$energy_amount = $forester->max_energy;
		}
		$energy_amount = (int) $energy_amount;

		$this->db->update('forest_users', array('energy' => $energy_amount), array('user_id' => $_user_id));
	}

	// ----------------------------------------------------------------------

	function waste_energy($_user_id){
		$energy = $this->db->query('SELECT energy
										FROM forest_users
										WHERE user_id = '.$_user_id.'
										LIMIT 1')->row()->energy;

		if($energy > 0){
			$this->db->update('forest_users', array('energy' => $energy-1), array('user_id' => $_user_id));
			return TRUE;
		} else {
			return FALSE;
		}

	}

	// ----------------------------------------------------------------------

	function consume_berry($_user_id){

		$user_info = $this->db->query('SELECT energy, berries
										FROM forest_users
										WHERE user_id = '.$_user_id.'
										LIMIT 1')->row();

		if($user_info->berries > 0){

			$this->db->update('forest_users', array('energy' => $user_info->energy+10, 'berries' => $user_info->berries-1), array('user_id' => $_user_id));
			return TRUE;
		} else {
			return FALSE;
		}

	}

	// ----------------------------------------------------------------------

	function enough_energy($_user_id){

		$energy = $this->db->query('SELECT energy FROM forest_users WHERE user_id = '.$_user_id.' LIMIT 1')->row()->energy;

		if($energy > 0){
			return TRUE;
		} else {
			return FALSE;
		}
	}


	// ----------------------------------------------------------------------

	function equipped_net($_user_id){

		$net = $this->db->query('SELECT item_id
									FROM user_items
									WHERE user_id = '.$_user_id.'
									AND equipped = 1
									AND ( item_id = 3324 OR item_id = 3323)')->num_rows();
		if($net > 0){
			return TRUE;
		} else {
			return FALSE;
		}

	}


	// ----------------------------------------------------------------------

	function bug_owned($_bug_id, $_user_id, $_amount){

		$amount = $this->db->query('SELECT id FROM forest_user_catches WHERE insect_id = '.$_bug_id.' AND user_id = '.$_user_id.'');

		if($amount->num_rows() < $_amount){
			return FALSE;
		} else {
			return TRUE;
		}
	}


	// ----------------------------------------------------------------------

	function bug_data($_bug_id){
		return $this->db->query('SELECT * FROM forest_insects WHERE id = '.$_bug_id.' LIMIT 1')->result_array();
	}


	// ----------------------------------------------------------------------

	function add_palladium($_user_id, $_amount){
		$query = $this->db->query("SELECT user_palladium FROM users WHERE user_id = ".$_user_id."")->row();

		$new_palladium = $query->user_palladium + $_amount;

		$this->db->where('user_id', $_user_id)->update('users', array('user_palladium' => $new_palladium));

	}



	// ----------------------------------------------------------------------

	function new_forester($user_id){

		$data = array(
			'user_id' => $user_id,
			'level' => 1,
			'exp' => 100,
			'next_level_exp' => 200,
			'bugs_caught' => 0,
			'catch_attempts' => 0,
			'energy' => 100,
			'energize_at' => time()
		);

		$this->db->insert('forest_users', $data);

	}


	// ----------------------------------------------------------------------

	function bug_catch_log($_bug_id, $_user_id){

		$logs = $this->db->query('SELECT id, insect, user_id, amount FROM forest_catch_logs WHERE user_id = '.$_user_id.' LIMIT 1');

		if($logs->num_rows() > 0){
			$this->db->update('forest_catch_logs', array('amount' => $logs->row()->amount+1), array('user_id' => $_user_id, 'insect' => $_bug_id));
		} else {
			$this->db->insert('forest_catch_logs', array('insect' => $_bug_id, 'user_id' => $_user_id, 'amount' => 1));
		}

	}

	// ----------------------------------------------------------------------


	function build_bug_pack($insects){

	$net = '<div id="bugs_area">';
		foreach($insects as $insect){
		$amount = $this->forest_engine->amount_owned($insect['insect_id'], $this->session->userdata('user_id'));
	$net .= '<div class="bug_shelf">
			<img src="'.$insect['image'].'" style="float:left; margin-right:10px; width:160px; height:160px;" alt=".'.$insect['name'].'" title="'.$insect['name'].'" />
			<h2>(x'.$amount.') '.$insect['name'].'</h2>
			<strong>Price:</strong> '.$insect['price'].' palladium<br />
			<strong>Description:</strong> '.$insect['description'].'<br />
			<strong>Rarity:</strong> '.ucfirst($insect['rarity_classification']).'<br />'.
		form_open('forest/sell/'.$insect['id']).' '.dropdown_count($amount).'<input type="submit" class="submit" value="Sell"></form><br />
		</div>
		<div class="clear"> </div>';
		}
	$net .= '</div> <!-- End of bugs_area --> ';
	return $net;
	}



	// ----------------------------------------------------------------------

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

	public function dropdown()
	{

	}

}


/* End of file untitled.php */
/* Location: ./system/application/controllers/untitled.php */