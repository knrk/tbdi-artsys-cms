<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/review/cabinet
 */
class Module_Review extends Art_Abstract_Module {
	
	const REQUEST_SEND	= 'vZSfmUjhCv';
	
	function indexAction() 
	{	
		$this->view->sended = false;

		$review = new Review(array('id_user'=>Art_User::getId()));

		if ( $review->isLoaded() )
		{	
			$this->view->sended = true;		

			if ( NULL == $review->reply )
			{
				$review->reply = __('module_review_reply_not_yet');
			}

			$this->view->review = $review;
		}
		else
		{
			//Send new review
			$request = Art_Ajax::newRequest(self::REQUEST_SEND);
			$request->setAction('review/send');
			$request->addUpdate('content', '.module_review_index');
			$this->view->request = $request;
		}
	}
	
	function sendAction()
	{
		if ( Art_Ajax::isRequestedBy(self::REQUEST_SEND) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			Helper_Default::getValidatedSQLData(array('body'), self::getFieldsValidators(), $data, $response);
			
			if ( $response->isValid() )
			{
				$review = new Review(Art_Router::getId());

				$userId = Art_User::getId();

				$review->id_user = $userId;
				$review->note = $data['body'];
				$review->save();

				$response->addMessage(__('module_review_send_success'));
			}
			
			$response->addVariable('content', Art_Module::createAndRenderModule('review','index'));
			$response->execute();
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	static function getFieldsValidators()
	{
		return	array(
	'body'				=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message' => __('module_review_v_body_min')],
		Art_Validator::MAX_LENGTH => ['value' => 200,'message' => __('module_review_v_body_max')]),
			);
	}
}