<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_HTML_Element_Void extends Art_Abstract_HTML_Element{
	
	/**
	 *	Element type
	 * 
	 *	@access protected
	 *	@var string
	 */
	protected $_type;
	
	/**
	 *	Element params
	 * 
	 *	@var array
	 */
	protected $_params;
	
	
	/**
	 *	Create element by type and params
	 * 
	 *	@param string $type
	 *	@param array|string $params
	 */
	function __construct( $type, $params = array() )
	{
		//Str to array
		if( is_string($params) )
		{
			$params = array( $params );
		}
		
		$this->_type = $type;
		$this->_params = $params;
	}
	
	
	/**
	 *	Add element to parent
	 * 
	 *	@param Art_Model_HTML_Element_Pair $parent
	 *	@param bool [optional] $as_copy If true, this will be copied
	 *	@return this
	 */
	function addTo( Art_Model_HTML_Element_Pair $parent, $as_copy = false )
	{
		$parent->addChild($this, $as_copy);
		
		return $this;
	}
	
	
	/**
	 *	Render element
	 * 
	 *	@return string
	 */
	function render( $tab_layer = 0 )
	{
		$output = '';
		
		//Add tabs
		for($i = 0; $i < $tab_layer; $i++)
		{
			$output .= "\t";
		}		
		
		//Add type to tag
		$output .= '<'.$this->_type;
		
		//Add params
		foreach( $this->_params AS $key => $value )
		{
			$output .= ' '.$key.'="'.$value.'" ';
		}
		
		//End tag
		$output .= '>';
		
		return $output;
	}
}