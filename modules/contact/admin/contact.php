<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/contact/admin
 */
class Module_Contact extends Art_Abstract_Module {
	
	const REQUEST_EDIT = 'OjLr7csiC0';
	const REQUEST_EDIT_FILES = '7G2aYpnHOw';
	
	const EDITABLE_FIELDS = array('name','surname','company_name',
									'phone','mobile','email','mail_to',
									'street','city','zip','country',
									'fb_link','tw_link','gp_link','yt_link',
									'map');
	
	function indexAction() 
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_EDIT) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			$settings = $this->getSettings();
			foreach(self::EDITABLE_FIELDS AS $field)
			{
				if( isset($data[$field]) )
				{
					$settings->$field = $data[$field];
				}
				else
				{
					$settings->$field = NULL;
				}
			}
			$this->setSettings($settings);
			$response->addMessage(__('module_contact_saved'));
			$response->execute();
		}
		elseif( Art_Ajax::isRequestedBy(self::REQUEST_EDIT_FILES) )
		{
			$response = Art_Ajax::newResponse();
			$uploader = new Art_Uploader();
			$uploader->moveToTemp();
			$uploader->removeTemp();
			
			//$file->moveTo('modules/contact/admin', 'test.jpg');
			//$file->moveTo('modules/contact/admin/views');
			//$file->rename('kek.jpg');

			
			$response->execute();
		}
		else
		{
			$data = array();
			
			$this->view->data = $this->getSettings();
			foreach(self::EDITABLE_FIELDS AS $field)
			{
				if( !isset( $this->view->data->$field ) )
				{
					$this->view->data->$field = NULL;
				}
			}
			
			$edit_request = Art_Ajax::newRequest(self::REQUEST_EDIT);
			$edit_request->setAsyncFileUpload(self::REQUEST_EDIT_FILES,array('*'),20,50);
			$edit_request->setAction('/'.Art_Router::getLayer().'/contact');
			$this->view->edit_request = $edit_request;
		}
	}
}