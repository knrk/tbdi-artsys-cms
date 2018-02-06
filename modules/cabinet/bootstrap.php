<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/cabinet
 */
class Module_Bootstrap_Cabinet extends Art_Abstract_Module_Bootstrap {
	
	static function initialize()
	{
		Art_Event::on(Art_Event::ROUTER_SETUP,function(){
			static::routerSetup();
		});
	}
	
	static function routerSetup()
	{
		Art_Router::addRoute('route_cabinet-mainpage', '/', ['layer'=>'cabinet','section'=>'cabinet'], 'club.tbdevelopment.cz');
		Art_Router::addRoute('route_cabinet', '/cabinet', ['layer'=>'cabinet','section'=>'cabinet'], 'club.tbdevelopment.cz');
		Art_Router::addRoute('route_cabinet-section', '/cabinet/$1', ['layer'=>'cabinet','section'=>'$1'], 'club.tbdevelopment.cz');
		Art_Router::addRoute('route_cabinet_section_action', '/cabinet/$1/$2', ['layer'=>'cabinet','section'=>'$1','action'=>'$2'], 'club.tbdevelopment.cz');
		Art_Router::addRoute('route_cabinet_section_action_id', '/cabinet/$1/$2/$3', ['layer'=>'cabinet','section'=>'$1','action'=>'$2','id'=>'$3'], 'club.tbdevelopment.cz');
	}

}