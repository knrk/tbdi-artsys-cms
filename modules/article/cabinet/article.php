<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/article/public
 */
class Module_Article extends Art_Abstract_Module {
	
	function embeddAction()
	{
		$url_name = $this->getParams('url_name');
		
		if( NULL !== $url_name )
		{
			$article = new Article(array('url_name'=>$url_name));
			if( $article->isPrivilegedActive() )
			{
				$this->view->article = $article;
			}
			else
			{
				$this->view->article = null;
			}
		}
		
/*		$id = $this->getParams('id');
		
		if( NULL !== $id )
		{
			$article = new Article($id);
			if( $article->isLoaded() && $article->isPrivilegedActive() )
			{
				$this->view->article = $article;
			}
			else
			{
				$this->view->article = '';
			}
		}*/
	}
	
	
	function indexAction() 
	{
		$id = Art_Router::getId();

		if( NULL !== $id )
		{
			$article = new Article($id);
			if( $article->isLoaded() && $article->isPrivilegedActive() )
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