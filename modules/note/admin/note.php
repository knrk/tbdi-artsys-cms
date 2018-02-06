<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/note/admin
 */
class Module_Note extends Art_Abstract_Module {
	
	const REQUEST_EDIT	= 'ejNfBYqaXb';
	
	function embeddAction() 
	{		
		$userId = $this->getParams('id_user');
		
		$note = new Note(array('id_user'=>$userId));
		
		$this->view->note = $note;
		
		$this->view->note->a_edit = '/'.Art_Router::getLayer().'/note/edit/'.$userId;
		
		if ( $note->isLoaded() && strlen($note->body) > 0)
		{
			$this->view->edit_ico = '<i class="fa fa-comment edit"></i>';
		}
		else
		{
			$this->view->edit_ico = '<i class="fa fa-comment-o edit"></i>';
		}
	}

	function editAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			Helper_Default::getValidatedSQLData(array('body'), self::getFieldsValidators(), $data, $response);
			
			//Everything is valid
			if( $response->isValid() )
			{
				$userId = Art_Router::getId();
				
				$note = new Note(array('id_user'=>$userId));
				$note->id_user = $userId;
				$note->body = $data['body'];
				$note->save();
				
				$response->addMessage(__('module_note_edit_success'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$note = new Note(array('id_user'=>Art_Router::getId()));
						
			$this->view->note = $note;
			
			$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
			$request->setRedirect('/'.Art_Router::getLayer().'/users');
			$this->view->request = $request;
		}
	}
	
	static function getFieldsValidators()
	{
		return	array(
	'body'			=> array( 
		Art_Validator::MAX_LENGTH => ['value' => 200,'message' => __('module_note_v_body_max')]),
			);
	}
}