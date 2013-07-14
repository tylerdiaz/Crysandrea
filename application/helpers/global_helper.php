<?php  //if ( ! defined('BASEPATH')) exit('No direct script access allowed'); NEED DIRECT SCRIPT ACCESS FOR POST LOADER SCRIPT
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Site URL
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('site_url'))
{
	function site_url($uri = '')
	{
		$CI =& get_instance();
		return $CI->config->site_url($uri);
	}
}

// ------------------------------------------------------------------------

/**
 * Base URL
 *
 * Returns the "base_url" item from your config file
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('base_url'))
{
	function base_url()
	{
		$CI =& get_instance();
		return $CI->config->slash_item('base_url');
	}
}

// ------------------------------------------------------------------------

/**
 * Header Redirect
 *
 * Header redirect in two flavors
 * For very fine grained control over headers, you could use the Output
 * Library's set_header() function.
 *
 * @access	public
 * @param	string	the URL
 * @param	string	the method: location or redirect
 * @return	string
 */
if ( ! function_exists('redirect'))
{
	function redirect($uri = '', $method = 'location', $http_response_code = 302)
	{
		if ( ! preg_match('#^https?://#i', $uri))
		{
			$uri = site_url($uri);
		}

		switch($method)
		{
			case 'refresh'	: header("Refresh:0;url=".$uri);
				break;
			default			: header("Location: ".$uri, TRUE, $http_response_code);
				break;
		}
		exit;
	}
}


// ------------------------------------------------------------------------


/**
 * Form Button
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('button'))
{
	function button($label = '', $class = '')
	{
		return '<button type="submit" class="'.$class.'"><span>'.$label.'</span></button>';
	}
}

// ------------------------------------------------------------------------


/**
 * Form Button
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('notice'))
{
	function notice($header = '', $string = '', $class = 'success')
	{
		switch($class){
			case "success":
				return '<div class="success"><h3>'.image('images/yay.png').' '.$header.'</h3><p>'.$string.'</p></div>';
			break;
			case "error":
				return '<div class="error"><h3>'.image('images/stop.png').' '.$header.'</h3><p>'.$string.'</p></div>';
			break;
			case "notice":
				return '<div class="notice"><h3>'.image('images/woah.png').' '.$header.'</h3><p>'.$string.'</p></div>';
			break;
		}
	}
}

// ------------------------------------------------------------------------


/**
 * Print_a echo's out a clean array
 *
 * @return void
 * @author Tyler Diaz
 **/
function print_a($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}


// ------------------------------------------------------------------------

/**
 * Check if an array is empty
 *
 * @return void
 * @author Tyler Diaz
 **/
function array_empty($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $value) {
            if (!array_empty($value)) {
                return false;
            }
        }
    }
    elseif (!empty($mixed)) {
        return false;
    }
    return true;
}


// ------------------------------------------------------------------------

/**
 * Grab the percentage of 2 values
 *
 * @return void
 * @author Tyler Diaz
 **/
if ( ! function_exists('percent'))
{
	function percent($num_amount, $num_total) {
		return floor(number_format(($num_amount / $num_total) * 100, 1));
	}
}


// ------------------------------------------------------------------------


/**
 * [Jan 18, 2009] becomes [19 days ago]
 *
 * @return void
 * @author Tyler Diaz
 **/
function human_time($date, $change_tense = FALSE)
{
//	$date = strtotime("+6 hours", strtotime($date));
   	if(empty($date)) { return "No date avalible!"; }
    $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths = array("60","60","24","7","4.35","12","10");
    $now = time();
    $unix_date = strtotime($date);
    if(empty($unix_date)) { return "Curropted date"; }
    if($now > $unix_date) {
        $difference = $now - $unix_date;
        $tense = "ago";
   } else {
        $difference = $unix_date - $now;
        if($difference == 0):
        	return 'a few seconds ago';
    	endif;
        if($change_tense):
	        $tense = "left";
        else:
	        $tense = "from now";
        endif;
    }
    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }
    $difference = round($difference);
    if($difference != 1) {
        $periods[$j].= "s";
    }
    return "$difference $periods[$j] {$tense}";
}


// ------------------------------------------------------------------------

/**
 * Sub-function to human_time to show a bit more accuracy
 *
 * @return void
 * @author Tyler Diaz
 **/
function _datadate($date){
	if(time()-86400 > strtotime($date)){
		return human_time($date).' (<small>'.date("M jS, Y", strtotime($date)).'</small>)';
	} else {
		return human_time($date);
	}
}


// ------------------------------------------------------------------------

/**
 * Get the topic page location
 *
 * @return void
 * @author Tyler Diaz
 **/
function get_topic_page($posts){

	$page = ceil($posts / 10);

	if($page == 1){
		return 0;
	} else {
		return ($page * 10)-10;
	}
}


function user_style($user_rank = 'user'){
	return 'color:'.user_color($user_rank).'; font-weight:'.($user_rank != 'user' ? 'bold' : 'normal');
}

// ------------------------------------------------------------------------

/**
 * User rank colors
 *
 * @return void
 * @author Tyler Diaz
 **/
function user_color($_user_rank){
	switch($_user_rank){
		case "admin":
			$value = "#dd8701";
		break;
		case "hs":
			$value = "#8b0d49";
		break;
		case "developer":
			$value = "#990100";
		break;
		case "artist":
			$value = "#009a5c";
		break;
		case "moderator":
			$value = "#063dc2";
		break;
		default:
		case "user":
			$value = "#000000";
		break;
	}

	return $value;
}

/**
 * Alternator
 *
 * Allows strings to be alternated.  See docs...
 *
 * @access	public
 * @param	string (as many parameters as needed)
 * @return	string
 */
if ( ! function_exists('cycle'))
{
	function cycle()
	{
		static $i;

		if (func_num_args() == 0)
		{
			$i = 0;
			return '';
		}
		$args = func_get_args();
		return $args[($i++ % count($args))];
	}
}

/**
 * Current_url
 *
 * Displays the current URL being accessed
 *
 * @access	public
 * @param	string (as many parameters as needed)
 * @return	string
 */
if ( ! function_exists('current_url'))
{
	function current_url()
	{
		$CI =& get_instance();
		return $CI->config->site_url($CI->uri->uri_string());
	}
}

/**
 * Language loader
 *
 * Allows strings to be loaded from language files.
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('lang'))
{
	function lang($line, $id = '')
	{
		$CI =& get_instance();
		$line = $CI->lang->line($line);

		if ($id != '')
		{
			$line = '<label for="'.$id.'">'.$line."</label>";
		}

		return $line;
	}
}


/*
*  View helpers, you know,
*  to make view code nicer. :D
*/

if ( ! function_exists('script'))
{
	function script($src = '')
	{
		$CI =& get_instance();

		$link = '<script type="text/javascript" ';

		if (is_array($src))
		{
			foreach ($src as $k=>$v)
			{
				if ($k == 'src' AND strpos($v, '://') === FALSE)
				{
					$link .= 'src="'.$CI->config->slash_item('base_url').$v.'"';
				}
				else
				{
					$link .= "$k=\"$v\" ";
				}
			}

			$link .= "></script> \n";
		}
		else
		{
			if ( strpos($src, '://') !== FALSE)
			{
				$link .= 'src="'.$src.'"';
			}
			else
			{
				$link .= 'src="'.$CI->config->slash_item('base_url').$src.'"';
			}

			$link .= "></script> \n";
		}


		return $link;
	}
}

if ( ! function_exists('heading'))
{
	function heading($data = '', $h = '1')
	{
		return "<h".$h.">".$data."</h".$h.">";
	}
}

if ( ! function_exists('stylesheet'))
{
	function stylesheet($href = '', $rel = 'stylesheet', $type = 'text/css', $title = '', $media = '', $index_page = FALSE)
	{
		$CI =& get_instance();

		$link = '<link';

		if (is_array($href))
		{
			foreach ($href as $k=>$v)
			{
				if ($k == 'href' AND strpos($v, '://') === FALSE)
				{
					if ($index_page === TRUE)
					{
						$link .= ' href="'.$CI->config->site_url($v).'" ';
					}
					else
					{
						$link .= ' href="'.$CI->config->slash_item('base_url').$v.'" ';
					}
				}
				else
				{
					$link .= "$k=\"$v\" ";
				}
			}

			$link .= "/> \n";
		}
		else
		{
			if ( strpos($href, '://') !== FALSE)
			{
				$link .= ' href="'.$href.'" ';
			}
			elseif ($index_page === TRUE)
			{
				$link .= ' href="'.$CI->config->site_url($href).'" ';
			}
			else
			{
				$link .= ' href="'.$CI->config->slash_item('base_url').$href.'" ';
			}

			$link .= 'rel="'.$rel.'" type="'.$type.'" ';

			if ($media	!= '')
			{
				$link .= 'media="'.$media.'" ';
			}

			if ($title	!= '')
			{
				$link .= 'title="'.$title.'" ';
			}

			$link .= "/> \n";
		}


		return $link;
	}
}

if ( ! function_exists('image'))
{
	function image($src = '', $attributes = '')
	{
		if ( ! is_array($src) )
		{
			$src = array('src' => $src);
		}

		$img = '<img';

		foreach ($src as $k=>$v)
		{

			if ($k == 'src' AND strpos($v, '://') === FALSE)
			{
				$CI =& get_instance();
				$img .= ' src="'.$CI->config->slash_item('base_url').$v.'" ';
			}
			else
			{
				$img .= " $k=\"$v\" ";
			}
		}

		$img .= $attributes.' />';

		return $img;
	}
}

if ( ! function_exists('anchor'))
{
	function anchor($uri = '', $title = '', $attributes = '')
	{
		$title = (string) $title;

		if ( ! is_array($uri))
		{
			$site_url = ( ! preg_match('!^\w+://! i', $uri)) ? site_url($uri) : $uri;
		}
		else
		{
			$site_url = site_url($uri);
		}

		if ($title == '')
		{
			$title = $site_url;
		}

		return '<a href="'.$site_url.'" '.$attributes.'>'.$title.'</a>';
	}

}

function is_ajax()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}


function simple_success_message($line1 = 'Success!', $line2 = '')
{

	$message = '<div class="success">
					<h3>'.$line1.'</h3>
					<p>'.$line2.'</p>
				</div>';
	return $message;
}

function simple_error_message($line1 = 'Error!', $line2 = '')
{

	$message = '<div class="error">
					<h3>'.$line1.'</h3>
					<p>'.$line2.'</p>
				</div>';
	return $message;
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

function sanitize($string = '', $reverse = FALSE)
{
    $characters = array(
        '&',
        '<',
        '>',
        '"',
        '\'',
        '`',
        '!',
        '%',
        '(',
        ')',
        '+',
        '}',
        '{'
    );

    $replacements = array(
        '&amp;',
        '&lt;',
        '&gt;',
        '&quot;',
        '&#x27;',
        '&#x60;',
        '&#x21;',
        '&#x25;',
        '&#x28;',
        '&#x29;',
        '&#x2B;',
        '&#x7D;',
        '&#x7B;'
    );

    if( ! mb_check_encoding($string, 'UTF-8')) $string = utf8_encode($string);

    if($reverse == FALSE) return str_replace($characters, $replacements, $string);
    if($reverse == TRUE) return str_replace($replacements, $characters, $string);

}
function generateRandomColor(){
    $randomcolor = '#' . strtoupper(dechex(rand(0,10000000)));
    if (strlen($randomcolor) != 7){
        $randomcolor = str_pad($randomcolor, 10, '0', STR_PAD_RIGHT);
        $randomcolor = substr($randomcolor,0,7);
    }
return $randomcolor;
}