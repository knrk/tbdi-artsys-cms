<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/abstract
 *	@abstract
 */
abstract class Art_Abstract_Module_Bootstrap {
	use Art_Event_Emitter;
	
	
	/**
	 *	Initialize this bootstrap
	 * 
	 *	@static
	 */
	static function initialize() {}
}