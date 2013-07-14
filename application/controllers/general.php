<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class General extends CI_Controller
{
	// --------------------------------------------------------------------

	/**
	 * Home page
	 *
	 * General main function
	 *
	 * @access  public
	 * @param   none
	 * @return  view
	 * @route   n/a
	 */

	public function index()
	{
		$view_data = array(
			'page_title' => 'General',
			'page_body' => 'general'
		);

		$this->system->quick_parse('general/index', $view_data);
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

	public function read_document($document_name = '')
	{
	    if( ! preg_match("/^([a-z0-9\s])+$/i", $document_name)) show_error('Invalid document name type');

	    $document_query = $this->db->get_where('documents', array('document_url' => $document_name));
	    if($document_query->num_rows() > 0):
	    	$document_data = $document_query->row_array();

	    	$data = array(
	    		'page_title' => $document_data['document_title'],
	    		'page_body' => $document_data['document_url'],
	    		'document' => $document_data
	    	);

	    	$this->system->quick_parse('general/read_document', $data);
	    else:
	    	show_error('Document could not be found.');
	    endif;
	}

}

/* End of file General.php */
/* Location: ./system/application/controllers/General.php */