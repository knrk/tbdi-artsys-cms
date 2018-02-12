<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/service
 */
class Module_Bootstrap_Showcase extends Art_Abstract_Module_Bootstrap {
	static function initialize() {
		Art_Event::on(Art_Event::ROUTER_SETUP,function(){		
			Art_Router::addRoute(
					'showcase-mainpage', 
					'/', 
					array ('layer'=> Art_Router::LAYER_FRONTEND, 'section' => 'showcase', 'action' => 'index'), 
					DEFAULT_DOMAIN);
			
			Art_Router::addRoute(
					'showcase-mainpage-section', 
					'/$1', 
					array ('layer'=> Art_Router::LAYER_FRONTEND, 'section' => 'showcase', 'action' => '$1'), 
					DEFAULT_DOMAIN);
			
			Art_Router::addRoute(
					'showcase-mainpage-section-id', 
					'/$1/$2', 
					array ('layer'=> Art_Router::LAYER_FRONTEND, 'section' => 'showcase', 'action'=>'$1', 'id' => '$2'), 
					DEFAULT_DOMAIN);
		});
	}
}