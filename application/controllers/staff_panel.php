<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Staff_panel extends CI_Controller
{
  var $route_navigation = array(
    'tickets'     => 'Tickets',
    'infractions' => 'Infractions',
    'users'       => 'Modify users',
  );

  public function __construct()
  {
    parent::__construct();

    if ( ! $this->system->is_staff()):
      show_404();
    endif;
  }

  // --------------------------------------------------------------------

  /**
   * Home page
   *
   * Staff_panel index
   *
   * @access  public
   * @param   none
   * @return  view
   */

  public function tickets()
  {
    $this->load->helper('text');
    $this->load->library('pagination');

    $config['base_url'] = '/staff_panel/tickets/';
    $config['total_rows'] = $this->db->count_all('staff_tickets');
    $config['per_page'] = '16';

    $this->pagination->initialize($config);

    $where_conditions = array();

    if ($user = $this->input->get('user')):
      $where_conditions['LOWER(username)'] = strtolower($user);
    endif;

    $tickets = $this->db->select('staff_tickets.* , users.username')
                        ->join('users', 'staff_tickets.user_id = users.user_id')
                        ->where($where_conditions)
                        ->order_by('staff_tickets.status', 'DESC')
                        ->order_by('staff_tickets.ticket_id', 'DESC')
                        ->limit($config['per_page'], $this->uri->segment(3, 0))
                        ->get('staff_tickets')
                        ->result_array();

    $view_data = array(
      'page_title' => 'Tickets - Staff',
      'page_body'  => 'staff_panel staff_tickets',
      'tickets'    => $tickets,
      'routes'     => $this->route_navigation,
      'active_url' => $this->uri->rsegment(2, 0),
    );

    $this->system->quick_parse('staff_panel/tickets', $view_data);
  }

  // --------------------------------------------------------------------

  /**
   * Home page
   *
   * Staff_panel index
   *
   * @access  public
   * @param   none
   * @return  view
   */

  public function infractions()
  {
    $view_data = array(
      'page_title' => 'Infractions - Staff',
      'page_body'  => 'staff_panel staff_infractions',
      'routes'     => $this->route_navigation,
      'active_url' => $this->uri->rsegment(2, 0),
    );

    $this->system->quick_parse('staff_panel/infractions', $view_data);
  }

  // --------------------------------------------------------------------

  /**
   * Home page
   *
   * Staff_panel index
   *
   * @access  public
   * @param   none
   * @return  view
   */

  public function users()
  {
    $view_data = array(
      'page_title' => 'Users - Staff',
      'page_body'  => 'staff_panel staff_users',
      'routes'     => $this->route_navigation,
      'active_url' => $this->uri->rsegment(2, 0),
    );

    $this->system->view_data['scripts'][] = '/global/js/staff_panel/users.js';
    $this->system->quick_parse('staff_panel/users', $view_data);
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

  public function view_ticket($ticket_id = 0)
  {
    $this->load->helper('forum');

    $ticket_data = $this->db->select('staff_tickets.* , users.username, users.last_saved_avatar')
                            ->join('users', 'staff_tickets.user_id = users.user_id')
                            ->limit(1)
                            ->get_where('staff_tickets', array('ticket_id' => $ticket_id))
                            ->row_array();

    $reply_message = array();
    if ($ticket_data['reply_message'] > 1):
      $reply_message = $this->db->join('users', 'mail.sender = users.user_id')->get_where('mail', array('mail_id' => $ticket_data['reply_message']))->row_array();
    endif;

    $view_data = array(
      'page_title'    => 'Tickets - '.$ticket_data['issue'],
      'page_body'     => 'staff_panel staff_tickets',
      'routes'        => $this->route_navigation,
      'active_url'    => $this->uri->rsegment(2, 0),
      'ticket_data'   => $ticket_data,
      'reply_message' => $reply_message,
    );

    $this->system->quick_parse('staff_panel/view_ticket', $view_data);
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

  public function reply_ticket($ticket_id = 0)
  {
    if( ! is_numeric($ticket_id)) show_error('ticket_id must be valid');

    $ticket_data = $this->db->select('staff_tickets.* , users.username, users.last_saved_avatar')
                            ->join('users', 'staff_tickets.user_id = users.user_id')
                            ->limit(1)
                            ->get_where('staff_tickets', array('ticket_id' => $ticket_id))
                            ->row_array();

    $resolved = $this->input->post('resolve');
    $auto_solve = ($this->input->post('auto_solve') == 'yes');

    if ($resolved):
      $this->db->where('ticket_id', $ticket_id)->set('solved_at', 'NOW()', false)->update('staff_tickets', array(
        'status' => 'solved',
      ));
    endif;

    $new_mail_id = $ticket_data['reply_message'];
    if ( ! $auto_solve):
      $this->load->helper('string');
      $unique_mail_id = substr(sha1(uniqid(mt_rand(), true)), 0, 42);

      foreach (array($ticket_data['user_id']) as $user_id):
          $this->db->set('date', 'NOW()', false)->insert('mail', array(
          'sender'           => $this->system->userdata['user_id'],
          'receiver'         => $ticket_data['user_id'],
          'subject'          => 'Response: '.$ticket_data['issue'],
          'status'           => 0,
          'conversation_key' => random_string('alnum', 42),
          'included_users'   => json_encode(array($this->system->userdata['user_id'] => $this->system->userdata['username'], $ticket_data['user_id'] => $ticket_data['username'])),
          'unique_mail_id'   => $unique_mail_id,
          'message'          => $this->input->post('message').'
[size=11]
  --[Ticket information]--
  Attended by: '.$this->system->userdata['username'].'
  Ticket number: #'.$ticket_data['ticket_id'].'
  Issue: '.$ticket_data['issue'].'[/size]'
          ));

          $new_mail_id = $this->db->insert_id();

          $this->cache->delete('total_mail_'.$user_id);

          if ($user_id != $this->system->userdata['user_id']):
            $this->notification->broadcast(array(
              'receiver'          => $ticket_data['username'],
              'receiver_id'       => $ticket_data['user_id'],
              'notification_text' => $this->system->userdata['username'].' has replied to your ticket.',
              'attachment_id'     => $new_mail_id,
              'attatchment_type'  => 'mailbox',
              'attatchment_url'   => '/mailbox/view_message/'.$new_mail_id.'/',
            ), FALSE);
          endif;
        endforeach;
    endif;

    $this->db->where('ticket_id', $ticket_id)->update('staff_tickets', array(
      'attended_by'   => $this->system->userdata['username'],
      'reply_message' => $new_mail_id
    ));

    redirect('/staff_panel/view_ticket/'.$ticket_id);
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

  public function unlock_ticket($ticket_id = 0)
  {
    if( ! is_numeric($ticket_id)) show_error('ticket_id must be valid');

    $ticket_data = $this->db->select('staff_tickets.* , users.username, users.last_saved_avatar')
                            ->join('users', 'staff_tickets.user_id = users.user_id')
                            ->limit(1)
                            ->get_where('staff_tickets', array('ticket_id' => $ticket_id))
                            ->row_array();

    $this->db->update('staff_tickets', array('status' => 'pending'), array('ticket_id' => $ticket_id));

    redirect('/staff_panel/view_ticket/'.$ticket_id);
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

  public function refund_items()
  {
    $this->load->model('user_engine');
      foreach ($this->input->post('items') as $item_name => $item_data):
        $this->user_engine->add_item($item_data['item_id'], $item_data['amount'], $this->input->post('user_id'));
      endforeach;

      $this->output->set_content_type('application/json')
                   ->set_output(json_encode(array('success'), JSON_NUMERIC_CHECK));
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

  public function search_item()
  {
    if($_SERVER['REQUEST_METHOD'] == "POST"):
      $item_name = $_POST['q'];
      $item_array = $this->db->query("SELECT name, item_id, child_id, thumb FROM avatar_items LEFT JOIN avatar_items_relations ON child_id = item_id WHERE (LOWER(name) LIKE LOWER(CONVERT((\"%".$item_name."%\") USING utf8)) OR name LIKE LOWER(CONVERT((\"".$item_name."\") USING utf8))) AND child_id IS NULL LIMIT 40")->result_array();

      $json['item_array'] = $item_array;
      foreach ($item_array as $item):
        $json['items'][] = $item['name'];
        $json['item_obj'][$item['name']] = $item;
      endforeach;

      $this->output->set_content_type('application/json')->set_output(json_encode($json, JSON_NUMERIC_CHECK));
    endif;
  }

}

/* End of file staff_panel.php */
/* Location: ./system/application/controllers/staff_panel.php */