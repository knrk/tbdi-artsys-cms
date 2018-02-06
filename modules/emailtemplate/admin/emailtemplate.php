<?php
/**
 *  @author Robin Zon <zon@itart.cz>
 *  @package modules/emailtemplate/admin
 */
class Module_Emailtemplate extends Art_Abstract_Module {
	
	const REQUEST_NEW				= 'V41uvTDob5';
	const REQUEST_EDIT				= 'iafZ94i8Js';
	const REQUEST_DELETE_SINGLE		= 'iafZ94i8Js';
	
	const CHECKBOXES_PREFIX			= 'emailtemplate_';
	
	function indexAction() 
	{		
		$emailtemplates = Art_Model_Email_Template::fetchAllPrivileged();
		$this->view->emailtemplates = $emailtemplates;

		//Delete item by button
		$delete_single_request = Art_Ajax::newRequest(self::REQUEST_DELETE_SINGLE);
		$delete_single_request->setAction('/'.Art_Router::getLayer().'/emailtemplate/deleteSingle/$id');
		$delete_single_request->addUpdate('content','.module_emailtemplate_index');
		$delete_single_request->setConfirmWindow(__('module_emailtemplate_delete_single_confirm'));
		$this->view->delete_single_request = $delete_single_request;
	}

	function deleteSingleAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_DELETE_SINGLE) )
		{
			$response = Art_Ajax::newResponse();
			$article = new Art_Model_Email_Template(Art_Router::getId());
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
				$response->addMessage(__('module_emailtemplate_deleted_not_found'));
			}
			
			$response->addVariable('content', Art_Module::createAndRenderModule('emailtemplate'));
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
			
			//Set each field validation options
			$fields = Art_Model_Email_Template::getCols('insert');
			
			$sql_data = Helper_Default::getValidatedSQLData($fields, $this->_getFieldsValidators(), $data, $response);
			
			//Everything is valid
			if( $response->isValid() )
			{
				$template = new Art_Model_Email_Template;
				$template->setDataFromArray($sql_data);
				$template->save();
				
				$response->addMessage(sprintf(__('module_emaildump_added_success'),$template->name));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$request = Art_Ajax::newRequest(self::REQUEST_NEW);
			$request->setRedirect('/'.Art_Router::getLayer().'/emailtemplate');
			$this->view->request = $request;
			
			
			if( Art_Register::hasNamespace('mail') )
			{
				$register = Art_Register::in('mail');
				
				$this->view->from_name = $register->get('from_name');
				$this->view->from_mail = $register->get('from_mail');
				$this->view->reply_to_name = $register->get('reply_to_name');
				$this->view->reply_to_mail = $register->get('reply_to_mail');
			}
			else
			{
				$this->view->from_name = '';
				$this->view->from_mail = '';
				$this->view->reply_to_name = '';
				$this->view->reply_to_mail = '';
			}
			
		}
	}
	
	function editAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			//Set each field validation options
			$fields = Art_Model_Email_Template::getCols('update');
			
			$template = new Art_Model_Email_Template(Art_Router::getId());		
			if ( !$template->isLoaded() )
			{
				$response->addMessage(__('module_emailtemplate_deleted_when_edit'), Art_Main::ALERT);
			}
			elseif( !$template->isPrivileged() )
			{
				$response->addMessage(__('module_emailtemplate_no_rights_when_edit'), Art_Main::ALERT);
			}
			
			$sql_data = Helper_Default::getValidatedSQLData($fields, $this->_getFieldsValidators(), $data, $response);
			
			//Everything is valid
			if( $response->isValid() )
			{
				$template->setDataFromArray($sql_data);
				$template->save();
				
				$response->addMessage(sprintf(__('module_emailtemplate_edited_success'),$template->name));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$emailtemplate = new Art_Model_Email_Template(Art_Router::getId());
			if( !$emailtemplate->isLoaded() )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			elseif( !$emailtemplate->isPrivileged() )
			{
				$this->allowTo(Art_User::NO_ACCESS);
			}
			
			$this->view->emailtemplate = $emailtemplate;
			
			$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
			$request->setRedirect('/'.Art_Router::getLayer().'/emailtemplate');
			$this->view->request = $request;
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
	
	
	protected function _getFieldsValidators()
	{
		return	array(
					'name'				=> array(
						Art_Validator::MIN_LENGTH => ['value' => 3,'message' => __('module_emailtemplate_invalid_name_short')],  
						Art_Validator::MAX_LENGTH => ['value' => 100,'message' => __('module_emailtemplate_invalid_name_long')]),
					'subject'			=> array(
						Art_Validator::MAX_LENGTH => ['value' => 100,'message' => __('module_emailtemplate_invalid_subject_long')]),
					'from_name'			=> array(
						Art_Validator::MAX_LENGTH => ['value' => 50,'message' => __('module_emailtemplate_invalid_from_name_long')]),
					'from_email'		=> array(
						Art_Validator::IS_EMAIL => ['message' => __('module_emailtemplate_invalid_from_email')]),
					'reply_to_name'		=> array(
						Art_Validator::MAX_LENGTH => ['value' => 100,'message' => __('module_emailtemplate_invalid_reply_to_name_long')]),
					'reply_to_email'	=> array(
						Art_Validator::IS_EMAIL => ['message' => __('module_emailtemplate_invalid_reply_to_email')]),
					'body'				=> array(
						Art_Validator::MIN_LENGTH => ['value' => 10,'message' => __('module_emailtemplate_invalid_body_short')]));
	}
}