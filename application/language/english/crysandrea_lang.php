<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Global Language file
 *
 * @author(s) Tyler Diaz / AlexBor
 * @version 1.0
 * @copyright Crysandrea - April 10, 2010
 **/
 
$lang['admin_email'] = 'me[at]tylerdiaz.com';

/* 
*  Inbox/Outbox/Savebox wordings.
*/
$lang['inbox_title'] = 'Your Inbox';
$lang['outbox_title'] = 'Your Outbox';
$lang['savebox_title'] = 'Your Savebox';
$lang['reading_message_title'] = 'Reading message';
$lang['create_message_title'] = 'Create a message';
$lang['reply_message_title'] = 'Reply to message';

$lang['header_message_sent'] = 'Message sent!';
$lang['message_sent'] = 'You have successfully sent your message to %s.';
$lang['header_messages_deleted'] = 'Messages deleted!';
$lang['messages_deleted'] = 'You have successfully deleted the selected messages.';
$lang['header_messages_set_read'] = 'Messages have been read!';
$lang['messages_set_read'] = 'You have successfully stamped the selected messages as read.';
$lang['header_messages_set_unread'] = 'Messages have been unread!';
$lang['messages_set_unread'] = 'You have successfully stamped the selected messages as unread.';
$lang['header_no_selected_messages'] = 'No message selected';
$lang['no_selected_messages'] = 'Sorry, you must select a message to apply that to.';
$lang['header_saved_message'] = 'Saved';
$lang['saved_message'] = 'Your selected messages has been successfully <b>saved</b>.';
$lang['header_unsave_message'] = 'Unsaved';
$lang['unsaved_message'] = 'Your selected messages has been successfully <b>unsaved</b>.';
$lang['header_message_trouble'] = 'Dear %s, we had some troubles.';
$lang['unsaved_message'] = 'The magic forest faries of Crysandrea seem to be having troubles giving you the mail you requested. Maybe it belongs to someone else?';
$lang['header_message_delete_error'] = 'Error Deleting';
$lang['message_delete_error'] = 'You are not the owner of that message.';
$lang['button_new_message'] = 'Create message';
$lang['inbox_table_label'] = 'Your private messages:';


/* 
*  Button label wordings.
*/
$lang['recover_password_button'] = 'Recover password';
$lang['reset_password_button'] = 'Set new password';

/* 
*  Authentication wordings.
*/
$lang['signin_title'] = '';
$lang['signup_title'] = '';
$lang['recover_password_title'] = 'I forgot my password';

// Recover password wordings
$lang['recover_password_header'] = 'Forgot your password?';
$lang['header_recover_password_steps'] = 'Simple instructions';
$lang['recover_password_steps'] = array(
	'Check your email inbox and spam box. *just incase*', 
	'Open the "Password recovery" email we will send you.', 
	'Click on the recovery link inside the email.'
);
$lang['recover_password_notice'] = 'If you haven\'t received an email from us in 30 minutes, or you are having troubles restoring your password. Feel free to email Pixeltweak ('.$lang['admin_email'].') for a manual password reset.';
$lang['header_password_reset'] = 'Your password has been reset';
$lang['password_reset'] = 'You may now check your email and retrieve your new password to sign in. We do recommend changing your password when you signin to something more memorable so you wont have to reset your password again. <br /><br /><small>If your account is experiencing difficulties, please contact a moderator or administrator.</small>';

// Signin wordings
$lang['header_login_attemps_max'] = 'Maxed out login attempts';
$lang['login_attemps_max'] = 'You have exceeded the attempts to login for now! We do this to protect unauthorized people from accessing your account, please try again in 5 minutes.';
$lang['wrong_recovery_key'] = 'Aww man, we can\'t register that password key for you, our security robots seem to prevent us from doing so. Send an email over to '.$lang['admin_email'].' so we can give you a personal hand of help.';

/* 
*  Signup words
*/
$lang['verified_human'] = 'Thanks for solving those security words, you already proved you\'re human.';
$lang['email_in_use'] = 'The email you entered is already used by another account.';
$lang['username_in_use'] = 'The Username you entered is already registered.';

/* 
*  Uncategorized wordings.
*/
$lang['header_ticket_created'] = 'You have sent in a ticket!';
$lang['ticket_created'] = 'Thank you for sending in a ticket, we will do our best to reply to it as soon as possible. 
If we have not replied to it in 48 hours, feel free to send a private message to an online staff member.';


/* 
*  Form validation callback errors
*/
$lang['header_typical_error'] = 'Uh oh, something went wrong';
// $lang['user_not_found'] = '%s was looked for all over the place, and wasn\'t found. Maybe that user doesn\'t exist?';
$lang['user_not_found'] = 'We searched all over the place for %s, but couldn\'t find them. Maybe that user doesn\'t exist?';
$lang['email_not_found'] = 'The email address you have gave us doesn\'t seem to be in use.';

/* End of file crysandrea_lang.php */
/* Location: ./system/application/language/english/crysandrea_lang.php */