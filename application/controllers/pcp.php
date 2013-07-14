<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pcp extends CI_Controller
{
	var $route_navigation = array(
		'index' => 'List items',
		'createitem' => 'Install item',
		'index' => 'List items',
	);

	function Pcp()
	{
		parent::__construct();
		if ( ! $this->system->is_staff()):
			show_error('You are not allowed in here');
		endif;
	}

	// --------------------------------------------------------------------

	function index()
	{
		if ( ! $total_rows = $this->cache->get('total_avatar_items')):
			$total_rows = $this->db->count_all('avatar_items');
			$this->cache->save('total_avatar_items', $total_rows, 200);
		endif;

		$this->load->library('pagination');
		$config['base_url'] = '/pcp/index/';
		$config['total_rows'] = $total_rows;
		$config['per_page'] = 12;
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);

		$items = $this->db->select('avatar_items.*, avatar_layers.name as layer_name')
						  ->order_by('item_id', 'DESC')
						  ->limit($config['per_page'], $this->uri->segment(3, 0))
						  ->join('avatar_layers', 'avatar_items.layer = avatar_layers.id')
						  ->get('avatar_items')
						  ->result_array();

		$view_data = array(
			'page_title' => 'Modify items',
			'page_body'  => 'pcp',
			'items'      => $items,
			'routes'     => $this->route_navigation,
			'active_url' => $this->uri->rsegment(2, 0),
		);

		$this->system->quick_parse('pcp/index', $view_data);
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

	public function edit_item($item_id = 0)
	{
		$item_data = $this->db->select('avatar_items.*, avatar_layers.name as layer_name')
							  ->join('avatar_layers', 'avatar_items.layer = avatar_layers.id')
							  ->get_where('avatar_items', array('item_id' => $item_id))
							  ->row_array();

		$layers = array();
		foreach($this->db->get('avatar_layers')->result_array() as $row):
			$layers[$row['order']] = array("name" => $row['name'], "id" => $row['id']);
		endforeach;
		ksort($layers);

		$item_children = array();

		$item_children = $this->db->select('avatar_item_parts.*, avatar_layers.name as layer_name')
							  ->join('avatar_layers', 'avatar_item_parts.layer = avatar_layers.id')
							  ->get_where('avatar_item_parts', array('item_id' => $item_id))
							  ->result_array();

		$view_data = array(
			'page_title' => 'Modify item',
			'page_body'  => 'pcp',
			'item'       => $item_data,
			'routes'     => $this->route_navigation,
			'active_url' => $this->uri->rsegment(2, 0),
			'layers'     => $layers,
			'children'   => $item_children
		);

		$this->system->quick_parse('pcp/edit_item', $view_data);
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

	function item_info($id){
        $item = $this->db->select('avatar_items.name, avatar_items.thumb, avatar_layers.name as l_name')
                 ->from('avatar_items')
                 ->join('avatar_layers', 'avatar_items.layer = avatar_layers.id')
                 ->where('item_id', $id)
                 ->get()
                 ->row_array();

        echo json_encode($item);
	}

	function multi_pose(){
		$this->system->quick_parse('pcp/multi_pose',array());
	}

	function uploaditems(){
		//Upload regular item images
		$this->system->quick_parse('pcp/uploadfull',array());
	}

	function uploadthumbs(){
		//Upload thunb item images
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

	function admin()
	{
	    $items = $this->db->select('*')
                          ->from('event_items')
                          ->join('avatar_items', 'event_items.item_id = avatar_items.item_id')
                          ->get()
                          ->result_array();

	    $data = array(
	       'page_title' => '',
	       'config' => $this->system->site_config,
	       'items' => $items
	    );

	    unset($data['config']['donation_item_ids'],
	    	$data['config']['dashboard_donation_image'],
	    	$data['config']['large_donation_image'],
	    	$data['config']['donation_name']);

	    $this->system->quick_parse('pcp/admin', $data);
	}


	function admin_save()
	{
	    foreach ($this->input->post('config') as $key => $value) {
	       $this->db->where('key', $key)->update('site_config', array('value' => $value));
	    }
	    redirect('pcp/admin');
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

	function grant_invitation()
	{
		$this->system->quick_parse('pcp/grant_invitation',array());
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

	function user_info($name = 0)
	{

		$name = str_replace('_', ' ', $name);
	    $item = $this->db->select('username, user_id, reffered')
                 ->from('users')
                 ->where('username', $name)
                 ->get()
                 ->row_array();

        echo json_encode($item);
	}

	function grant_prizes(){
        $user = $this->db->select('username, user_palladium, user_id, reffered')
                 ->from('users')
                 ->where('username', $this->input->post('username'))
                 ->get()
                 ->row_array();

	    $this->load->model('user_engine');

		$this->user_engine->add_palladium($this->input->post('pallaas'), $user['user_id']);

	    foreach($this->input->post('prizes') as $prize){
	        switch($prize):
                case 'p_1':
    				$this->user_engine->add_palladium('150', $user['user_id']);
    				$response[] = "Palla granted!";
    			break;
    			case 'p_2':
    				$this->user_engine->add_item(5393, 1, $user['user_id']);
    				$response[] = "Belt granted!";
    			break;
    			case 'p_3':
    				$this->user_engine->add_items(array(3323, 3324, 3324), $user['user_id']);
    				$response[] = "Nets granted!";
    			break;
    			case 'p_4':
    				$this->user_engine->add_item(5395, 1, $user['user_id']);
    				$response[] = "Shirt granted!";
    			break;
	        endswitch;
	    }
	    echo json_encode($response);
	}

	/* ITEM CREATION */
	function createitem(){
		$this->system->view_data['scripts'][] = '/global/js/pcp/index.js';
		$query = $this->db->get('avatar_layers');
		$layers = array();
		foreach($query->result_array() as $row){
			$layers[$row['order']] = array("name"=>$row['name'], "id"=>$row['id']);
		}
		ksort($layers);
		$query1 = $this->db->query("SELECT name, item_id FROM avatar_items ORDER BY item_id DESC LIMIT 15");

		$latest = $query1->result_array();

		$data = array(
			'layers' => $layers,
			'latest' => $latest,
			'page_body' => 'pcp'
		);
		$this->system->quick_parse('pcp/additem',$data);
	}

		function docreate(){

			// $this->session->set_flashdata('part_gender', $_POST['part']['gender']);
			// $this->session->set_flashdata('part_layer', $_POST['part']['layer']);
			// $this->session->set_flashdata('item_single', $_POST['item']);

			$item = array();
			$item['name']		= $_POST['item']['name'];
			$item['gender']		= $_POST['item']['gender'];
			$item['order']		= 0;
			$item['layer']		= $_POST['item']['layer'];
			$item['composite']	= (isset($_POST['item']['composite']) ? $_POST['item']['composite'] : 0);
			$item['default']	= (isset($_POST['item']['default']) ? $_POST['item']['default'] : 0);
			$item['thumb']		= $this->_getUniqueCode().".png";
			$fullpaththumb		= realpath(BASEPATH.'../images/items/').'/'.$item['thumb'];
			//var_dump( $fullpaththumb );
			//exit;
			//Handle Items
			$data = $_FILES['item'];
			$name	= $data['name']['thumbnail'];
			$size	= $data['size']['thumbnail'];
			$error	= $data['error']['thumbnail'];
			$temp	= $data['tmp_name']['thumbnail'];
			if($error == 0 && $size > 0 && substr($name,-3) == 'png') // No need for freakishly validation. We trust our pixelists. :D
			{
				if(!move_uploaded_file($temp, $fullpaththumb)) {
					echo "Failed to move thumbnail, creating without thumbnail.<br>";
					unset($item['thumb']);
				}
			} else {
				echo "Failed to upload thumbnail Reason: ".$this->_fileUploadErrorMessage($error)." Continuing without creating thumbnail.<br>";
			}
			$this->db->insert('avatar_items',$item);
			$item_id = $this->db->insert_id();
			unset($data,$name,$size,$error,$temp,$fullpaththumb,$item);
			$data = $_FILES['part'];
			foreach($data['name']['path'] as $k=>$v){
				$name	= $v;
				$temp	= $data['tmp_name']['path'][$k];
				$error	= $data['error']['path'][$k];
				$size	= $data['size']['path'][$k];
				if($size > 0 && $error == 0 && substr($name,-3) == 'png'){
					$insert = array();
					$insert['image_path']	= $this->_getUniqueCode().".png";
					$insert['item_id']		= $item_id;
					$insert['gender']		= $_POST['part']['gender'][$k];
					$insert['layer']		= $_POST['part']['layer'][$k];
					$insert['name']			= $_POST['item']['name'];
					$fullpath				= realpath(BASEPATH.'../images/items/').'/'.$insert['image_path'];
					if(move_uploaded_file($temp,$fullpath)){
						if($insert['gender'] == 'Male' || $insert['gender'] == 'Female'):
							$this->db->insert('avatar_item_parts', $insert);
						else:
							$insert['gender'] = 'Male';
							$this->db->insert('avatar_item_parts',$insert);
							$insert['gender'] = 'Female';
							$this->db->insert('avatar_item_parts',$insert);
						endif;

					} else {
						//echo "Failed to upload: ".$name." Reason: Could not move file.<br>";
						//redirect('pcp/createitem');
					}
				} else {
	//				echo "Failed to upload: ".$name." Reason: ".$this->_fileUploadErrorMessage($error)." (or invalid file type)<br>";
						//redirect('pcp/createitem');
				}
			}
			redirect('pcp/createitem');
		}

	/* LAYERING GUIDE */
	function layers(){
		$this->load->helper('form');
		$layers = $this->db->query(" select * from avatar_layers where id in (SELECT distinct layer FROM `avatar_items` where name!='') order by `order` asc")->result_array();


		$data = array('main_layers' => $layers,  'page_body' => 'pcp', 'page_title' => 'View layering',);
		$this->system->quick_parse('pcp/layering_info',$data);


		}
	function get_items_on_layer($layer_id){
		$items = $this->db->query("SELECT * FROM `avatar_items` where name!='' and layer=".mysql_real_escape_string($layer_id)." group by replace(name, substr(name, locate('(', name) ), '') order by name")
									->result_array();
		foreach($items as $item):
			echo "<img id=\"i".$item['item_id']."\" src=\"".site_url('images/items/'.$item['thumb'])."\" onclick=\"get_subs(".$item['item_id'].")\" height=\"42\", width=\"42\" alt=\"".$item['name']."\" title=\"".$item['name']."\"/>";
		endforeach;
	}


	function get_sub_layers($item_id){
		$items = $this->db->query("SELECT a.*, l.name as layername FROM `avatar_item_parts` a, avatar_layers l where a.layer=l.id and a.name!='' and a.item_id=".mysql_real_escape_string($item_id)." group by a.item_id, a.layer")
									->result_array();
		echo "<a href=\"".site_url('item_manager/item_id/'.$items[0]['item_id'])."\" target=\"_blank\">";
		echo $items[0]['name']."</a><br/>";
		foreach($items as $item):
			echo "<img src=\"".site_url('images/items/'.$item['image_path'])."\" alt=\"".$item['name']."\" title=\"".$item['name']."\"/> on ".$item['layername'];
		endforeach;
		}


		function get_other_items($item_id){
			$item = $this->db->query("SELECT replace(name, substr(name, locate('(', name) ), '') as name, layer FROM `avatar_items` where item_id=".mysql_real_escape_string($item_id))
									->result_array();
			$items = $this->db->query("SELECT * FROM `avatar_items` where item_id!=".mysql_real_escape_string($item_id)." and layer=".mysql_real_escape_string($item[0]['layer'])." and replace(name, substr(name, locate('(', name) ), '')='".mysql_real_escape_string($item[0]['name'])."' ")
									->result_array();
			if (!empty($items)):
				echo "<h4>other colors:</h4>";
				foreach($items as $i):
					echo "<a href=\"".site_url('item_manager/item_id/'.$i['item_id'])."\" target=\"_blank\"><img src=\"".site_url('images/items/'.$i['thumb'])."\" alt=\"".$i['name']."\" title=\"".$i['name']." - manage\"/></a><br/>";
				endforeach;
			endif;
			$poses = $this->db->query("SELECT * FROM `avatar_items` where item_id!=".mysql_real_escape_string($item_id)." and layer!=".mysql_real_escape_string($item[0]['layer'])." and replace(name, substr(name, locate('(', name) ), '')='".mysql_real_escape_string($item[0]['name'])."' ")
									->result_array();
			if (!empty($poses)):
				echo "<h4>other poses:</h4>";
				foreach($poses as $item):
					echo "<a href=\"".site_url('item_manager/item_id/'.$item['item_id'])."\" target=\"_blank\">";
					echo "<img src=\"".site_url('images/items/'.$item['thumb'])."\" alt=\"".$item['name']."\" title=\"".$item['name']." - manage\"/></a>";
					echo "<span class=\"pointer spanlink\" onclick=\"get_subs(".$item['item_id'].")\">view</span><br/>";
				endforeach;
			endif;

		}

	// --------------------------------------------------------------------

	/**
	 * TYI manager
	 *
	 * Update the TYI's
	 *
	 * @access	public
	 * @param	none
	 * @return	redirect
	 * @route	n/a
	 */
	function tyi_manager(){
		$this->load->helper('file');
//		$files = get_dir_file_info($this->system->site_config['donation_image_folder']);
//		$files = get_dir_file_info('images/tyi_images');
		$files = scandir($this->system->site_config['donation_image_folder']);
		for($i = 0; $i<count($files); $i++){
			if($files[$i] == '.' || $files[$i] == '..' || $files[$i] == '.DS_Store'){
				unset($files[$i]);
			}
		}
		$data['files'] = $files;
		$this->system->quick_parse('pcp/tyi_manager', $data);
	}

	function new_tyi_dir()
	{
		if(!$this->input->post('new_dir_name') || strlen($this->input->post('new_dir_name')) == 0){
			die('No name!');
		}
		$dir_name = $this->system->url_formatted_sting($this->input->post('new_dir_name'));
		if(mkdir('./'.$this->system->site_config['donation_image_folder'].'/'.$dir_name, 0777)){
		//	chmod($this->system->site_config['donation_image_folder'].'/'.$dir_name, 777);
			redirect('pcp/tyi_manager');
		}else{
			die('Error making dir');
		}
	}

	function upload_tyi_images()
	{
		//CHECK IF DIR EXISTS!!!
		if(strlen($this->input->post('upload_dir')) == 0 )
		{
			die('Error picking DIR');
		}
		$upload_dir = $this->input->post('upload_dir');

		$config['upload_path'] = $this->system->site_config['donation_image_folder'].'/'.$this->input->post('upload_dir');
		$config['allowed_types'] = 'gif|jpg|png';

		$file_name = $_FILES['upload_item']['name'];
		$file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
		$pure_file_name = basename($file_name,  '.'.$file_ext);
		$config['file_name'] = $this->system->url_formatted_sting($pure_file_name);


		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload('upload_item'))
		{
			$error = array('error' => $this->upload->display_errors());
			print_r($error);
			die();

		}
		else
		{
			redirect('pcp/tyi_manager');

		}

	}


	function tyi_read_dir($dir)
	{
		$files = scandir($this->system->site_config['donation_image_folder'].'/'.$dir);
		for($i = 0; $i<=count($files); $i++){
			if($files[$i] == '.' || $files[$i] == '..' || $files[$i] == '.DS_Store'){
				unset($files[$i]);
			}
		}
		foreach($files as $file){
			echo '<option value="'.$file.'">'.$file.'</option>';
		}
	}


	function set_new_tyi(){
		$img_dir = $this->input->post('dir_name');
		$item_name = htmlspecialchars($this->input->post('tyi_name'));
		$dashboard_image = $this->input->post('dashboard_donation_image');
		$large_donation_image = $this->input->post('large_donation_image');
		$item_ids = $this->input->post('item_ids');

		$update_data = array(
								'dashboard_donation_image' => $img_dir.'/'.$dashboard_image,
								'large_donation_image' => $img_dir.'/'.$large_donation_image,
								'donation_name' => $item_name,
								'donation_item_ids' => serialize($item_ids)
							);

	    foreach ($update_data as $key => $value) {
	       $this->db->where('key', $key)->update('site_config', array('value' => $value));
	    }
		redirect('pcp/tyi_manager');
	}


	function add_bug_item()
	{
//		$data['insets'] = $this->forest_engine->get_bugs();

		if($_POST){
			$inset_array = array(
						'item_id' => $this->input->post('item_id'),
						'shop_id' => 6,
						'item_parent' => 0,
						'price' => $this->input->post('item_price'),
						'item_currency' => 'Bugs',
						'insect_id' => $this->input->post('insect_id'),
						'second_price' => $this->input->post('second_price')
					);
			$this->db->insert('shop_items', $inset_array);
		}


		$this->system->quick_parse('pcp/add_bug_item');


	}




	function navigation()
	{

		$nav = $this->db->order_by('group asc, order asc')->get('site_navigation')->result_array();
		$data = array(
					'navigation' => $nav
				);

		$this->system->quick_parse('pcp/navigation', $data);

	}


	function update_navigation()
	{
		$array	= $_POST['listids'];

		if ($_POST['update'] == "update"){

			$count = 1;
			foreach ($array as $idval) {
				$update_array = array(
							'order' => $count
						);
				$this->db->where('nav_id', $idval)->update('site_navigation', $update_array);
				$count ++;
			}
			echo 'All saved! refresh the page to see the changes';
		}



	}


	function _getUniqueCode($length = "")
	{
		$code = md5(uniqid(rand(), true));
		if ($length != "") return substr($code, 0, $length);
		else return $code;
	}
	function _fileUploadErrorMessage($error_code) {
		switch ($error_code) {
			case UPLOAD_ERR_INI_SIZE:
				return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
			case UPLOAD_ERR_FORM_SIZE:
				return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
			case UPLOAD_ERR_PARTIAL:
				return 'The uploaded file was only partially uploaded';
			case UPLOAD_ERR_NO_FILE:
				return 'No file was uploaded';
			case UPLOAD_ERR_NO_TMP_DIR:
				return 'Missing a temporary folder';
			case UPLOAD_ERR_CANT_WRITE:
				return 'Failed to write file to disk';
			case UPLOAD_ERR_EXTENSION:
				return 'File upload stopped by extension';
			default:
				return 'Unknown upload error';
		}
	}
}
?>
