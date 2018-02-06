<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/abstract
 *	@abstract
 */
abstract class Art_Abstract_HTML_Element {
	
	/**
	 *	Function used to get HTML output of element
	 * 
	 *	@abstract
	 */
	abstract function render( $tab_layer = 0 );
	
	
	/**
	 *	Used to add element to parent
	 * 
	 *	@abstract
	 */
	abstract function addTo(Art_Model_HTML_Element_Pair $parent);
}