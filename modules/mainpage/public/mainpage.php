<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/mainpage/public
 */
class Module_Mainpage extends Art_Abstract_Module {
	
	function indexAction()
    {
		$article = new Article(1);		
		$this->view->article = $article;
    }
}