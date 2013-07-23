<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forum extends CI_Controller
{
  private $posts_per_page = 10;
  var $route_navigation = array(
    'index' => 'All Forums',
    'your_topics' => 'Your Topics',
    'bookmarked' => 'Bookmarked topics',
    // 'invite' => 'Invite friends'
  );

  function __construct(){
    parent::__construct();
    $this->forum_config = $this->system->get_feature_config('forum');
    $this->load->model('forum_engine');
  }

  // --------------------------------------------------------------------

  /**
   * Home page
   *
   * Forum main function
   *
   * @access  public
   * @param   none
   * @return  view
   * @route   n/a
   */

  public function index()
  {
    $this->system->view_data['scripts'][] = '/global/js/forum/index.js';

    if ( ! $view_data = $this->cache->get('forum_categories')):
      $forum_data = array();

      $forum_query = $this->db->select('categories.name, forums.forum_id, forums.forum_name, forums.forum_description')
                  ->join('forums', 'forums.parent_id = categories.id', 'left')
                  ->get_where('categories', array('categories.staff' => 0))->result_array();

      foreach ($forum_query as $f_data):
        $forum_data[$f_data['name']][] = $f_data;
      endforeach;

      $total_posts = $this->db->count_all('topic_posts');
      $total_users = $this->db->count_all('users');

      $view_data = array(
        'page_title'   => 'Forum',
        'page_body'    => 'forums',
        'forums'       => $forum_data,
        'total_posts'  => $total_posts,
        'total_users'  => $total_users,
        'routes'       => $this->route_navigation,
        'active_url'   => $this->uri->rsegment(2, 0),
        'navigation_header' => 'Crysandrea\'s forums'
      );

      $this->cache->save('forum_categories', $view_data, 60);
    endif;

    $users_online = $this->db->select('username, user_level, last_activity')
                 ->order_by('username', 'asc')
                 ->join('users', 'users.user_id = sessions.user_id')
                 ->where('sessions.last_activity >='. (time()-$this->forum_config['offline_time']))
                 ->group_by('username')
                 ->get('sessions')
                 ->result_array();

    $view_data['users_online'] = $users_online;

    $this->system->quick_parse('forum/index', $view_data);
  }

  // --------------------------------------------------------------------

  /**
   * Your topics
   *
   * Topics the user has created
   *
   * @access  public
   * @param   none
   * @return  output
   */

  public function your_topics()
  {
    if( ! $this->session->userdata('user_id')) redirect('auth/signin?r=/forum/your_topics');

    if ( ! $total_topics = $this->cache->get('user:'.$this->system->userdata['user_id'].'_topic_post_counter')):
      $total_topics = $this->db->select('COUNT(1) as total')->get_where('topics', array('topics.topic_author' => $this->system->userdata['user_id']))->row()->total;
      $this->cache->save('user:'.$this->system->userdata['user_id'].'_topic_post_counter', $total_topics, 300);
    endif;

    $this->load->library('pagination');
    $config['base_url'] = base_url().'forum/your_topics/';
    $config['total_rows'] = $total_topics;
    $config['per_page'] = 12;
    $config['uri_segment'] = 3;
    $this->pagination->initialize($config);

    $forum_topics = $this->db->select('topics.topic_author, topics.topic_type, topics.topic_id, topics.topic_title, topics.topic_status, topics.total_posts, topics.last_post_username, topics.last_post, users.username')
                             ->join('users', 'users.user_id = topics.topic_author')
                             ->order_by('topics.topic_time', 'desc')
                             ->limit($config['per_page'], $this->uri->segment(3, 0))
                             ->get_where('topics', array('topics.topic_author' => $this->system->userdata['user_id']))->result_array();

    $view_data = array(
      'page_title'        => 'Your Topics',
      'page_body'         => 'forums',
      'routes'            => $this->route_navigation,
      'active_url'        => $this->uri->rsegment(2, 0),
      'navigation_header' => 'Topics you\'ve created',
      'topics'            => $forum_topics
    );

    $this->system->quick_parse('forum/your_topics', $view_data);
  }

  // --------------------------------------------------------------------

  /**
   * Bookmarked topics
   *
   * See your bookmarked topics
   *
   * @access  public
   * @param   none
   * @return  output
   */

  public function bookmarked()
  {
    if( ! $this->session->userdata('user_id')) redirect('auth/signin?r=/forum/bookmarked');

    $topics = array();
    $suscribed_topics_query = $this->db->get_where('subscribed_topics', array('user_id' => $this->system->userdata['user_id']));

    if($suscribed_topics_query->num_rows() > 0):
      $suscribed_topic_ids = unserialize($suscribed_topics_query->row()->subscribed_topics);

      if (count($suscribed_topic_ids) > 0):
        $this->load->library('pagination');
        $config['base_url'] = base_url().'forum/bookmarked/';
        $config['total_rows'] = count($suscribed_topic_ids);
        $config['per_page'] = 12;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);

        $topics = $this->db->select('topics.topic_author, topics.topic_type, topics.topic_id, topics.topic_title, topics.topic_status, topics.total_posts, topics.last_post_username, topics.last_post, users.username')
                           ->join('users', 'users.user_id = topics.topic_author')
                           ->where_in('topics.topic_id', array_values($suscribed_topic_ids))
                           ->order_by('topics.last_post', 'desc')
                           ->limit($config['per_page'], $this->uri->segment(3, 0))
                           ->get('topics')
                           ->result_array();
      endif;
    endif;

    $view_data = array(
      'page_title'        => 'Bookmarked topics',
      'page_body'         => 'forums',
      'routes'            => $this->route_navigation,
      'active_url'        => $this->uri->rsegment(2, 0),
      'navigation_header' => 'Bookmarked topics',
      'topics'            => $topics
    );

    $this->system->quick_parse('forum/bookmarked', $view_data);
  }

  // --------------------------------------------------------------------

  /**
   * View forum
   *
   * List the topics inside a certain forum
   *
   * @access  public
   * @param none
   * @return  redirect
   */

  function view($forum_id = 0)
  {
    $forum_data = $this->forum_engine->get_forum_data($forum_id);

    if($forum_data == FALSE):
      show_404('forum');
    elseif($forum_data['staff'] == 1):
      if( ! $this->system->is_staff()):
        show_error('This forum is only for staff members!');
      endif;
    endif;

    $this->load->library('pagination');
    $config['base_url'] = base_url().'forum/view/'.$forum_id;
    $config['total_rows'] = $forum_data['total_topics'];
    $config['per_page'] = 12;
    $config['uri_segment'] = 4;
    $this->pagination->initialize($config);

    $topics = $this->forum_engine->get_forum_topics($forum_id, $this->uri->segment(4, 0), $config['per_page']);

    if ($this->system->is_staff()):
      $this->system->view_data['scripts'][] = '/global/js/crysandrea.staff.scripts.js';
    endif;

    $data = array(
      'page_title' => $forum_data['forum_name'],
      'page_body'  => 'forums '.$forum_data['forum_name'],
      'forum_id'   => $forum_id,
      'topics'     => $topics
    );

    $this->system->quick_parse('forum/view', $data);
  }

  // --------------------------------------------------------------------

  /**
   * View Topic
   *
   * View a topic, along with it's posts
   *
   * @access  public
   * @param   int
   * @return  view
   */

  public function view_topic($id_string = 0)
  {
    $id_string = explode('-', $id_string);
    $topic_id = $id_string[0];

      $this->system->view_data['scripts'][] = '/global/js/forum/view_topic.js?12345';
      $this->system->view_data['scripts'][] = '/global/js/auto_suggest.js';

    if(is_numeric($topic_id)):
        $topic_data = $this->forum_engine->get_topic_data($topic_id);
    else:
        show_error('The topic id must be a valid number!');
    endif;

    if(($topic_data['total_posts']-1) < $this->uri->segment(4, 0)):
      $highest_page = floor(($topic_data['total_posts'] - 1) / $this->posts_per_page) * $this->posts_per_page;
      redirect('topic/view/'.$topic_data['topic_id']);
    endif;

    $this->load->helper('forum');

    if($topic_data == FALSE):
      show_404('Topic not found');
    elseif($topic_data['staff']):
      if( ! $this->system->is_staff()):
        show_error('You cannot visit this thread');
      endif;
    endif;

    if ( ! $total_posts = $this->cache->get('topic_total_posts'.$topic_id)):
      $total_posts = $topic_data['total_posts'];
      $this->cache->save('topic_total_posts'.$topic_id, $total_posts, 2400);
    endif;

    $this->load->library('pagination');
    $this->pagination->initialize(array(
      'base_url'    => '/topic/view/'.$topic_id,
      'total_rows'  => $total_posts,
      'per_page'    => $this->posts_per_page,
      'uri_segment' => 4
    ));

    $this->posts = $this->forum_engine->get_topic_posts($topic_data, $this->uri->segment(4, 0), $this->posts_per_page);

    if ( ! $this->posts):
      show_error('This topic has no posts to load');
    endif;

    if($this->session->userdata('user_id')):
      $subscribed = $this->db->limit(1)->select('subscribed_topics')->where('user_id', $this->session->userdata('user_id'))->get('subscribed_topics');

      if($subscribed->num_rows == 0):
        $subscribed = FALSE;
      else:
        $subscribed_array = unserialize($subscribed->row()->subscribed_topics);
        $subscribed = in_array($topic_id, $subscribed_array);
      endif;
    else:
      $subscribed = FALSE;
    endif;

    // pre loop for data mining
    $authors = array();
    foreach ($this->posts as $post):
      if($post['username'] != $this->system->userdata['username']):
        $authors[] = '@'.$post['username'].':';
      endif;
    endforeach;

    foreach ($this->system->userdata['online_friends'] as $user):
      $authors[] = '@'.$user['username'].':';
    endforeach;

    $this->system->quick_parse('forum/view_topic', array(
      'page_body'  => 'forums topic',
      'staff'      => $this->system->is_staff(),
      'my_id'      => $this->system->userdata['user_id'],
      'page_title' => stripcslashes($topic_data['topic_title']),
      'topic'      => $topic_data,
      'suscribed'  => $subscribed,
      'authors'    => $authors
    ));
  }

  // --------------------------------------------------------------------

  /**
   * Edit post
   *
   * Modify an already existing post
   *
   * @access  public
   * @param   int
   * @return  redirect
   */

  public function edit_post($post_id = 0)
  {
    if( ! is_numeric($post_id)) show_error('post_id must be valid');

    $this->load->model('user_engine');
    $this->load->helper('forum');

      $post_data = $this->db->join('topic_post_text', 'topic_post_text.post_id = topic_posts.post_id')->get_where('topic_posts', array('topic_posts.post_id' => $post_id));

      if($post_data->num_rows() > 0):
        $post_data = $post_data->row_array();
      else:
        show_error('topic post could not be found.');
      endif;

      $topic_data = $this->db->join('forums', 'forums.forum_id = topics.forum_id')->get_where('topics', array('topic_id' => $post_data['topic_id']));

      if($topic_data->num_rows() > 0):
        $topic_data = $topic_data->row_array();
      else:
        show_error('topic could not be found.');
      endif;

      $first_post_query = $this->db->limit(1)->order_by('post_id', 'ASC')->get_where('topic_posts', array('topic_id' => $post_data['topic_id']));

      if($first_post_query->num_rows() > 0):
        $first_post_data = $first_post_query->row_array();
        $first_post = ($first_post_data['post_id'] == $post_id);
      else:
        show_error('This topic has no other post');
      endif;

    if($post_data['author_id'] != $this->system->userdata['user_id'] && ! $this->system->is_staff()):
      show_error('You cannot modify this post due to lack of permissions.');
    endif;

    if($post_data['lock_edits'] == 1 && ! $this->system->is_staff()):
      show_error('A member of staff has removed access for you to edit this post.');
    endif;

    // What will you fill me with?! :D
    $this->load->library('form_validation');

    if($first_post == TRUE):
      $this->form_validation->set_rules('message', 'message', 'required|xss_clean');
      $this->form_validation->set_rules('title', 'topic title', 'max_length[50]|required|xss_clean');
    else:
      $this->form_validation->set_rules('message', 'message', 'required|xss_clean');
    endif;

      if($this->form_validation->run() == FALSE):
      $this->system->quick_parse('forum/edit_post', array(
        'page_body'  => 'forums topic',
        'page_title' => stripcslashes($topic_data['topic_title']),
        'topic'      => $topic_data,
        'post'    => $post_data,
        'first_post' => $first_post
      ));
      else:
      $this->db->update('topic_post_text', array(
        'text' => $this->input->post('message')
      ), array('post_id' => $post_id));

      if($first_post === TRUE):
        $this->db->where(array('topic_id' => $topic_data['topic_id']))->update('topics', array('topic_title' => $this->input->post('title')));
      endif;

      $previous_edit = array(
        'post_id' => $post_id,
        'post_body' => $post_data['text'],
        'post_author_ip' => $this->input->ip_address(),
        'author_id' => $this->system->userdata['user_id']
      );

      $this->db->set('post_time', 'NOW()', false)->insert('topic_post_previous_edits', $previous_edit);

      if($topic_data['palladium_enabled'] == 1):
        $shaved_chars = (strlen($post_data['post_body'])-strlen($this->input->post('message')));
        if($shaved_chars > 0):
          $this->user_engine->remove('user_palladium', parse_earned_palladium($shaved_chars));
        endif;
      endif;

      if ($post_data['topic_post_id'] > 12):
        $result_value = (floor((($post_data['topic_post_id'] - 1) / 12)) * 12);
        redirect('topic/view/'.$post_data['topic_id'].'/'.$result_value.'#'.$post_id);
      else:
        redirect('topic/view/'.$post_data['topic_id'].'#'.$post_id);
      endif;
      endif;
  }

  // --------------------------------------------------------------------

  /**
   * New Topic
   *
   * A page for users to create a new topic
   *
   * @access  public
   * @param   none
   * @return  output
   */

  public function new_topic($forum_id = 0)
  {
      if($this->session->userdata('user_id') == FALSE) redirect('auth/signin');

      if ($this->cache->get('topic_limit:'.$this->system->userdata['user_id'])) show_error('Slow down! You must wait at least 30 seconds before creating a new topic');

      $this->system->view_data['scripts'][] = '/global/js/forum/new_topic.js';

      $forum_data = $this->forum_engine->get_forum_data($forum_id);

      if($forum_data == FALSE):
        show_404('forum');
      elseif($forum_data['staff'] == 1 || $forum_id == 1):
        if( ! $this->system->is_staff() ):
          show_error('Staff only!');
        endif;
      endif;

      $this->load->library('form_validation');
      $this->form_validation->set_rules('message', 'message', 'required|xss_clean');
      $this->form_validation->set_rules('title', 'topic title', 'max_length[50]|required|xss_clean');

      if($this->form_validation->run() == FALSE):
        $data = array(
          'page_title' => 'New topic | '.$forum_data['forum_name'],
          'page_body' => 'forums new_topic',
          'forum_id' => $forum_id,
          'forum_data' => $forum_data
        );

        $this->system->quick_parse('forum/new_topic', $data);
      else:
        $topic_data = array(
          'forum_id' => $forum_id,
          'topic_title' => $this->input->post('title'),
          'topic_author' => $this->session->userdata('user_id'),
          'topic_type' => '',
          'total_posts' => 0,
          'last_post_username' => $this->session->userdata('username')
        );

        $this->cache->save('topic_limit:'.$this->system->userdata['user_id'], TRUE, 30);
        $topic_id = $this->forum_engine->create_topic($topic_data);

        if ( ! $total_posts = $this->cache->get('topic_total_posts_'.$topic_id)):
          $total_posts = 1;
          $this->cache->save('topic_total_posts_'.$topic_id, 1, 600);
        endif;

        $post_data = array(
          'topic_id' => $topic_id,
          'author_id' => $this->input->post('title'),
          'post_body' => $this->session->userdata('username')
        );

        $new_topic_data = array(
            'topic_id' => $topic_id,
            'forum_id' => $forum_id,
            'total_posts' => 0
        );

        $post_id = $this->forum_engine->create_post($this->input->post('message'), $new_topic_data, (int)$this->system->userdata['user_id']);

        $this->load->model('user_engine');
        $this->load->helper('forum');

        $palladium = 0;

        $palladium = parse_earned_palladium($this->input->post('message'));
        $this->user_engine->add('user_palladium', $palladium);

        $this->notification->broadcast(array(
        'notification_text' => $this->system->userdata['username'].' just created the topic &#8220;'.$this->input->post('title').'&#8221;',
        'attachment_id'     => $topic_id,
        'attatchment_type'  => 'new_topic',
        'attatchment_url'   => '/topic/view/'.$topic_id,
        ), TRUE);

        redirect('topic/view/'.$topic_id);
      endif;
  }

  // --------------------------------------------------------------------

  /**
   * Topic Reply
   *
   * Post a reply to a topic
   *
   * @access  public
   * @param   int
   * @return  json
   */

  public function topic_reply($topic_id = 0)
  {
    if( ! $this->session->userdata('user_id')) redirect('signin');

    if(is_numeric($topic_id)):
      $topic_data = $this->forum_engine->get_topic_data($topic_id);
    else:
      show_error('The topic id must be a valid number!');
    endif;

    $this->load->helper('forum');
    $this->load->library('form_validation');
    $this->load->model('user_engine');

    $this->form_validation->set_rules('message', 'Message', 'required|xss_clean|addslashes');

    if($topic_data == FALSE):
      show_404('forum');
    elseif($topic_data['staff'] == 1):
      if( $this->session->userdata('user_level') == 'user' || ! $this->system->is_staff()):
        $this->system->yield('error', lang('header_staff_only'), lang('staff_only_error'));
        return;
      endif;
    elseif($topic_data['topic_status'] == 'locked'):
      $this->system->yield('error', 'Topic locked!', 'Replies cannot be made to locked topics.');
      return;
    endif;

    if ($this->form_validation->run() == TRUE):
      $message = htmlspecialchars($this->input->post('message'));

      if ($message == $this->cache->get('old_message_topic:'.$this->system->userdata['user_id'])):
        if($topic_data['total_posts'] > 11):
          show_error('You cannot post the same thing twice in a row!');
        endif;
      endif;

      $this->cache->save('old_message_topic:'.$this->system->userdata['user_id'], $message, 60);

      $last_id = $this->forum_engine->create_post($message, $topic_data, $this->system->userdata['user_id']);

      if($topic_data['palladium_enabled'] == 1):
        $palladium = parse_earned_palladium($message);
        $this->user_engine->add('user_palladium', $palladium);
        // This is for events only
        // $this->db->set('special_currency', '(special_currency+1)', FALSE)->where('user_id', $this->system->userdata['user_id'])->update('users');
      elseif($topic_data['forum_id'] == 32):
        $this->user_engine->add('user_palladium', 1);
      else:
        $palladium = 0;
      endif;

      $this->cache->save('topic_total_posts'.$topic_id, $topic_data['total_posts']+1, 2400);

      $page_id = get_topic_page($topic_data['total_posts']);

      $full_replies = array();
      preg_match_all('/@([A-Za-z0-9\s]+):\s((\w+\s){0,}\w+.)/', $message, $replies);

      if (isset($replies[0]) && count($replies[0]) > 0):
        foreach ($replies[0] as $key => $reply):
          $full_replies[strtolower($replies[1][$key])] = $replies[2][$key];
        endforeach;

        $reply_users = $this->db->select('user_id, username')->where_in('username', array_keys($full_replies))->limit(count($full_replies))->get('users');

        foreach ($reply_users->result_array() as $user):
          $string = $full_replies[strtolower($user['username'])];
          $reply = (strlen($string) > 18) ? substr($string, 0, 18).'...' : $string;

          $this->notification->broadcast(array(
            'receiver'          => $user['username'],
            'receiver_id'       => $user['user_id'],
            'notification_text' => $this->system->userdata['username'].' mentioned you: '.$reply,
            'attachment_id'     => $last_id,
            'attatchment_type'  => 'mention',
            'attatchment_url'   => '/topic/view/'.$topic_id.'/'.$page_id.'#'.$last_id,
          ), FALSE);
        endforeach;
      endif;

        if($this->input->is_ajax_request() == TRUE):
          $json['post_html'] = $this->load->view('forum/partials/post_template', array('post' => array(
            'post_body'      => parse_bbcode(stripslashes(nl2br($this->input->post('message')))),
            'user_signature' => parse_bbcode(stripslashes(nl2br($this->system->userdata['user_signature']))),
            'username'       => $this->system->userdata['username'],
            'post_id'        => $last_id,
            'user_id'        => $this->system->userdata['user_id'],
            'last_action'    => time(),
            'donated'        => $this->system->userdata['donated'],
            'user_level'     => $this->system->userdata['user_level'],
            'post_time'      => date("Y-m-d H:i:s", time())
          )), TRUE);

          $this->output->set_content_type('application/json')->set_output(json_encode($json, JSON_NUMERIC_CHECK));
        else:
          redirect('topic/view/'.$topic_id.'/'.$page_id.'#footer');
        endif;
    endif;
  }


  // --------------------------------------------------------------------

  /**
   * Group change
   *
   * Do a batch editing on a group of topics
   *
   * @access  public
   * @param none
   * @return  redirect
   * @route n/a
   */

   function group_change()
   {
    $redirect = $this->input->post('redirect_url');
    if( ! $this->system->is_staff()):
      redirect($redirect);
    endif;

    $topics = $this->input->post('topic_id');
    if( ! is_array($topics) || empty($topics)):
      redirect($redirect);
    endif;

    switch ($this->input->post('do')):
      case 'lock':
        foreach($topics as $topic):
          $this->forum_engine->lock_topic((int) $topic);
        endforeach;
      break;

      case 'unlock':
        foreach($topics as $topic):
          $this->forum_engine->unlock_topic((int) $topic);
        endforeach;
      break;

      case 'sticky':
        foreach($topics as $topic):
          $this->forum_engine->make_sticky((int) $topic);
        endforeach;
      break;

      case 'unsticky':
        foreach($topics as $topic):
          $this->forum_engine->make_unsticky((int) $topic);
        endforeach;
      break;

      case 'move_to':
        $new_forum_id = $this->input->post('forum_id');
        if($new_forum_id == 'none' || ! is_numeric($new_forum_id)){
          break;
        } else {
          $new_forum_id = (int) $new_forum_id;
        };

        foreach($topics as $topic):
          $this->forum_engine->move_topic((int) $topic, $new_forum_id);
        endforeach;
      break;

      default:
        die('Error understanding what you wanted to do...');
      break;

    endswitch;

    redirect($redirect);
   }

   // --------------------------------------------------------------------

   /**
    * Load new posts
    *
    * Load new posts from the database
    *
    * @access  public
    * @param   int / int
    * @return  json
    */

   public function load_new_posts($topic_id = 0, $last_post = 0)
   {
    if( ! is_numeric($topic_id)) show_error('topic_id must be valid');
    if( ! is_numeric($last_post)) show_error('topic_id must be valid');

    if ( ! $total_posts = $this->cache->get('topic_total_posts'.$topic_id)):
      $topic_query = $this->db->get_where('topics', array('topic_id' => $topic_id));
      if($topic_query->num_rows() > 0):
        $topic_data = $topic_query->row_array();
      else:
        show_error('topic could not be found.');
      endif;

      $total_posts = $topic_data['total_posts'];
      $this->cache->save('topic_total_posts'.$topic_id, $total_posts, 2400);
    endif;

    if($total_posts > $last_post):
      $this->load->helper('forum');

      $this->posts = $this->forum_engine->get_topic_posts(array('topic_id' => $topic_id, 'total_posts' => $total_posts), $last_post, 12);
      $post_html = $this->load->view('forum/partials/topic_posts', array(), TRUE);

      $this->output->set_content_type('application/json')->set_output(json_encode(array(
        'post_html' => $post_html,
        'new_post_id' => $total_posts,
        'total_posts' => count($this->posts)
      ), JSON_NUMERIC_CHECK));
    else:
      $this->output->set_content_type('application/json')->set_output(json_encode(array(), JSON_NUMERIC_CHECK));
    endif;
   }

  // --------------------------------------------------------------------

  /**
   * Toggle Bookmark
   *
   * Add or remove a bookmarked topic
   *
   * @access  public
   * @param   int
   * @return  json
   */

  public function toggle_bookmark($topic_id = 0)
  {
    $reply['error'] = false;
    $reply['message'] = '';
    $first_time_to_subscribe = false;

    if( ! $this->session->userdata('user_id')) redirect('signin');
    $topic_id = (int) $topic_id;

    $topic_data = $this->forum_engine->get_topic_data($topic_id);
    if($topic_data == FALSE): // Does the topic exist?
      $reply['error'] = true;
      $reply['message'] = 'Topic doesn\t exist.';
      die(json_encode($reply));
    else:
      $sub = $this->db->select('subscribed_topics')->where('user_id', $this->session->userdata('user_id'))->get('subscribed_topics');

      if($sub->num_rows == 0):
        $sub = array();
        $first_time_to_subscribe = true;
      else:
        $sub = $sub->result();
        $sub = unserialize($sub[0]->subscribed_topics);
      endif;

      if(in_array($topic_id, $sub)):
        $key = array_search($topic_id, $sub);

        unset($sub[$key]);
        $sub = serialize($sub);
        if($first_time_to_subscribe == false):
          $this->db->update('subscribed_topics', array('subscribed_topics' => $sub), array('user_id' => $this->session->userdata('user_id')));
        else:
          $this->db->insert('subscribed_topics', array('subscribed_topics' => $sub, 'user_id' => $this->session->userdata('user_id')));
        endif;

        $reply['error'] = false;
        $reply['message'] = 'Un-subscribed to topic.';
        die(json_encode($reply));
      else:
        $sub[] = $topic_id;
        $sub = serialize($sub);

        if($first_time_to_subscribe == false):
          $this->db->update('subscribed_topics', array('subscribed_topics' => $sub), array('user_id' => $this->session->userdata('user_id')));
        else:
          $this->db->insert('subscribed_topics', array('subscribed_topics' => $sub, 'user_id' => $this->session->userdata('user_id')));
        endif;

        $reply['error'] = false;
        $reply['message'] = 'Subscribed to topic.';
        $this->output->set_content_type('application/json')->set_output(json_encode($reply, JSON_NUMERIC_CHECK));
      endif;
    endif;
  }

   // --------------------------------------------------------------------

   /**
    * Search forum
    *
    * Search through topic titles
    *
    * @access  public
    * @param   none
    * @return  json
    */

   public function search()
   {
       if( ! preg_match("/^([a-z0-9\s])+$/i", $query = $this->input->get('q'))) show_error('Invalid datatype');

       // Know what would be cool? To have them list your friend's topics first. Maybe later.
       $topics = $this->db->select('topic_author, topic_title, topics.forum_id, topic_id, total_posts, forum_name, username, LOWER(name) as category_name')
                          ->join('forums', 'forums.forum_id = topics.forum_id')
                          ->join('categories', 'forums.parent_id = categories.id')
                          ->join('users', 'users.user_id = topics.topic_author')
                          ->like('topic_title', $query)
                          ->where('forums.staff', 0)
                          ->order_by('total_posts', 'desc')
                          ->order_by('last_post', 'desc')
                          ->limit(5)
                          ->get('topics')
                          ->result_array();

       $this->output->set_content_type('application/json')->set_output(json_encode($topics, JSON_NUMERIC_CHECK));
   }

   // --------------------------------------------------------------------

   /**
    * Set spotlight topic
    *
    * Set a spotlight topic
    *
    * @access  public
    * @param   none
    * @return  redirect
    */

   public function spotlight_topic()
   {
       if ( ! $this->system->is_staff()) show_404();

       $topic_id = $this->input->post('topic_id');

       if( ! is_numeric($topic_id)) show_error('topic_id must be valid');

       $this->db->update('topics', array('spotlight_topic' => 1), array('topic_id' => $topic_id));
       $this->db->insert('spotlight_topics', array(
        'topic_id' => $topic_id,
        'highlighted_by' => $this->system->userdata['username']
       ));

       redirect($this->input->post('url'));
   }

   // --------------------------------------------------------------------

   /**
    * Toggle topic lock
    *
    * Lock, or unlock a topic
    *
    * @access  public
    * @param   none
    * @return  redirect
    */

   public function toggle_lock()
   {
       if ( ! $this->system->is_staff()) show_404();

       $topic_id = $this->input->post('topic_id');

       if( ! is_numeric($topic_id)) show_error('topic_id must be valid');

       $topic_data = $this->forum_engine->get_topic_data($topic_id);

       if ($topic_data['topic_status'] == 'unlocked'):
        $this->db->update('topics', array('topic_status' => 'locked'), array('topic_id' => $topic_id));
       elseif ($topic_data['topic_status'] == 'locked'):
        $this->db->update('topics', array('topic_status' => 'unlocked'), array('topic_id' => $topic_id));
       endif;

     redirect($this->input->post('url'));
   }


   // --------------------------------------------------------------------

   /**
    * Delete post
    *
    * Deletes a post, or an entire topic from the database
    *
    * @access  public
    * @param   none
    * @return  redirect
    */

   public function delete_post()
   {
      if ( ! $this->system->is_staff()) show_404();

      $post_id = $this->input->post('post_id');

      if( ! is_numeric($post_id)) show_error('post_id must be valid');

      $post_data = $this->db->get_where('topic_posts', array('post_id' => $post_id))->row();
      $topic_id = $post_data->topic_id;

      if(is_numeric($topic_id)):
        $topic_data = $this->forum_engine->get_topic_data($topic_id);
      else:
        show_error('The topic id must be a valid number!');
      endif;

      $this->load->helper('forum');

      if ( ! $total_posts = $this->cache->get('topic_total_posts'.$topic_id)):
        $total_posts = $topic_data['total_posts'];
        $this->cache->save('topic_total_posts'.$topic_id, $total_posts, 2400);
      endif;

      if ($post_data->topic_post_id == 1 || $total_posts == 1):
        // This means to delete the topic all-together
        $this->db->where('topic_id', $topic_id)->delete('topics');
        $this->db->where('post_id', $post_id)->delete('topic_posts');
        $this->db->where('post_id', $post_id)->delete('topic_post_text');
        $this->cache->delete('topic_total_posts'.$topic_id);

        redirect('forum');
      else:
        $this->db->where('post_id', $post_id)->delete('topic_posts');
        $this->db->where('post_id', $post_id)->delete('topic_post_text');
        $this->cache->delete('topic_total_posts'.$topic_id);

        redirect($this->input->post('url'));
      endif;
   }


   // --------------------------------------------------------------------

   /**
    * Move a topic
    *
    * Move topic to another category/forum
    *
    * @access  public
    * @param   none
    * @return  output
    */

   public function move_topic()
   {
       if ( ! $this->system->is_staff()) show_404();

       $topic_id = $this->input->post('topic_id');

       if( ! is_numeric($topic_id)) show_error('topic_id must be valid');

       $this->db->update('topics', array('forum_id' => $this->input->post('forum_id')), array('topic_id' => $topic_id));

     redirect($this->input->post('url'));
   }

}

/* End of file Forum.php */
/* Location: ./system/application/controllers/forum.php */