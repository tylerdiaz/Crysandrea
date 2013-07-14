<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mailbox extends CI_Controller
{
  var $max_mail = 200;
  var $route_navigation = array(
    'index'  => 'Your Inbox',
    'saved'  => 'Saved messages',
    'outbox' => 'Outbox'
  );

  function __construct(){
    parent::__construct();

    if( ! $this->session->userdata('user_id')):
      redirect('/auth/signin/?r=mailbox');
    endif;
  }

  // --------------------------------------------------------------------

  /**
   * Home page
   *
   * Mailbox main function
   *
   * @access  public
   * @param   none
   * @return  view
   * @route   n/a
   */

  public function index()
  {
    $this->system->view_data['scripts'][] = '/global/js/mailbox/index.js';

    if($_SERVER['REQUEST_METHOD'] == "POST"):
      if ($this->input->post('action1') != 'none'):
        $action = $this->input->post('action1');
      elseif ($this->input->post('action2') != 'none'):
        $action = $this->input->post('action2');
      endif;

      $target = $this->input->post('mail');

      if( ! isset($target[0])):
        redirect('mailbox/index?error='.urlencode('No messages were selected'));
      endif;

      switch($action):
        case 'delete':
          $return_value = $this->db->where_in('mail_id', $target)->delete('mail');
          $success_notice = 'The selected messages have been deleted!';
        break;
        case 'read':
          $return_value = $this->db->where_in('mail_id', $target)->update('mail', array('status' => 1));
          $success_notice = 'The selected messages have been marked as read!';
        break;
        case 'unread':
          $return_value = $this->db->where_in('mail_id', $target)->update('mail', array('status' => 0));
          $success_notice = 'The selected messages have been marked as unread!';
        break;
        case 'save':
          $return_value = $this->db->where_in('mail_id', $target)->update('mail', array('saved' => 1));
          $success_notice = 'The selected messages have now been saved!';
        break;
        case 'unsave':
          $return_value = $this->db->where_in('mail_id', $target)->update('mail', array('saved' => 0));
          $success_notice = 'The selected messages have been placed back in your inbox';
        break;
      endswitch;

      $this->_update_new_mail_count();
      redirect('mailbox/index?success='.urlencode($success_notice));
    else:
      if ( ! $total_mail = $this->cache->get('total_mail_'.$this->system->userdata['user_id'])):
        $total_mail = $this->db->where(array(
          'receiver' => $this->system->userdata['user_id'],
          'saved'    => 0
        ))->from('mail')->count_all_results();

        $this->cache->save('total_mail_'.$this->system->userdata['user_id'], $total_mail, 120);
      endif;

      $this->load->library('pagination');
      $config['base_url'] = '/mailbox/index/';
      $config['total_rows'] = $total_mail;
      $config['per_page'] = 18;
      $config['uri_segment'] = 3;
      $this->pagination->initialize($config);

      $messages = $this->db->select('mail.mail_id, mail.sender, mail.subject, mail.status, mail.date, mail.receiver, users.username')
                           ->where('receiver', $this->system->userdata['user_id'])
                           ->where('mail.saved', 0)
                           ->order_by('mail_id', 'DESC')
                           ->limit($config['per_page'], $this->uri->segment(3, 0))
                           ->join('users', 'mail.sender = users.user_id')
                           ->get('mail')
                           ->result_array();

      $view_data = array(
        'page_title'        => 'Your Mailbox',
        'page_body'         => 'mailbox',
        'routes'            => $this->route_navigation,
        'active_url'        => $this->uri->rsegment(2, 0),
        'messages'          => $this->_message_view_format($messages),
        'navigation_header' => 'Your mailbox',
        'total_mail'        => $total_mail,
        'max_mail'          => $this->max_mail,
      );

      $this->system->quick_parse('mailbox/index', $view_data);
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

  public function view_message($message_id = 0)
  {
      if( ! is_numeric($message_id)) show_error('message_id must be valid');

      $this->system->view_data['scripts'][] = '/global/js/mailbox/view_message.js';
      $mail_data = $this->_fetch_mail($message_id);
      $this->load->helper('forum');

      $sub_messages = array();

      if($mail_data['conversation_key'] != NULL):
        $sub_mail_query = $this->db->select('user_id, last_saved_avatar, message, username, date, count(1) as total')
                                   ->limit(5)
                                   ->order_by('date', 'DESC')
                                   ->group_by('unique_mail_id')
                                   ->join('users', 'users.user_id = mail.sender')
                                   ->get_where('mail', array('mail_id <' => $mail_data['mail_id'], 'conversation_key' => $mail_data['conversation_key']));

        if($sub_mail_query->num_rows() > 0):
          $sub_messages = $sub_mail_query->result_array();
          $sub_messages = array_reverse($sub_messages);
          if ($this->system->userdata['user_id'] == 14):
            $this->output->enable_profiler(true);
          endif;
        endif;
      endif;

      if ($mail_data['sender'] == $this->system->userdata['user_id'] && $mail_data['receiver'] != $this->system->userdata['user_id']):
        # you're in your outbox!
      else:
        # you're reading this from your inbox.
        $new_mail_id = $this->db->set('read_timestamp', 'NOW()', false)->where(array('mail_id' => $message_id))->update('mail', array('status' => 1));
      endif;

    $subject = (strlen($mail_data['subject']) > 28) ? substr($mail_data['subject'], 0, 28).'...' : $mail_data['subject'];

    $reply_to = array();

    if ($mail_data['included_users'] !== NULL && count(json_decode($mail_data['included_users'], TRUE)) > 1):
      $reply_to[$mail_data['sender']] = $mail_data['username'];
      foreach (json_decode($mail_data['included_users'], TRUE) as $user_id => $username):
        if($user_id != $this->system->userdata['user_id']):
          $reply_to[$user_id] = $username;
        endif;
      endforeach;
    endif;

      $view_data = array(
        'page_title' => 'Your Mailbox',
        'page_body'  => 'mailbox',
        'routes'     => $this->route_navigation,
        'active_url' => $this->uri->rsegment(2, 0),
        'navigation_header' => $subject,
        'sub_messages' => $sub_messages,
        'mail' => $mail_data,
        'reply_to' => $reply_to
      );

      $this->system->quick_parse('mailbox/view_message', $view_data);
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

  public function reply_message($message_id = 0)
  {
    if( ! is_numeric($message_id)) show_error('message_id must be valid');
    $mail_data = $this->_fetch_mail($message_id);

    $this->load->helper(array('form', 'string'));
    $this->load->library('form_validation');

    $this->form_validation->set_rules('title', 'Title', 'required|min_length[1]|max_length[42]|xss_clean');
    $this->form_validation->set_rules('message', 'Message', 'required|xss_clean');

    if(is_array($this->input->post('to'))):
      $send_to = implode(', ', $this->input->post('to'));
    else:
      $send_to = $this->input->post('to');
    endif;

    $error = FALSE;

    if ($this->form_validation->run() === TRUE):
      $to = $send_to;
      $title = $this->input->post('title');

      if (preg_match("/Re: /i", $title)):
        $title = $title;
      else:
        $title = "Re: ".$title;
      endif;

      $body = $this->input->post('message');

      $multiple_to = explode(', ', $to);
      $missing_users = array();

      if (is_array($multiple_to) && count($multiple_to) > 1):
        $users = $this->db->where_in('LOWER(username)', array_map('strtolower', $multiple_to))->get('users')->result_array();
        foreach ($users as $user):
          $new_user_list[$user['user_id']] = $user['username'];
          $send_user_ids[] = $user['user_id'];
        endforeach;

        $missing_users = array_diff(array_map('strtolower', $multiple_to), array_map('strtolower', array_values($new_user_list)));
      else:
        $user = $this->db->where_in('LOWER(username)', array_map('strtolower', $multiple_to))->get('users')->row_array();
        if (count($user) > 0):
          $send_user_ids[] = $user['user_id'];
            $new_user_list[$user['user_id']] = $user['username'];
          else:
            $missing_users[] = $user['username'];
        endif;
      endif;

      if (count($missing_users) == 0):

        $unique_mail_id = substr(sha1(uniqid(mt_rand(), true)), 0, 42);

        foreach ($send_user_ids as $user_id):
            // Do you have this conversation key in your inbox queue? If so, delete it!
          // $this->db->get_where('mail', array('user_id' => $user_id, 'conversation_key' => $mail_data['conversation_key']));

            $this->db->set('date', 'NOW()', false)->insert('mail', array(
              'sender'           => $this->system->userdata['user_id'],
              'receiver'         => $user_id,
              'subject'          => $title,
              'message'          => $body,
              'status'           => 0,
              'conversation_key' => $mail_data['conversation_key'],
              'included_users'   => json_encode($new_user_list),
              'unique_mail_id'   => $unique_mail_id
            ));

            $new_mail_id = $this->db->insert_id();
            $this->cache->delete('total_mail_'.$user_id);

            if ($user_id != $this->system->userdata['user_id']):
              $this->notification->broadcast(array(
                'receiver'          => $new_user_list[$user_id],
                'receiver_id'       => $user_id,
                'notification_text' => $this->system->userdata['username'].' just sent you a private message.',
                'attachment_id'     => $new_mail_id,
                'attatchment_type'  => 'mailbox',
                'attatchment_url'   => '/mailbox/view_message/'.$new_mail_id.'/',
              ), FALSE);
            endif;
        endforeach;

        redirect('mailbox/index?success='.urlencode('Your message has been successfully sent to '.implode('/', array_values($new_user_list)).'!'));
      else:
        $error = 'The user '.implode('/', $missing_users).' could not be found.';
      endif;
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

  public function create_message()
  {
    // $this->output->enable_profiler(true);

    $this->system->view_data['scripts'][] = '/global/js/mailbox/create_message.js';
    $this->system->view_data['scripts'][] = '/global/js/auto_suggest.js';

    $this->load->helper(array('form', 'string'));
    $this->load->library('form_validation');

    $this->form_validation->set_rules('title', 'Title', 'required|min_length[1]|max_length[42]|htmlentities|xss_clean');
    $this->form_validation->set_rules('to', 'Receiver', 'required|htmlentities|xss_clean|callback_user_exists|callback_private_messaging');
    $this->form_validation->set_rules('message', 'Message', 'required|htmlentities|xss_clean');

    $error = FALSE;

    if ($this->form_validation->run() === TRUE):
      $to = $this->input->post('to');
      $title = $this->input->post('title');
      $body = $this->input->post('message');

      $multiple_to = explode(',', ltrim(rtrim($to)));
      $missing_users = array();

      if (is_array($multiple_to) && count($multiple_to) > 1):
        $users = $this->db->where_in('LOWER(username)', array_map('strtolower', $multiple_to))->get('users')->result_array();
        foreach ($users as $user):
          $new_user_list[$user['user_id']] = $user['username'];
          $send_user_ids[] = $user['user_id'];
        endforeach;

        $missing_users = array_diff(array_map('strtolower', $multiple_to), array_map('strtolower', array_values($new_user_list)));
      else:
        $user = $this->db->where_in('LOWER(username)', array_map('strtolower', $multiple_to))->get('users')->result_array();
        if (count($user) > 0):
          $send_user_ids[] = $user[0]['user_id'];
            $new_user_list[$user[0]['user_id']] = $user[0]['username'];
          else:
            $missing_users[] = $user['username'];
        endif;
      endif;

      if (count($missing_users) == 0):

        $unique_mail_id = substr(sha1(uniqid(mt_rand(), true)), 0, 42);

        foreach ($send_user_ids as $user_id):
            $this->db->set('date', 'NOW()', false)->insert('mail', array(
              'sender'           => $this->system->userdata['user_id'],
              'receiver'         => $user_id,
              'subject'          => $title,
              'message'          => $body,
              'status'           => 0,
              'conversation_key' => random_string('alnum', 42),
              'included_users'   => json_encode($new_user_list),
              'unique_mail_id'   => $unique_mail_id
            ));

            $new_mail_id = $this->db->insert_id();

            $this->cache->delete('total_mail_'.$user_id);

            if ($user_id != $this->system->userdata['user_id']):
              $this->notification->broadcast(array(
                'receiver'          => $new_user_list[$user_id],
                'receiver_id'       => $user_id,
                'notification_text' => $this->system->userdata['username'].' just sent you a private message.',
                'attachment_id'     => $new_mail_id,
                'attatchment_type'  => 'mailbox',
                'attatchment_url'   => '/mailbox/view_message/'.$new_mail_id.'/',
              ), FALSE);
            endif;
        endforeach;

        redirect('mailbox/index?success='.urlencode('Your message has been successfully sent to '.implode('/', array_values($new_user_list)).'!'));
      else:
        $error = 'The user '.implode('/', $missing_users).' could not be found.';
      endif;
    endif;

    $view_data = array(
      'page_title'        => 'Create a messge',
      'page_body'         => 'mailbox',
      'routes'            => $this->route_navigation,
      'active_url'        => $this->uri->rsegment(2, 0),
      'error'             => $error,
      'navigation_header' => 'Send a message',
    );

    $this->system->quick_parse('mailbox/create_message', $view_data);
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

  public function saved()
  {
    $this->system->view_data['scripts'][] = '/global/js/mailbox/saved.js';

    if($_SERVER['REQUEST_METHOD'] == "POST"):
      if ($this->input->post('action1') != 'none'):
        $action = $this->input->post('action1');
      elseif ($this->input->post('action2') != 'none'):
        $action = $this->input->post('action2');
      endif;

      $target = $this->input->post('mail');

      if( ! isset($target[0])):
        redirect('mailbox/index?error='.urlencode('No messages were selected'));
      endif;

      foreach ($target as $mail_id):
        $mail_data[] = $this->_fetch_mail($mail_id);
      endforeach;

      switch($action):
        case 'delete':
          $return_value = $this->db->where_in('mail_id', $target)->delete('mail');
        break;
        case 'read':
          $return_value = $this->db->where_in('mail_id', $target)->update('mail', array('status' => 1));
        break;
        case 'unread':
          $return_value = $this->db->where_in('mail_id', $target)->update('mail', array('status' => 0));
        break;
        case 'save':
          $return_value = $this->db->where_in('mail_id', $target)->update('mail', array('saved' => 1));
        break;
        case 'unsave':
          $return_value = $this->db->where_in('mail_id', $target)->update('mail', array('saved' => 0));
        break;
      endswitch;

      $this->_update_new_mail_count();

      redirect('mailbox/outbox?success='.urlencode('The selected messages have been deleted!'));
    else:
      $total_outmail = $this->db->select('COUNT(1) as total')->where('saved', 1)->where('receiver', $this->system->userdata['user_id'])->get('mail')->row()->total;

      $this->load->library('pagination');
      $config['base_url'] = '/mailbox/saved/';
      $config['total_rows'] = $total_outmail;
      $config['per_page'] = 18;
      $config['uri_segment'] = 3;
      $this->pagination->initialize($config);

      $messages = $this->db->select('mail.mail_id, mail.sender, mail.subject, mail.status, mail.date, mail.receiver, users.username')
                           ->where('receiver', $this->system->userdata['user_id'])
                           ->where('saved', 1)
                           ->order_by('mail_id', 'DESC')
                           ->limit($config['per_page'], $this->uri->segment(3, 0))
                           ->join('users', 'mail.sender = users.user_id')
                           ->get('mail')
                           ->result_array();

      $view_data = array(
        'page_title'        => 'Your Mailbox',
        'page_body'         => 'mailbox',
        'routes'            => $this->route_navigation,
        'active_url'        => $this->uri->rsegment(2, 0),
        'messages'          => $this->_message_view_format($messages),
        'total_mail'        => $total_outmail,
        'max_mail'          => $this->max_mail,
        'navigation_header' => 'Your mailbox',
      );

      $this->system->quick_parse('mailbox/index', $view_data);
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

  public function outbox()
  {
      $this->system->view_data['scripts'][] = '/global/js/mailbox/outbox.js';

      if($_SERVER['REQUEST_METHOD'] == "POST"):
        if ($this->input->post('action1') != 'none'):
          $action = $this->input->post('action1');
        elseif ($this->input->post('action2') != 'none'):
          $action = $this->input->post('action2');
        endif;

        $target = $this->input->post('mail');

        if( ! isset($target[0])):
          redirect('mailbox/index?error='.urlencode('No messages were selected'));
        endif;

        foreach ($target as $mail_id):
          $mail_data[] = $this->_fetch_mail($mail_id);
        endforeach;

      switch($action):
        case 'delete':
          $return_value = $this->db->where_in('mail_id', $target)->delete('mail');
        break;
        case 'read':
          $return_value = $this->db->where_in('mail_id', $target)->update('mail', array('status' => 1));
        break;
        case 'unread':
          $return_value = $this->db->where_in('mail_id', $target)->update('mail', array('status' => 0));
        break;
        case 'save':
          $return_value = $this->db->where_in('mail_id', $target)->update('mail', array('saved' => 1));
        break;
        case 'unsave':
          $return_value = $this->db->where_in('mail_id', $target)->update('mail', array('saved' => 0));
        break;
      endswitch;

        $this->_update_new_mail_count();

        redirect('mailbox/outbox?success='.urlencode('The selected messages have been deleted!'));
      else:
        $total_outmail = $this->db->select('COUNT(1) as total')->where('sender', $this->system->userdata['user_id'])->get('mail')->row()->total;

      $this->load->library('pagination');
      $config['base_url'] = '/mailbox/outbox/';
      $config['total_rows'] = $total_outmail;
      $config['per_page'] = 18;
      $config['uri_segment'] = 3;
      $this->pagination->initialize($config);

      $messages = $this->db->select('mail.mail_id, mail.receiver as sender, mail.subject, mail.status, mail.date, mail.receiver, users.username')
                 ->where('sender', $this->system->userdata['user_id'])
                 ->order_by('status', 'ASC')
                 ->order_by('mail_id', 'DESC')
                 ->limit($config['per_page'], $this->uri->segment(3, 0))
                 ->join('users', 'mail.receiver = users.user_id')
                 ->get('mail')
                 ->result_array();

        $view_data = array(
          'page_title'        => 'Your Mailbox',
          'page_body'         => 'mailbox outbox',
          'routes'            => $this->route_navigation,
          'active_url'        => $this->uri->rsegment(2, 0),
          'messages'          => $this->_message_view_format($messages),
          'total_mail'        => $total_outmail,
          'max_mail'          => $this->max_mail,
          'navigation_header' => 'Your mailbox'
        );

        $this->system->quick_parse('mailbox/index', $view_data);
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

  public function ajax($action = 'unread', $message_id = 0)
  {
    if( ! is_numeric($message_id)) show_error('message_id must be valid');
      $mail_data = $this->_fetch_mail($message_id);

      switch ($action):
        case 'read':
          $this->db->update('mail', array('status' => 1), array('mail_id' => $message_id));
        break;
        case 'unread':
          $this->db->update('mail', array('status' => 0), array('mail_id' => $message_id));
        break;
      endswitch;

      echo $this->_update_new_mail_count();
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

  private function _fetch_mail($mail_id = 0)
  {
      $user_id = $this->system->userdata['user_id'];
      $mail_query = $this->db->where('(receiver = '.$user_id.' OR sender = '.$user_id.')')->join('users', 'mail.sender = users.user_id')->get_where('mail', array('mail_id' => $mail_id));
      if($mail_query->num_rows() == 0) show_error('mail could not be found.');

      return $mail_query->row_array();
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

  private function _update_new_mail_count()
  {
    $new_mail_number = $this->db->select('receiver')
                  ->where('receiver', $this->system->userdata['user_id'])
                  ->where('saved', 0)
                  ->where('status', 0)
                  ->get('mail')
                  ->num_rows();

    $this->db->update('users', array('new_mail' => $new_mail_number), array('user_id' => $this->system->userdata['user_id']));

    return $new_mail_number;
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

  private function _message_view_format($messages = array())
  {
    $message_format = array();

    foreach ($messages as $key => $message):
      $message_format[] = array(
        'cycle'      => cycle('', 'alt'),
        'status'     => ($message['status'] == 1 ? 'read' : 'unread'),
        'mail_id'    => $message['mail_id'],
        'unread_img' => '<img src="/images/icons/newmail.png" '.($message['status'] == 1 ? 'style="visibility:hidden;" title="Mark as unread"' : 'title="Mark as read"').' />',
        'subject'    => $message['subject'],
        'sender'     => $message['username'],
        'timestamp'  => _datadate($message['date'])
      );
    endforeach;

    return $message_format;
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

  public function backup_inbox()
  {
    $this->output->set_content_type('application/text');

    echo "<pre>";
    $messages = $this->db->where('receiver', $this->system->userdata['user_id'])
                         ->where('mail.saved', 0)
                         ->order_by('mail_id', 'DESC')
                         ->join('users', 'mail.sender = users.user_id')
                         ->get('mail')
                         ->result_array();

    echo "INBOX BACKUP FOR: (#".$this->system->userdata['user_id'].") ".$this->system->userdata['username'];
    echo "\n";
    echo "======================================";
    echo "\n";
    echo "\n";

    foreach ($messages as $message):
      echo strtoupper($message['subject']);
      echo " - From ".($message['username']);
      echo " (".date("M/d/Y", strtotime($message['date'])).")";
      echo "\n";
      echo $message['message'];
      echo "\n";
      echo "\n";
      echo "----";
      echo "\n";
      echo "\n";
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

  public function clear_inbox_messages()
  {
    $user_inboxes = $this->db->select('COUNT(1) as total, receiver')->order_by('total', 'DESC')->having('total >', $this->max_mail)->group_by('receiver')->get('mail')->result_array();
      foreach ($user_inboxes as $inbox):
        $this->db->limit(0, $this->max_mail)->order_by('date', 'ASC')->where('receiver', $inbox['receiver'])->where('saved', 0)->delete('mail');
        echo $inbox['receiver']." - ".$inbox['total']."<br />";
      endforeach;
  }

}

/* End of file Mailbox.php */
/* Location: ./system/application/controllers/Mailbox.php */