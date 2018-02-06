<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/article/public
 */
class Module_Article extends Art_Abstract_Module {
	
	function embeddAction()
	{
		$id = $this->getParams('id');
		
		if( NULL !== $id )
		{
			$article = new Article($id);
			if( $article->isPrivileged() )
			{
				$this->view->article = $article;
			}
			else
			{
				$this->view->article = '';
			}
		}
	}
	
	
	function indexAction() 
	{
		$id = Art_Router::getId();

		if( NULL !== $id )
		{
			$article = new Article($id);
			if( $article->isPrivileged() )
			{
				$this->view->article = $article;
			}
			else
			{
				$this->view->article = '';
			}
		}
		
		Art_Template::setMeta($article->getMeta());
	}
}