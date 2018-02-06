<?php
/**
 *  @author Pastuszek Jakub <pastuszek@itart.cz>
 *  @package modules/login/cabinet
 */
class Module_Login extends Art_Abstract_Module {

	const REQUEST_LOGOUT = 'H31Zp1rqqc';
	
    function embeddAction() 
	{
		$logout_request = Art_Ajax::newRequest(self::REQUEST_LOGOUT);
		$logout_request->setAction('/login/logout');
		$logout_request->setRedirect('/');

		$this->view->logout_request = $logout_request;
	}

	function logoutAction()
	{
		$this->showTo(self::ALLOW_AJAX);

		$response = Art_Ajax::newResponse();

		$login = Art_User::getCurrentLogin();
		if($login->isLoaded())
		{
			$login->log_tag = '';
			$login->expire = '';
			$login->save();
		}

		cookie_unset(Art_User::LOG_TAG_NAME);

		$response->addMessage(__('module_login_logged_out'));
		$response->willRedirect();
		$response->execute();
	}
}
