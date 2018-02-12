<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Route {
	
	/**
	 * Route name
	 * @var string
	 */
	protected $_name = array();
	
	/**
	 *	@var array URL mask
	 *	@example array('product','$1');
	 */
	protected $_url_mask = array();
	
	/**
	 *	@var array Output mask
	 *	@example array('section'=>'$1','action'=>'product') 
	 */
	protected $_output_mask = array();
	
	/**
	 *	@var string Domain of this rout
	 */
	protected $_domain;
	
	const ALL = '*';
	const VARIABLE = '$';
	
	
	/**
	 *	Create new route
	 *	
	 *	@param string Route name
	 *	@param string|array URL mask
	 *	@param array Output mask
	 *	@param string|int [optional] $domain
	 *	@return this
	 *	@example new Art_Model_Route('/product/&1',array('section'=>'product','action'=>'view','id'=>'$1'), 'itart.cz')
	 */
	function __construct($name, $url_mask, array $output_mask, $domain = NULL)
	{
		$this->_name = $name;
		
		//Input validation
		if( is_string($url_mask) )
		{			
			//Trim slashes
			$url_mask = trim($url_mask,'/');
			
			//Explode mask to array
			$this->_url_mask = explode("/",$url_mask);
			
		}
		elseif( is_array($url_mask) )
		{
			//Use only values
			$this->_url_mask = array_values($url_mask);
		}
		else
		{
			trigger_error('Invalid argument supplied for constructor of '.get_called_class(),E_USER_ERROR);
		}
		
		$this->_output_mask = $output_mask;
		
		//If is integer
		if ($domain === (int) $domain)
		{
			$domains = Art_Main::getDomains();
			$domain = (int) $domain;
			if (isset($domains[$domain])) {
				$domain = $domains[$domain];
			} else {
				$domain = NULL;
			}
		}
		
		$this->_domain = $domain;
	}
	
	
	/**
	 *	Get URL mask of this route
	 * 
	 *	@return array
	 */
	function getURLMask()
	{
		return $this->_url_mask;
	}
	
	
	/**
	 *	Get output mask of this route
	 * 
	 *	@return array
	 */
	function getOutputMask()
	{
		return $this->_output_mask;
	}
	
	
	/**
	 *	Get route name
	 * 
	 *	@return string
	 */
	function getName()
	{
		return $this->_name;
	}
	
	
	/**
	 *	Get route domain
	 * 
	 *	@return string
	 */
	function getDomain()
	{
		return $this->_domain;
	}
	
	/**
	 *	Create URL from route
	 * 
	 *	@param array $params [optional] 
	 *	@param string $locale [optional]
	 *	@param bool $full_protocol [optional]
	 *	@return string
	 */
	function createURL( array $params = array(), $locale = NULL, $full_protocol = false )
	{
		$url_parts = $this->_url_mask;
		
		foreach($url_parts AS &$url_part)
		{
			if( !empty($url_part) && $url_part[0] == self::VARIABLE )
			{
				if( isset($params[$url_part]) )
				{
					$url_part = $params[$url_part];
				}
				else
				{
					trigger_error('Param '.$url_part.' not set when creating URL from '.$this->_name, E_USER_NOTICE);
					$url_part = '?????';
				}				
			}
		}
		
		//If route points to another domain
		if( !empty($this->_domain) && Art_Server::getDomain() !== $this->_domain )
		{
			if( $full_protocol )
			{
				$domain = Art_Server::getServerProtocol().'://'.$this->_domain;
			}
			else
			{
				$domain = '//'.$this->_domain;
			}
		}
		else
		{
			$domain = '';
		}
		
		//If locale is not set
		if( NULL === $locale )
		{
			$locale = Art_Main::getLocale();
		}
		
		//If locale equals to default locale
		if( Art_Main::getDefaultLocale() == $locale )
		{
			$locale = '';
		}
		else
		{
			$locale = '/'.$locale;
		}
		
		$url = $domain.$locale.'/'.implode('/', $url_parts);
		
		return $url;
	}
	
	
	/**
	 *	Match this route to target URL mask
	 * 
	 *	@param string|array Target URL mask
	 *	@return bool|array False if no match
	 *	@example match('product/new-car') Matches this route to product/new-car
	 */
	function match($target_mask, $target_domain = NULL)
	{
		//If domains do not match
		if( NULL !== $this->_domain && $this->_domain != $target_domain )
		{
			return NULL;
		}
		
		//Input validation
		if( is_string($target_mask) )
		{
			$target_mask = trim($target_mask);
			$target_mask = explode('/',$target_mask);
		}

		//Input validation
		if( is_array($target_mask) )
		{
			//Get only mask values
			$target_mask = array_values($target_mask);
			
			//Get length of longer array
			$length = count($target_mask) > count($this->_url_mask) ? count($target_mask) : count($this->_url_mask);
			
			$match = false;
			
			//Array of variables and it's values array('$1'=>'product-name')
			$variables = array();
			for($i = 0; $i < $length ; $i++)
			{
				if( isset($this->_url_mask[$i]) && isset($target_mask[$i]) )
				{
					//If MASK equals by name or *
					if( $this->_url_mask[$i] == $target_mask[$i] || $this->_url_mask[$i] == self::ALL )
					{
						$match = true;
					}
					//If MASK element is variable
					elseif( isset($this->_url_mask[$i][0]) && $this->_url_mask[$i][0] == self::VARIABLE )
					{
						$match = true;
						//Save variable and it's value
						$variables[$this->_url_mask[$i]] = $target_mask[$i];
					}
					else
					//No match
					{
						return NULL;
					}
				}
				else
				//No match
				{
					return NULL;
				}
			}

			//If match
			if( $match )
			{
				$out_vars = array();
				//For each output mask
				foreach($this->_output_mask AS $key => $value)
				{
					//If value is found in URL variables
					if( isset($variables[$value]) )
					{
						$out_vars[$key] = $variables[$value]; 
					}
					//If value is static
					else
					{
						$out_vars[$key] = $value;
					}
				}
				
				return $out_vars;
			}
			else
			{
				return NULL;
			}
		}
		else
		{
			trigger_error('Invalid argument supplied for '.get_called_class().'->match()',E_USER_ERROR);
		}
	}
}