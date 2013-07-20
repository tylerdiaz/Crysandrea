<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forest extends CI_Controller
{
  var $recover_interval = 300; // 5 minutes
  var $user_table = 'forest_users';
  var $snap_free = TRUE;
  var $route_navigation = array(
    'index'        => 'Forest',
    'museum'       => 'Your museum',
    'leaderboards' => 'Leaderboards',
    'bugpack'      => 'Bugpack'
  );

  function __construct(){
    parent::__construct();

    if( ! $this->session->userdata('user_id')):
      redirect('/auth/signin/?r=forest');
    endif;
  }

  // --------------------------------------------------------------------

  /**
   * Home page
   *
   * Forest main function
   *
   * @access  public
   * @param   none
   * @return  view
 */

  public function index()
  {
    $this->system->view_data['scripts'][] = '/global/js/forest/index.js';

    $user_id = $this->session->userdata('user_id');

      if( ! $hunter_data = $this->_get_hunter_data($user_id)):
        redirect('/forest/tour');
      else:
        $hunter_data = $this->_sync_energy($hunter_data);
    endif;

    $net_query = $this->db->get('forest_nets')->result_array();
    $this->nets = array();

    foreach ($net_query as $key => $value):
      $this->nets[$value['item_id']] = $value;
    endforeach;

    $net_equipped = $this->db->where('user_id', $this->system->userdata['user_id'])
                             ->where('equipped', 1)
                             ->where('soft_deleted', 0)
                             ->where_in('user_items.item_id', array_keys($this->nets))
                             ->join('avatar_items', 'avatar_items.item_id = user_items.item_id', 'left')
                             ->join('forest_nets', 'avatar_items.item_id = forest_nets.item_id', 'left')
                             ->limit(1)
                             ->get('user_items');

    $view_data = array(
      'page_title'        => 'Forest',
      'page_body'         => 'forest',
      'routes'            => $this->route_navigation,
      'active_url'        => $this->uri->rsegment(2, 0),
      'navigation_header' => 'Crysandrea\'s Forest',
      'hunter_data'       => $hunter_data,
      'recover_time_left' => ($hunter_data['max_energy']-$hunter_data['energy'])*$this->recover_interval,
      'net'               => ($net_equipped->num_rows() > 0 ? $net_equipped->row()->tag : 'none')
    );

    $this->system->quick_parse('forest/index', $view_data);
  }

  // --------------------------------------------------------------------

  /**
   * Check nets
   *
   * Go through the logic flow of what to do when a person breaks their net
   *
   * @access  public
   * @param   array
   * @return  json
   */

  public function check_nets($hunter_data = array(), $json = TRUE)
  {
    $response = array();

    if($_SERVER['REQUEST_METHOD'] == "POST"):

    else:
      if( ! is_array($hunter_data)):
        if( ! $hunter_data = $this->_get_hunter_data($this->system->userdata['user_id'])):
          $this->system->parse_json(array('error' => 'not a forester'));
        endif;
      endif;

      $net_query = $this->db->get('forest_nets')->result_array();
      $this->nets = array();

      foreach ($net_query as $key => $value):
        $this->nets[$value['item_id']] = $value;
      endforeach;

      $net_equipped = $this->db->where('user_id', $this->system->userdata['user_id'])
                               ->where('equipped', 1)
                               ->where('soft_deleted', 0)
                               ->where_in('user_items.item_id', array_keys($this->nets))
                               ->join('avatar_items', 'avatar_items.item_id = user_items.item_id', 'left')
                               ->join('forest_nets', 'avatar_items.item_id = forest_nets.item_id', 'left')
                               ->limit(1)
                               ->get('user_items');

      if($net_equipped->num_rows() > 0):
        $net_data = $net_equipped->row_array();
        $response['message'] = 'You already have a net equipped!';
        $response['error'] = 1;
        $response['response'] = 'currently_equipped';
      else:
        $nets_owned = $this->db->select('user_items.id, avatar_items.name, avatar_items.item_id, COUNT(1) as total')
                               ->where('user_id', $this->system->userdata['user_id'])
                               ->where('equipped', 0)
                               ->where('soft_deleted', 0)
                               ->where_in('user_items.item_id', array_keys($this->nets))
                               ->group_by('avatar_items.item_id')
                               ->join('avatar_items', 'avatar_items.item_id = user_items.item_id', 'left')
                               ->join('forest_nets', 'avatar_items.item_id = forest_nets.item_id', 'left')
                               ->get('user_items');

            if($nets_owned->num_rows() > 0):
              $nets_owned = $nets_owned->result_array();

              if (count($nets_owned) == 1):
                $response['message'] = 'You have an extra net in your inventory, here\'s a useful button so you can equip it right away. &darr;';
              else:
                $response['message'] = 'You have some nets in your inventory, here\'s the ones you could equip right away.';
              endif;

              $response['options'] = array();
              $response['response'] = 'already_owned';

              foreach ($nets_owned as $net):
                $response['options'][] = array(
                  'label' => 'Equip '.$net['name'],
                  'id' => $net['id'],
                  'tag' => $this->nets[$net['item_id']]['tag']
                );
              endforeach;

            else:
              $nets_for_sale = $this->db->order_by('price', 'DESC')->join('avatar_items', 'avatar_items.item_id = shop_items.item_id')->where_in('shop_items.item_id', array_keys($this->nets))->get('shop_items')->result_array();
              $affordable_nets = array();

              foreach ($nets_for_sale as $net):
                if($this->system->userdata['user_palladium'] >= $net['price']):
                  $affordable_nets[$net['item_id']] = array(
                    'item_name' => $net['name'],
                    'price' => $net['price'],
                    'item_id' => $net['item_id'],
                    'shop_item_id' => $net['shop_item_id'],
                  );
                endif;
              endforeach;

              if (count($affordable_nets) > 0):
                // hey check it out, you can buy these! Would you like one?
                $response['message'] = 'We searched the shops and found you could afford '.(count($affordable_nets) == count($this->nets) ? 'any net you want' : (count($affordable_nets) == 1 ? 'a net' : 'some nets')).', below is a convinient quick-purchase area.';
                $response['response'] = 'shop_purchase';

                foreach ($affordable_nets as $shop_net):
                  $response['options'][] = array(
                    'label'        => $shop_net['item_name'],
                    'shop_item_id' => $shop_net['shop_item_id'],
                    'price'        => $shop_net['price']
                  );
                endforeach;
              else:
                // $cached_gift_net = $this->cache->get('free_net_gift'.$this->system->userdata['user_id']);
                // if( ! $cached_gift_net && strtotime($this->system->userdata['register_date']) > time()-604800): // 1 week in seconds
                //  // Are you just one week old? If so, we'll give you a free net this once
                //  $this->load->model('user_engine');

                //  // add a small bug net to the user's inventory and auto-equip it onto them
                //  // $this->user_engine->add_item(3323, 1, $this->system->userdata['user_id'], 1);

                //  // $this->cache->set('free_net_gift'.$this->system->userdata['user_id'], '1', 604800);
                // else:
                  $forest_user_catches_query = $this->db->get_where('forest_user_catches', array('user_id' => $this->system->userdata['user_id']));
                  if($forest_user_catches_query->num_rows() > 0):
                    // Hey! You have some bugs avaliable to sell, you should sell some to afford a new net. Take me to my bugpack >>
                    $response['message'] = 'You can\'t afford any nets at the momemnt, but you have '.($forest_user_catches_query->num_rows() > 10 ? 'plenty of' : 'a few').' bugs to sell in your bugpack. Here is a <a href="/forest/bugpack">handy link to your bugpack &rsaquo;</a>';
                    $response['response'] = 'sell_bugs';
                  else:
                    // You should post around the forums to get some palladium and buy a new net!
                    $response['message'] = 'You can\'t afford any nets at the moment, you should <a href="/forum">post around the forums &rsaquo;</a> to get some more palladium!';
                    $response['response'] = 'no_qualifications';
                  endif;
                // endif;
              endif;
            endif;
      endif;
    endif;

    if ($json):
      $this->system->parse_json($response);
    else:
      return $response;
    endif;
  }


  // --------------------------------------------------------------------

  /**
   * Forest Tour
   *
   * Give a brief description of what the forest is about, while setting up their forest data
   *
   * @access  public
   * @param   none
   * @return  view
   */

  public function tour()
  {
    $user_id = $this->session->userdata('user_id');

    $this->load->model('forest_engine');

    if( ! $hunter_data = $this->_get_hunter_data($user_id)):
      $this->forest_engine->new_forester($user_id);
    endif;

    $view_data = array(
      'page_title'        => 'Forest Tour',
      'page_body'         => 'forest',
      'routes'            => $this->route_navigation,
      'active_url'        => $this->uri->rsegment(2, 0),
      'navigation_header' => 'Crysandrea\'s Forest Tour',
    );

    $this->system->quick_parse('forest/tour', $view_data);
  }


  // --------------------------------------------------------------------

  /**
   * Forest Museum
   *
   * See the bugs that a user has in their "bugpack"
   *
   * @access  public
   * @param   string
   * @return  view
 */

  public function museum($username = '')
  {
    if (strlen($username) == 0):
      $username = $this->system->userdata['username'];
      $user_id = $this->system->userdata['user_id'];
    else:
      $user = $this->db->get_where('users', array('username' => $username));
      if ($user->num_rows() == 0):
        show_error('This user does not exist');
      endif;

      $user = $user->row_array();
      $username = $user['username'];
      $user_id = $user['user_id'];
    endif;

    if( ! $hunter_data = $this->_get_hunter_data($user_id)):
      show_error('This user has no museum!');
    endif;

    $total_forest_stats = $total_caught_stats = array('common' => 0, 'uncommon' => 0, 'rare' => 0);

    if ( ! $all_bugs = $this->cache->get('forest_bugs')):
      $all_bugs_query = $this->db->get('forest_insects')->result_array();

      foreach ($all_bugs_query as $bug):
        $all_bugs[$bug['id']] = $bug;
      endforeach;

      $this->cache->save('forest_bugs', $all_bugs, 900);
    endif;

    $forest_insects = $this->db->select('insect_id, COUNT(1) as total')
                               ->where(array('user_id' => $user_id))
                               ->group_by('insect_id')
                               ->get('forest_user_catches')
                               ->result_array();

    $formatted_forest_insects = array();

    foreach ($forest_insects as $insect):
      $formatted_forest_insects[$insect['insect_id']] = $insect;
    endforeach;

    foreach ($all_bugs as $bugs):
      $return_insect[$bugs['id']] = $bugs;

      if(isset($formatted_forest_insects[$bugs['id']])):
        $return_insect[$bugs['id']]['total'] = $formatted_forest_insects[$bugs['id']]['total'];
        $total_caught_stats[$all_bugs[$bugs['id']]['rarity_classification']]++;
        $total_forest_stats[$all_bugs[$bugs['id']]['rarity_classification']]++;
      else:
        $return_insect[$bugs['id']]['total'] = NULL;
        $total_forest_stats[$all_bugs[$bugs['id']]['rarity_classification']]++;
      endif;
    endforeach;

    $view_data = array(
      'page_title'        => $username.'\'s Museum',
      'page_body'         => 'forest forest_museum',
      'routes'            => $this->route_navigation,
      'active_url'        => $this->uri->rsegment(2, 0),
      'navigation_header' => $username.'\'s Museum',
      'bugs'              => $return_insect,
      'user_id'           => $user_id,
      'username'          => $username,
      'forest_stats'      => $total_forest_stats,
      'caught_stats'      => $total_caught_stats,
      'leaderboard_place' => $this->leaderboard_place($hunter_data),
      'hunter_data'       => $hunter_data
    );

    $this->system->quick_parse('forest/museum', $view_data);
  }

  // --------------------------------------------------------------------

  /**
   * Forest Leaderboards
   *
   * List out friends and overall community by level
   *
   * @access  public
   * @param   none
   * @return  view
 */

  public function leaderboards()
  {
    $user_id = $this->system->userdata['user_id'];

    if( ! $hunter_data = $this->_get_hunter_data($user_id)):
      $this->forest_engine->new_forester($user_id);
    endif;


    $friends = $this->db->select('username, user_email, friend_id')
                        ->join('users', 'users.user_id = friends.friend_id')
                        ->where('friends.user_id', $this->system->userdata['user_id'])
                        ->where('active', 1)
                        ->get('friends');

    $global_leaderboards = array();
    $friend_leaderboards = array();

    if($friends->num_rows() > 0):
      $friends = $friends->result_array();
      $friend_array = array();
      foreach ($friends as $friend):
        $friend_array[] = $friend['friend_id'];
      endforeach;

      $friend_array[] = $this->system->userdata['user_id'];

      $forester_friends = $this->db->select('users.user_id, users.username, forest_users.level')
                                  ->from('forest_users')
                                  ->where_in('forest_users.user_id', $friend_array)
                                  ->join('users', 'forest_users.user_id = users.user_id')
                                  ->order_by('exp', 'desc')
                                  ->limit(10)
                                  ->get();

      $friend_leaderboards = $forester_friends->result_array();

      $forester_global = $this->db->select('users.user_id, users.username, forest_users.level')
                                  ->from('forest_users')
                                  ->join('users', 'forest_users.user_id = users.user_id')
                                  ->order_by('exp', 'desc')
                                  ->limit(10)
                                  ->get();

      $global_leaderboards = $forester_global->result_array();
    endif;

    $view_data = array(
      'page_title'        => 'Forest Leaderboards',
      'page_body'         => 'forest',
      'routes'            => $this->route_navigation,
      'active_url'        => $this->uri->rsegment(2, 0),
      'navigation_header' => 'Forest Leaderboards',
      'lb_global'         => $global_leaderboards,
      'lb_friends'        => $friend_leaderboards,
      'your_place'        => $this->leaderboard_place($hunter_data)
    );

    $this->system->quick_parse('forest/leaderboards', $view_data);
  }

  // --------------------------------------------------------------------

  /**
   * Get Leaderboards
   *
   * Get JSON representation of the leaderboards
   *
   * @access  public
   * @param   none
   * @return  json
   */

  public function get_leaderboards()
  {
    $user_id = $this->session->userdata('user_id'); // Looks cleaner, and it's better for static debugging.

    if( ! $hunter_data = $this->_get_hunter_data($user_id)):
      die(json_encode(array('error' => 'Hunter does not exist', 'error_data' => 'redirect')));
    endif;

    $friends = $this->db->select('username, user_email, friend_id')
                        ->join('users', 'users.user_id = friends.friend_id')
                        ->where('friends.user_id', $this->system->userdata['user_id'])
                        ->where('active', 1)
                        ->get('friends');

    if($friends->num_rows() > 0):
      $friends = $friends->result_array();
      $friend_array = array();
      foreach ($friends as $friend):
        $friend_array[] = $friend['friend_id'];
      endforeach;

      $forester_friends = $this->db->select('users.user_id, users.username, forest_users.exp')
                                  ->from('forest_users')
                                  ->where_in('forest_users.user_id', $friend_array)
                                  ->join('users', 'forest_users.user_id = users.user_id')
                                  ->order_by('exp', 'desc')
                                  ->limit(5)
                                  ->get();

      $foresters = $forester_friends->result_array();
      $foresters[] = array('username' => $this->system->userdata['username'], 'user_id' => $this->system->userdata['user_id'], 'exp' => $hunter_data['exp']);

      $exp_array_hack = array();

      foreach ($foresters as $key => $value):
        $exp_array_hack[$value['exp']] = $value;
      endforeach;

      if($forester_friends->num_rows() > 0):
        krsort($exp_array_hack);
        $foresters = array_values($exp_array_hack);
        unset($foresters[5]);
        $this->system->parse_json($foresters);
      endif;
    else:
      $this->system->parse_json(array());
    endif;
  }

  // --------------------------------------------------------------------

  /**
   * Bugpack
   *
   * A place to view and sell your collected bugs
   *
   * @access  public
   * @param   none
   * @return  view
 */

  public function bugpack()
  {
    $this->system->view_data['scripts'][] = '/global/js/forest/index.js';

    $user_id = $this->session->userdata('user_id');

      if( ! $hunter_data = $this->_get_hunter_data($user_id)):
        redirect('/forest/tour');
      else:
        $hunter_data = $this->_sync_energy($hunter_data);
    endif;

    $this->load->helper('form');

    $insect_list = $this->db->query('SELECT forest_user_catches.user_id,
                                    forest_user_catches.insect_id,
                                    forest_insects.*,
                                    COUNT(forest_user_catches.insect_id) as amount
                                    FROM forest_user_catches
                                    JOIN forest_insects ON forest_user_catches.insect_id = forest_insects.id
                                    WHERE forest_user_catches.user_id = '.$user_id.'
                                    GROUP BY forest_user_catches.insect_id
                                    ORDER BY forest_insects.exp ASC, amount DESC')->result_array();

    $insects = array();

    foreach ($insect_list as $bug):
      $inc = 5;
      $sell_incremenets = array(1 => 'Sell x1');

      if(5 <= $bug['amount']) $sell_incremenets[5] = 'Sell x5';

      while($inc*5 < $bug['amount']):
        $sell_incremenets[5*$inc] = 'Sell x'.(5*$inc);
        $inc += $inc;
      endwhile;

      $sell_incremenets[$bug['amount']] = 'Sell all (x'.$bug['amount'].')';

      $insects[$bug['insect_id']] = array(
        'name'        => $bug['name'],
        'price'       => $bug['price'],
        'description' => $bug['description'],
        'amount'      => $bug['amount'],
        'rarity'      => $bug['rarity_classification'],
        'img'         => $bug['image'],
        'dropdown'    => $sell_incremenets
      );
    endforeach;

    $view_data = array(
      'page_title'        => 'Your bugpack',
      'page_body'         => 'forest bugpack',
      'routes'            => $this->route_navigation,
      'active_url'        => $this->uri->rsegment(2, 0),
      'navigation_header' => 'Your bugpack',
      'insects'           => $insects
    );

    $this->system->quick_parse('forest/bugpack', $view_data);
  }

  // --------------------------------------------------------------------

  /**
   * Prehunt checks
   *
   * Things to check for before going on a hunt
   *
   * @access  public
   * @param   array
   * @return  json
   */

  public function prehunt_checks($hunter_data = array(), $return_net = FALSE)
  {
    if(count($hunter_data) < 1):
        if( ! $hunter_data = $this->_get_hunter_data($this->system->userdata['user_id'])):
          redirect('/forest/tour');
        endif;
    endif;

    if(($hunter_data['energy']) < 1) die(json_encode(array('error' => 'Not enough energy!', 'error_data' => 'yield', 'error_msg' => 'You need to have more energy to be able to hunt!')));

    // 2) Do they have a net equiped?
    $net_query = $this->db->get('forest_nets')->result_array();
    $this->nets = array();

    foreach ($net_query as $key => $value) $this->nets[] = $value['item_id'];

    $net_equipped = $this->db->where('user_id', $this->system->userdata['user_id'])
                             ->where('equipped', 1)
                             ->where('soft_deleted', 0)
                             ->where_in('user_items.item_id', $this->nets)
                             ->join('avatar_items', 'avatar_items.item_id = user_items.item_id', 'left')
                             ->join('forest_nets', 'avatar_items.item_id = forest_nets.item_id', 'left')
                             ->limit(1)
                             ->get('user_items');

    if($net_equipped->num_rows() < 1):
      die(json_encode(array('error' => 'You need a net!', 'error_data' => 'yield', 'error_msg' => 'You need to have a net equipped to start hunting!')));
    endif;

    if ($return_net):
      return $net_equipped->row_array();
    else:
      if($this->input->is_ajax_request()):
        $this->system->parse_json(array('success' => 1));
      endif;
    endif;
  }


  // --------------------------------------------------------------------

  /**
   * Hunt
   *
   * The hunting logic when you press a button (swing your net)
   *
   * @access  public
   * @param   none
   * @return  output
   */

  public function hunt()
  {
      if( ! $hunter_data = $this->_get_hunter_data($this->system->userdata['user_id'])):
        redirect('/forest/tour');
      endif;

      $this->load->model('forest_engine');
      $user_id = $this->system->userdata['user_id'];

      $response = array();

      header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
      header("Cache-Control: no-store, no-cache, must-revalidate");
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Pragma: no-cache");

      $hunter_data['net'] = $this->prehunt_checks($hunter_data, TRUE);

      // Did the net snap?
      $snap_debug = FALSE;

      if( ! $this->snap_free):
        $random_snap = mt_rand(-($hunter_data['net']['break_ratio']/15), ($hunter_data['net']['break_ratio']/15));
        $snap_ratio = $hunter_data['swings_till_snap']/($hunter_data['net']['break_ratio']+$random_snap);
        $extra_nets = $this->db->where('user_id', $user_id)
                               ->where('equipped', 0)
                               ->where_in('user_items.item_id', $this->nets)
                               ->get('user_items')
                               ->num_rows();

        if($snap_debug) $snap_ratio = 2;

        if($snap_ratio > 1):
            $hunter_data['swings_till_snap'] = 0;

          $this->load->model('user_engine');

          $this->user_engine->add_item($hunter_data['net']['broken_version'], 1, $this->session->userdata('user_id'));
          $this->user_engine->remove_item($hunter_data['net']['item_id'], 1, $this->session->userdata('user_id'), 1);

          $response = array(
            'event'       => 'snap',
            'name'        => 'Your net snapped!',
            'value'       => '0',
            'image'       => '/images/forest/net_snap.jpg',
            'description' => 'Aww, my net just snapped! Well, the poor thing <i>was</i> getting a little worn out...',
            'auxilary'    => $this->check_nets($hunter_data, FALSE)
          );

          if ($snap_debug):
            $this->user_engine->add_item($hunter_data['net']['item_id'], 1, $this->session->userdata('user_id'));
          endif;
        endif;
      endif;
    // Berry obtained!
    if (count($response) < 1):
      if(mt_rand(1, 55) == 55):
          $this->forest_engine->add_berries(1, $this->session->userdata('user_id'));
          $response = array('event' => 'berry', 'name' => 'Energy Berry', 'value' => '10', 'image' => 'images/forest/berry.jpg', 'description' => 'This looks like a sweet delectable treat! (Berries grant +10 energy to continue hunting)');
      elseif(mt_rand(1, 85) == 2):
          $chest_palla = mt_rand(1, 15);
          $this->load->model('user_engine');

          $response = (array(
            'event'       => 'special',
            'name'        => 'Treasure bag!',
            'value'       => $chest_palla,
            'image'       => '/images/forest/palladium_bag.jpg',
            'description' => 'You found a total of <b>'.$chest_palla.'</b> palla!'
          ));

          $this->user_engine->add_palladium($chest_palla, $this->session->userdata('user_id'));
      else:
          // Otherwise, catch a bug!
          $bug = $this->catch_bug($hunter_data);

          $bug_data = array(
            'event'         => 'bug',
            'description'   => $bug['description'],
            'id'            => $bug['id'],
            'image'         => $bug['image'],
            'name'          => $bug['name'],
            'value'         => $bug['price'],
            'rarity'        => $bug['rarity_classification'],
            'experience'    => $bug['exp']
          );

          $hunter_data['exp'] = ($hunter_data['exp']+$bug['exp']);

          // Ooooh-yeah, level up time! :D
          if($hunter_data['exp'] >= $hunter_data['next_level_exp']):
            $current_exp = $this->db->where('user_id', $user_id)->get('forest_users')->row();
            $new_exp = ((floor(floor($current_exp->exp*9/5)/50))*50);
            $this->db->where('user_id', $user_id)->update('forest_users', array('next_level_exp' => $new_exp, 'level' => ($current_exp->level+1)));
            $new_bugs = $this->db->select('COUNT(1) as new_bugs')->where('min_level', $current_exp->level+1)->from('forest_insects')->get()->row()->new_bugs;

            $new_level = array('new_level' => ($current_exp->level+1), 'new_exp' => $new_exp, 'new_bugs' => $new_bugs);

            $bug_data['level_up'] = true;
            $bug_data['new_level'] = $new_level['new_level'];
            $bug_data['new_exp'] = $new_level['new_exp'];
            $bug_data['new_bugs'] = $new_level['new_bugs'];

            // Regenerate full energy!
            $hunter_data['energy'] = $hunter_data['max_energy'];
          endif;

          $response = $bug_data;
      endif;
    endif;

    // Let's do some updates!
    $update_data = array(
      'energy'           => ($hunter_data['energy']-1),
      'exp'              => $hunter_data['exp'],
      'swings_till_snap' => ($hunter_data['swings_till_snap']+1)
    );

    $this->db->set('last_hunt', 'NOW()', false)
             ->where('user_id', $this->session->userdata('user_id'))
             ->update('forest_users', $update_data);

    // Please do not blow up the server. Thank you!
    usleep(10000);

    $this->system->parse_json($response);
  }

  // --------------------------------------------------------------------

  /**
   * Catch bug
   *
   * Determine which bug you've caught
   *
   * @access  public
   * @param   array
   * @return  array
 */

  public function catch_bug($forester_data)
  {
    $this->cache->delete('user_'.$this->session->userdata('user_id').'_forest');
    if(($bug_table = $this->cache->get('user_'.$this->session->userdata('user_id').'_forest')) === FALSE):
      $bug_table = array(
        'possibilities' => array(),
        'data'          => array(),
      );

      $bugs = $this->forest_engine->get_bugs_avalible($forester_data['level']);

      foreach ($bugs as $key => $bug):
          $i = 0;
          while($i < $bug['rarity']):
            $bug_table['possibilities'][] = $bug['id'];
            $i++;
          endwhile;
          $bug_table['data'][$bug['id']] = $bug;
      endforeach;

      $this->cache->save('user_'.$this->session->userdata('user_id').'_forest', $bug_table, 200);
    endif;

    $this->random_number = (mt_rand() % count($bug_table['possibilities']));
    $this->caught_bug = $bug_table['data'][$bug_table['possibilities'][$this->random_number]];
    $this->forest_engine->caught_bug($this->caught_bug['id'], $this->session->userdata('user_id'));

    return $this->caught_bug;
  }

  // --------------------------------------------------------------------

  /**
   * Sell bug
   *
   * Sell bug in your bugpack
   *
   * @access  public
   * @param   none
   * @return  redirect / json
   */

  public function sell_bug()
  {
    if( ! $hunter_data = $this->_get_hunter_data($this->system->userdata['user_id'])):
      redirect('/forest/tour');
    endif;

    $user_id = $this->system->userdata['user_id'];
    $response = array();

    $bug_id = $this->input->post('bug_id');
    $amount = $this->input->post('amount');

    $get_insects = $this->db->select('forest_user_catches.*, forest_insects.price')
                            ->from('forest_user_catches')
                            ->where('user_id', $user_id)
                            ->where('insect_id', $bug_id)
                            ->join('forest_insects', 'forest_user_catches.insect_id = forest_insects.id')
                            ->limit($amount)
                            ->get();

      if($get_insects->num_rows() == $amount):
          $insects = $get_insects->result_array();
          $profits = 0;

          foreach($insects as $insect):
              $profits += $insect['price'];
              $this->db->where('id', $insect['id'])->delete('forest_user_catches');
          endforeach;

          $this->load->model('user_engine');
          $this->user_engine->add('user_palladium', $profits);

          $response = array('profits' => $profits, 'sold' => $get_insects->num_rows());
      else:
        $response = array('error' => 'You do not have enough of these insects to sell.');
      endif;

      if( ! $this->input->is_ajax_request()):
        redirect('forest/bugpack');
      else:
        $this->system->parse_json($response);
    endif;
  }

  // --------------------------------------------------------------------

  /**
   * Sell all bugs
   *
   * A handy function to sell your entire bugpack
   *
   * @access  public
   * @param   none
   * @return  output
   */

   public function sell_all_bugs()
   {
     if( ! $hunter_data = $this->_get_hunter_data($this->system->userdata['user_id'])):
      redirect('/forest/tour');
    endif;

    $user_id = $this->system->userdata['user_id'];
    $response = array();

    $get_insects = $this->db->select('forest_user_catches.*, forest_insects.price')
                              ->from('forest_user_catches')
                              ->where('user_id', $user_id)
                              ->join('forest_insects', 'forest_user_catches.insect_id = forest_insects.id')
                              ->get();

     if($get_insects->num_rows() > 0):
      $insects = $get_insects->result_array();
      $profits = 0;

      foreach($insects as $insect):
        $profits += $insect['price'];
        $this->db->where('id', $insect['id'])->delete('forest_user_catches');
      endforeach;

      $this->load->model('user_engine');
      $this->user_engine->add('user_palladium', $profits);

      $response = array('profits' => $profits, 'sold' => $get_insects->num_rows());
    else:
      $response = array('error' => 'You do not have any insects to sell!');
    endif;

    if( ! $this->input->is_ajax_request()):
      redirect('forest/bugpack');
    else:
      $this->system->parse_json($response);
    endif;
   }

  // --------------------------------------------------------------------

  /**
   * Berry snacking
   *
   * Consume a berry in exchange for 10 energy
   *
   * @access  public
   * @param   none
   * @return  json
   */

  public function snack()
  {
    if( ! $hunter_data = $this->_get_hunter_data($this->system->userdata['user_id'])):
      redirect('/forest/tour');
    endif;

    $user_id = $this->system->userdata['user_id'];
    $response = array();

    if($hunter_data['berries'] > 0):
      $current_energy =  $hunter_data['energy'];
      $max_energy =  $hunter_data['max_energy'];

      if($current_energy >= $max_energy):
        die(json_encode(array('error' => 'Your energy is full!')));
      endif;

      $new_energy = min($max_energy, ($current_energy+10));

      $this->db->update('forest_users', array('energy' => $new_energy, 'berries' => $hunter_data['berries']-1), array('user_id' => $user_id));

      $response = array('energy' => ($current_energy+10), 'berries' => ($hunter_data['berries']-1));
    else:
      $response = array('error' => 'You do not own any berries to snack on');
    endif;

    $this->system->parse_json($response);
  }

  // --------------------------------------------------------------------

  /**
   * Quick net equip
   *
   * A handy function to equip your net with the press of a button
   *
   * @access  public
   * @param   none
   * @return  json
   */

  public function quick_net_equip()
  {
    $net_query = $this->db->get('forest_nets')->result_array();
    $nets = array();

    foreach ($net_query as $key => $value):
      $nets[] = $value['item_id'];
    endforeach;

    $current_net = $this->db->where('user_id', $this->session->userdata('user_id'))
                            ->where('equipped', 1)
                            ->where_in('item_id', $nets)
                            ->limit(1)
                            ->get('user_items');

    $net = $this->db->select('user_items.id, user_items.item_id, forest_nets.tag')
                    ->where('user_id', $this->session->userdata('user_id'))
                    ->where('user_items.equipped', 0)
                    ->where_in('user_items.item_id', $nets)
                    ->join('avatar_items', 'avatar_items.item_id = user_items.item_id', 'left')
                    ->join('forest_nets', 'avatar_items.item_id = forest_nets.item_id', 'left')
                    ->limit(1)
                    ->get('user_items');

    if($net->num_rows() > 0 && $current_net->num_rows == 0):
      $net = $net->row_array();
      $this->db->where('id', $net['id'])->update('user_items', array('equipped' => 1));
      $this->system->parse_json($net);
    else:
      $this->system->parse_json(array('error' => true, 'error_msg' => 'Unable to equip net.'));
    endif;
  }

  // --------------------------------------------------------------------

  /**
   * Quick shop purchase
   *
   * Purchases a net from the shop and automatically equips it.
   *
   * @access  public
   * @param   none
   * @return  output
   */

  public function quick_shop_purchase()
  {
    if($_SERVER['REQUEST_METHOD'] == "POST"):
      $net_query = $this->db->get('forest_nets')->result_array();
      $this->nets = array();

      foreach ($net_query as $key => $value):
        $this->nets[$value['item_id']] = $value;
      endforeach;

      $net_shop_id = $this->input->post('shop_item_id');

      if( ! is_numeric($net_shop_id)) show_error('net_shop_id must be valid');

      $shop_item_query = $this->db->get_where('shop_items', array('shop_item_id' => $net_shop_id));
      if($shop_item_query->num_rows() > 0):
        $shop_item_data = $shop_item_query->row_array();
        if(in_array($shop_item_data['item_id'], array_keys($this->nets))):
          $this->load->model('user_engine');

          if($this->system->userdata['user_palladium'] >= $shop_item_data['price']):
            $this->user_engine->remove('user_palladium', $shop_item_data['price']);
            $this->user_engine->add_item($shop_item_data['item_id'], 1, $this->system->userdata['user_id'], 1);

            $this->system->parse_json(array(
              'price' => $shop_item_data['price'],
              'tag'   => $this->nets[$shop_item_data['item_id']]['tag']
            ));
          else:
            show_error('You cannot afford this net!');
          endif;
        else:
          show_error('The item could not be found'.$shop_item_data['item_id'].' vs '.implode(', ', array_keys($this->nets)));
        endif;
      else:
        show_error('shop_item could not be found.');
      endif;
    endif;
  }

  // --------------------------------------------------------------------

  /**
   * Leaderboard place
   *
   * Find which place in the leaderboards you're in
   *
   * @access  public
   * @param   array
   * @return  int
   */

  public function leaderboard_place($hunter_data = array())
  {
      if( ! isset($this->total_foresters)):
        if ( ! $this->total_foresters = $this->cache->get('total_foresters')):
          $this->total_foresters = $this->db->count_all($this->user_table);
          $this->cache->save('total_foresters', $this->total_foresters, 1200);
        endif;
    endif;

    $foresters_below_you = $this->db->select('*')
                      ->from('forest_users')
                      ->where('exp <', $hunter_data['exp'])
                      ->order_by('exp', 'desc')
                      ->get()
                      ->num_rows();

    return (($this->total_foresters)-$foresters_below_you);
  }


  // --------------------------------------------------------------------

  /**
   * Fix nets
   *
   * Runs through your nets and fix them. (Function was written by Alex a few years ago, needs refactoring!)
   *
   * @access  public
   * @param   none
   * @return  json
   */

  public function fix_nets()
  {
    $user_id = $this->system->userdata['user_id'];

    $this->load->model('user_engine');

    // PRO NET FIXING
    $broken_pro_nets = $this->db->query('SELECT item_id, COUNT(id) as amount FROM user_items WHERE item_id = 3485 AND soft_deleted = 0 AND user_id = '.$user_id)->row()->amount;
    $amount_of_new_pro_nets = 0;
    if($broken_pro_nets >= 2) :
      for($i=1; $broken_pro_nets >= $i; $i++){
        if( is_integer( $i/2 ) ){
          $amount_of_new_pro_nets++;
        }
      };
    endif;

    if($amount_of_new_pro_nets > 0){
      $this->user_engine->remove_item(3485, ($amount_of_new_pro_nets * 2), $user_id);
      for($i=0; ($amount_of_new_pro_nets > $i) AND ($i < 25); $i++){
        $this->user_engine->add_item(3324, 1, $user_id);
      }
    }

    // BASIC NET FIXING
    $broken_basic_nets = $this->db->query('SELECT item_id, COUNT(id) as amount FROM user_items WHERE item_id = 3486 AND soft_deleted = 0 AND user_id = '.$user_id)->row()->amount;
    $amount_of_new_basic_nets = 0;
    if($broken_basic_nets >= 3) :
      for($i=1; $broken_basic_nets >= $i; $i++){
        if( is_integer( $i/3) ){
          $amount_of_new_basic_nets++;
        }
      };
    endif;

    if($amount_of_new_basic_nets > 0){
      $this->user_engine->remove_item(3486, ($amount_of_new_basic_nets * 3), $user_id);
      for($i=0; ($amount_of_new_basic_nets > $i) AND ($i < 25); $i++){
        $this->user_engine->add_item(3323, 1, $user_id);
      }
    }

    if($this->input->is_ajax_request()):

      $message = "";
      if($amount_of_new_basic_nets > 0){
        $message = 'You now have '.$amount_of_new_basic_nets.' more basic net(s)!';
      }
      if($amount_of_new_pro_nets > 0){
        $message .= 'You now have '.$amount_of_new_pro_nets.' more pro net(s)!';
      }
      if($amount_of_new_pro_nets > 0 || $amount_of_new_basic_nets > 0){
        $reply['message'] = $message;
      }else{
        $reply['message'] = "Nothing to fix! Remember you need 3 broken basic nets to fix a basic net and 2 broken pro nets to fix a pro net.";
      }

      $this->output->set_content_type('application/json')->set_output(json_encode($reply, JSON_NUMERIC_CHECK));
    endif;
  }


  // --------------------------------------------------------------------

  /**
   * Synchronize Energy
   *
   * Figure out how much energy you've gained since you last regained any
   *
   * @access  private
   * @param   array
   * @return  array
   */

  private function _sync_energy($forester_data = array())
  {
    $last_energized = (time()-$forester_data['energize_at']);
    $bonus_energy = floor($last_energized/$this->recover_interval);

    if($bonus_energy > 0):
      $forester_data['energy'] = min(($bonus_energy+$forester_data['energy']), $forester_data['max_energy']);
      $this->db->update('forest_users', array(
        'energy'      => $forester_data['energy'],
        'energize_at' => time()
      ), array('user_id' => $forester_data['user_id']));
    endif;

    return $forester_data;
  }

  // --------------------------------------------------------------------

  /**
   * Get hunter data
   *
   * A quick way to retrieve hunter data
   *
   * @access  private
   * @param   int
   * @return  array
   */

  private function _get_hunter_data($user_id = 0)
  {
    $hunter_query = $this->db->get_where($this->user_table, array('user_id' => $user_id));

    if($hunter_query->num_rows() > 0):
      return $hunter_query->row_array();
    else:
      return FALSE;
    endif;
  }

}

/* End of file Forest.php */
/* Location: ./system/application/controllers/forest.php */