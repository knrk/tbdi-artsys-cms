

<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/meta/admin
 */
class Module_Meta extends Art_Abstract_Module {
		
	const REQUEST_DELETE	= '0hvPvtHLxR';
	const REQUEST_ADD		= 'NUWUrRF0aH';
	const REQUEST_EDIT		= 'mNDSlc0THf';
	
	const EDITABLE_META_LAYER = 'public';
	
	function indexAction() 
	{		
		$this->view->metas = Art_Model_Meta::fetchAll(array('layer' => self::EDITABLE_META_LAYER));

		$delete_request = Art_Ajax::newRequest(self::REQUEST_DELETE);
		$delete_request->setAction('/'.Art_Router::getLayer().'/meta/delete');
		$delete_request->addUpdate('content', '.module_meta_index');
		$delete_request->setConfirmWindow(__('module_meta_q_delete'));
		$this->view->delete_request = $delete_request;
	}
	
	function newAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ADD) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			//Set each field validation options
			$fields = Art_Model_Meta::getCols('insert');
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
			
			$sql_data['layer'] = self::EDITABLE_META_LAYER;
					
			//Everything is valid
			if( $response->isValid() )
			{
				$meta = new Art_Model_Meta(array("key" => $sql_data['key'], "layer" => $sql_data['layer']));
				if ( $meta->isLoaded() )
				{
					$response->addAlert(sprintf(__('module_meta_already_exist'), $sql_data['key']));
				}
				else
				{	
					$meta = new Art_Model_Meta();
					$meta->setDataFromArray($sql_data);
					$meta->save();

					$response->addMessage(sprintf(__('module_meta_create_succ'), $sql_data['key']));
					$response->willRedirect();
				}
			}
			
			$response->execute();
		}
		else
		{
			$metas = Art_Model_Meta::fetchAllPrivileged();
			$default = Art_Model_Meta::getDefaults();

			foreach( $metas as $item )
			{
				if( $item->layer == Art_Router::LAYER_FRONTEND )
				{
					unset($default[$item->key]);
				}
			}

			$this->view->default = $default;
			
			$request = Art_Ajax::newRequest(self::REQUEST_ADD);
			$request->setRedirect('/'.Art_Router::getLayer().'/meta');
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
			$item_prefix = 'meta_';
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

			//Delete metas
			foreach($ids AS $id)
			{
				$meta = new Art_Model_Meta($id);
				if( $meta->isLoaded() )
				{
					$meta->delete();
				}
			}
			
			switch(count($ids))
			{
				case 0:
					$response->addAlert(__('module_meta_delete_none'));
					break;
				case 1:
					$response->addMessage(__('module_meta_delete'));
					$response->addVariable('content', Art_Module::createAndRenderModule('meta'));
					break;
				default :
					$response->addMessage(sprintf(__('module_meta_delete_more'), count($ids)));
					$response->addVariable('content', Art_Module::createAndRenderModule('meta'));
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
			
			$meta = new Art_Model_Meta(Art_Router::getId());
			
			if ( !$meta->isLoaded() )
			{
				$response->addAlert(__('alert_deleted_when_edit'));
			}
			
			$fields = Art_Model_Meta::getCols('update');
			
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
			
			//Everything is valid
			if( $response->isValid() )
			{
				$meta->setDataFromArray($sql_data);
				$meta->save();
				
				$response->addMessage(__('module_meta_edit_succ'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$meta = new Art_Model_Meta(Art_Router::getId());
			
			if( !$meta->isLoaded() )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			else if( !$meta->isPrivileged() )
			{
				$this->allowTo(Art_User::NO_ACCESS);
			}
			
			$this->view->meta = $meta;
			
			$defaults = Art_Model_Meta::getDefaults();
			
			if( isset($defaults[$meta->key]) )
			{
				$this->view->default = $defaults[$meta->key];
			}
			else
			{
				$this->view->default = null;
			}
			
			$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
			$request->setRedirect('/'.Art_Router::getLayer().'/meta');
			$this->view->request = $request;
		}
	}
	
	
	static function getFieldsValidators()
	{
		return	array(
	'key'				=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message' => __('module_meta_v_key_min')],  
		Art_Validator::MAX_LENGTH => ['value' => 60,'message' => __('module_meta_v_key_max')]),
	'content_cs'			=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message' => __('module_meta_v_content_cs_min')],
		Art_Validator::MAX_LENGTH => ['value' => 300,'message' => __('module_meta_v_content_cs_max')]));
	}
}