<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Chirp (A notification tool)
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 * @copyright Tyler Diaz
 **/

class Chirp
{
	private $redis = FALSE;
	private $salt = 'chrp54l7';
	private $hash = 'ripemd128';

	function __construct()
	{
		if(class_exists('Redis') && ! $this->redis):
			$this->redis = new Redis();
		  $this->redis->connect('127.0.0.1', 6379);
		endif;
	}

	function __destruct()
	{
		if($this->redis) $this->redis->close();
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

	public function broadcast($room = 0, $message = '')
	{
		if ($this->redis):
			$this->redis->publish($room, $message);
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

	public function encrypt_key($user_id = 0)
	{
		return 'chirp:'.hash($this->hash, $user_id.$this->salt);
	}

}


/* End of file chirp.php */
/* Location: ./application/models/chirp.php */