<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* Pagination Class
*
* @package        CodeIgniter
* @subpackage    Libraries
* @category    Pagination
* @author      Crysandrea
* @link        http://crysandrea.com/
*/
class CRYS_Pagination extends CI_Pagination
{
	var $base_url			= ''; // The page we are linking to
	var $total_rows  		= ''; // Total number of items (database results)
	var $per_page	 		= 10; // Max number of items you want shown per page
	var $num_links			=  2; // Number of "digit" links to show before/after the currently viewed page
	var $cur_page	 		=  0; // The current page being viewed
	var $first_link   		= '&laquo; First';
	var $next_link			= 'Next &rsaquo;';
	var $prev_link			= '&lsaquo; Previous';
	var $last_link			= 'Last &raquo;';
	var $uri_segment		= 3;
	var $full_tag_open		= '';
	var $full_tag_close		= '';
	var $first_tag_open		= '';
	var $first_tag_close	= '&nbsp;';
	var $last_tag_open		= '&nbsp;';
	var $last_tag_close		= '';
	var $cur_tag_open		= '&nbsp;<strong class="current_page">';
	var $cur_tag_close		= '</strong>';
	var $next_tag_open		= '&nbsp;';
	var $next_tag_close		= '&nbsp;';
	var $prev_tag_open		= '&nbsp;';
	var $prev_tag_close		= '';
	var $num_tag_open		= '&nbsp;';
	var $num_tag_close		= '&nbsp;';
	var $page_query_string	= FALSE;
	var $query_string_segment = 'per_page';


	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 */
	function CI_Pagination($params = array())
	{
		if (count($params) > 0)
		{
			$this->initialize($params);		
		}
		
		log_message('debug', "Pagination Class Initialized");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize Preferences
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	void
	 */
	function initialize($params = array())
	{
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Generate the pagination links
	 *
	 * @access	public
	 * @return	string
	 */	
	function create_links()
	{
			// If our item count or per-page total is zero there is no need to continue.
			if ($this->total_rows == 0 OR $this->per_page == 0)
			{
			   return '';
			}
	
			// Calculate the total number of pages
			$num_pages = ceil($this->total_rows / $this->per_page);
	
			// Is there only one page? Hm... nothing more to do here then.
			if ($num_pages == 1)
			{
				return '';
			}
			
			
			$CI =& get_instance();
	
			if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
			{
				if ($CI->input->get($this->query_string_segment) != 0)
				{
					$this->cur_page = $CI->input->get($this->query_string_segment);
	
					// Prep the current page - no funny business!
					$this->cur_page = (int) $this->cur_page;
				}
			}
			else
			{
				if ($CI->uri->segment($this->uri_segment) != 0)
				{
					$this->cur_page = $CI->uri->segment($this->uri_segment);
	
					// Prep the current page - no funny business!
					$this->cur_page = (int) $this->cur_page;
				}
			}
	
			$this->num_links = (int)$this->num_links;
	
			if ($this->num_links < 1)
			{
				show_error('Your number of links must be a positive number.');
			}
	
			if ( ! is_numeric($this->cur_page))
			{
				$this->cur_page = 0;
			}
	
			
			$uri_page_number = $this->cur_page;
			$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);
	
			// Calculate the start and end numbers. These determine
			// which number to start and end the digit links with
			$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
			$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;
	
			// Is pagination being used over GET or POST?  If get, add a per_page query
			// string. If post, add a trailing slash to the base URL if needed
			if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
			{
				$this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';
			}
			else
			{
				$this->base_url = rtrim($this->base_url, '/') .'/';
			}
	
	
			$targetpage = $this->base_url; 	
			$limit = $this->per_page; 
			$total_pages = $this->total_rows;
			
			$stages = $this->num_links;
			$page = $this->cur_page;
		
		// Initial page num setup
		if ($page == 0){$page = 0;}
		$prev = (($page - 1) * $this->per_page) - $this->per_page;	
		$prev = ($prev == 0) ? $prev = "" : $prev = $prev;
		$next = (($page - 1) * $this->per_page) + $this->per_page;							
		$lastpage = ceil($total_pages/$limit);		
		$LastPagem1 = $lastpage - 1;					

		$LastPagem1Real = $LastPagem1 * $this->per_page;
		$LastPagem1Real = $LastPagem1Real - $this->per_page;
		$LastPagemReal = $lastpage * $this->per_page;
		$LastPagemReal = $LastPagemReal - $this->per_page;
		
		$paginate = '';
		if($lastpage > 1)
		{	
		
	
		
		
			$paginate .= "<div class='paginate'>";
			// Previous
			if ($page > 1 ){
//				$paginate .= "<a href='{$targetpage}/'>$this->first_link</a> ";
				$paginate.= "<a href='{$targetpage}/{$prev}' rel=\"prev\">$this->prev_link</a> ";
			}else{
			 //	$paginate.= "<span class='disabled'>&lt;</span> ";	
			}
				
	
			
			// Pages	
			if ($lastpage < 7 + ($stages * 2))	// Not enough pages to breaking it up
			{	
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $page){
						$paginate.= "<span class='current'>$counter</span> ";
					}else{
						$counterPage = $counter * $this->per_page;
						$counterPage = $counterPage - $this->per_page;
						$paginate.= "<a href='$targetpage/{$counterPage}'>{$counter}</a> ";}					
				}
			}
			elseif($lastpage > 5 + ($stages * 2))	// Enough pages to hide a few?
			{
				// Beginning only hide later pages
				if($page < 1 + ($stages * 2))		
				{
					for ($counter = 1; $counter < 4 + ($stages * 2); $counter++)
					{
						if ($counter == $page){
							$paginate.= "<span class='current'>$counter</span> ";
						}else{
							$counterPage = $counter * $this->per_page;
							$counterPage = $counterPage - $this->per_page;
							$paginate.= "<a href='$targetpage/$counterPage'>$counter</a> ";}					
					}
					$paginate.= "... ";
					$paginate.= "<a href='$targetpage/$LastPagem1Real'>$LastPagem1</a> ";
					$paginate.= "<a href='$targetpage/$LastPagemReal'>$lastpage</a> ";		
				}
				// Middle hide some front and some back
				elseif($lastpage - ($stages * 2) > $page && $page > ($stages * 2))
				{
					$numb = 0;
					$numb2 = 1 * $this->per_page;
					$paginate.= "<a href='$targetpage/$numb'>1</a> ";
					$paginate.= "<a href='$targetpage/$numb2'>2</a> ";
					$paginate.= "... ";
					for ($counter = $page - $stages; $counter <= $page + $stages; $counter++)
					{
						if ($counter == $page){
							$paginate.= "<span class='current'>$counter</span> ";
						}else{
							$counterPage = $counter * $this->per_page;
							$counterPage = $counterPage - $this->per_page;
							$paginate.= "<a href='$targetpage/$counterPage'>$counter</a> ";}					
					}
					$paginate.= "... ";
					$paginate.= "<a href='$targetpage/$LastPagem1Real'>$LastPagem1</a> ";
					$paginate.= "<a href='$targetpage/$LastPagemReal'>$lastpage</a> ";		
				}
				// End only hide early pages
				else
				{
					$numb = 0;
					$numb2 = 1 * $this->per_page;
					$paginate.= "<a href='$targetpage/$numb'>1</a> ";
					$paginate.= "<a href='$targetpage/$numb2'>2</a> ";
					$paginate.= "... ";
					for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page){
							$paginate.= "<span class='current'>$counter</span> ";
						} else {
							$counterPage = $counter * $this->per_page;
							$counterPage = $counterPage - $this->per_page;
							$special_class = ($counter == ($lastpage - (2 + ($stages * 2))) ? 'first_on_list' : '');

							$paginate.= "<a href='$targetpage/$counterPage' id=\"$special_class\">$counter</a> ";
						}
					}
				}
			}
						
					// Next
			if ($page < $counter - 1){
				$paginate.= "<a href='$targetpage/$next' rel=\"next\">$this->next_link</a> ";
//				$paginate.= "<a href='$targetpage/$LastPagemReal'>$this->last_link</a> ";
			}else{
				//$paginate.= "<span class='disabled'>next</span> ";
			}
				
			$paginate.= "</div>";		
			// Kill double slashes.  Note: Sometimes we can end up with a double slash
			// in the penultimate link so we'll kill all double slashes.
			$paginate = preg_replace("#([^:])//+#", "\\1/", $paginate);
	
			// Add the wrapper HTML if exists
			$paginate = $this->full_tag_open.$paginate.$this->full_tag_close;
	
			return $paginate;
		}
	}
}
// END CRYS_Pagination Class
