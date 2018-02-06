<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/review/admin
 */
class Module_Review extends Art_Abstract_Module {
	
	const REQUEST_ACTIVE_TOGGLE	= 'FmCZWDFrhW';
	const REQUEST_DELETE_SINGLE = 'yVmSjQFDdc';
	const REQUEST_REPLY			= 'MgAzEALDEB';
	
	function indexAction() 
	{		
		$reviews = Review::fetchAllPrivileged();
		
		foreach ($reviews as $key => $value) /* @var $value Review */ 
		{
			$reviews[$key]->a_reply = '/'.Art_Router::getLayer().'/review/reply/'.$value->id;
			$reviews[$key]->a_detail = '/'.Art_Router::getLayer().'/review/detail/'.$value->id;

			$reviews[$key]->p_fullname = $value->getUser()->fullname;
		}
		
		$this->view->reviews = $reviews;
		
		//Change active state
		$active_request = Art_Ajax::newRequest(self::REQUEST_ACTIVE_TOGGLE);
		$active_request->setAction('/'.Art_Router::getLayer().'/review/toggleActive/$id');
		$active_request->addUpdate('content', '.module_review_index');
		$this->view->active_request = $active_request;
		
		//Delete item by button
		$delete_single_request = Art_Ajax::newRequest(self::REQUEST_DELETE_SINGLE);
		$delete_single_request->setAction('/'.Art_Router::getLayer().'/review/deleteSingle/$id');
		$delete_single_request->addUpdate('content','.module_review_index');
		$delete_single_request->setConfirmWindow(__('module_review_delete_single_confirm'));
		$this->view->delete_single_request = $delete_single_request;
	}
	
	function detailAction()
	{
		$review = new Review(Art_Router::getId());
		
		if ( $review->isLoaded() )
		{
			$this->view->review = $review;

			$user = $review->getUser();
			
			if ( $user->isLoaded() )
			{
				$this->view->fullname = $user->fullname;
			}
			else
			{
				$this->view->fullname = __('module_review_no_user');
			}
		}
		else
		{			
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function replyAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_REPLY) )
		{
			$response = Art_Ajax::newResponse();			
			$data = Art_Ajax::getData();
			
			Helper_Default::getValidatedSQLData(array('reply'), self::getFieldsValidators(), $data, $response);
			
			if( $response->isValid() )
			{
				$review = new Review(Art_Router::getId());
				
				if ( $review->isLoaded() )
				{
					$review->reply = $data['reply'];
					$review->save();

					$response->addMessage(__('module_review_reply_added'));
					$response->willRedirect();
				}
				else
				{
					$response->addAlert(__('module_review_reply_not_found'));
				}
			}		
			
			$response->execute();
		}
		else
		{			
			$review = new Review(Art_Router::getId());
		
			if ( $review->isLoaded() )
			{
				$this->view->review = $review;

				$user = $review->getUser();	

				if ( $user->isLoaded() )
				{
					$this->view->fullname = $user->fullname;
				}
				else
				{
					$this->view->fullname = __('module_review_no_user');
				}
			}
			else
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			
			$request = Art_Ajax::newRequest(self::REQUEST_REPLY);
			$request->setRedirect('/'.Art_Router::getLayer().'/review');
			$request->addUpdate('content', '.module_review_index');
			$this->view->request = $request;
		}
	}
	
	function toggleActiveAction() 
	{		
		if( Art_Ajax::isRequestedBy(self::REQUEST_ACTIVE_TOGGLE) )
		{
			$response = Art_Ajax::newResponse();
			
			$review = new Review(Art_Router::getId());
			
			if( $review->isLoaded() )
			{
				$review->visible = $review->visible ? 0 : 1;
				$review->save();
			}
			else
			{
				$response->addMessage(__('module_review_not_found'));
			}
			
			$response->addVariable('content', Art_Module::createAndRenderModule('review'));
			$response->execute();
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
			
			$review = new Review(Art_Router::getId());
			
			if( $review->isLoaded() )
			{
				$review->delete();				
			}
			else
			{
				$response->addMessage(__('module_review_delete_not_found'));
			}
			
			$response->addVariable('content', Art_Module::createAndRenderModule('review'));
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
	'reply'				=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message' => __('module_review_v_reply_min')],
		Art_Validator::MAX_LENGTH => ['value' => 200,'message' => __('module_review_v_reply_max')]),	
			);		
	}
}