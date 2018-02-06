<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_HTML_Element_Pair extends Art_Abstract_HTML_Element {
	
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
	 *	Children of this element
	 * 
	 *	@var array
	 */
	protected $_children = array();
	
	
	/**
	 *	Create element by type, params and children
	 * 
	 *	@param string $type
	 *	@param array|string $params
	 *	@param Art_Abstract_HTML_Element[] $children
	 */
	function __construct( $type, $params = array(), $children = array() )
	{
		//Str to array
		if( is_string($params) )
		{
			$params = array( $params );
		}
		
		//To array
		if( !is_array($children) )
		{
			$children = array( $children ); 
		}
		
		$this->_type = $type;
		$this->_params = $params;		
		
		//Add children
		foreach( $children AS $child )
		{
			if( $child instanceof Art_Abstract_HTML_Element )
			{
				$this->addChild($child);
			}
			elseif( is_string($child) )
			{
				$this->addContent($child);
			}
			else
			{
				trigger_error('Unknown child instance, expecting Art_Abstract_HTML_Element or string', E_USER_ERROR);
			}
		}
	}
	
	
	/**
	 *	Add child to element
	 * 
	 *	@param Art_Abstract_HTML_Element $child	
	 *	@param bool [optional] $as_copy If true, child will be copied
	 *	@return this
	 */
	function addChild( Art_Abstract_HTML_Element $child, $as_copy = false )
	{
		if( $as_copy )
		{
			$this->_children[] = clone $child;
		}
		else
		{
			$this->_children[] = $child;
		}
		
		return $this;
	}
	
	
	/**
	 *	Add string content to element
	 * 
	 *	@param string $content
	 *	@return this
	 */
	function addContent( $content )
	{
		if( is_string($content) )
		{
			$this->_children[] = $content;
		}
		else
		{
			trigger_error('Only strings can be added to HTML element content', E_USER_WARNING);
		}
		
		return $this;
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
		
		//Get children output
		foreach( $this->_children AS $child )
		{
			$output .= "\n";
			
			//Render or append child
			if( $child instanceof Art_Abstract_HTML_Element )
			{
				$output .= $child->render( $tab_layer + 1 );
			}
			else
			{
				//Add tabs
				for($i = 0; $i < $tab_layer; $i++)
				{
					$output .= "\t";
				}				
				$output .= "\t".$child;
			}
		}
		
		//Add new line if childrens exist
		if( !empty($this->_children) )
		{
			$output .= "\n";
			for($i = 0; $i < $tab_layer; $i++)
			{
				$output .= "\t";
			}			
		}
		
		//Close tag
		$output .= '</'.$this->_type.'>';
		
		return $output;
	}
}