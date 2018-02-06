<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/label/admin
 */
class Module_Label extends Art_Abstract_Module {

	const REQUEST_DELETE	= 'qwQtEQr6nF';
	const REQUEST_ADD		= 'ayBn4hynEF';
	const REQUEST_EDIT		= 'bOneStd9Gx';
	
	function indexAction() 
	{		
		$this->view->languages = array_intersect(Art_Main::getLocales(), Art_Model_Label::getCols('select'));
		$this->view->labels = Art_Model_Label::fetchAllOrdered();

		$delete_request = Art_Ajax::newRequest(self::REQUEST_DELETE);
		$delete_request->setAction('/'.Art_Router::getLayer().'/label/delete');
		$delete_request->addUpdate('content', '.module_label_index');
		$delete_request->setConfirmWindow(__('module_label_q_delete'));
		$this->view->delete_request = $delete_request;
	}
		
	function newAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ADD) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			//Set each field validation options
			$fields = Art_Model_Label::getCols('insert');
			
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);		
					
			//Everything is valid
			if( $response->isValid() )
			{
				$label = new Art_Model_Label(array("key" => $sql_data['key']));
				if ( $label->isLoaded() )
				{
					$response->addAlert(sprintf(__('module_label_added_alert'), $sql_data['key']));
				}
				else
				{	
					$label = new Art_Model_Label();
					$label->setDataFromArray($sql_data);
					$label->save();
					
					$response->addMessage(sprintf(__('module_label_added'), $sql_data['key']));
					$response->willRedirect();
				}
			}
			
			$response->execute();
		}
		else
		{
			$this->view->languages = array_intersect(Art_Main::getLocales(), Art_Model_Label::getCols('select'));
			
			$request = Art_Ajax::newRequest(self::REQUEST_ADD);
			$request->setRedirect('/'.Art_Router::getLayer().'/label');
			$this->view->request = $request; 
		}
	}
	
	
	function deleteAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_DELETE))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			//Get Ids
			$item_prefix = 'label_';
			$item_prefix_length = strlen($item_prefix);
			
			$ids = array();
			
			foreach($data AS $item => $st)
			{
				if( strpos($item,$item_prefix) === 0 )
				{
					$id = substr($item,$item_prefix_length);
					if( Art_Validator::validate($id, Art_Validator::IS_INTEGER) )
					{
						$ids[] = $id;
					}
				}
			}

			//Delete labels
			foreach($ids AS $id)
			{
				$label = new Art_Model_Label($id);
				if( $label->isLoaded() )
				{
					$label->delete();
				}
			}
			
			switch(count($ids))
			{
				case 0:
					$response->addAlert(__('module_label_deleted_none'));
					break;
				case 1:
					$response->addMessage(__('module_label_deleted'));
					$response->addVariable('content', Art_Module::createAndRenderModule('label'));
					break;
				default :
					$response->addMessage(sprintf(__('module_label_deleted_more'), count($ids)));
					$response->addVariable('content', Art_Module::createAndRenderModule('label'));
			}
			
			$response->execute();
		}
	}
	
	
	function editAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();

			$meta = new Art_Model_Label(Art_Router::getId());	
			
			if ( !$meta->isLoaded() )
			{
				$response->addAlert(__('module_label_not_found'));
			}
			
			$fields = Art_Model_Label::getCols('update');
			
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
			
			//Everything is valid
			if( $response->isValid() )
			{
				$meta->setDataFromArray($sql_data);
				$meta->save();
				
				$response->addMessage(__('module_label_added'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$this->view->languages = array_intersect(Art_Main::getLocales(), Art_Model_Label::getCols('select'));
			
			$label = new Art_Model_Label(Art_Router::getId());
			
			if( !$label->isLoaded() )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			else if( !$label->isPrivileged() )
			{
				$this->allowTo(Art_User::NO_ACCESS);
			}
			
			$this->view->label = $label;
			
			$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
			$request->setRedirect('/'.Art_Router::getLayer().'/label');
			$this->view->request = $request;
		}
	}
	
	
	static function getFieldsValidators()
	{
		return	array(
	'key'				=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message' => __('module_label_v_key_min')],  
		Art_Validator::MAX_LENGTH => ['value' => 60,'message' => __('module_label_v_key_max')]));
	}
}
