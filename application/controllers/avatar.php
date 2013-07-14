<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Avatar extends CI_Controller
{
	var $avatar_config;
	var $avatar_data = NULL;
	var $memcached = FALSE;
	var $cache_durations = array(
		'inventory_cache' => 3600 // 1 hour
	);
	var $item_ids_tried_on = array();
	var $cached_sub_items = array();
	var $cached_sub_item_ids = array();

	function __construct()
	{
		parent::__construct();
		ini_set('memory_limit', '2016M');

		$query = $this->db->get('avatar_config'); // Getting config options...

		foreach($query->result_array() as $row):
			$this->avatar_config[$row['key']] = $row['value'];
		endforeach;

		$this->load->helper('avatar'); // Load some helper functions
		$this->load->model('avatar_engine'); // Helps make the code more poetic-like

		$this->user_id = $this->system->userdata['user_id'];
		$this->avatar_engine->user_id = $this->user_id;
	}

	// --------------------------------------------------------------------

	/**
	 * Avatar index
	 *
	 * Displays the main inventory with all the users items
	 *
	 * @access	public
	 * @param	none
	 * @return	view
	 * @route	n/a
	 */

	function index()
	{
		if( ! $this->user_id) redirect('/auth/signin');
		$this->system->view_data['scripts'][] = '/global/js/avatar/index.js';

		$equiped_items = $this->db->select('id, item_id, trying_on')->get_where('user_items', array('user_id' => $this->user_id, 'trying_on >' => 0))->result_array();

		foreach ($equiped_items as $key => $value):
			$this->item_ids_tried_on[$value['id']] = $value['trying_on'];
		endforeach;

		$this->benchmark->mark('inventory_query_start');
		$query = $this->avatar_engine->get_user_inventory($this->user_id);
		$this->benchmark->mark('inventory_query_end');

		$this->benchmark->mark('create_inventory_start');
		$items = $this->_create_inventory_array($query);
		$this->benchmark->mark('create_inventory_end');

		ksort($items);

		$compliments = array(
			"Lookin' good!",
			"Awesome avatar!",
			"Nice combination!",
			"Stunning look!",
			"Great look!",
			"You look great!",
			"Stunning outfit!",
			"Looks great!",
			"Wonderful look!",
			"Outstanding outfit!",
		);

		$view_data = array(
			'page_title'         => 'My Inventory',
			'page_body'          => 'avatar sub-inventory',
			'items'              => $items,
			'avatar_preview_url' => site_url('avatar/preview/'.md5(microtime())), // Another Cache workaround
			'compliment'         => $compliments[array_rand($compliments)]
		);

		$this->system->quick_parse('avatar/index', $view_data);
	}


	// --------------------------------------------------------------------

	/**
	 * Customize avatar
	 *
	 * Creates the avatar image and the headshot
	 *
	 * @access	public
	 * @param	none
	 * @return	redirect
	 * @route	n/a
	 */

	function customize()
	{
		$avatar_items = $this->avatar_engine->get_avatar_inventory($this->user_id);
		$items = array(); // Define empty array for the foreach()

		foreach($avatar_items as $row):
			$items[$row['layerorder']][$row['layername']][] = array(
				'name'       => $row['name'],
				'id'         => $row['main_id'],
				'equipped'   => $this->avatar_engine->is_tried_on($row['item_id']),//(isset($this->avatar_data['items'][$row['main_id']]) ? true : false),
				'num'        => $row['num'],
				'thumb'      => $row['thumb'],
				'compulsive' => true
			);
		endforeach;

		$data = array(
			'page_title'         => 'Avatar',
			'items'              => $items,
			'page_body'          => 'avatar sub-avatar',
			'avatar_preview_url' => site_url('avatar/preview/'.md5(microtime())) // Another Cache workaround
		);

		$this->system->quick_parse('avatar/main', $data);
	}


	// --------------------------------------------------------------------

	/**
	 * Equip item
	 *
	 * Equips or Unequips an item from the users avatar
	 *
	 * @access	public
	 * @param	(int) | (bool)
	 * @return	redirect
	 * @route	n/a
	 */
	function unequip(){
		if( ! $this->user_id) redirect('/auth/signin');
		$this->avatar_engine->strip();
		//redirect('avatar');
	}

	function revert($revert=true){
		if( ! $this->user_id) redirect('/auth/signin');
		$success=$this->avatar_engine->avatar_revert();
		echo $success;
		//if($revert) redirect('avatar');
	}

	function equip($item_id = null, $ajax = false) {
		if( ! is_null($item_id) && preg_match("/^([\-0-9])+$/i", $item_id)): //ok id

			$response = $this->avatar_engine->toggle_on($item_id);

			if($this->input->is_ajax_request()):
				$this->output->set_content_type('application/json')->set_output(json_encode($response, JSON_NUMERIC_CHECK));
			else:
				redirect('avatar');
			endif;
		endif;
	}


	// --------------------------------------------------------------------

	/**
	 * Save avatar
	 *
	 * Creates the avatar image and the headshot
	 *
	 * @access	public
	 * @param	none
	 * @return	redirect
	 * @route	n/a
	 */
	function save($ajax = false)
	{
		$my_id = $this->user_id;
		// $allowed_users = array(14, 2787, 1639, 2588, 13116, 3790);
		// if (in_array($this->user_id, $allowed_users)):
			// $image = $this->weird_test();
		// else:
			$image = $this->preview(true);
		// endif;

		$path = realpath(BASEPATH.$this->avatar_config['avatar_path']).'/'.$my_id.'.png';

		if($path):
			imagepng($image, $path);

			$this->headshot($my_id, 'new', true); // Lets generate the headshot!

			$items = $this->avatar_engine->avatar_save();

			$this->load->model('user_engine');
			$this->user_engine->set('last_saved_avatar', time());

			// Commented out the non-Ajax version, since we no longer support non-javascript avatar usage

			// if( ! $this->input->is_ajax_request()): // If the call is not an AJAX one, sleep a bit and redirect back
			// 	usleep(2000);
			// 	redirect('avatar/');
			// endif;
		else:
			trigger_error("An error occurred while attempting to save the avatar.");
		endif;
	}

	// --------------------------------------------------------------------

	/**
	 * View avatar
	 *
	 * Allows for viewing the avatar freshly generated or flipped
	 *
	 * @access	public
	 * @param	(int) | (bool) | (bool)
	 * @return	png.img
	 * @route	n/a
	 */

	function view($user_id = NULL, $flipped = NULL, $db_render = false)
	{
		$my_id = $this->user_id;

		if($db_render):
			$equipped_items = $this->avatar_engine->get_users_equipped_items((is_null($user_id) ? $my_id : $user_id), 'complex');
			$image		= imagecreatetruecolor($this->avatar_config['width'], $this->avatar_config['height']);
			$transcol	= imagecolorallocatealpha($image, 255, 0, 255, 127);
			$equipped = $equipped_items->result_array();
			if (is_array($equipped) && ! empty($equipped)):
				foreach($equipped as $row):
					$path	= realpath(BASEPATH.$this->avatar_config['items_path'].$row['path']);
					if($path):
						$image = merge_layers($image,$path,$this->avatar_config);
					endif;
				endforeach;
			endif;
		else:
			$path = realpath(BASEPATH.$this->avatar_config['avatar_path'].'/'.(is_null($user_id) ? $my_id : $user_id).'.png');
			if($path):
				$image = imagecreatefrompng($path);
			else:
				$image = imagecreatefrompng('/images/avatar_error.png');
			endif;
		endif;

		if($flipped):
			$flipped_sprite = $this->_gd_transparecy(imagecreatetruecolor($this->avatar_config['width'], $this->avatar_config['height']));

			for($i = 0; $i < $this->avatar_config['width']; $i++):
				imagecopy($flipped_sprite, $image, ($this->avatar_config['width'] - $i - 1), 0, $i, 0, 1, $this->avatar_config['height']);
			endfor;

			$image = $flipped_sprite;
		endif;

		header('content-type: image/png'); // PNG FILE
		header ("cache-control: must-revalidate");

		$transcol = imagecolorallocatealpha($image, 255, 0, 255, 127);
		$trans    = imagecolortransparent($image,$transcol);

		imagefill($image, 0, 0, $transcol);
		imagesavealpha($image, true);
		imagealphablending($image, true);

		imagepng($image);
		imagedestroy($image);
	}

	// --------------------------------------------------------------------

	/**
	 * View avatar
	 *
	 * Allows for viewing the avatar freshly generated or flipped
	 *
	 * @access	public
	 * @param	(int) | (bool) | (bool)
	 * @return	png.img
	 * @route	n/a
	 */

	function headshot($user_id = NULL, $gen = false)
	{
		$my_id = $this->user_id;
		$initial_path = realpath(BASEPATH.$this->avatar_config['avatar_path']).'/';

		if($gen != false):
			$path1	= $initial_path.(!is_null($user_id) ? $user_id : $my_id).'.png';
			$path2	= $initial_path.(!is_null($user_id) ? $user_id : $my_id).'_'.$this->avatar_config['headshot_ext'].'.png';

			// make the thumb
			$baseimage = imagecreatefrompng($initial_path.'headshot_base.png');
			imagealphablending($baseimage,true);
			imagesavealpha($baseimage, true);

			// add the image on it
			$image 	= imagecreatefrompng($path1); // create main graphic
			imagealphablending($image,true);
			imagesavealpha($image, true);

			// save the thumb
			imagecopy($baseimage, $image, 0, 0, 69, 40, 60, 60);
			imagealphablending($baseimage,true);
			imagesavealpha($baseimage, true);
			imagepng($baseimage,$path2);
			imagedestroy($image);  imagedestroy($baseimage);
		else:
			$path1 = $initial_path.( ! is_null($user_id) ? $user_id : $my_id).'_'.$this->avatar_config['headshot_ext'].'.png';

			if( ! $path1):
				$path1	= $initial_path.(!is_null($user_id) ? $user_id : $my_id).'.png';
				$path2	= $initial_path.(!is_null($user_id) ? $user_id : $my_id).'_'.$this->avatar_config['headshot_ext'].'.png';
				$baseimage = imagecreatefrompng($initial_path.'headshot_base.png');
				imagealphablending($baseimage,true);
				imagesavealpha($baseimage, true);

				// add the image on it
				$image 	= imagecreatefrompng($path1); // create main graphic
				imagealphablending($image,true);
				imagesavealpha($image, true);

				// save the thumb
				imagecopy($baseimage, $image, 0, 0, 69, 40, 60, 60);
				imagealphablending($baseimage,true);
				imagesavealpha($baseimage, true);
				imagepng($baseimage,$path2);
				imagedestroy($image);
				imagedestroy($baseimage);
			else:
				header('content-type: image/png');
				$image 	= imagecreatefrompng($path1);imagealphablending($image,true);
				imagesavealpha($image, true);

				imagepng($image);
				imagedestroy($image);
			endif;
		endif;
	}


	// --------------------------------------------------------------------

	/**
	 * Generate item thumbnail
	 *
	 * Allows generating
	 *
	 * @access	public
	 * @param	(int) | (bool) | (bool)
	 * @return	img
	 * @route	n/a
	 */

	function thumbnail($item_id = NULL, $num = 1)
	{
		if( ! is_null($item_id)):
			$image		= imagecreatetruecolor($this->avatar_config['thumbnail_width'], $this->avatar_config['thumbnail_height']);
			$transcol	= imagecolorallocatealpha($image, 255, 0, 255, 127);
			$trans		= imagecolortransparent($image,$transcol);

			imagefill($image, 0, 0, $transcol);
			imagesavealpha($image, true);
			imagealphablending($image, true);

			$query = $this->avatar_engine->get_item_thumbnail($item_id);

			if($query->num_rows > 0):
				$row	= $query->row_array();
				$path	= realpath(BASEPATH.$this->avatar_config['items_path'].$row['thumb']);

				if($path):
					$image = merge_layers($image, $path, $this->avatar_config);
				endif;
			endif;

			$font_path = APPPATH.'resources/arial.ttf';

			if( (int) $num != 1):
				$white = imagecolorallocate($image, 255, 255, 255);
				$black	= imagecolorallocate($image, 30, 30, 30);
				$strlen 	= (strlen($num) > 1 ? floor(strlen($num))*10 : 10)+30;
				imagettftext($image, 9, 10, 20, 42, $black, $font_path, $num.'x');
				imagettftext($image, 9, 10, 20, 38, $black, $font_path, $num.'x');
				imagettftext($image, 9, 10, 18, 40, $black, $font_path, $num.'x');
				imagettftext($image, 9, 10, 22, 40, $black, $font_path, $num.'x');
				imagettftext($image, 9, 10, 20, 40, $white, $font_path, $num.'x');
			endif;

			header('content-type: image/png'); // PNG FILE
			$transcol	= imagecolorallocatealpha($image, 255, 0, 255, 127);
			$trans		= imagecolortransparent($image,$transcol);

			imagepng($image);
			imagedestroy($image);
		else:
			echo "Invalid Handler";
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

	public function preview_item($item_id = 0, $flip_x = 0)
	{
	    $data = $this->avatar_engine->get_tried_on();

	    $items = array();
	    foreach($data as $equipped_item):
	    	if($equipped_item['trying_on'] > 1):
	    		// get which real avatar item this belongs to!
	    		$subitems = $this->avatar_engine->get_item_siblings($equipped_item['item_id'], $equipped_item['item_id'], $this->user_id);
	    		foreach ($subitems as $sub_item_key => $sub_item_data):
	    			if($equipped_item['trying_on'] == ($sub_item_key+2)):
	    				$items[] = (int)$sub_item_data['item_id'];
	    			endif;
	    		endforeach;
	    	else:
	    		$items[] = (int)$equipped_item['item_id'];
	    	endif;
	    endforeach;

	    $query2 = $this->avatar_engine->get_user_prew_items($items, $this->user_id, $this->system->userdata['avatar_gender']);

	    if ($item_id > 0):
	    	$avatar_item_query = $this->db->get_where('avatar_items', array('item_id' => $item_id));
	    	if($avatar_item_query->num_rows() > 0):
	    		$avatar_item_data = $avatar_item_query->row_array();
	    	else:
	    		show_error('avatar_item could not be found.');
	    	endif;
	    endif;

	    $new_query = array();

	    foreach ($query2 as $key => $item):
	    	$new_query[$item['layer']] = $item;
	    endforeach;

	    $new_query[$avatar_item_data['layer']] = $avatar_item_data;

	    ksort($new_query);

	    $images = array();
	    foreach($new_query as $r):
	    	$subimages = $this->avatar_engine->get_images($r['item_id'],$this->user_id);
	    	foreach($subimages as $s):
	    		$images[$s['order']] = $s['image_path'];
	    	endforeach;
	    endforeach;

	    ksort($images);

	    $avatar_frame = imagecreatetruecolor($this->avatar_config['width'], $this->avatar_config['height']);
	    $avatar_frame = $this->_gd_transparecy($avatar_frame);

	    foreach ($images as $item):
	    	imagecopy($avatar_frame, imagecreatefrompng(realpath(BASEPATH.$this->avatar_config['items_path'].'/'.$item)), 0, 0, 0, 0, $this->avatar_config['width'], $this->avatar_config['height']);
	    endforeach;

    	header('Content-type: image/png');
    	imagepng($avatar_frame);
    	imagedestroy($avatar_frame);
	}


	// --------------------------------------------------------------------

	/**
	 * Avatar preview
	 *
	 * Generates
	 *
	 * @access	public
	 * @param	(int) | (bool) | (bool)
	 * @return	img
	 * @route	n/a
	 */

	function preview($return = false, $item_id = NULL)
	{
		$data = $this->avatar_engine->get_tried_on();

		$items = array();
		foreach($data as $equipped_item):
			if($equipped_item['trying_on'] > 1):
				// get which real avatar item this belongs to!
				$subitems = $this->avatar_engine->get_item_siblings($equipped_item['item_id'], $equipped_item['item_id'], $this->user_id);
				foreach ($subitems as $sub_item_key => $sub_item_data):
					if($equipped_item['trying_on'] == ($sub_item_key+2)):
						$items[] = (int)$sub_item_data['item_id'];
					endif;
				endforeach;
			else:
				$items[] = (int)$equipped_item['item_id'];
			endif;
		endforeach;

		$query2 = $this->avatar_engine->get_user_prew_items($items, $this->user_id, $this->system->userdata['avatar_gender']);

		$images = array();
		foreach($query2 as $r):
			$subimages = $this->avatar_engine->get_images($r['item_id'],$this->user_id);
			foreach($subimages as $s):
				$images[$s['order']] = $s['image_path'];
			endforeach;
		endforeach;

		ksort($images);

		$avatar_frame = imagecreatetruecolor($this->avatar_config['width'], $this->avatar_config['height']);
		$avatar_frame = $this->_gd_transparecy($avatar_frame);

		foreach ($images as $item):
			imagecopy($avatar_frame, imagecreatefrompng(realpath(BASEPATH.$this->avatar_config['items_path'].'/'.$item)), 0, 0, 0, 0, $this->avatar_config['width'], $this->avatar_config['height']);
		endforeach;

		if($return == false || !is_bool($return)):
			header('Content-type: image/png');
			imagepng($avatar_frame);
			imagedestroy($avatar_frame);
		else:
			return $avatar_frame;
		endif;
	}

	// --------------------------------------------------------------------

	/**
	 * Get avatar flashdata
	 *
	 * Retrieves an array of the equipped items
	 *
	 * @access	private
	 * @param	n/a
	 * @return	array
	 * @route	n/a
	 */

	function _getFlashdata()
	{
		$avatar = $this->session->flashdata('avatar');
		if( ! is_null($this->avatar_data)):
			return $this->avatar_data;
		elseif($avatar):
			$this->_setFlashdata($avatar, false);
			return $avatar;
		else:
			$this->load->model('avatar_engine');
			$query = $this->avatar_engine->get_user_equipped_items($this->user_id, 'simple');
			$data = array('items' => array());

			foreach($query->result_array() as $row):
				$data['items'][$row['item_id']] = true;
			endforeach;

			$this->_setFlashdata($data);
			return $data;
		endif;

		trigger_error("An unknown error occurred while trying to load your avatar data.");
	}


	// --------------------------------------------------------------------

	/**
	 * Set avatar flashdata
	 *
	 * Sets new avatar equiped-items flashdata
	 *
	 * @access	private
	 * @param	n/a
	 * @return	array
	 * @route	n/a
	 */

	function _setFlashdata($new = array(), $redirect = true)
	{
		$this->avatar_data = $new;
		// $this->session->set_flashdata('avatar', $new);
	}


	// --------------------------------------------------------------------

	/**
	 * Item Composite check
	 *
	 * Check if item is composite
	 *
	 * @access	private
	 * @param	n/a
	 * @return	array
	 * @route	n/a
	 */

	function _isComposite($item_id = 0)
	{
		$data  = array('composite' => false, 'layername' => 'Unknown', 'order' => 999999999, 'layerid' => 0);
		$query = $this->avatar_engine->get_composite_item_data($item_id);

		foreach($query->result_array() as $row):
			if($row['layercomposite'] == 1):
				return array(
						'composite' => true,
						'layername' => $row['layername'],
						'order'     => $row['layerorder'],
						'layerid'   => $row['layerid']
					);
			else:
				$data = array(
					'composite' => false,
					'layername' => $row['layername'],
					'order'     => $row['layerorder'],
					'layerid'   => $row['layerid']
				);
			endif;
		endforeach;

		return $data;
	}


	// --------------------------------------------------------------------

	/**
	 * Get avatar flashdata
	 *
	 * Retrieves an array of the equipped items
	 *
	 * @access	private
	 * @param	n/a
	 * @return	array
	 * @route	n/a
	 * "Magical 2 value for sub items": Computer counting always starts at 0, and PHP knows 0 as false.
	 *  -> 1 would represent the main item, so we need to add one to show that it's a sub item,
	 *     add another one to make sure the code isn't interpreting the value as false. +1 would make the subkeys 1,
	 *     which would mean the item is targeting the parent and not the sub item keys
	 */

	function all(){

		$avatar	= $this->avatar_data;

		$query = $this->db->query("
			SELECT *,
				cai.name as itemname,
				al.order as layerorder,
				al.name as layername,
				caui.id as main_id,
				count( caui.id ) AS num
				FROM (`user_items` caui)
				JOIN `avatar_items` cai ON `cai`.`item_id` = `caui`.`item_id`
				JOIN `users` cu ON `cu`.`user_id` = `caui`.`user_id`
				JOIN `avatar_layers` al ON al.id = cai.layer
			WHERE `al`.`composite` = '0'
			AND caui.soft_deleted = 0
			AND `caui`.`user_id` = '".$this->user_id."'
			AND (
				CAST( cai.gender AS CHAR ) = CAST( cu.avatar_gender AS CHAR )
				OR CAST( cai.gender AS CHAR ) = 'Unisex'
			)
			GROUP BY `caui`.`item_id`
		")->result_array();

		$items = array(); // Define empty array for the foreach()

		foreach($query as $row)
		{
			$items[$row['main_tab']][$row['layerorder']][] = array(
				'name'     => $row['itemname'],
				'id'       => $row['main_id'],
				'equipped' => (isset($this->avatar_data['items'][$row['main_id']]) ? true : false),
				'num'      => $row['num'],
				'thumb'    => $row['thumb']
			);
		}

		$data = array(
			'page_title'         => 'My Inventory',
			'page_body'          => 'avatar sub-inventory',
			'items'              => $items,
			'avatar_preview_url' => site_url('avatar/preview/'.md5(microtime().uniqid())) // Another Cache workaround
		);

		$this->system->quick_parse('avatar/inventory_old', $data);
	}

	function _create_inventory_array($inventory_query)
	{
		$items = array();
		foreach($inventory_query as $row):
			if(strlen($row['item_grouper']) == 36):
				$subitems = $this->avatar_engine->get_item_siblings($row['item_id'], $row['item_id'], $this->user_id);
				$subs = array();
				if(count($subitems) > 0):
					foreach($subitems as $row_key => $sub_row):
						$subs[] = array(
							'name'         => $sub_row['itemname'],
							'equipped'     => (isset($this->item_ids_tried_on[$row['main_id']]) && $this->item_ids_tried_on[$row['main_id']] == ($row_key+2)),
							'thumb'        => $sub_row['thumb'],
							'layer'        => $sub_row['layer_id'],
							'item_id'      => $sub_row['item_id'],
							'sub_item_key' => ($row_key+2), // "Magical 2 value for sub items"
							'group_key'    => $row['item_grouper']
						);
					endforeach;
				endif;
			else:
				$subs = false;
			endif;

			$items[$row['main_tab']][$row['tab_name']][$row['layerorder']][] = array(
				'name'      => $row['itemname'],
				'id'        => $row['main_id'],
				'equipped'  => (isset($this->item_ids_tried_on[$row['main_id']]) && $this->item_ids_tried_on[$row['main_id']] == 1),
				'num'       => $row['num'],
				'layer'     => $row['layer_id'],
				'thumb'     => $row['thumb'],
				'sub_items' => $subs,
				'group_key' => $row['item_grouper']
			);
		endforeach;

		return $items;
	}

	function avatar_save_state(){
		echo $this->avatar_engine->get_save_state();
	}

	private function _gd_transparecy($image_obj = array())
	{
		$transcol   = imagecolorallocatealpha($image_obj, 255, 0, 255, 127);
		$trans      = imagecolortransparent($image_obj, $transcol);
		imagefill($image_obj, 0, 0, $transcol);
		imagesavealpha($image_obj, true);
		imagealphablending($image_obj, true);

		return $image_obj;
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

	public function swap_gender()
	{
	    if ( ! $this->user_id) redirect('signin');
	    if( ! $this->input->is_ajax_request()) show_error('You are not allowed to access this page');
	    if($_SERVER['REQUEST_METHOD'] != "POST"){ show_error('Data cannot be auto-linked'); }

	    $gender = $this->db->query("SELECT avatar_gender FROM users WHERE user_id = ".$this->user_id)->row();

	    if($gender->avatar_gender == "Male"){
	    	$new_gender = "Female";
	    	$this->db->where('item_id', 368)->update('user_items', array('equipped' => 1));
	    } elseif($gender->avatar_gender == "Female") {
	    	$new_gender = "Male";
	    	$this->db->where('item_id', 368)->update('user_items', array('equipped' => 0));
	    }

	    $this->db->where('user_id', $this->user_id)->update('users', array('avatar_gender' => $new_gender));

	    $this->system->parse_json(array('success' => 1));
	}

	// --------------------------------------------------------------------

	/**
	 * Weird Test
	 *
	 * This should be reserved to test out per-pixel effects
	 *
	 * @access  public
	 * @param   none
	 * @return  output
	 */

	public function weird_test()
	{
		if ( ! isset($this->user_id)):
			show_error('Sign in required');
		endif;

		$avatar_frame = $this->preview(TRUE);

		$image = $avatar_frame;
		// $transcol	= imagecolorallocatealpha($image, 255, 0, 255, 127);

		$z_rand = mt_rand(1, 10);

		for($x = 0; $x < $this->avatar_config['width']/2; $x++) imagecopy($image, $avatar_frame, ($this->avatar_config['width'] - $x - 1), 0, $x, 0, $z_rand, $this->avatar_config['height']);
		for($x = 0; $x < $this->avatar_config['width']*2; $x++) imagecopy($image, $avatar_frame, ($this->avatar_config['width'] - $x - 1), 0, $x, 0, 1, $this->avatar_config['height']);

		// $edge_image = imagecreatetruecolor($this->avatar_config['width'], $this->avatar_config['height']);
		// imagecopy($edge_image, $image, 0, 0, 0, 0, $this->avatar_config['width'], $this->avatar_config['height']);
		$image = $this->_gd_transparecy($image);
		imagefilter($image, IMG_FILTER_COLORIZE, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 50));
		imagefilter($image, IMG_FILTER_MEAN_REMOVAL);
		imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);

		// imagecopy($image, $this->_gd_transparecy($edge_image), 0, ($this->avatar_config['height']/2), 0, ($this->avatar_config['height']/2), $this->avatar_config['width'], $this->avatar_config['height']);

		return $image;
	}
}

/* End of file avatar.php */
/* Location: ./system/application/controllers/avatar.php */