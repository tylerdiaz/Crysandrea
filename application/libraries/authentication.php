<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Authentication Library - Manages sesions & accounts
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 * @copyright Origamee - May 18, 2011
 * @last_update: May 18, 2011 by Tyler Diaz
 **/

require('extentions/passwordhash.php');

class Authentication
{
	var $CI;
	var $user_table = 'users';

	function __construct(){
		$this->CI =& get_instance();
	}

    // --------------------------------------------------------------------

    /**
     * Sign in
     *
     * New page description
     *
     * @access  public
     * @param   username
     * @param   user_pass
     * @return  boolean
     * @route   n/a
     */

	function signin($username_or_email = '', $user_pass = '')
	{
		if($username_or_email == '' OR $user_pass == '') return false;
		if(isset($this->CI->system->userdata['username']) && $this->CI->system->userdata['username'] == $username_or_email) return true;

		if(preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $username_or_email)):
		    $query = $this->CI->db->where('email', $username_or_email)->get($this->user_table);
		else:
		    $query = $this->CI->db->where('username', $username_or_email)->get($this->user_table);
		endif;

		if ($query->num_rows() > 0):
			$user_data = $query->row_array();

			if( ! $this->verify_password($user_pass, $user_data['user_id'])) return false;

			// $this->CI->session->destroy();
			$this->CI->session->create();

            $this->CI->db->set('user_last_login', 'NOW()', false)->where('user_id', $user_data['user_id'])->update($this->user_table);

			$session_data = array(
			    'username'  => $user_data['username'],
			    'user_id'   => $user_data['user_id'],
			    'user_level' => $user_data['user_level']
			);

            $this->CI->load->helper('string');

            $new_token = random_string('alnum', 32);

            // $this->CI->db->where('user_id', $user_data['user_id'])->update('users', array('auto_login_token' => $new_token));

            set_cookie(array(
                'name'   => 'session_token',
                'value'  => $new_token,
                'expire' => '605500'
            ));

			$this->CI->session->set_userdata($session_data);

			return true;
		else:
			return false;
		endif;
	}

    // --------------------------------------------------------------------

    /**
     * Sign out
     *
     * New page description
     *
     * @access  public
     * @param   none
     * @return  redirect
     * @route   n/a
     */

	function signout(){
        $this->CI->load->helper('string');

	    $this->CI->db->where('user_id', $this->CI->session->userdata('user_id'))
	    			 ->update('users', array('auto_login_token' => 'LOGGED_OUT_'.random_string('alnum', 24)));

	    delete_cookie("session_token");
	    delete_cookie("session");
		$this->CI->session->destroy();
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

	function verify_password($password, $user_id){
	    $query = $this->CI->db->where(array('user_id' => $user_id))->get($this->user_table);

		if ($query->num_rows() > 0):
			$user_data = $query->row_array();
			$hasher = new PasswordHash();
			if($hasher->check_password($password, $user_data['user_pass'])):
			    return TRUE;
			else:
			    return FALSE;
		    endif;
		else:
		    return FALSE;
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

	public function hash_password($password = '')
	{
		$hasher = new PasswordHash();
	    return $hasher->hash_password($password);
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

	function create_user($userdata = array())
	{
		$hasher = new PasswordHash();

        $text_password = $userdata['user_pass'];
		$userdata['user_pass'] = $hasher->hash_password($userdata['user_pass']);
		$userdata['user_ip'] = $this->CI->input->ip_address();

		$this->CI->db->set('register_date', 'NOW()', false)->insert('users', $userdata);

		$user_id = $this->CI->db->insert_id();

        $this->signin($userdata['username'], $text_password);

		return $user_id;
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

	public function auto_login($key = '')
	{
	    if(strlen($key) < 30 || ctype_alnum($key) == FALSE) return FALSE;

	    $user = $this->CI->db->get_where('users', array('auto_login_token' => $key));

	    if($user->num_rows() > 0){
	        $user_data = $user->row_array();
	        $this->CI->session->sess_destroy();
			$this->CI->session->sess_create();

            $this->CI->db->set('last_login', 'NOW()', false)
                         ->where('user_id', $user_data['user_id'])
                         ->update($this->user_table);

			$session_data = array(
			    'username'  => $user_data['username'],
			    'user_id'   => $user_data['user_id'],
			    'user_rank' => $user_data['user_rank'],
			    'signed_in' => true
			);

            $this->CI->load->helper('string');

            $new_token = random_string('alnum', 32);

            $this->CI->db->where('user_id', $user_data['user_id'])->update('users', array('last_activity' => time(), 'auto_login_token' => $new_token));

            set_cookie(array(
                'name'   => 'session_token',
                'value'  => $new_token,
                'expire' => 2422000 // One month in seconds
            ));

			$this->CI->session->set_userdata($session_data);

			return TRUE;
	    } else {
	        return FALSE;
	    }
	}

	var $banned_words = array('heroin', 'methamphetamine', 'crack', 'lsd', 'ecstasy', 'opium', 'marijuana', 'mushrooms', 'psilocybin', 'pcp', "4r5e","5h1t","5hit","a55","anal","anus","ar5e","arrse","arse","ass","ass fucker","asses","assfucker","assfukka","asshole","assholes","asswhole","a s s","b!tch","b00bs","b17ch","b1tch","ballbag","balls","ballsack","bastard","beastial","beastiality","bellend","bestial","bestiality","bi+ch","biatch","bitch","bitcher","bitchers","bitches","bitchin","bitching","bloody","blow job","blowjob","blowjobs","boiolas","bollock","bollok","boner","boob","boobs","booobs","boooobs","booooobs","booooooobs","breasts","buceta","bugger","bum","bunny fucker","butt","butthole","buttmuch","buttplug","c0ck","c0cksucker","carpet muncher","cawk","chink","cipa","cl1t","clit","clitoris","clits","cnut","cock","cock sucker","cockface","cockhead","cockmunch","cockmuncher","cocks","cocksuck ","cocksucked ","cocksucker","cocksucking","cocksucks ","cocksuka","cocksukka","cok","cokmuncher","coksucka","coon","cox","crap","cum","cummer","cumming","cums","cumshot","cunilingus","cunillingus","cunnilingus","cunt","cuntlick ","cuntlicker ","cuntlicking ","cunts","cyalis","cyberfuc","cyberfuck ","cyberfucked ","cyberfucker","cyberfuckers","cyberfucking ","d1ck","damn","dick","dickhead","dildo","dildos","dink","dinks","dirsa","dlck","dog fucker","doggin","dogging","donkeyribber","doosh","duche","dyke","ejaculate","ejaculated","ejaculates ","ejaculating ","ejaculatings","ejaculation","ejakulate","f u c k","f u c k e r","f4nny","fag","fagging","faggitt","faggot","faggs","fagot","fagots","fags","fanny","fannyflaps","fannyfucker","fanyy","fatass","fcuk","fcuker","fcuking","feck","fecker","felching","fellate","fellatio","fingerfuck","fingerfucked","fingerfucker","fingerfuckers","fingerfucking","fingerfucks","fistfuck","fistfucked","fistfucker","fistfuckers","fistfucking","fistfuckings","fistfucks","flange","fook","fooker","fuck","fucka","fucked","fucker","fuckers","fuckhead","fuckheads","fuckin","fucking","fuckings","fuckingshitmotherfucker","fuckme","fucks","fuckwhit","fuckwit","fudge packer","fudgepacker","fuk","fuker","fukker","fukkin","fuks","fukwhit","fukwit","fux","fux0r","f u c k","gangbang","gangbanged","gangbangs","gaylord","gaysex","goatse","god","god dam","god damned","goddamn","goddamned","hardcoresex","hell","heshe","hoar","hoare","hoer","homo","hore","horniest","horny","hotsex","jack off","jackoff","jap","jerk off","jism","jiz","jizm","jizz","kawk","knob","knobead","knobed","knobend","knobhead","knobjocky","knobjokey","kock","kondum","kondums","kum","kummer","kumming","kums","kunilingus","l3ich","l3itch","labia","lmfao","lusting","m0f0","m0fo","m45terbate","ma5terb8","ma5terbate","masochist","master bate","masterb8","masterbat3","masterbate","masterbation","masterbations","masturbate","mo fo","mof0","mofo","mothafuck","mothafucka","mothafuckas","mothafuckaz","mothafucked ","mothafucker","mothafuckers","mothafuckin","mothafucking ","mothafuckings","mothafucks","mother fucker","motherfuck","motherfucked","motherfucker","motherfuckers","motherfuckin","motherfucking","motherfuckings","motherfuckka","motherfucks","muff","mutha","muthafecker","muthafuckker","muther","mutherfucker","n1gga","n1gger","nazi","nigg3r","nigg4h","nigga","niggah","niggas","niggaz","nigger","niggers ","nob","nob jokey","nobhead","nobjocky","nobjokey","numbnuts","nutsack","orgasim ","orgasims ","orgasm","orgasms ","p0rn","pawn","pecker","penis","penisfucker","phonesex","phuck","phuk","phuked","phuking","phukked","phukking","phuks","phuq","pigfucker","pimpis","piss","pissed","pisser","pissers","pisses ","pissflaps","pissin ","pissing","pissoff ","poop","porn","porno","pornography","pornos","prick","pricks ","pron","pube","pusse","pussi","pussies","pussy","pussys ","rectum","retard","rimjaw","rimming","s hit","s.o.b.","sadist","schlong","screwing","scroat","scrote","scrotum","semen","sex","sh!+","sh!t","sh1t","shag","shagger","shaggin","shagging","shemale","shi+","shit","shitdick","shite","shited","shitey","shitfuck","shitfull","shithead","shiting","shitings","shits","shitted","shitter","shitters ","shitting","shittings","shitty ","skank","slut","sluts","smegma","smut","snatch","son of a bitch","spac","spunk","s h i t","t1tt1e5","t1tties","teets","teez","testical","testicle","tit","titfuck","tits","titt","tittie5","tittiefucker","titties","tittyfuck","tittywank","titwank","tosser","turd","tw4t","twat","twathead","twatty","twunt","twunter","v14gra","v1gra","vagina","viagra","vulva","w00se","wang","wank","wanker","wanky","whoar","whore","willies","willy","xrated","xxx");

	public function username_check($username = '')
	{
	    if( ! in_array(strtolower($username), $this->banned_words) && preg_match("/^([a-zA-Z0-9\s])+$/i", $username)):
	    	return TRUE;
	    else:
	    	return FALSE;
    	endif;
	}

}
?>
