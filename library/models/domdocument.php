<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_DOMDocument extends DOMDocument {
	
	/**
	 *	@var DOMXPath 
	 */
	protected $_xpath;
	
	
	/**
	 * Append this to XPATH query for selecting only "visible" content of elements
	 */
	const EXCLUDE_NONTEXT = 'not(ancestor::script) and not(ancestor::style) and not(ancestor::button) and not(ancestor::input) and not(normalize-space(.) = "")';
	
	
	/**
	 * 
	 * @param string $selector
	 * @param bool [optional] $text_only If true, only text of the elements will be selected
	 * @return DOMNodeList
	 */
	function search( $selector, $text_only = false )
	{
		if( NULL === $this->_xpath )
		{
			$this->_xpath = new DOMXPath($this);
		}
		
		if( $text_only )
		{
			$selector = $selector.'//text()';
		}
		
		return $this->_xpath->query($selector);
	}
	
	
	/**
	 * Searches for an element with a certain class
	 * 
	 * @param string $class
	 * @return DOMNodeList
	 */
	function getElementsByClass( $class )
	{
		return $this->search('//*[contains(@class,"'.$class.'")]');
	}
}