<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/error/public
 */
class Module_Error extends Art_Abstract_Module {
	
	function noAccessAction()
	{		
		Art_Template::setTemplate('index', 'loginTemplate');
		Art_Template::setTitle(__('login_title'));
	}
	
	function notFoundAction()
	{

	}
}