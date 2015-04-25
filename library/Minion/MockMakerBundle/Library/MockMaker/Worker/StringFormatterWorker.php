<?php

/**
 *	StringFormatterWorker
 *
 *	Used by various classes to format strings with delineated values.
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 20, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Library\MockMaker\Worker;

class StringFormatterWorker
{

	/**
	 * Interpolate an associative array
	 * of values into a string.
	 * Placeholders in string must == $k of array.
	 *
	 * Please beware of keys that are similar, but different.
	 * E.g. 'destinationCity' and 'destinationCityId' will both
	 * be replaced by 'destinationCity' because of string matching.
	 *
	 * @param	string	$str
	 * @param	array	$vars
	 * @param	string	$char
	 * @return	string
	 */
	public static function vsprintf2( $str = false, $vars = [], $char = '%' )
	{
		if( !$str ) {
			return '';
		}
		if( count($vars) > 0 ) {
			foreach( $vars as $k => $v ) {
				if( !is_array($v) ) {
					$txt = (is_null($v)) ? 'NULL' : $v;
					$str = str_replace( $char.$k.$char, "{$txt}", $str );
				}
			}
		}

		return $str;
	}

}
