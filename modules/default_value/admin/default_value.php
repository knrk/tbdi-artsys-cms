<?php
/**
 *  @author Robin Zon <zon@itart.cz>
 *  @package modules/default_value/admin
 */
class Module_Default_Value extends Art_Abstract_Module {
		
	const REQUEST_DELETE	= 'YyvQEhR45s';
	const REQUEST_ADD		= '4c7qxFWhOv';
	const REQUEST_EDIT		= 'IsX9v5NeXb';
	
	function indexAction() 
	{		
		$this->view->types = Art_Model_Default_Value::getUserReadableTypes();
		
		$this->view->items = Art_Model_Default_Value::fetchAllPrivileged();
	}
	
	function newAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ADD) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$valueHtml = isset($data['value_html']) ? $data['value_html'] : '';
			$valueText = isset($data['value_text']) ? $data['value_text'] : '';
			
			unset($data['value_html'], $data['value_text']);
			
			if( $data['type'] == Art_Model_Default_Value::TYPE_HTML )
			{
				$data['value'] = $valueHtml;
			}
			else
			{
				$data['value'] = $valueText;
				
				if (!Art_Model_Default_Value::isValueValid($data['type'], $data['value']))
				{
					$response->addField('value_text', __('module_default_value_v_wrong_value'), Art_Main::ALERT);
				}
			}
			
			//Set each field validation options
			$fields = Art_Model_Default_Value::getCols('insert');
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
						
			//Everything is valid
			if( $response->isValid() )
			{
				$item = new Art_Model_Default_Value(array("name" => $sql_data['name']));
				if ( $item->isLoaded() )
				{
					$response->addAlert(sprintf(__('module_default_value_already_exist'), $item->name));
				}
				else
				{	
					$item = new Art_Model_Default_Value();
					$item->setDataFromArray($sql_data);
					$item->save();

					$response->addMessage(sprintf(__('module_default_value_create_succ'), $item->name));
					$response->willRedirect();
				}
			}
			
			$response->execute();
		}
		else
		{
			$this->view->types = Art_Model_Default_Value::getUserReadableTypes();

			$request = Art_Ajax::newRequest(self::REQUEST_ADD);
			$request->setRedirect('/'.Art_Router::getLayer().'/default_value');
			$this->view->request = $request;
		}
	}
	
	
	function editAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$item = new Art_Model_Default_Value(Art_Router::getId());
			
			if ( !$item->isLoaded() )
			{
				$response->addAlert(__('alert_deleted_when_edit'));
			}
			
			if (!Art_Model_Default_Value::isValueValid($item->type, $data['value']))
			{
				$response->addField('value', __('module_default_value_v_wrong_value'), Art_Main::ALERT);
			}
			
			$fields = Art_Model_Default_Value::getCols('update');
			
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
			
			//Everything is valid
			if( $response->isValid() )
			{
				$item->setDataFromArray($sql_data);
				$item->save();
				
				$response->addMessage(__('module_default_value_edit_succ'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$item = new Art_Model_Default_Value(Art_Router::getId());
			
			if( !$item->isLoaded() )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			else if( !$item->isPrivileged() )
			{
				$this->allowTo(Art_User::NO_ACCESS);
			}
			
			$this->view->item = $item;
						
			$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
			$request->setRedirect('/'.Art_Router::getLayer().'/default_value');
			$this->view->request = $request;
		}
	}
	
	
	static function getFieldsValidators()
	{
		return	array(
			'name'				=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message' => __('module_default_value_v_key_min')],  
		Art_Validator::MAX_LENGTH => ['value' => 60,'message' => __('module_default_value_v_key_max')]),
			'type'				=> array(
		Art_Validator::IN_ARRAY => ['value' => Art_Model_Default_Value::getTypes(),'message' => __('module_default_value_v_wrong_type')],  
			));
	}
}