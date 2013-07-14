<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Crysandrea Forum Engine
 *
 * @author(s) 	Tyler Diaz
 * @version 	1.1
 * @copyright 	Crysandrea - July 30, 2010
 * @last_update July 30, 2010 by Tyler Diaz
 **/

class Avatar_engine extends CI_Model
{
	var $cache_prefix;
	var $memcached = FALSE;
	var $cache_durations = array(
		'total_registered_users'	=> 300 	// 5 minutes
	);
	var $user_id = 0;

	function __construct()
	{
		parent::__construct();
		// We only really need this stuff if the memcache is enabled
		if($this->memcached === TRUE):
			$this->cache_prefix = strtolower(get_class($this)).'_';
			$this->load->library('cache');
		endif;
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

	function get_user_inventory($user_id = 0)
	{
		if($user_id == 0):
			$user_id = $this->user_id;
		else:
			$this->user_id = $user_id;
		endif;

		$sql = "SELECT cai.name as itemname,
		                al.order as layerorder,
		                al.name as layername,
		                al.id as layer_id,
		                al.main_tab,
		                caui.id as main_id,
		                cai.item_id as item_id,
		                avatar_layer_tabs.tab_name,
		                cai.thumb,
		                (
		                	SELECT count(air.child_id)
		                	FROM avatar_items_relations air
		                	WHERE air.child_id = cai.item_id
		                ) as item_identifier,
		                IF(
		                    (
		                    SELECT count(air.child_id)
		                    FROM avatar_items_relations air
		                    WHERE air.parent_id = cai.item_id
		                    ) > 0, UUID(), cai.item_id
		                ) as item_grouper,
		                count( caui.id ) AS num
		        FROM (`user_items` caui)
		        JOIN `avatar_items` cai ON `cai`.`item_id` = `caui`.`item_id`
		        JOIN `users` cu ON `cu`.`user_id` = `caui`.`user_id`
		        JOIN `avatar_layers` al ON al.id = cai.layer
		        JOIN `avatar_layer_tabs` ON avatar_layer_tabs.tab_id = al.main_tab
		        AND caui.soft_deleted = 0
		        AND `caui`.`user_id` = '".$this->user_id."'
		        AND (
		            CAST( cai.gender AS CHAR ) = CAST( cu.avatar_gender AS CHAR )
		            OR CAST( cai.gender AS CHAR ) = 'Unisex'
		        )
		        GROUP BY item_grouper
		        HAVING item_identifier = 0
		        ORDER BY al.order ASC, cai.name ASC";

		return $this->db->query($sql)->result_array();
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

    function get_item_siblings($parent_id = 0, $item_id = 0, $user_id = 0)
    {
        if($user_id == 0):
			$user_id = $this->user_id;
		else:
			$this->user_id = $user_id;
		endif;

		$query = $this->db->select('id, child_id as item_id, layer as layer_id, parent_id, child_id, gender, name as itemname, thumb')
			 			  ->where('parent_id', $item_id)
			 			  ->join('avatar_items', 'avatar_items.item_id = avatar_items_relations.child_id')
			 			  ->order_by('avatar_items_relations.id', 'desc')
			 			  ->get('avatar_items_relations')
			 			  ->result_array();

	    return $query;
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

    function get_avatar_inventory($user_id = 0)
    {
        if($user_id == 0):
			$user_id = $this->user_id;
		else:
			$this->user_id = $user_id;
		endif;

        $query = $this->db->query("SELECT *,
		                    al.order as layerorder,
		                    al.name as layername ,
		                    caui.id as main_id,
		                    count( caui.id ) as num
                          FROM (`user_items` caui)
                          JOIN `avatar_items` cai ON `cai`.`item_id` = `caui`.`item_id`
                          JOIN `users` cu ON `cu`.`user_id` = `caui`.`user_id`
                          JOIN `avatar_layers` al ON al.id = cai.layer
                          WHERE `al`.`composite` = '1'
                          AND `caui`.`user_id` = '".$user_id."'
                          AND (
                            CAST( cai.gender AS CHAR ) = CAST( cu.avatar_gender AS CHAR )
                            OR CAST( cai.gender AS CHAR ) = 'Unisex'
                          )
		                  GROUP BY `caui`.`item_id`")->result_array();

		return $query;
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

    function get_item_info($item_id = 0, $user_id = 0)
    {
        if($user_id == 0):
			$user_id = $this->user_id;
		else:
			$this->user_id = $user_id;
		endif;

        return $this->db->where('user_id',$user_id)
				        ->where('user_items.id',$item_id)
				        ->get('user_items');
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

    function unequip_all($user_id = 0)
    {
        if($user_id == 0):
			$user_id = $this->user_id;
		else:
			$this->user_id = $user_id;
		endif;

        $this->db->update('user_items', array('equipped' => '0'), array('user_id' => $user_id));
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

    function equip_items($items = array(), $user_id = 0)
    {
        if($user_id == 0):
			$user_id = $this->user_id;
		else:
			$this->user_id = $user_id;
		endif;

        $this->db->where('user_id', $user_id)
                 ->where_in('id', $items)
                 ->update('user_items', array('equipped' => '1'));

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

    function get_user_equipped_items($user_id = 0, $type = 'simple')
    {
        if($user_id == 0):
			$user_id = $this->user_id;
		else:
			$this->user_id = $user_id;
		endif;

        if($type == 'complex'):
            $query = $this->db->select('caip.image_path as path')
					          ->from('avatar_item_parts caip')
					          ->join('avatar_items cai','cai.item_id = caip.item_id')
					          ->join('avatar_layers cal', 'cal.id = caip.layer')
					          ->join('user_items caui', 'caui.item_id = cai.item_id')
					          ->order_by('cal.order','asc')
					          ->order_by('cai.order', 'asc')
					          ->where('caui.user_id',(is_null($user_id) ? $my_id : $user_id))
					          ->where('caui.equipped','1')
					          ->get();
		else:
		    $query = $this->db->select('id as item_id')
								->where('user_id', $user_id)
								->where('equipped', '1')
								->get('user_items');
		endif;
		return $query;
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

    function get_composite_item_data($item_id = 0)
    {
        $query	= $this->db->select('al.name as layername')
								->select('al.composite as layercomposite')
								->select('al.order as layerorder')
								->select('al.id as layerid')
								->from('avatar_items ai')
								->join('avatar_layers al','al.id = ai.layer')
								->join('user_items ui','ui.item_id = ai.item_id')
								->where('ui.id', $item_id)
								->get();
		return $query;
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

    function get_item_thumbnail($item_id = 0)
    {
        $query = $this->db->select('ai.item_id, ai.thumb')
		                    ->where('ui.id', $item_id)
		                    ->join('avatar_items ai','ai.item_id = ui.item_id')
		                    ->limit(1)
		                    ->get('user_items ui');

		return $query;
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

    function get_preview_item_data($items = array(), $user_id = 0, $gender = 'Male')
    {
        if($user_id == 0):
			$user_id = $this->user_id;
		else:
			$this->user_id = $user_id;
		endif;

		$sql_if = "(IF( CAST(cai.gender AS CHAR) = 'Unisex',
		IF( CAST(caip.gender AS CHAR) = CAST(\"".$gender."\" as CHAR)
			OR CAST(caip.gender AS CHAR) = 'Unisex', true, false),
		true))";

        $query = $this->db->select('cal.order, caip.*')
                          ->from('avatar_item_parts caip')
                          ->join('avatar_items cai', 'cai.item_id=caip.item_id')
                          ->join('avatar_layers cal', 'cal.id=caip.layer')
                          ->where('cai.item_id IN ('.implode(',', $items).')')
                          ->where($sql_if)
                          ->group_by('caip.layer')
                          ->order_by('cal.order', 'asc')
                          ->order_by('cai.order', 'asc')
                          ->get();

        return $query->result_array();
    }

    function get_preview_items($items = array(), $user_id = 0)
    {
        if($user_id == 0):
			$user_id = $this->user_id;
		else:
			$this->user_id = $user_id;
		endif;

		$sql_if = "(IF( CAST(cai.gender AS CHAR) = 'Unisex',
		IF( CAST(caip.gender AS CHAR) = CAST(\"".$gender."\" as CHAR)
			OR CAST(caip.gender AS CHAR) = 'Unisex', true, false),
		true))";

        $query = $this->db->select('image_path as path, caip.layer, cal.order')
                          ->from('avatar_item_parts caip')
                          ->join('avatar_items cai', 'cai.item_id=caip.item_id')
                          ->join('avatar_layers cal', 'cal.id=caip.layer')
                          ->where('cai.item_id IN ('.implode(',', $items).')')
                          ->where($sql_if)
                          ->group_by('caip.layer')
                          ->order_by('cal.order', 'asc')
                          ->order_by('cai.order', 'asc')
                          ->get();

        return $query;
    }

//---------------new get items after user_items
 function get_user_prew_items($items = array(), $user_id = 0)
    {
        if($user_id == 0):
			$user_id = $this->user_id;
		else:
			$this->user_id = $user_id;
		endif;

        $query = $this->db->select('caip.*')
                          ->from('avatar_items caip')
                          ->join('avatar_layers cal', 'cal.id=caip.layer')
                          ->where('caip.item_id IN ('.implode(',', $items).')')
                          ->order_by('cal.order', 'asc')
                          ->get()
                          ->result_array();

        if (!empty($query)) return $query; else return false;
    }

    function get_images($item_id=null, $user_id=0){
	 if($user_id == 0):
			$user_id = $this->user_id;
		else:
			$this->user_id = $user_id;
		endif;
	$sql_if = "(IF( CAST(cai.gender AS CHAR) = 'Unisex',
					    IF( CAST(caip.gender AS CHAR) = CAST(u.avatar_gender as CHAR)
					        OR CAST(caip.gender AS CHAR) = 'Unisex', true, false),
				        true)
				    )";
	$gender="";
	$users = $this->db->select('*')
                          ->from('users')
                          ->where('user_id',$user_id)
                          ->get()->result_array();
	foreach($users as $user):
		$gender=$user['avatar_gender'];
	endforeach;
        $query = $this->db->select('caip.*, cal.order')
                          ->from('avatar_item_parts caip')
                          ->join('avatar_items cai', 'cai.item_id=caip.item_id')
                          ->join('avatar_layers cal', 'cal.id=caip.layer')
                          ->where('caip.item_id ',$item_id)
                          ->where('caip.gender', $gender)
                          ->order_by('cal.order', 'asc')
                          ->get()->result_array();

         if (!empty($query)) return $query; else return false;
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

    function get_item_children($parent_item = 0, $user_id = 0)
    {
        if($user_id == 0):
			$user_id = $this->user_id;
		else:
			$this->user_id = $user_id;
		endif;

        $query = $this->db->where('air.parent_id', $parent_item['item_id'])
                          ->join('avatar_items ai','ai.item_id = air.child_id')
                          ->join('avatar_layers al','al.id = ai.layer')
                          ->select('ai.name as itemname')
                          ->select('al.id as layer_id')
                          ->select('ai.item_id')
                          ->select('ai.thumb')
                          ->group_by('ai.item_id')
                          ->get('avatar_items_relations air');

        return $query;
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

	// NEW FUNCTIONS! that help not use sessions to store equipping info -- NEW -- NEW -- NEW -- NEW -- NEW -- NEW -- NEW -- NEW -- NEW -- NEW --

	// get the items that were equipped last time when saved
	function get_saved_items() {
		if($this->user_id != true):
			$user_id = 0;
		else:
			$user_id = $this->user_id;

			$user_items = $this->db->select('*')
	                          ->from('user_items')
	                          ->where('user_id',$user_id)
														->where('equipped', 1)
														->order_by('id asc')
	                          ->get()->result_array();
			if (!empty($user_items)) return $user_items; else return false;
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

	public function get_equiped_items($user_id = 0)
	{
		if ( ! $user_id):
			$user_id = $this->system->userdata['user_id'];
		endif;

		$equiped_items = array();
	    $equiped_items_query = $this->db->select('id, item_id, trying_on')->get_where('user_items', array('user_id' => $user_id, 'trying_on >' => 0))->result_array();

	    foreach ($equiped_items_query as $key => $value):
	    	$equiped_items[$value['id']] = $value['trying_on'];
	    endforeach;

	    return $equiped_items;
	}

	// get teh trying_on items for this user
	function get_tried_on(){
		if($this->user_id != true):
			$user_id = 0;
			return false;
		else:
			$user_id = $this->user_id;

			$user_items = $this->db->select('user_items.*, avatar_items.layer as layer')
	                          	   ->from('user_items')
	                          	   ->where('user_id',$user_id)
							  	   ->where('trying_on > ', 0)
							  	   ->where('soft_deleted', 0)
							  	   ->join('avatar_items', 'avatar_items.item_id = user_items.item_id', 'LEFT')
	                          	   ->get()
	                          	   ->result_array();

	        foreach ($user_items as $item_key => $item):
	        	if($item['trying_on'] > 1):
	        		$siblings = $this->get_item_siblings($item['parent_id'], $item['item_id'], $this->system->userdata['user_id']);
	        		if( ! empty($siblings)):
	        			foreach($siblings as $sibling_key => $sibling):
	        				if(($sibling_key+2) == $item['trying_on']):
	        					$user_items[$item_key] = $sibling;
	        					$user_items[$item_key]['trying_on'] = 1;
	        					$user_items[$item_key]['soft_deleted'] = $item['soft_deleted'];
	        					$user_items[$item_key]['soft_deleted_by_user_id'] = $item['soft_deleted_by_user_id'];
	        					$user_items[$item_key]['equipped'] = $item['equipped'];
	        					$user_items[$item_key]['layer'] = $sibling['layer_id'];
	        					$user_items[$item_key]['user_id'] = $item['user_id'];
	        					unset($user_items[$item_key]['layer_id']);
	        					unset($user_items[$item_key]['itemname']);
	        					unset($user_items[$item_key]['thumb']);
	        					unset($user_items[$item_key]['gender']);
	        					unset($user_items[$item_key]['child_id']);
	        				endif;
	        			endforeach;
	        		endif;
        		endif;
	        endforeach;

			if ( ! empty($user_items)){
				return $user_items;
			} else {
				return false;
			}
		endif;
	}

	function get_save_state(){
		if($this->user_id != true):
			$user_id = 0;
		else:
			$user_id = $this->user_id;

			$user_items = $this->db->select('*')
	                          ->from('user_items')
	                          ->where('user_id',$user_id)
	                          ->get()->result_array();
			if (!empty($user_items)) {
				foreach($user_items as $ui):
					if($ui['equipped']!=$ui['trying_on']) return "false";
				endforeach;
				return "true";
			}else return "false";
		endif;
	}

	// get teh trying_on items for this user
	function is_tried_on($item_id){
		if($this->user_id != true):
			$user_id = 0;
			return false;
		else:
			$user_id = $this->user_id;

			$user_items = $this->db->select('*')
	                          ->from('user_items')
	                          ->where('user_id',$user_id)
														->where('item_id', $item_id)
														->where('trying_on', 1)
	                          ->get()->result_array();
			if (!empty($user_items)) return true; else return false;
		endif;
	}

	function toggle_on($item_id) {
		$debugger_data = array();

		if( ! $this->user_id):
			return false;
		else:
			// Step 1: Break the hash!
			$item_hash = explode('-', $item_id);
			$item_id = $item_hash[0];
			$sub_item_id = (isset($item_hash[1]) ? $item_hash[1] : FALSE);

			$user_id = $this->user_id;

			$equipping_item_query = $this->db->select('user_items.*, avatar_items.layer')
											 ->from('user_items')
											 ->where('user_items.user_id',$user_id)
											 ->where('user_items.id', $item_id)
											 ->where('soft_deleted', 0)
											 ->join('avatar_items', 'avatar_items.item_id=user_items.item_id', 'LEFT')
											 ->get();

	        if($equipping_item_query->num_rows() == 0):
	        	show_error('You do not own this item');
	        else:
	        	$user_item = $equipping_item_query->row_array();
	        endif;

	        $unquipping_item = ( ! $sub_item_id ? ($user_item['trying_on'] == 1) : ($user_item['trying_on'] == $sub_item_id));

			if($unquipping_item):
				$user_item['trying_on'] = 0;
				$debugger_data[] = "removed item";
			else:
				$active_item = $user_item;
				//TODO! check for other items on this layer?
				$equipped_items = $this->get_tried_on();
				$item_pose = 1;

				// siblings
				$item_siblings = array();

				if ($sub_item_id):
					$siblings = $this->get_item_siblings($user_item['parent_id'], $user_item['item_id'], $user_id);

					if( ! empty($siblings)):
						foreach($siblings as $sibling_key => $sibling):
							if(($sibling_key+2) == $sub_item_id):
								$item_pose = ($sibling_key+2);
								$active_item = $sibling;
								$active_item['layer'] = $sibling['layer_id'];
								$debugger_data[] = "put on subling ".($item_pose-1);
							endif;
						endforeach;
					endif;
				endif;

				if ( ! empty($equipped_items)):
					foreach($equipped_items as $equipped_item):
						if ($equipped_item['layer'] == $active_item['layer']):
							// there is a item on this layer already
							$equipped_item['trying_on'] = 0;
							unset($equipped_item['layer']);
							$this->db->update('user_items', $equipped_item, array('id' => $equipped_item['id']));
							$debugger_data[] = "removed other on here";
						endif;
					endforeach;
				endif;

				if( ! $sub_item_id):
					$debugger_data[] = "putting on main item";
				endif;

				$user_item['trying_on'] = $item_pose;
			endif;

			unset($user_item['layer']);
			$this->db->update('user_items', $user_item, array('id' => $user_item['id']));
			$debugger_data[] = 'affected rows: '.$this->db->affected_rows();
		endif;

		// get the user_item and see if it's tried on or not, reverse what found
		return $debugger_data;
	}

// copy the eqippped info from temp to perm field aka trying_on->equipped
function avatar_save() {
	if($this->user_id != true):
		$user_id = 0;
	else:
		$user_id = $this->user_id;

		$user_items = $this->db->select('*')
                          ->from('user_items')
                          ->where('user_id',$user_id)
                          ->get()->result_array();
		if (!empty($user_items)) {
			foreach($user_items as $user_item):
				$user_item['equipped']=$user_item['trying_on'];
				$this->db->update('user_items', $user_item, array('id' => $user_item['id']));
			endforeach;
		}
	endif;
}

// copy the eqippped info from temp to perm field aka trying_on->equipped
function avatar_revert() {
	if($this->user_id != true):
		$user_id = 0;
	else:
		$user_id = $this->user_id;
		$this->strip_all();
		$user_items = $this->db->select('*')
								->from('user_items')
								->where('user_id',$user_id)
								->where('equipped >', 0)
								->get()
								->result_array();

		if (!empty($user_items)) {
			$items="";
			foreach($user_items as $user_item):
				$items=$items.'#'.$user_item['id'].';';
				$user_item['trying_on'] = $user_item['equipped'];
				$this->db->update('user_items', $user_item, array('id' => $user_item['id']));
			endforeach;
			return $items;
		}
	endif;
}

// unequip all the trying_on items
function strip(){
 	if($this->user_id != TRUE):
		$user_id = 0;
	else:
		$user_id = $this->user_id;
		$user_items = $this->db->select('user_items.*')
								->from('user_items')
								->where('user_items.user_id',$user_id)
								->where('avatar_layers.composite', 0)
								->where('user_items.trying_on >', 0)
								->join('avatar_items', 'avatar_items.item_id = user_items.item_id', 'LEFT')
								->join('avatar_layers', 'avatar_items.layer = avatar_layers.id', 'LEFT')
								->get()
								->result_array();

		if ( ! empty($user_items)):
			foreach($user_items as $user_item):
				$user_item['trying_on'] = 0;
				$this->db->update('user_items', $user_item, array('id' => $user_item['id']));
			endforeach;
		endif;
	endif;
}

function strip_all(){
 	if($this->user_id != true):
		$user_id = 0;
	else:
		$user_id = $this->user_id;
		$user_items = $this->db->select('user_items.*')
                          ->from('user_items')
                          ->where('user_items.user_id',$user_id)
													->where('user_items.trying_on',1)
													->join('avatar_items', 'avatar_items.item_id=user_items.item_id', 'LEFT')
													->join('avatar_layers', 'avatar_items.layer=avatar_layers.id', 'LEFT')
                          ->get()->result_array();
		if (!empty($user_items)) {

			foreach($user_items as $user_item):
				$user_item['trying_on']=0;
				$this->db->update('user_items', $user_item, array('id' => $user_item['id']));
			endforeach;
		}
		//$this->db->update('user_items', array('trying_on' => '0'), array('user_id' => $user_id));
	endif;
}

}

/* End of file forum_engine.php */
/* Location: ./system/application/models/forum_engine.php */
