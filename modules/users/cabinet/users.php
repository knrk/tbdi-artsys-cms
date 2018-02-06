<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/users/cabinet
 */
class Module_Users extends Art_Abstract_Module {
	
	const REQUEST_CHANGE_PASSWD	= 'UuRQNaTLXS';
	const REQUEST_SET_PASSWD	= 'MnSFXNMTdt';
	const REQUEST_EDIT			= 'eryAmGwXqX';
	const REQUEST_ADD_INV_CODE	= 'ezcMYPYwZh';
	const REQUEST_SET_BANKACC	= 'u2gnACPt6G';
	const REQUEST_REMOVE_BANKACC = 'fvT5sdFw2s';
	
	const ADDRESS_FIELDS = array('street','housenum','city','zip','area_code','phone','id_country');
	
	function changePasswordAction() 
	{		
		if( Art_Ajax::isRequestedBy(self::REQUEST_CHANGE_PASSWD) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$user = Art_User::getCurrentUser();
			
			if ( $user->isLoaded() )
			{
				$userData = $user->getData();
				
				$hash = Art_User::hashPassword($data['passwd_old'],$userData->salt);
				
				if ( $userData->password !== $hash )
				{
					$response->addAlert(__('module_users_v_change_password_wrong_actual'));
				}
				else if ( $data['passwd_old'] === $data['passwd_new1'] )
				{
					$response->addAlert(__('module_users_v_change_password_old_new_same'));
				}
				else if ( $data['passwd_new1'] !== $data['passwd_new2'] )
				{
					$response->addAlert(__('module_users_v_change_password_not_same'));
				}
				else 			
				{
					Helper_Default::getValidatedSQLData(array('passwd_new1'), self::getFieldsValidators(), $data, $response);
					
					if ( $response->isValid() )
					{
						$newhash = Art_User::hashPassword($data['passwd_new1'],$userData->salt);

						$userData->password = $newhash;
						$userData->pass_changed_date = dateSQL();
						$userData->save();

						$response->addMessage(__('module_users_change_password_changed'));
						$response->willRedirect();
					}
				}
			}
			else 
			{
				$response->addAlert(__('module_users_change_password_not_found'));
			}

			$response->execute();
		}
		else
		{
			//Change password request
			$request = Art_Ajax::newRequest(self::REQUEST_CHANGE_PASSWD);
			$request->setRedirect('/cabinet');
			$this->view->request = $request;
		}
	}
	
	function bank_AccountAction() 
	{		
		if( Art_Ajax::isRequestedBy(self::REQUEST_SET_BANKACC) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$user = Art_User::getCurrentUser();
			
			if ( $user->isLoaded() )
			{			
				if ( !empty($data['prefix']) )
				{
					$arrayFieldsToChack = array('prefix','basic','bank_code','note');
				}
				else
				{
					$arrayFieldsToChack = array('basic','bank_code','note');	
				}

				$dataArray = Helper_Default::getValidatedSQLData($arrayFieldsToChack, self::getBankAccFieldsValidators(), $data, $response);

				$userBankAcc = new User_X_Bank_Account();
				
				if ( $response->isValid() )
				{
					$userBankAcc->setDataFromArray($dataArray);
					
					if ( empty($data['prefix']) )
					{
						$userBankAcc->prefix = null;
					}
					
					$userBankAcc->setUser($user);
					$userBankAcc->save();

					$response->addMessage(__('module_users_set_bank_account_set'));
					$response->willRedirect();
				}
			}
			else 
			{
				$response->addAlert(__('module_users_user_not_found'));
			}

			$response->execute();
		}
		else
		{
			$user = Art_User::getCurrentUser();
			
			$this->view->bankAccounts = User_X_Bank_Account::fetchAllPrivileged(array('id_user'=>$user->id));
			
			//Set bank account request
			$request = Art_Ajax::newRequest(self::REQUEST_SET_BANKACC);
			$request->setRefresh();
			$this->view->request = $request;
			
			//Remove bank account request
			$requestRemove = Art_Ajax::newRequest(self::REQUEST_REMOVE_BANKACC);
			$requestRemove->setAction('/'.Art_Router::getLayer().'/users/bank_account_delete/$id'); 
			$requestRemove->setConfirmWindow(__('module_users_delete_bank_account_confirm')); 
			$requestRemove->addUpdate('content', '.module_users_bank_account');
			$this->view->requestRemove = $requestRemove;
		}
	}
	
	function bank_Account_DeleteAction() 
	{		
		if( Art_Ajax::isRequestedBy(self::REQUEST_REMOVE_BANKACC) )
		{
			$response = Art_Ajax::newResponse();
			
			$id = Art_Router::getId();
			$user = Art_User::getCurrentUser();
			
			$userBankAcc = new User_X_Bank_Account($id);
			
			if ( $userBankAcc->isLoaded() )
			{
				if ( $userBankAcc->id_user === $user->id )
				{
					$userBankAcc->delete();
					
					$response->addMessage(__('module_users_remove_bank_account_success'));
					$response->addVariable('content', Art_Module::createAndRenderModule('users','bank_account'));
				}
			}
			else
			{
				$response->addAlert(__('module_users_remove_bank_account_not_found'));
			}
				
			$response->execute();
		}
	}
	
	function editAction() 
	{		
		if( Art_Ajax::isRequestedBy(self::REQUEST_EDIT) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$user = Art_User::getCurrentUser();
			
			if ( Helper_TBDev::isUserRepresentsCompany($user) )
			{	
				$representant = Helper_TBDev::getCompanyRepresentant($user);
				
				$hasRepresentant = false;

				if ( $representant->id !== $user->id )
				{
					$hasRepresentant = true;
				}
				
				$addressFields = Art_Model_Address::getCols('update');

				//Data to insert to database
				$sql_companyAddress = Helper_Default::getValidatedSQLData($addressFields, self::getCompanyFieldsValidators(), $data, $response, Art_Model_Address::COMPANY_PREFIX);
				$sql_deliveryAddress = Helper_Default::getValidatedSQLData($addressFields, self::getDeliveryFieldsValidators(), $data, $response, Art_Model_Address::DELIVERY_PREFIX);			
				
				if ( $hasRepresentant ) 
				{
					$sql_contactAddress = Helper_Default::getValidatedSQLData($addressFields, self::getContactFieldsValidators(), $data, $response, Art_Model_Address::CONTACT_PREFIX);
				}
				
				if ( $response->isValid() && $user->isLoaded() )
				{	
					//*************************************//
					//******* COMPANY ADDRESS *******//

					$companyAddress = Art_Model_Address_Type::getCompany();

					$insertUserAddress = new Art_Model_Address(array('id_user'=>$representant->id,'id_address_type'=>$companyAddress->id));				
					$insertUserAddress->setDataFromArray($sql_companyAddress);
					$insertUserAddress->id_country = $data[Art_Model_Address::COMPANY_PREFIX.'country'];
					$insertUserAddress->setUser($representant);
					$insertUserAddress->setType($companyAddress);
					$insertUserAddress->save();
					
					//*************************************//
					//******* USER DELIVERY ADDRESS *******//

					$deliveryAddress = Art_Model_Address_Type::getDelivery();

					$insertUserAddress = new Art_Model_Address(array('id_user'=>$user->id,'id_address_type'=>$deliveryAddress->id));				
					$insertUserAddress->setDataFromArray($sql_deliveryAddress);
					$insertUserAddress->id_country = $data[Art_Model_Address::DELIVERY_PREFIX.'country'];
					$insertUserAddress->setUser($user);
					$insertUserAddress->setType($deliveryAddress);
					$insertUserAddress->save();

					if ( $hasRepresentant ) 
					{
						//************************************//
						//******* USER CONTACT ADDRESS *******//	

						$contactAddress = Art_Model_Address_Type::getDelivery();	//Contact address for representant is his delivery address

						$insertUserAddress = new Art_Model_Address(array('id_user'=>$representant->id,'id_address_type'=>$contactAddress->id));
						$insertUserAddress->setDataFromArray($sql_contactAddress);
						$insertUserAddress->id_country = $data[Art_Model_Address::CONTACT_PREFIX.'country'];
						$insertUserAddress->setUser($representant);
						$insertUserAddress->setType($contactAddress);
						$insertUserAddress->save();
					}
					
					$response->addMessage(__('module_users_edit_success'));
				}
			}
			else
			{
				$addressFields = Art_Model_Address::getCols('update');

				//Data to insert to database
				$sql_deliveryAddress = Helper_Default::getValidatedSQLData($addressFields, self::getDeliveryFieldsValidators(), $data, $response, Art_Model_Address::DELIVERY_PREFIX);			

				if( Helper_Default::isPropertyChecked($data,Art_Model_Address::CONTACT_PREFIX.'active') )
				{
					$sql_contactAddress = Helper_Default::getValidatedSQLData($addressFields, self::getContactFieldsValidators(), $data, $response, Art_Model_Address::CONTACT_PREFIX);
				}

				if ( $response->isValid() && $user->isLoaded() )
				{			
					//*************************************//
					//******* USER DELIVERY ADDRESS *******//

					$deliveryAddress = Art_Model_Address_Type::getDelivery();

					$insertUserAddress = new Art_Model_Address(array('id_user'=>$user->id,'id_address_type'=>$deliveryAddress->id));				
					$insertUserAddress->setDataFromArray($sql_deliveryAddress);
					$insertUserAddress->id_country = $data[Art_Model_Address::DELIVERY_PREFIX.'country'];
					$insertUserAddress->setUser($user);
					$insertUserAddress->setType($deliveryAddress);
					$insertUserAddress->save();

					if( Helper_Default::isPropertyChecked($data,Art_Model_Address::CONTACT_PREFIX.'active') )
					{
						//************************************//
						//******* USER CONTACT ADDRESS *******//	

						$contactAddress = Art_Model_Address_Type::getContact();

						$insertUserAddress = new Art_Model_Address(array('id_user'=>$user->id,'id_address_type'=>$contactAddress->id));
						$insertUserAddress->setDataFromArray($sql_contactAddress);
						$insertUserAddress->id_country = $data[Art_Model_Address::CONTACT_PREFIX.'country'];
						$insertUserAddress->setUser($user);
						$insertUserAddress->setType($contactAddress);
						$insertUserAddress->save();
					}
					else
					{
						$address = new Art_Model_Address(array('id_user'=>$user->id, 'id_address_type'=>Art_Model_Address_Type::getContactId()));
						if ( $address->isLoaded() )
						{
							$address->delete();
						}
					}

					$response->addMessage(__('module_users_edit_success'));
				}
			}

			$response->execute();
		}
		else
		{
			$this->view->countryList = Art_Model_Country::fetchAllPrivilegedActive();
			
			$user = Art_User::getCurrentUser();

			$data = array();
			
			$isEditedCompany = false;
			
			if ( Helper_TBDev::isUserRepresentsCompany($user) )
			{
				$isEditedCompany = true;
				
				$this->view->companyAddress = $companyAddress = Helper_TBDev::getCompanyAddress($user);
				$representant = Helper_TBDev::getCompanyRepresentant($user);
				
				$hasRepresentant = false;

				if ( $representant->id !== $user->id )
				{
					$hasRepresentant = true;
				}
				
				$this->view->hasRepresentant = $hasRepresentant;
				
				$deliveryAddress = new Art_Model_Address(array('id_user'=>$user->id,'id_address_type'=>Art_Model_Address_Type::getDeliveryId()));
				
				$this->view->deliveryAddress =  null;
				
				if ( $deliveryAddress->isLoaded() )
				{
					$this->view->deliveryAddress = $deliveryAddress;
				}
				
				if ( $hasRepresentant )
				{
					$representantAddress = new Art_Model_Address(array('id_user'=>$representant->id,'id_address_type'=>Art_Model_Address_Type::getDeliveryId()));

					$this->view->representantAddress =  null;

					if ( $representantAddress->isLoaded() )
					{
						$this->view->representantAddress = $representantAddress;
					}				
				}
			}
			else
			{
				$deliveryAddress = Art_Model_Address::fetchAllPrivileged(array('id_user'=>$user->id,'id_address_type'=>Art_Model_Address_Type::getDeliveryId()));

				if ( !empty($deliveryAddress) )
				{
					foreach ( $deliveryAddress[0] as $key => $value )
					{
						if ( in_array($key, self::ADDRESS_FIELDS) )
						{
							$key = Art_Model_Address::DELIVERY_PREFIX.$key; 
							$data[$key] = $value; 
						}
					}
				}

				$hasContactAddress = false;

				$contactAddress = Art_Model_Address::fetchAllPrivileged(array('id_user'=>$user->id,'id_address_type'=>Art_Model_Address_Type::getContactId()));

				if ( !empty($contactAddress) )
				{
					foreach ( $contactAddress[0] as $key => $value )
					{
						if ( in_array($key, self::ADDRESS_FIELDS) )
						{
							$key = Art_Model_Address::CONTACT_PREFIX.$key; 
							$data[$key] = $value; 
							$hasContactAddress = true;
						}
					}
				}

				$this->view->hasContactAddress = $hasContactAddress;
			}
			
			$this->view->data = $data;
			
			$this->view->isEditedCompany = $isEditedCompany;
			
			//Edit user data (addresses and phone) request
			$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
			$this->view->request = $request;
		}
	}
	
	function embeddAction() 
	{				
		$user = Art_User::getCurrentUser();
		$manager = Helper_TBDev::getManagerForUser($user);
		
		$this->view->isCompany = Helper_TBDev::isUserRepresentsCompany($user);
		
		$this->view->user = $user;
		$this->view->manager = $manager;
		$this->view->isManager = $isManager = Helper_TBDev::isManager($user);
		
		if ( !$isManager )
		{
			$membership_to = Helper_TBDev::getMembershipToForUser($user);
			$this->view->membership_to = Helper_TBDev::renderTrueFalseDateTo(dateSQL($membership_to) < dateSQL(), nice_date($membership_to));

			$this->view->p_managerDetail = '/'.Art_Router::getLayer().'/users/managerdetail';
		}
	}

	function invCodesAction ()
	{
		$user = Art_User::getCurrentUser();
		
		$codes = Invite_Code::fetchAllPrivilegedActive(array('id_user'=>$user->id));
		
		foreach ($codes as $key => $value)	/* @var $value Invite_Code */
		{
			if ( $value->created_by !== $user->id )
			{
				unset($codes[$key]);
				continue;
			}
			
			$value->URL = Helper_TBDev::getInvitedCodeURL($value);
		}
		
		$this->view->codes = $codes;
		
		$userInvitedBy = Helper_TBDev::getUserInvitedBy($user);
				
		$this->view->userInvitedByFullname = (NULL !== $userInvitedBy) ? $userInvitedBy->fullname : null;
		
		$joinedUsers = Helper_TBDev::getAllInvitedUsersWithInvCode($user);
		
		$this->view->joinedUsers = $joinedUsers;
		
		//Add invite code for user by button
		$add_invite_code_request = Art_Ajax::newRequest(self::REQUEST_ADD_INV_CODE); 
		$add_invite_code_request->setAction('/'.Art_Router::getLayer().'/users/genInvCode'); 
		$add_invite_code_request->addUpdate('content', '.module_users_invcodes');
		$this->view->add_invite_code_request = $add_invite_code_request;
	}
	
	function managerdetailAction()
	{
		$user = Art_User::getCurrentUser();
		$userManager = new User_X_Manager(array('id_user'=>$user->id));
		$manager = new Art_Model_User($userManager->id_manager);
		
		$this->view->manager = $manager->getData();
		
		$this->view->phone = Helper_TBDev::getTelephoneForUser($manager);
	}
	
	function genInvCodeAction ()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ADD_INV_CODE) )
		{	
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$user = Art_User::getCurrentUser();
			
			$codes = Invite_Code::fetchAllPrivilegedActive(array('id_user'=>$user->id));

			foreach ($codes as $key => $value)	/* @var $value Invite_Code */
			{
				if ( $value->created_by !== $user->id )
				{
					unset($codes[$key]);
				}
			}
					
			//Validate field			
			Helper_Default::getValidatedSQLData(array('note'), self::getCodeFieldsValidators(), $data, $response);
			
			if ( $response->isValid() )
			{
				if ( count($codes) < Helper_Default::getDefaultValue(Helper_TBDev::MAX_INV_CODES_PER_USER) )
				{
					$inviteCode = new Invite_Code();
					$inviteCode->id_user = $user->id;
					$inviteCode->code = Helper_TBDev::generateInviteCode();
					$inviteCode->active = 1;
					$inviteCode->note = $data['note'];
					$inviteCode->save();

					$response->addMessage(__('module_users_new_invite_code_success'));

					$response->addVariable('content', Art_Module::createAndRenderModule('users','invcodes'));
				}
				else
				{
					$response->addAlert(__('module_users_new_invite_aborted'));
				}
			}
			
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
	'passwd_new1'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 6,'message' => __('module_users_v_password_new_min')],
		Art_Validator::MAX_LENGTH => ['value' => 40,'message' => __('module_users_v_password_new_max')]),	
			);		
	}
	
	static function getDeliveryFieldsValidators()
	{
		return	array(
	'delivery-country'	=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_delivery_country_not_integer')]),
	'delivery-city'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_delivery_city_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_delivery_city_max')]),
	'delivery-street'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_delivery_street_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_delivery_street_max')]),
	'delivery-housenum'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_delivery_housenum_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_delivery_housenum_max')]),
	'delivery-zip'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_delivery_zip_min')],
		Art_Validator::MAX_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_delivery_zip_max')]),
	'delivery-area_code'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 3,'message'	=> __('module_registration_v_delivery_area_code_min')],
		Art_Validator::MAX_LENGTH => ['value' => 4,'message'	=> __('module_registration_v_delivery_area_code_max')]),
	'delivery-phone'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 9,'message'	=> __('module_registration_v_delivery_phone_min')],
		Art_Validator::MAX_LENGTH => ['value' => 9,'message'	=> __('module_registration_v_delivery_phone_max')],
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_delivery_phone_not_integer')]),
			);
	}
	
	static function getContactFieldsValidators()
	{
		return	array(
	'contact-country'	=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_contact_country_not_integer')]),
	'contact-city'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_contact_city_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_contact_city_max')]),
	'contact-street'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_contact_street_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_contact_street_max')]),
	'contact-housenum'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_contact_housenum_min')],
		Art_Validator::MAX_LENGTH => ['value' => 20,'message'	=> __('module_registration_v_contact_housenum_max')]),
	'contact-zip'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_contact_zip_min')],
		Art_Validator::MAX_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_contact_zip_max')]),
			);
	}
	
	static function getCodeFieldsValidators()
	{
		return	array(
	'note'			=> array(
		Art_Validator::MAX_LENGTH => ['value' => 40,'message'	=> __('module_users_v_code_note_max')]),
			);
	}
	
	static function getCompanyFieldsValidators()
	{
		return	array(
	Art_Model_Address::COMPANY_PREFIX.'country'	=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_company_country_not_integer')]),
	Art_Model_Address::COMPANY_PREFIX.'city'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_company_city_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_company_city_max')]),
	Art_Model_Address::COMPANY_PREFIX.'street'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_company_street_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_company_street_max')]),
	Art_Model_Address::COMPANY_PREFIX.'housenum'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_company_housenum_min')],
		Art_Validator::MAX_LENGTH => ['value' => 20,'message'	=> __('module_registration_v_company_housenum_max')]),
	Art_Model_Address::COMPANY_PREFIX.'zip'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_company_zip_min')],
		Art_Validator::MAX_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_company_zip_max')]),
			);
	}
	
	static function getBankAccFieldsValidators()
	{
		return	array(
	'prefix'	=> array(
		Art_Validator::IS_NUMERIC => ['message'					=> __('module_users_v_prefix_not_integer')]),
	'basic'		=> array(
		Art_Validator::IS_NUMERIC => ['message'					=> __('module_users_v_basic_not_integer')]),
	'bank_code'	=> array(
		Art_Validator::IS_NUMERIC => ['message'					=> __('module_users_v_bank_code_not_integer')]),
	'note'	=> array(
		Art_Validator::MAX_LENGTH => ['value' => 30,'message'	=> __('module_users_v_note_max')]),
			);
	}
}