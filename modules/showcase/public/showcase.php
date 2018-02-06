<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/article/public
 */
class Module_Showcase extends Art_Abstract_Module {
		
	function indexAction()
	{
		
	}
	
	function onasAction()
	{
		
	}
	
	function kontaktAction()
	{
		/*if( Art_Ajax::isRequestedBy(self::REQUEST_SEND) )
		{
			
			
			$data['name'];
			$data['email'];
			$data['body'];
		}*/
	}
	
	function sluzbyAction()
	{
		if( !empty(Art_Router::getId()) )
		{
			$this->setView('sluzby-'.Art_Router::getId());
		}
		else
		{
			$this->setView('sluzby-reklamni-cinnost');
		}
	}
	
	
}