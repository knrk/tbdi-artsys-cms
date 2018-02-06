<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_XMLWriter extends XMLWriter {
	
	/**
	 *	Create new 1.0 XML writer with UTF-8 encoding
	 */
	function __construct() 
	{
		$this->openMemory();
		$this->startDocument('1.0', 'UTF-8');
	}
	
	
	/**
	 *	Save formated XML to file
	 * 
	 *	@param string $file_path
	 *	@return int The number of bytes that were written to the file, or FALSE on failure.
	 */
	function saveTo( $file_path )
	{
		return file_put_contents($file_path, $this->render());
	}
	
	
	/**
	 *	Render formated XML output
	 * 
	 *	@return string
	 */
	function render()
	{
		$dom = new Art_Model_DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($this->outputMemory(true));
		return $dom->saveXML();
	}
}