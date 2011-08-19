<?php if ( ! defined('EXT')) { exit('Invalid file request'); }
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2009, EllisLab, Inc.
 * @license		http://expressionengine.com/docs/license.html
 * @link		http://expressionengine.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Valid URL
 *
 * This plugin generates a usable URL for anchor tags, or anything else you desire.
 *
 * @package		ExpressionEngine
 * @subpackage	Core
 * @category	Plugins
 * @author		ExpressionEngine Dev Team
 * @link		http://expressionengine.com
 */
$plugin_info = array(
						'pi_name'			=> 'Valid URL',
						'pi_version'		=> '1.1',
						'pi_author'			=> 'Justin Hurlburt',
						'pi_author_url'		=> 'http://expressionengine.com/',
						'pi_description'	=> 'Generates a URL valid for use in XHTML as an href or src attribute',
						'pi_usage'			=> Valid_url::usage()
					);
					
class Valid_url {

	var $return_data = '';

	/**
	 * Valid URL
	 *
	 * @access	public
	 * @return	void	// sets $return_data
	 */
	function Valid_url($str = '')
	{
		$this->EE =& get_instance();
		
		// characters we don't want urlencoded
		$protected = array('&' => 'AMPERSANDMARKER', '/' => 'SLASHMARKER', '=' => 'EQUALSMARKER');

		if ($str == '')
		{
			$str = $this->EE->TMPL->tagdata;
		}

		// decode first since we'll do our own encoding later
		$str = str_replace(SLASH, '/', trim(urldecode(str_replace('&amp;', '&', $str))));
		
		// error trapping for seriously malformed URLs, take 1
		if (($url = @parse_url($str)) === FALSE)
		{
			$this->EE->TMPL->log_item('Valid URL Plugin error: unable to parse URL '.htmlentities($str));
			return;
		}

		// error trapping for seriously malformed URLs, take 2
		if ( ! isset($url['scheme']) && ($url = @parse_url("http://{$str}")) === FALSE)
		{
			$this->EE->TMPL->log_item('Valid URL Plugin error: unable to parse URL '.htmlentities($str));
			return;
		}

		foreach ($url as $key => $value)
		{
			switch($key)
			{
				case 'path':
					$url[$key] = urlencode(str_replace(array_keys($protected), $protected, $value));
					break;
				case 'query':
					$url[$key] = '?'.urlencode(str_replace(array_keys($protected), $protected, $value));
					break;
				case 'scheme':
					$url[$key] .= ($value == 'file') ? ':///' : '://';
					break;
			}
		}

		$this->return_data = implode('', str_replace('&', '&amp;', str_replace($protected, array_keys($protected), $url)));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Usage
	 *
	 * @access	public
	 * @return	string
	 */
	function usage()
	{
		return <<<ONESY
		Makes sure that a URL has a protocol, that ampersands are converted to entities, and all
		other characters are properly URL encoded.
		
		{exp:valid_url}www.example.com/foo bar/bat?=bag&mice=men!{/exp:valid_url}
		
		Produces:
		http://www.example.com/foo+bar/bat?=bag&amp;mice=men%21
		
		Version 1.1
		******************
		- Updated plugin to be 2.0 compatible
		
ONESY;
	}

	// --------------------------------------------------------------------
	
}
// END CLASS

/* End of file pi.valid_url.php */
/* Location: ./system/expressionengine/third_party/valid_url/pi.valid_url.php */