<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Ajax_Response {
	
	/**
	 *	Response exit status
	 *	Determines which messages are send back to client 
	 *	Uses Art_Main::OK, Art_Main::ALERT, Art_Main::ERROR
	 * 
	 *	@var int Exit code 
	 */
	protected $_exitStatus = Art_Main::OK;
	
	/**
	 *	@var string Response name
	 */
	protected $_name = null;
	
	/**
	 *	All fields to send to client 
	 *	Uses Art_Main::OK, Art_Main::ALERT, Art_Main::ERROR
	 * 
	 *	@var array [field_name => status] 
	 */
	protected $_fields = array();

	/**
	 *	@var bool True if response contains only vald data
	 */
	protected $_is_valid = true;
	
	/**
	 *	All messages to send to client
	 *	Only those with $exitStatus are sent actually
	 * 
	 *	@var array [Art_Main::ERROR=>array('First message','Second Message')]
	 */
	protected $_messages = array(Art_Main::ERROR => array(),
								Art_Main::ALERT => array(),
								Art_Main::OK => array());
	
	
	/**
	 * @var array Variables to be parsed to JSON with messages 
	 */
	protected $_variables = array();
	
	
	/**
	 *	@var bool True if this response will be redirected 
	 */
	protected $_is_redirected = false;
	
	
	function __construct($name = null)
	{
		$this->_name = $name;
	}
	
	/**
	 *	Returns TRUE if response contains only valid data
	 *	@return bool
	 */
	function isValid()
	{
		return $this->_is_valid;
	}
	
	
	/**
	 *	Returns TRUE if response field is valid
	 * 
	 *	@param string $name
	 *	@return bool
	 */
	function isFieldValid($name)
	{
		return isset($this->_fields[$name]) && $this->_fields[$name] == Art_Main::OK;
	}
	
	
	/**
	 *	If set true, all messages will be saved to session
	 * 
	 *	@return Art_Model_Ajax_Response
	 */
	function willRedirect()
	{
		$this->_is_redirected = true;
	}
	
	
	/**
	 *	Set exit status of response
	 *	Uses Art_Main::OK, Art_Main::ALERT, Art_Main::ERROR
	 * 
	 *	@param int $status Exit code
	 *	@return \Art_Model_Ajax_Response
	 */
	function setExitStatus($status)
	{
		//Input validation
		if(is_numeric($status))
		{
			if(in_array($status,[Art_Main::OK, Art_Main::ALERT, Art_Main::ERROR]))
			{
				$this->_exitStatus = $status;
			}
			else
			{
				trigger_error('Invalid status code supplied for setExitStatus('.$status.')',E_USER_WARNING);
			}
		}
		else
		{
			trigger_error('Invalid argument supplied for Art_Model_Ajax_Response->setExitStatus()',E_USER_ERROR);
		}
		
		return $this;
	}
	
	
	/**	
	 *	Return array of fields by their status
	 * 
	 *	@param int $status
	 *	@return array
	 */
	function getFieldsByStatus($status)
	{
		$result = array();
		
		foreach($this->_fields AS $field_name => $field_status)
		{
			if($field_status == $status)
			{
				$result[] = $field_name;
			}
		}
		
		return $result;
	}
	
	
	/**
	 *	Add input field result
	 * 
	 *	@param string $name Field name
	 *	@param string [message] $message Response message
	 *	@param int [optional] $status Status code
	 *	@return \Art_Model_Ajax_Response
	 *	@example addField('email','Email was not found')
	 */
	function addField($name, $message = '', $status = Art_Main::ALERT)
	{
		//Add field to fields array
		$this->_fields[$name] = $status;
		//Add message to messages array
		if($message)
		{
			$this->addMessage($message, $status);
		}
		
		if($status > $this->_exitStatus)
		{	
			$this->_is_valid = false;
			$this->setExitStatus($status);
		}
		
		return $this;
	}
	
	
	/**
	 *	Validate input field by given name, value and options
	 * 
	 *	@param string $name Field name
	 *	@param mixed $value Field value
	 *	@param array $options Validator options
 	 *	@return boolean True if field is valid
	 *	@example validateField('name','Foo Bar',array(Art_Validator::MAX_LENGTH=>['value':30,'message':'Name is too long!'])
	 */
	function validateField($name,$value,$options)
	{
		//Prepare options and messages (extract from $options array)
		$messages = [];
		$options_out = [];
		foreach($options AS $type => $option)
		{
			$options_out[$type] = isset($option['value']) ? $option['value'] : true;
			if( isset($option['message']) )
			{
				$messages[$type] = $option['message'];
			}
			else
			{
				$messages[$type] = null;
			}
		}

		//Validate field value
		$response = Art_Validator::validate($value,$options_out,true);

		//If error occured
		if( count($response) )
		{
			//For each response - add field to response			
			foreach($response AS $resp)
			{
				$this->addField($name,$messages[$resp],Art_Main::ALERT);
			}
			return false;
		}
		else
		{
			$this->addField($name,NULL,Art_Main::OK);
			return true;
		}
	}
	
	
	/**
	 *	Add message to output
	 * 
	 *	@param string $message
	 *	@param int [optional] $status
	 *	@return \Art_Model_Ajax_Response
	 */
	function addMessage($message, $status = Art_Main::OK)
	{
		//Input validation
		if(in_array($status, [Art_Main::OK,Art_Main::ALERT,Art_Main::ERROR]))
		{
			$this->_messages[$status][] = $message;
		}
		else
		{
			trigger_error('Invalid argument supplied for '.get_called_class().'->addMessage()');
		}
		
		if($status > $this->_exitStatus)
		{
			$this->_is_valid = false;
			$this->setExitStatus($status);
		}
		
		return $this;
	}
	
	
	/**
	 *	Add alert message to response
	 * 
	 *	@param string $message
	 *	@return Art_Model_Ajax_Response
	 */
	function addAlert( $message )
	{
		$this->addMessage($message, Art_Main::ALERT);
		
		return $this;
	}
	
	
	/**
	 *	Add single variable to JSON output
	 *
	 *	@param string $name
	 *	@param string|array $value
	 */
	function addVariable($name, $value)
	{
		if( is_string($name) || is_array($name) )
		{
			$this->_variables[$name] = $value;
		}
		else
		{
			trigger_error('Invalid argument supplied for '.get_called_class().'->addVariable()');
		}
	}
	
	
	/**
	 *	Execute response and exit
	 * 
	 *	@param int [optional] $options
	 */
	function execute($option = NULL)
	{
		$redirect = $this->_is_redirected;
		
		//Input validation
		if( NULL !== $option )
		{
			if( in_array($option, array(Art_Main::OK,  Art_Main::ALERT, Art_Main::ERROR)) )
			{
				$this->_setExitStatus($option);
			}
			if( in_array($option, array(Art_Ajax::REDIRECT)) )
			{
				$redirect = true;
			}
		}

		//Prepare variables
		$response['variables'] = $this->_variables;
		
		//Prepare status
		$response['status'] = $this->_exitStatus;
		
		//If ok - save alert to session
		if($this->_exitStatus == Art_Main::OK && $redirect)
		{
			Art_Session::set(Art_Main::ALERT_SESSION_NAME, $this->_messages[$this->_exitStatus]);
			Art_Session::set(Art_Ajax::SESSION_STATUS_NAME, $this->_exitStatus);
			Art_Session::set(Art_Ajax::SESSION_RESPONSE_NAME, $this->_name);
		}
		//Send error to client
		else
		{
			//Prepare fields
			$response['fields'] = $this->getFieldsByStatus(Art_Main::ALERT);
			//Prepare messages
			$response['messages'] = $this->_messages[$this->_exitStatus];
		}
		
		
		//Encode response and exit
		$response = json_encode($response);
		
		header('Content-type: application/json');
		exit($response);
	}
}