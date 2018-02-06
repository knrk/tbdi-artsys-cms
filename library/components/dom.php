<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_DOM extends Art_Abstract_Component {
	
	/**
	 *	Load XML from file
	 * 
	 *	@static
	 *	@param string $path
	 *	@return \SimpleXMLElement Or NULL if input string cannot be decoded
	 */
	static function loadXMLFromFile( $path )
	{
		$xml = file_get_contents($path);
		return static::loadXMLFromString($xml);
	}
	
	
	/**
	 *	Load XML from string
	 * 
	 *	@param string $xml XML string
	 *	@return \SimpleXMLElement
	 */
	static function loadXMLFromString( $xml )
	{
		try
		{
			libxml_use_internal_errors(true);
			$output = new SimpleXMLElement($xml, LIBXML_NOCDATA);
			libxml_use_internal_errors(false);
			return $output;
		}
		catch(Exception $e)
		{
			return NULL;
		}
	}
	
	
	/**
	 *	Load DOM object from file
	 * 
	 *	@param string $path
	 *	@return Art_Model_DOMDocument
	 */
	static function loadDOMFromFile( $path )
	{
		$content = file_get_contents($path);
			
		$content = mb_convert_encoding($content, 'utf-8', mb_detect_encoding($content));
		$content = mb_convert_encoding($content, 'html-entities', 'utf-8');
		
		return static::loadDOMFromString($content);
	}
	
	
	/**
	 *	Load DOM object from string
	 * 
	 *	@param string $dom DOM string
	 *	@return Art_Model_DOMDocument
	 */
	static function loadDOMFromString( $dom )
	{
		$document = new Art_Model_DOMDocument();
		libxml_use_internal_errors(true);
		@$document->loadHTML($dom);
		libxml_use_internal_errors(false);

		return $document;
	}
	
	
	/**
	 *	Get new instance of XML writer
	 * 
	 *	@return \Art_XMLWriter
	 */
	static function newXMLWriter()
	{
		return new Art_Model_XMLWriter;
	}
}