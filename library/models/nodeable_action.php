<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Nodeable_Action {
	
	protected $_name;
	
	protected $_route_name;

	function __construct($name, $route_name)
	{
		$this->_name = $name;
		if( NULL === Art_Router::getRoute($route_name) )
		{
			trigger_error('Route '.$route_name.' not found');
		}
		else
		{
			$this->_route_name = $route_name;
		}
	}
	
	function getName()
	{
		return $this->_name;
	}
	
	function getRouteName()
	{
		return $this->_route_name;
	}
	
	function getRoute()
	{
		return Art_Router::getRoute($this->_route_name);
	}
}