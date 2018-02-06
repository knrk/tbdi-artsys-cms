<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/helperss
 * 
 *	Default helper used as template
 */
class Art_Helper_Default extends Art_Abstract_Helper {

	/**
	 *	Default function used as template
	 * 
	 *	@static
	 *	@param string $name
	 *	@param string|int $content
	 *	@return string
	 */
	static function elementPaired( $name, $content )
	{
		return '<'.$name.'>'.$content.'</'.$name.'>';
	}
}