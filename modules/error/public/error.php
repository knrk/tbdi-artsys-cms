<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/error/public
 */
class Module_Error extends Art_Abstract_Module {
	
	function noAccessAction()
	{
		if ( Art_User::isLogged() )
		{
			Art_Template::setTemplate('index', 'clubTemplate');
		}
		else
		{
			Art_Template::setTemplate('index', 'loginTemplate');
			Art_Template::setTitle(__('login_title'));
		}
		
		$this->view->isActive = Art_User::getCurrentUser()->isActive();
		$this->view->membershipEndDate = nice_date(Art_User::getCurrentUser()->modified_date);
	}
	
	function notFoundAction()
	{
		
	}
}