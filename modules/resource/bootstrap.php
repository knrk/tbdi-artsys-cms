<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/resource
 */
class Module_Bootstrap_Resource extends Art_Abstract_Module_Bootstrap {
	
	static function initialize()
	{
		Art_Event::on(Art_Event::ROUTER_SETUP, function(){
			static::routerSetup();
		});
	}
	
	static function routerSetup() 
	{
		Art_Router::addRoute('module_resource_initial' ,'/resource/$1', array (
			'layer' => Art_Router::LAYER_FRONTEND, 
			'section' => 'resource', 
			'action' => 'index', 
			'id' => '$1'
		));
	}
}