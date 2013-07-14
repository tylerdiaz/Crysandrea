<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ticket extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Home page
	 *
	 * Ticket index
	 *
	 * @access  public
	 * @param   none
	 * @return  view
	 */

	public function index()
	{
		if( ! $this->session->userdata('user_id')):
			redirect('signin');
		endif;

		$success = false;

		if($_SERVER['REQUEST_METHOD'] == "POST" && strlen($this->input->post('description')) > 0):
			$this->db->insert('staff_tickets', array(
				'user_id'     => $this->session->userdata('user_id'),
				'issue'       => $this->input->post('issue'),
				'description' => $this->input->post('description'),
				'status'      => 'pending',
				'url'         => $this->input->post('url')
			));

  			$new_ticket_id = $this->db->insert_id();
			$this->load->helper('string');
			$unique_mail_id = substr(sha1(uniqid(mt_rand(), true)), 0, 42);

			foreach (array($this->system->userdata['user_id']) as $user_id):
	  			$this->db->set('date', 'NOW()', false)->insert('mail', array(
					'sender'   => 2187,
					'receiver' => $user_id,
					'subject'  => 'Your support ticket has been created!',
					'status'   => 0,
					'conversation_key' => random_string('alnum', 42),
					'included_users' => json_encode(array($this->system->userdata['user_id'] => $this->system->userdata['username'], 2187 => 'Zebra')),
					'unique_mail_id' => $unique_mail_id,
					'message'  => 'Hello!

Your ticket #'.$this->db->insert_id().' with the subject of "'.$this->input->post('issue').'" has been created at '.date("F j, Y, g:i a", time()).'. One of my responsible team members will attend to your ticket shortly!',
	  			));

	  			$new_mail_id = $this->db->insert_id();

	  			$this->cache->delete('total_mail_'.$user_id);
	  		endforeach;

	  		$staff_members = $this->db->get_where('users', array('user_level !=' => 'user'))->result_array();

	  		foreach ($staff_members as $user):
	  			$this->notification->broadcast(array(
	  				'receiver' => $this->system->userdata['username'],
	  				'receiver_id' => $user['user_id'],
	  				'notification_text' => $this->system->userdata['username'].' has filed a staff ticket.',
	  				'attachment_id' => $new_ticket_id,
	  				'attatchment_type' => 'ticket',
	  				'attatchment_url' => '/staff_panel/view_ticket/'.$new_ticket_id.'/',
	  			), FALSE);
	  		endforeach;


			$success = true;
		endif;

		$this->system->view_data['scripts'][] = '/global/js/ticket/index.js';

	    $view_data = array(
			'page_title' => 'Ticket system',
			'page_body'  => 'ticket',
			'success'    => $success
	    );

	    $this->system->quick_parse('ticket/index', $view_data);
	}

}

/* End of file ticket.php */
/* Location: ./system/application/controllers/ticket.php */