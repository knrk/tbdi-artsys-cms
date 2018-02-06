<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/article/admin
 */
class Module_Article extends Art_Abstract_Module {
	
	const REQUEST_DELETE_SELECTED	= 'p69TLSaefi';
	const REQUEST_DELETE_SINGLE		= 'vIgdNsunh5';
	const REQUEST_PUBLISH			= 'aqUej7UxV7';
	const REQUEST_UNPUBLISH			= 'r9wiQUnHB0';
	const REQUEST_ACTIVE_TOGGLE		= 'g2tFrV0ZdT';
	const REQUEST_MOVE_UP			= '0AQ5AGnZYM';
	const REQUEST_MOVE_DOWN			= 'WqNcsB2RSj';
	const REQUEST_NEW				= 'V41uvTDob5';
	const REQUEST_EDIT				= 'iafZ94i8Js';
	
	const CHECKBOXES_PREFIX			= 'article_';
	
	static function getNodeItemsList($type = NULL) 
	{
		switch($type){
			case 'index':
			{
				$items = Article::fetchAllPrivileged();
				break;
			}
			default:
			{
				$items = array();
			}
		}
		
		$output = array();
		
		/* @var $item Article */
		foreach( $items AS $item )
		{
			$output[] = array('id' => $item->id, 'name' => $item->title, 'description' => $item->perex);
		}
		
		return $output;
	}
	
	function indexAction() 
	{		
		$articles = Article::fetchAllPrivileged(NULL,array('id_article_category','sort'));
		$this->view->sort_buttons = Art_Model::sortButtons($articles, 'id_article_category');
		$this->view->articles = $articles;

		//Delete by selected checkboxes
		$delete_selected_request = Art_Ajax::newRequest(self::REQUEST_DELETE_SELECTED);
		$delete_selected_request->setAction('/'.Art_Router::getLayer().'/article/deleteSelected');
		$delete_selected_request->addUpdate('content', '.module_article_index');
		$delete_selected_request->setConfirmWindow(__('module_article_delete_selected_confirm'));
		$this->view->delete_selected_request = $delete_selected_request;

		//Publish by selected checkboxes
		$publish_request = Art_Ajax::newRequest(self::REQUEST_PUBLISH);
		$publish_request->setAction('/'.Art_Router::getLayer().'/article/publish');
		$publish_request->addUpdate('content', '.module_article_index');
		$this->view->publish_request = $publish_request;
		
		//Unpublish by selected checkboxes
		$unpublish_request = Art_Ajax::newRequest(self::REQUEST_UNPUBLISH);
		$unpublish_request->setAction('/'.Art_Router::getLayer().'/article/unpublish');
		$unpublish_request->addUpdate('content', '.module_article_index');
		$this->view->unpublish_request = $unpublish_request;
		
		//Delete item by button
		$delete_single_request = Art_Ajax::newRequest(self::REQUEST_DELETE_SINGLE);
		$delete_single_request->setAction('/'.Art_Router::getLayer().'/article/deleteSingle/$id');
		$delete_single_request->addUpdate('content','.module_article_index');
		$delete_single_request->setConfirmWindow(__('module_article_delete_single_confirm'));
		$this->view->delete_single_request = $delete_single_request;
		
		//Switch active status
		$active_request = Art_Ajax::newRequest(self::REQUEST_ACTIVE_TOGGLE);
		$active_request->setAction('/'.Art_Router::getLayer().'/article/toggleActive/$id');
		$active_request->addUpdate('content','.module_article_index');
		$this->view->active_request = $active_request;
		
		//Move article up
		$move_up_request = Art_Ajax::newRequest(self::REQUEST_MOVE_UP);
		$move_up_request->setAction('/'.Art_Router::getLayer().'/article/moveUp/$id');
		$move_up_request->addUpdate('content','.module_article_index');
		$this->view->move_up_request = $move_up_request;
		
		//Mode article down
		$move_down_request = Art_Ajax::newRequest(self::REQUEST_MOVE_DOWN);
		$move_down_request->setAction('/'.Art_Router::getLayer().'/article/moveDown/$id');
		$move_down_request->addUpdate('content','.module_article_index');
		$this->view->move_down_request = $move_down_request;
	}
	
	function moveUpAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_MOVE_UP) )
		{
			$this->_moveByDirection('up');
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function moveDownAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_MOVE_DOWN) )
		{
			$this->_moveByDirection('down');
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function deleteSingleAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_DELETE_SINGLE) )
		{
			$response = Art_Ajax::newResponse();
			$article = new Article(Art_Router::getId());
			if( $article->isLoaded() )
			{
				if( !$article->isPrivileged() )
				{
					$this->allowTo(Art_User::NO_ACCESS);
				}

				$article->delete();				
			}
			else
			{
				$response->addMessage(__('module_article_deleted_not_found'));
			}
			
			$response->addVariable('content', Art_Module::createAndRenderModule('article'));
			$response->execute();
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function toggleActiveAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ACTIVE_TOGGLE) )
		{
			$response = Art_Ajax::newResponse();
			$article = new Article(Art_Router::getId());
			if( $article->isLoaded() )
			{
				if( !$article->isPrivileged() )
				{
					$this->allowTo(Art_User::NO_ACCESS);
				}

				$article->active = $article->active ? 0 : 1;
				$article->save();
			}
			else
			{
				$response->addMessage(__('module_article_not_found'));
			}
			
			$response->addVariable('content', Art_Module::createAndRenderModule('article'));
			$response->execute();
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function deleteSelectedAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_DELETE_SELECTED))
		{
			$response = Art_Ajax::newResponse();
			
			//Get Ids
			$ids = $this->_getIdsFromCheckboxes();

			//Delete articles if user is privilegged for them
			foreach($ids AS $id)
			{
				$article = new Article($id);
				if( $article->isLoaded() && $article->isPrivileged() )
				{
					$article->delete();
				}
			}
			
			if(count($ids))
			{
				$response->addVariable('content', Art_Module::createAndRenderModule('article'));
			}

			$response->execute();
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function newAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_NEW))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			//For TBD ONLY !!!
			$data['id_article_category'] = 1;
			$data['active'] = 1;
			
			//Set each field validation options
			$fields = Article::getCols('insert');
			
			$sql_data = Helper_Default::getValidatedSQLData($fields, $this->_getFieldsValidators(), $data, $response);
			
			//Convert active to bool int
			$sql_data['active'] = Art_Filter::toBoolInt($sql_data['active']);
			
			//Get sort
			$sql_data['sort'] = Article::getNextSort($sql_data['id_article_category']);
			
			//Create url name if not set
			if( empty($data['url_name']) )
			{
				//Generate URL name
				$url_name = Art_Filter::urlName($sql_data['title']);
				
				//It must be unique - load other similar url names
				$where_stmt = new Art_Model_Db_Where(array(array('name' => 'url_name', 'value' => $url_name.'%', 'relation' => Art_Model_Db_Where::REL_LIKE)));
				$articles = Article::fetchAll($where_stmt);
				
				$other_url_names = array();
				foreach($articles AS $article)
				{
					$other_url_names[] = $article->url_name;
				}
				
				//Compare and create unique url name
				$url_name = Art_Filter::makeUnique($url_name,$other_url_names);
				
				$sql_data['url_name'] = $url_name;	
			}
			else
			{
				$url_name = $data['url_name'];
				
				$where_stmt = new Art_Model_Db_Where();
				$where_stmt->addField('url_name', $url_name);
				$articles = Article::fetchAll($where_stmt);
				
				if( count($articles) )
				{
					$response->addAlert(__('module_article_url_used_already'));
				}
				else
				{
					$sql_data['url_name'] = $url_name;	
				}
			}
			
			//Everything is valid
			if( $response->isValid() )
			{
				$article = new Article;
				$article->setDataFromArray($sql_data);
				$article->id_article_category = 1;
				$article->rights = 2;
				$article->active = 1;
				$article->save();
				
				$response->addMessage(sprintf(__('module_article_added_success'),$sql_data['title']));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$this->view->categories = Article_Category::fetchAllPrivileged();
			$this->view->rights = Art_Model_Rights::fetchAllNotHigher();

			$request = Art_Ajax::newRequest(self::REQUEST_NEW);
			$request->setRedirect('/'.Art_Router::getLayer().'/article');
			$this->view->request = $request;
		}
	}
	
	function publishAction()
	{
		$ids = $this->_getIdsFromCheckboxes();
		$response = Art_Ajax::newResponse();
		
		foreach($ids AS $id)
		{
			$article = new Article($id);
			if( $article->isLoaded() && $article->isPrivileged() )
			{
				$article->active = 1;
				$article->save();
			}
		}
		
		if( count($ids) )
		{
			$response->addVariable('content', Art_Module::createAndRenderModule('article'));
		}
		$response->execute();
	}
	
	function unpublishAction()
	{
		$ids = $this->_getIdsFromCheckboxes();
		$response = Art_Ajax::newResponse();
		
		foreach($ids AS $id)
		{
			$article = new Article($id);
			if( $article->isLoaded() && $article->isPrivileged() )
			{
				$article->active = 0;
				$article->save();
			}
		}
		
		if( count($ids) )
		{
			$response->addVariable('content', Art_Module::createAndRenderModule('article'));
		}
		$response->execute();
	}
	
	function editAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			//For TBD ONLY !!!
			$data['id_article_category'] = 1;
			$data['active'] = 1;
			
			//Set each field validation options
			$fields = Article::getCols('update');
			
			$article = new Article(Art_Router::getId());		
			if ( !$article->isLoaded() )
			{
				$response->addMessage(__('module_article_deleted_when_edit'), Art_Main::ALERT);
			}
			elseif( !$article->isPrivileged() )
			{
				$response->addMessage(__('module_article_no_rights_when_edit'), Art_Main::ALERT);
			}
			
			$sql_data = Helper_Default::getValidatedSQLData($fields, $this->_getFieldsValidators(), $data, $response);
			
			//Convert active to bool int
			$sql_data['active'] = Art_Filter::toBoolInt($sql_data['active']);
			
			//Recreate sort if category changed
			if( $article->id_article_category != $sql_data['id_article_category'])
			{
				$sql_data['sort'] = Article::getNextSort($sql_data['id_article_category']);
			}
			
			//Create url name if not set
			if( empty($data['url_name']) )
			{
				//Generate URL name
				$url_name = Art_Filter::urlName($sql_data['title']);
				
				//It must be unique - load other similar url names
				$where_stmt = new Art_Model_Db_Where(array(array('name' => 'url_name', 'value' => $url_name.'%', 'relation' => Art_Model_Db_Where::REL_LIKE)));
				$where_stmt->add(array('name' => 'id', 'relation' => Art_Model_Db_Where::REL_NOT_EQUALS, 'value' => $article->id));
				$articles = Article::fetchAll($where_stmt);
				
				$other_url_names = array();
				foreach($articles AS $article)
				{
					$other_url_names[] = $article->url_name;
				}
				
				//Compare and create unique url name
				$url_name = Art_Filter::makeUnique($url_name,$other_url_names);
				
				$sql_data['url_name'] = $url_name;	
			}
			else
			{
				$url_name = $data['url_name'];
				
				$where_stmt = new Art_Model_Db_Where();
				$where_stmt->addField('url_name', $url_name);
				$where_stmt->add(array('name' => 'id', 'relation' => Art_Model_Db_Where::REL_NOT_EQUALS, 'value' => $article->id));
				$articles = Article::fetchAll($where_stmt);
				
				if( count($articles) )
				{
					$response->addAlert(__('module_article_url_used_already'));
				}
				else
				{
					$sql_data['url_name'] = $url_name;	
				}
			}
			
			//Everything is valid
			if( $response->isValid() )
			{
				$article->setDataFromArray($sql_data);
				$article->save();
				
				$response->addMessage(sprintf(__('module_article_edited_success'),$sql_data['title']));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$this->view->categories = Article_Category::fetchAllPrivileged();
			$this->view->rights = Art_Model_Rights::fetchAllNotHigher();

			$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
			$request->setRedirect('/'.Art_Router::getLayer().'/article');
			$this->view->request = $request;

			$article = new Article(Art_Router::getId());
			if( !$article->isLoaded() )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			elseif( !$article->isPrivileged() )
			{
				$this->allowTo(Art_User::NO_ACCESS);
			}
			
			$this->view->article = $article;
		}
	}
	
	
	/**
	 *	Load AJAX data and get ids from checkboxes
	 * 
	 *	@access protected
	 *	@return array
	 */
	protected function _getIdsFromCheckboxes()
	{
		$data = Art_Ajax::getData();
		$prefix_length = strlen(self::CHECKBOXES_PREFIX);
		
		$ids = array();
		foreach($data AS $item => $st)
		{
			if( strpos($item,self::CHECKBOXES_PREFIX) === 0 )
			{
				$id = substr($item,$prefix_length);
				if( Art_Validator::validate($id, Art_Validator::IS_INTEGER) )
				{
					$ids[] = $id;
				}
			}
		}		
		
		return $ids;
	}
	
	//Move article up or down in its category
	protected function _moveByDirection( $dir )
	{
		$response = Art_Ajax::newResponse();

		//Load article
		$article_1 = new Article(Art_Router::getId());

		if( $article_1->isLoaded() && $article_1->isPrivileged() )
		{
			switch( strtolower($dir) )
			{
				case 'up':
					$article_2 = $article_1->getUpper();
					break;
				case 'down':
					$article_2 = $article_1->getDowner();
					break;
			}

			if( $article_2->isLoaded() && $article_2->isPrivileged() )
			{
				Article::swapPositions($article_1, $article_2);
			}
		}

		$response->addVariable('content', Art_Module::createAndRenderModule('article'));
		$response->execute();
	}
	
	
	protected function _getFieldsValidators()
	{
		return	array(
					'title'				=> array(
						Art_Validator::MIN_LENGTH => ['value' => 3,'message' => __('module_article_invalid_name_short')],  
						Art_Validator::MAX_LENGTH => ['value' => 100,'message' => __('module_article_invalid_name_long')]),
					'perex'				=> array(
						Art_Validator::MAX_LENGTH => ['value' => 500,'message' => __('module_article_invalid_perex_long')]),
					'url_name'			=> array(
						Art_Validator::MAX_LENGTH => ['value' => 100,'message' => __('module_article_invalid_url_long')]),
					'id_article_category' => array(
						Art_Validator::IS_INTEGER => ['message' => __('module_article_invalid_bad_cat')],
						Art_Validator::MIN_VALUE => ['value' => 1,'message' => __('module_article_invalid_bad_cat')]),
					'content'			=> array(
						Art_Validator::MIN_LENGTH => ['value' => 10,'message' => __('module_article_invalid_content_short')]),
					'meta_title'		=> array(
						Art_Validator::MAX_LENGTH => ['value' => 70,'message' => __('module_article_invalid_meta_title_long')]),
					'meta_keywords'		=> array(
						Art_Validator::MAX_LENGTH => ['value' => 100,'message' => __('module_article_invalid_meta_kw_long')]),
					'meta_description'	=> array(
						Art_Validator::MAX_LENGTH => ['value' => 300,'message' => __('module_article_invalid_meta_desc')]));
	}
}