<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Ajax_Request {
	
	/**
	 *	@var string URL to call when event occures
	 */
	protected $_action = '';
	
	/**
	 *	@var string Name of this request
	 */
	protected $_name = NULL;
	
	/**
	 *	What to update when event occures
	 *	Contains multiple arrays with values 
	 *		- from = variable name
	 *		- element = element to update selector
	 *		- only_content = true to update only content of element, false to replace
	 *  
	 *	@var Array Array of arrays 
	 */
	protected $_update = array();
	
	/**
	 *	All URL variables
	 *	@var array 
	 */
	protected $_variables = array();
	
	/**
	 *	Text used in confirm window
	 *	@var string 
	 */
	protected $_confirmText = '';
	
	/**
	 *	Href to redirect after sucessful response
	 *	@var string
	 */
	protected $_redirect;
	
	/**
	 *	Auto submit form after X seconds of inactivity
	 * 
	 *	@var bool 
	 */
	protected $_auto_submit = false;
	
	/**
	 *	Auto submit form time
	 */
	protected $_auto_submit_time = self::AUTO_SUBMIT_TIME_DEFAULT;
	
	/**
	 *	If true, all input type='file' ar uploaded asynchronously
	 *	@var bool 
	 */
	protected $_file_async = false;
	
	/**
	 *	Name of async request
	 *	@var type 
	 */
	protected $_file_request_name;
	
	/**
	 *	Max size of one file (in MB)
	 *	@var float
	 */
	protected $_file_max_size_single;
	
	/**
	 *	Max sum of sizes of all files (in MB)
	 *	@var float
	 */
	protected $_file_max_size_sum;
	
	/**
	 *	All allowed file extensions
	 *	@var array
	 */
	protected $_file_extensions;
	
	/**
	 *	Default single filesize (in MB)
	 */
	const FILE_DEFAULT_SIZE_SINGLE = 5;
	
	/**
	 *	Default sum of all filesizes (in MB)
	 */
	const FILE_DEFAULT_SIZE_SUM = 30;
	
	/**
	 *	Default file extensions (array)
	 */
	const FILE_DEFAULT_EXTENSIONS = '*';
	
	/**
	 *	Default auto submit form time
	 */
	const AUTO_SUBMIT_TIME_DEFAULT = 2;
	
	/**
	 *	URL variable prefix
	 */
	const VARIABLE_PREFIX = '$';
	
	/**
	 *	Refresh after sucessful response
	 */
	CONST REFRESH = '.';
	
	/**
	 *	Create new request and set action
	 * 
	 *	@param string $name
	 *	@param string $action_url
	 *	@return Art_Model_Ajax_Request
	 */
	function __construct($name, $action_url = NULL) 
	{
		$this->setName($name);
		
		if( NULL !== $action_url )
		{
			$this->setAction($action_url);
		}
	}
	
	
	/**
	 *	Set name of request
	 * 
	 *	@param string $name
	 *	@return Art_Model_Ajax_Request
	 */
	function setName( $name )
	{
		$this->_name = $name;
		
		return $this;
	}
	
	
	/**
	 *	Replace variable with its value
	 * 
	 *	@param string $name
	 *	@param string $value
	 *	@return Art_Model_Ajax_Request
	 */
	function setVariable($name,$value)
	{
		$this->_action = str_replace(self::VARIABLE_PREFIX.$name, $value, $this->_action);
		$this->_confirmText = str_replace('{'.self::VARIABLE_PREFIX.$name.'}', $value, $this->_confirmText);
		
		return $this;
	}
	
	
	/**
	 *	Replace variables with its values
	 * 
	 *	@param array $values
	 *	@return Art_Model_Ajax_Request
	 */
	function setVariables($values)
	{
		if( is_string($values) || is_numeric($values) )
		{
			$values = array($values);
		}
		
		foreach($values AS $name => $value)
		{
			//Remove variable prefix if set
			if( $name[0] == self::VARIABLE_PREFIX )
			{
				$name = substr($name, 1);
			}
			
			$this->setVariable($name, $value);
		}
		
		return $this;
	}
	
	
	/**
	 *	Set URL to call when event occures
	 * 
	 *	@param string $url
	 *	@return Art_Model_Ajax_Request
	 */
	function setAction($url)
	{
		$this->_action = $url;
		$this->_findVariables($url, true);
		
		return $this;
	}
	
	
	/**
	 *	Show confirm prompt when event occures
	 * 
	 *	@param string $text
	 *	@return Art_Model_Ajax_Request
	 */
	function setConfirmWindow($text)
	{
		$this->_confirmText = $text;
		$this->_findVariables($text);
	}
	
	
	/**
	 *	Sets redirect after sucessful response
	 * 
	 *	@param string $href
	 *	@return Art_Model_Ajax_Request
	 */
	function setRedirect( $href )
	{
		$this->_redirect = $href;
		
		return $this;
	}
	
	
	/**
	 *	Sets refresh after sucessful response
	 * 
	 *	@return Art_Model_Ajax_Request
	 */
	function setRefresh()
	{
		$this->_redirect = self::REFRESH;
		
		return $this;
	}
	
	
	/**
	 *	Set form to submit automatically after X seconds of inactivity
	 * 
	 *	@param int $time
	 *	@return this
	 */
	function setAutosubmit( $time = NULL )
	{
		$this->_auto_submit = true;
		
		if( $time !== NULL && is_numeric($time) )
		{
			$this->_auto_submit_time = $time;
		}
		
		return $this;
	}
	
	
	/**
	 *	Sets all input type='file' parameters
	 * 
	 *	@param bool [optional] $async_request_name Name of file upload async request
	 *	@param array [optional] $extensions Allowed extensions
	 *	@param float [optional] $max_filesize_single_mb Maximal filesize of one file
	 *	@param float [optional] $max_filesize_sum_mb Maximal sum of all filesizes
	 * 
	 *	@return Art_Model_Ajax_Request
	 */
	function setAsyncFileUpload( $async_request_name, $extensions = self::FILE_DEFAULT_EXTENSIONS, $max_filesize_single_mb = self::FILE_DEFAULT_SIZE_SINGLE, $max_filesize_sum_mb = self::FILE_DEFAULT_SIZE_SUM )
	{
		$this->_file_async = true;
		$this->_file_request_name = $async_request_name;
		$this->_file_max_size_single = $max_filesize_single_mb;
		$this->_file_max_size_sum = $max_filesize_sum_mb;
		$this->_file_extensions = $extensions;
		
		return $this;
	}
	
	
	/**
	 *	Return array with all instance values
	 *	
	 *	@param array [optional] $variables
	 *	@return array
	 */
	function getData($variables = array())
	{		
		$buff = array('action'=>$this->_action,'confirmText'=>$this->_confirmText);
		
		$this->setVariables($variables);
		
		$output = array('action'=>$this->_action,'update'=>$this->_update,'name'=>$this->_name);
		if($this->_confirmText)
		{
			$output['confirm'] = $this->_confirmText; 
		}
		
		$this->_action = $buff['action'];
		$this->_confirmText = $buff['confirmText'];
		
		if( NULL !== $this->_redirect )
		{
			$output['redirect'] = $this->_redirect;
		}
		
		if( $this->_auto_submit )
		{
			$output['auto_submit'] = true;
			$output['auto_submit_time'] = $this->_auto_submit_time;
		}
		
		if( $this->_file_async )
		{
			$output['file_async'] = $this->_file_async;
			$output['file_request_name'] = $this->_file_request_name;
			$output['file_extensions'] = $this->_file_extensions;
			$output['file_max_size_single'] = $this->_file_max_size_single;
			$output['file_max_size_sum'] = $this->_file_max_size_sum;
		}
		
		return $output;
	}
	
	
	/**
	 *	Get JSON of all instance values
	 * 
	 *	@param array [optional] $variables
	 *	@return string
	 */
	function getJSON($variables = array())
	{
		$data = $this->getData($variables);
		return json_encode(array_filter($data));
	}
	
	
	/**
	 *	Get complete request HTML
	 * 
	 *	@param array [optional] $variables
	 *	@return string
	 */
	function getHTML($variables = array())
	{
		return ' data-method="ajax" data-params=\''.$this->getJSON($variables).'\'';
	}
	
	
	/**
	 *	Add rule to update element when event occures
	 * 
	 *	@param string $var_name Name of variable where HTML is stored
	 *	@param string $element Element selector
	 *	@param bool $animate If true, change will be animated
	 *	@param bool $only_content True to update only content of element, false to replace
	 *	@return this
	 */
	function addUpdate($var_name,$element,$animate = false,$only_content = false)
	{
		$this->_update[] = array('from'=>$var_name,'element'=>$element,'animate'=>$animate,'only_content'=>$only_content);
		
		return $this;
	}
	
	
	/**
	 *	Find variables in string
	 * 
	 *	@param string $string
	 *	@param bool $is_url
	 *	@return Art_Model_Ajax_Request
	 */
	protected function _findVariables($string,$is_url = false)
	{
		$variables = array();
		
		if( $is_url )
		{
			preg_match_all('/\$(.+?)(\/|$)/', $string, $variables);
		}
		else
		{
			preg_match_all('/\{\$(.+?)\}/', $string, $variables);
		}
		
		
		if( count($variables[1]) )
		{
			$this->_variables = array_merge($this->_variables,$variables[1]);
		}
		
		return $this;
	}
}