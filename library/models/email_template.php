<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int $id
 *	@property string	$guid
 *	@property string	$name
 *	@property string	$subject
 *	@property string	$body
 *	@property string	$from_name
 *	@property string	$from_email
 *	@property string	$reply_to_name
 *	@property string	$reply_to_email
 *	@property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 */
class Art_Model_Email_Template extends Art_Abstract_Model_DB {
	
    protected static $_table = 'email_template';
    	
    protected static $_cols = array('id'				=>	array('select','insert'),
                                    'guid'				=>	array('select','insert','update'),
                                    'name'				=>	array('select','insert'),
                                    'subject'			=>	array('select','insert','update'),
                                    'body'				=>	array('select','insert','update'),
                                    'from_name'			=>	array('select','insert','update'),
                                    'from_email'		=>	array('select','insert','update'),
                                    'reply_to_name'		=>	array('select','insert','update'),
                                    'reply_to_email'	=>	array('select','insert','update'),
									'created_by'		=>	array('select','insert'),
									'modified_by'		=>	array('select','update'),
									'created_date'		=>	array('select'),
									'modified_date'		=>	array('select'));
	
	
	/**
	 *	Replace identities in subject (mail subject or body) with values
	 *	Ex.: "Hello [name]" being replaced by array("name" => "Peter"); returns "Hello Peter"
	 * 
	 *	@param array $values
	 *	@param string $subject
	 *	@param string [optional] $forward_separator
	 *	@param string [optional] $backward_separator
	 *	@return string
	 */
	static function replaceIdentities( $values, $subject, $forward_separator = '[', $backward_separator = ']' )
	{
		$keys = array();
		foreach( $values AS $key => &$value )
		{
			$keys[] = $forward_separator.$key.$backward_separator;
		}
		
		return str_replace($keys, $values, $subject);
	}
}