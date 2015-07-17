<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
Copyright (C) 2003 - 2015 EllisLab, Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
ELLISLAB, INC. BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Except as contained in this notice, the name of EllisLab, Inc. shall not be
used in advertising or otherwise to promote the sale, use or other dealings
in this Software without prior written authorization from EllisLab, Inc.
*/

/**
 * Valid URL
 *
 * This plugin generates a usable URL for anchor tags, or anything else you desire.
 *
 * @package			ExpressionEngine
 * @category		Plugins
 * @author			EllisLab
 * @copyright		Copyright (c) 2004 - 2015, EllisLab, Inc.
 * @link			https://github.com/EllisLab/Valid-Url
 */
class Valid_url {

	public $return_data = '';

	/**
	 * Valid URL
	 *
	 * @access	public
	 * @return	void	// sets $return_data
	 */
	function __construct($str = '')
	{
		// characters we don't want urlencoded
		$protected = array('&' => 'AMPERSANDMARKER', '/' => 'SLASHMARKER', '=' => 'EQUALSMARKER');

		if ($str == '')
		{
			$str = ee()->TMPL->tagdata;
		}

		// decode first since we'll do our own encoding later
		$str = str_replace(SLASH, '/', trim(urldecode(str_replace('&amp;', '&', $str))));

		// error trapping for seriously malformed URLs, take 1
		if (($url = @parse_url($str)) === FALSE)
		{
			ee()->TMPL->log_item('Valid URL Plugin error: unable to parse URL '.htmlentities($str));
			return;
		}

		// error trapping for seriously malformed URLs, take 2
		if ( ! isset($url['scheme']) && ($url = @parse_url("http://{$str}")) === FALSE)
		{
			ee()->TMPL->log_item('Valid URL Plugin error: unable to parse URL '.htmlentities($str));
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
}
