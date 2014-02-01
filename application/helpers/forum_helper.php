<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed'); NEED DIRECT SCRIPT ACCESS FOR POST LOADER SCRIPT

/**
 * Forum/Topic Helper
 *
 * @author(s) Tyler Diaz, Alex Bor
 * @version 1.0
 * @copyright Crysandrea - August 27, 2010
 * @last_update: March 06, 2012 by Alex Bor
 **/

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

function edit_post($post_data = array(), $user_data = array(), $label = 'Edit')
{
    // $CI =& get_instance();
    if($post_data['author_id'] == $user_data['user_id'] || $user_data['user_level'] != 'user'):
         return anchor('topic/post/edit/'.$post_data['post_id'], $label).' | ';
    else:
        return '';
    endif;
}

function delete_post($post_data = array(), $user_data = array(), $label = 'Delete')
{
     // $CI =& get_instance();
     if($user_data['user_level'] != 'user' && $user_data['user_id'] > 0):
         return ' | '.anchor('topic/delete_post/'.$post_data['post_id'], $label, 'class="delete_post" onclick="javascript:return confirm(\'Are you sure you want to delete this post?\')"');
    else:
        return '';
    endif;
}

function user_online($last_action = '')
{
    $time_ago = time()-$last_action;

    if($time_ago >= 1800)
    {
        return ' | <span class="user_offline">Offline</span>';
    }
    else
    {
        return ' | <span class="user_online">Online</span>';
    }
}

function previous_edits($post_data = array(), $user_data = array())
{
	if($user_data['user_level'] != 'user' && $user_data['user_id'] > 0):
		if(!isset($post_data['number_of_edits'])){
			$post_data['number_of_edits'] = 0;
		}

		if($post_data['number_of_edits'] == 1){
			$text = $post_data['number_of_edits'] . ' Edit';
		}else{
			$text = $post_data['number_of_edits']. ' Edits';
		}

		if($post_data['number_of_edits'] > 0){
	 		return anchor('topic/previous_edits/'.$post_data['post_id'], $text).' | ';
	 	} else {
	 		return;
	 	}
	endif;

	return false;
}


function lock_link($post_data = array(), $user_data = array())
{
     // $CI =& get_instance();
	if($user_data['user_level'] != 'user' && $user_data['user_id'] > 0):
		if(!isset($post_data['lock_edits'])){
			$post_data['lock_edits'] = 0;
		}

		if($post_data['lock_edits'] == 0){
			return anchor('topic/lock_post/'.$post_data['post_id'], 'Lock').' | ';
		}else{
			return anchor('topic/unlock_post/'.$post_data['post_id'], 'Unlock').' | ';
		}
	else:
        return '';
    endif;
}



function display_ribbons($post_data = array(), $user_data = array())
{
    $return_data = '';
    if($post_data['user_level'] != 'user'):
        $return_data .= image('/images/ribbons/'.$post_data['user_level'].'.png', 'class="ribbon"');
    endif;
    if($post_data['donated'] == 1):
        $return_data .= image('/images/ribbons/donated.png', 'title="I donated!" class="ribbon"');
    endif;
    return $return_data;
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

function cleantext($message)
{
    $message = strtolower($message);
	$pattern = '|[[\/\!]*?[^\[\]]*?]|si';
	$message = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", '', $message);
	$message = preg_replace("(\[url=(.+?)\](.+?)\[\/url\])is",'',$message);
	$message = preg_replace("(\[quote\](.+?)\[\/quote\])is",'',$message);
	$message = preg_replace("(\[spoiler\](.+?)\[\/spoiler\])is",'',$message);
	$message = preg_replace("(\[spoiler=(.+?)\](.+?)\[\/spoiler\])is",'',$message);
	$message = preg_replace("(\[quote=(.+?)\](.+?)\[\/quote\])is",'',$message);
	$message = preg_replace("(\[img\](.+?)\[\/img\])is",'',$message);
	$message = preg_replace("(\[imgleft\](.+?)\[\/imgleft\])is",'',$message);
	$message = preg_replace("(\[imgright\](.+?)\[\/imgright\])is",'',$message);
	$message = preg_replace("(\[imgcenter\](.+?)\[\/imgcenter\])is",'',$message);
	return strlen(preg_replace('/\s*/m', '', preg_replace($pattern, '', $message)));
}

// --------------------------------------------------------------------

/**
 * New Function
 *
 * Function Description
 *
 * @access	public
 * @param	none
 * @return	redirect
 * @route	n/a
 */

function post_ip_link($post_data = array(), $user_data = array())
{
     // $CI =& get_instance();
     if($user_data['user_level'] != 'user' && $user_data['user_id'] > 0):
		return '<a class="magicTip" title="'.$post_data['author_ip'].'">Author IP</a> |';
	else:
        return '';
    endif;
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

function parse_earned_palladium($message)
{
    if(is_numeric($message)){
        $palladium = ceil($message / 30);
    	if($palladium > 20) $palladium = 20; // Max gold amount cap
    	if($palladium < 1) $palladium = 1; // Min gold amount cap
    	return $palladium;
    }
    else
    {
        $palladium = ceil(cleantext($message) / 30);
    	if($palladium > 20) $palladium = 20; // Max gold amount cap
    	if($palladium < 1) $palladium = 1; // Min gold amount cap
    	return $palladium;
    }
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

function parse_bbcode($text = '')
{
	$text = str_replace('crysandrea.com/signout', 'crysandrea.com/signout/__REMOVE__', $text);
	$url_regex = " a-zA-Z0-9\:\/\-\?\&\.\=\_\~\#\'+";
//You can create smileys using something along these lines. Just as an example here is a smiley which replaces 		:goodnight: with an image from a different website. You can also do something local of course :). 
//$text = preg_replace("(\:goodnight\:)is",'<img src="http://www.smileyvault.com/albums/forum/smileyvault-goodnight.gif" border="0" alt="Good Night Smiley" title="Good Night Smiley" /></a>',$text);
	$text = str_replace('onload="','',$text);
	$text = preg_replace("/\[url\]([$url_regex]*)\[\/url\]/", '<a href="$1" target="_blank">$1</a>', $text);
	$text = preg_replace("(\[url\=([$url_regex]*)\](.+?)\[/url\])", '<a href="$1" target="_blank">$2</a>', $text);
	$text = preg_replace("(\[!\](.+?)\[\/!])is",'<span class="highlight">$1</span>',$text);
	$text = preg_replace("(\[b\](.+?)\[\/b])is",'<strong>$1</strong>',$text);
	$text = preg_replace("(\[i\](.+?)\[\/i])is",'<i>$1</i>',$text);
	$text = preg_replace("(\[u\](.+?)\[\/u])is",'<u>$1</u>',$text);
	$text = preg_replace("(\[s\](.+?)\[\/s])is",'<s>$1</s>',$text);
	$text = preg_replace("(\[code\](.+?)\[\/code])is",'<code>$1</code>',$text);
	$text = preg_replace("(\[quote\](.+?)\[\/quote\])is","<div class=\"quote\"><strong>Anonymous quote:</strong><br />$1</div>",$text);

	$text = preg_replace("(\[spoiler\](.+?)\[\/spoiler])is",'<div class="spoiler_container"><a href="#" class="reveal_spoiler">Reveal hidden text <span class="reveal_arrow">&darr;</span></a><div class="spoiler_value">$1</div></div>',$text);
	$text = preg_replace("(\[spoiler=(.+?)\](.+?)\[\/spoiler\])is",'<div class="spoiler_container"><a href="#" class="reveal_spoiler">$1 <span class="reveal_arrow">&darr;</span></a><div class="spoiler_value">$2</div></div>',$text);
	$text = preg_replace("(\[align=(.+?)\](.+?)\[\/align\])is","<div style=\"text-align:$1\">$2</div>",$text);
	$text = preg_replace("(\[color=(.+?)\](.+?)\[\/color\])is","<span style=\"color: $1\">$2</span>",$text);
	$text = preg_replace("(\[size=([1-5][0-9]|[6-9])\](.+?)\[\/size\])is","<span style=\"font-size: $1px\">$2</span>",$text);
	$text = preg_replace("(\[font=(.+?)\](.+?)\[\/font\])is","<span style=\"font-family: $1;\">$2</span>",$text);
    $text = preg_replace("/\[imgright\](.+?)\[\/imgright\]/i", '<img style=" float: right;" src="$1$2" alt="Image" />', $text);
    $text = preg_replace("/\[imgleft\](.+?)\[\/imgleft\]/i", '<img style="float: left;" src="$1$2" alt="Image" />', $text);
    $text = preg_replace("/\[imgcenter\](.+?)\[\/imgcenter\]/i", '<span style=\"textalign: center;\" ><img style=" clear:both;" src="$1$2" alt="Image" /></span>', $text);
	$text = preg_replace("/\[img\](.+?)\[\/img\]/i", '<img src="$1$2" alt="Image" />', $text);

	$quotes = substr_count(strtolower($text),'[/quote]');
	for($i=0; $i < $quotes;$i++) {
		$text = preg_replace("'\[quote=(.+?)\](.+?)\[\/quote\]'is",'<div class="quote-1"><strong>\\1 said:</strong><br />\\2</div>',$text);
	}

	$text = preg_replace("(\@(.+?)\:)is",'@<strong>$1</strong>:',$text);
	$text = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "'\\1<a href=\"\\2\" >\\2</a>'", $text);

	return $text;
}