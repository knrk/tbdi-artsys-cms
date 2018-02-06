<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/dashboard/admin
 */
class Module_Dashboard extends Art_Abstract_Module {
	
	const REQUEST_NEW	= 'kzweMJSLXS';
	const REQUEST_EDIT	= 'mgJVheBrYL';
	const REQUEST_DELETE_SINGLE	= 'aTPqkFKvTB';
	
	function indexAction() 
	{		
		$dashboard = Dashboard::fetchAllPrivileged();
		
		foreach ($dashboard as $key => $value) /* @var $value Dashboard */ 
		{
			$dashboard[$key]->a_detail = '/'.Art_Router::getLayer().'/dashboard/detail/'.$value->id;
			$dashboard[$key]->a_edit = '/'.Art_Router::getLayer().'/dashboard/edit/'.$value->id;
		}
		
		$this->view->dashboard = $dashboard;
		
		//Delete item by button
		$delete_single_request = Art_Ajax::newRequest(self::REQUEST_DELETE_SINGLE); 
		$delete_single_request->setAction('/'.Art_Router::getLayer().'/dashboard/deleteSingle/$id'); 
		$delete_single_request->addUpdate('content','.module_dashboard_index'); 
		$delete_single_request->setConfirmWindow(__('module_dashboard_delete_single_confirm')); 
		$this->view->delete_single_request = $delete_single_request;
	}
	
	function newAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_NEW) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();

			$fields = Dashboard::getCols('insert');
			
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);		
			
			if ( $response->isValid() )
			{
				$post = new Dashboard();
				$post->setDataFromArray($sql_data);
				$post->important = (Helper_Default::isPropertyChecked($data,'important')) ? 1 : 0;
				$post->save();
				
				$response->addMessage(__('module_dashboard_new_success'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			//Add new post on dashboard
			$request = Art_Ajax::newRequest(self::REQUEST_NEW);
			$request->setRedirect('/'.Art_Router::getLayer().'/dashboard');
			$this->view->request = $request;
		}
	}
	
	function detailAction()
	{
		$post = new Dashboard(Art_Router::getId());
		
		if ( $post->isLoaded() )
		{
			$this->view->post = $post;
		}
		else
		{			
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function editAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();

			$fields = Dashboard::getCols('update');
			
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);		
			
			//Everything is valid
			if( $response->isValid() )
			{
				$post = new Dashboard(Art_Router::getId());
				
				if ( $post->isLoaded() )
				{
					$post->setDataFromArray($sql_data);
					$post->important = (Helper_Default::isPropertyChecked($data,'important')) ? 1 : 0;
					$post->save();

					$response->addMessage(__('module_dashboard_edit_success'));
					$response->willRedirect();
				}
				else
				{
					$response->addMessage(__('module_dashboard_edit_not_found'));
				}
			}
			
			$response->execute();
		}
		else
		{
			$post = new Dashboard(Art_Router::getId());
		
			if ( $post->isLoaded() )
			{
				$this->view->post = $post;
				
				$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
				$request->setRedirect('/'.Art_Router::getLayer().'/dashboard');
				$this->view->request = $request;
			}
			else
			{			
				$this->showTo(Art_User::NO_ACCESS);
			}
		}
	}

	function deleteSingleAction()
	{
  		if( Art_Ajax::isRequestedBy(self::REQUEST_DELETE_SINGLE) )
  		{
  			$response = Art_Ajax::newResponse();			
  
			$dashboard = new Dashboard(Art_Router::getId());
 
			if( $dashboard->isLoaded() )
  			{
  				if( !$dashboard->isPrivileged() )
  				{
  					$this->allowTo(Art_User::NO_ACCESS);
  				}

				$dashboard->delete();		
  
				$response->addMessage(__('module_dashboard_delete_success'));
			}
  			else
 			{
				$response->addAlert(__('module_dashboard_delete_not_found'));
  			}
 
			$response->addVariable('content', Art_Module::createAndRenderModule('dashboard'));

			$response->execute();
  		}
  		else
  		{
  			$this->showTo(Art_User::NO_ACCESS);
  		}
  	}
		
	function embeddAction() 
	{			
		$limit = Helper_Default::getDefaultValue('dashboard-max-results-embedd', Helper_TBDev::MAX_DASHBOARD_RESULTS_EMBEDD);
		
		$dashboard = Dashboard::fetchAllPrivileged(NULL,array('name'=>'id','type'=>Art_Model_Db_Order::TYPE_DESC), $limit);
		
		$this->view->dashboard = $dashboard;
	}
	
	static function getFieldsValidators()
	{
		return	array(
	'header'		=> array( 
		Art_Validator::MAX_LENGTH => ['value' => 60,'message' => __('module_dashboard_v_header_max')]),
	'body'			=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message' => __('module_dashboard_v_body_min')],
		Art_Validator::MAX_LENGTH => ['value' => 4500,'message' => __('module_dashboard_v_body_max')]),
			);		
	}
}