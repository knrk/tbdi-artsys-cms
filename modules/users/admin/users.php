<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/users/admin
 */
class Module_Users extends Art_Abstract_Module {
	
	const REQUEST_ADD			= 'PscKzDDCsf';
	const REQUEST_EDIT			= 'dwbwVXKBjd';
	const REQUEST_AUTHORIZE		= 'RrQqFrZRKD';
	const REQUEST_RESEND_REG	= 'EPNfRSxvwa';
	const REQUEST_RESEND_AUTH	= 'RyuLGzpFmf';
	const REQUEST_SEND_SELECTED = 'sCqAWfyDYE';
	const REQUEST_DELETE_USER	= 'AKXXCtGwaP';
	const REQUEST_UNMEMBER_USER	= 'jMqSeakmMN';
	const REQUEST_ADD_INV_CODE	= 'MpbrvxCdPm';
	const REQUEST_DELETE_REQUEST	= 'FlSDmckdDe';
	const REQUEST_COMPLETE_REQUEST	= 'fKDZmdAoep';
	const REQUEST_SEND_REQUEST		= 'kWndSIOmnc';
	const REQUEST_COMPLETE_FIRM_REGISTRATION	= 'd4IrdGeE7v';
	const REQUEST_GOT_APPLICATION	= 'lDeanPdFDe';
	const REQUEST_GOT_APPLICATION_COMPANY	= 'EnFPmGbxlq';
	const REQUEST_NOT_GOT_APPLICATION	= 'ofdsNFedsD';
	const REQUEST_EDIT_SALUTATION	= 'nFDdksNedS';
	const REQUEST_CHANGE_MANAGER	= 'fRNdsOebfi';
		
	const REQUEST_DEACTIVATE_SERVICE_FOR_USER = 'fEd4uBe9dC';
	
	const ENABLE_ADDRESS_DELIVERY	= true;
	const ENABLE_ADDRESS_CONTACT	= true;	
	
	const CHECKBOXES_USER_PREFIX	= 'user_';	
	const CHECKBOXES_SERVICE_PREFIX	= 'service_';
	const CHECKBOXES_GROUP_PREFIX	= 'group_';
	
	const SESSION_REG_MAIL = 'session-regmail-';
	const SESSION_AUTH_MAIL = 'session-authmail-';
	
	const AUTH_PERSON	= 'auth-person-';
	
	function indexAction() {	//Art_Template::setTemplate("index","ajax");
		$sortById = Art_Router::getFromURI('id');
		$sortByFirstname = Art_Router::getFromURI('firstname');
		$sortBySurname = Art_Router::getFromURI('surname');
		$sortByMembershipFrom = Art_Router::getFromURI('membership_from');
		$sortByMembershipTo = Art_Router::getFromURI('membership_to');
		
		$this->view->sortBy = $sortBy = Helper_TBDev::getSortBy($sortById, $sortByFirstname, $sortBySurname, $sortByMembershipFrom, $sortByMembershipTo);
		
		$allUsers = $this->_getAllAuthenticatedUserDataForTable($sortBy === -1 ? 4 : $sortBy);
		// $authenticatedUsersData = $this->_getAllAuthenticatedUserDataForTable($sortBy);
		
		// $activeUsers = array();
		
		// $nonactiveUsers = array();
		
		// foreach ( $authenticatedUsersData as $value ) /* @var $value Art_Model_User_Data */
		// {		
		// 	if ( $value->getUser()->active )
		// 	{
		// 		$activeUsers[] = $value;
		// 	}
		// 	else
		// 	{
		// 		$nonactiveUsers[] = $value;
		// 	}
		// }
		
		// $allUsers = array_merge($activeUsers,$nonactiveUsers);
		
		$this->view->usersData = $allUsers;
		$this->view->count = count($allUsers);
	}
	
	function companiesAction() {	
		
		//Art_Template::setTemplate("index","ajax");
		$sortById = (int) Art_Router::getFromURI('id');
		$sortByCompanyName = (int) Art_Router::getFromURI('company_name');
		$sortByMembershipFrom = (int) Art_Router::getFromURI('membership_from');
		$sortByMembershipTo = (int) Art_Router::getFromURI('membership_to');

		// p($sortByMembershipFrom);

				// case 0: $param = 'id'; break;
				// case 1:	$param = 'idR'; break;
				// case 2: $param = 'firstname'; break;
				// case 3:	$param = 'firstnameR'; break;
				// case 4:	$param = 'surname'; break;
				// case 5:	$param = 'surnameR'; break;
				// case 6:	$param = 'membership_from'; break;
				// case 7:	$param = 'membership_fromR'; break;
				// case 8:	$param = 'membership_to'; break;
				// case 9:	$param = 'membership_toR'; break;	
				// case 10: $param = 'company_name'; break;
				// case 11: $param = 'company_nameR'; break;

		$this->view->sortBy = $sortBy = Helper_TBDev::getSortBy($sortByMembershipFrom, $sortByMembershipTo, $sortByCompanyName);
				
				// $authenticatedUsersData = $this->_getAllAuthenticatedUserDataForTable($sortBy, true);
		$allUsers = $this->_getAllAuthenticatedUserDataForTable($sortBy, true);
		
		// $activeUsers = array();
		// $nonactiveUsers = array();
		
		// foreach ($authenticatedUsersData as $value) /* @var $value Art_Model_User_Data */ {		
		// 	if ($value->getUser()->active) {
		// 		$activeUsers[] = $value;
		// 	} else {
		// 		$nonactiveUsers[] = $value;
		// 	}
		// }
		
		// $allUsers = array_merge($activeUsers, $nonactiveUsers);

		// p($allUsers);

		$this->view->usersData = $allUsers;
		$this->view->count = count($allUsers);
	}
	
	function embeddGroupAction() 
	{				
		$userUserGroup = Art_Model_User_X_User_Group::fetchAllPrivileged(array('id_user_group'=>$this->getParams('id_group')));
		
		$excludedUsers = array();

		foreach ($userUserGroup as $value) 
		{
			$excludedUsers[] = $value->id_user;
		}
		
		$sortById = Art_Router::getFromURI('id');
		$sortByFirstname = Art_Router::getFromURI('firstname');
		$sortBySurname = Art_Router::getFromURI('surname');
		$sortByMembershipFrom = Art_Router::getFromURI('membership_from');
		$sortByMembershipTo = Art_Router::getFromURI('membership_to');
		
		$this->view->sortBy = $sortBy = Helper_TBDev::getSortBy($sortById, $sortByFirstname, $sortBySurname, $sortByMembershipFrom, $sortByMembershipTo);
		
		$authenticatedUsersData = $this->_getAllAuthenticatedUserDataForTable($sortBy);
		
		$activeUsers = array();
		
		$nonactiveUsers = array();
		
		foreach ($authenticatedUsersData as $key => $value) /* @var $value Art_Model_User_Data */ 
		{		
			if ( in_array($value->id_user, $excludedUsers) )
			{
				unset($authenticatedUsersData[$key]);
			}
			else
			{
				if ( $value->getUser()->active )
				{
					$activeUsers[] = $value;
				}
				else
				{
					$nonactiveUsers[] = $value;
				}
			}
		}
		
		$allUsers = array_merge($activeUsers,$nonactiveUsers);
		
		$this->view->usersData = $allUsers;
		
		$this->view->count = count($authenticatedUsersData);
	}
	
	function editAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$user = new Art_Model_User(Art_Router::getId());	
			
			if ( !$user->isLoaded() )
			{
				$response->addAlert(__('module_users_not_found'));
			}
			
			$userDataFields = Art_Model_User_Data::getCols('update');
			$addressFields = Art_Model_Address::getCols('update');

			//Data to insert to database
			$sql_userData = Helper_Default::getValidatedSQLData($userDataFields, self::getUserDataFieldsValidators(), $data, $response);
			$sql_deliveryAddress = Helper_Default::getValidatedSQLData($addressFields, self::getDeliveryFieldsValidators(), $data, $response, Art_Model_Address::DELIVERY_PREFIX);
			
			if ( Helper_Default::isPropertyChecked($data,Art_Model_Address::CONTACT_PREFIX.'active') )
			{
				$sql_contactAddress = Helper_Default::getValidatedSQLData($addressFields, self::getContactFieldsValidators(), $data, $response, Art_Model_Address::CONTACT_PREFIX);
			}	
			
			//Check email validity
			if ( !is_email($data['email']) )
			{
				$response->addAlert(__('module_registration_v_wrong_email'));
			}
			
			//Check duplicate email address
			$users = Art_Model_User_Data::fetchAll(array('email'=>$data['email']));
			if ( !empty($users) && $users[0]->id_user !== $user->id )
			{
				$response->addAlert(__('module_registration_v_email_duplication'));
			}

			//Everything is valid
			if( $response->isValid() )
			{
				$userId = $user->id;
				
				$userData = $user->getData();
				
				if ( $userData->isLoaded() )
				{
					$userData->setDataFromArray($sql_userData);
					$userData->save();

					$response->addMessage(__('module_users_edit_success'));
					$response->willRedirect();
				
					//*************************************//
					//******* USER DELIVERY ADDRESS *******//

					$deliveryAddress = Art_Model_Address_Type::getDelivery();

					$insertUserAddress = new Art_Model_Address(array('id_user'=>$userId, 'id_address_type'=>Art_Model_Address_Type::getDeliveryId()));
					
					if( $insertUserAddress->isLoaded() )
					{
						$insertUserAddress->setDataFromArray($sql_deliveryAddress);
						$insertUserAddress->setUser($user);
						$insertUserAddress->setType($deliveryAddress);
						$insertUserAddress->id_country = $data[Art_Model_Address::DELIVERY_PREFIX.'country'];
						$insertUserAddress->save();
					}

					//************************************//
					//******* USER CONTACT ADDRESS *******//				
			
					$contactAddress = Art_Model_Address_Type::getContact();
			
					if ( Helper_Default::isPropertyChecked($data,Art_Model_Address::CONTACT_PREFIX.'active') )
					{
						$insertUserAddress = new Art_Model_Address(array('id_user'=>$userId, 'id_address_type'=>Art_Model_Address_Type::getContactId()));

						$insertUserAddress->setDataFromArray($sql_contactAddress);
						$insertUserAddress->setUser($user);
						$insertUserAddress->setType($contactAddress);
						$insertUserAddress->id_country = $data[Art_Model_Address::CONTACT_PREFIX.'country'];
						$insertUserAddress->save();
					}
					else
					{
						$address = new Art_Model_Address(array('id_user'=>$userId, 'id_address_type'=>Art_Model_Address_Type::getContactId()));
						if ( $address->isLoaded() )
						{
							$address->delete();
						}
					}
				}
			}
			
			$response->execute();
		}
		else
		{	
			$user = new Art_Model_User(Art_Router::getId());
			
			if ( $user->isLoaded() )
			{
				$this->view->days = Helper_TBDev::getDayRange();
				$this->view->months = Helper_TBDev::getMonthRange();
				$this->view->years = Helper_TBDev::getBornYearRange();
				
				$this->view->user = $user; 

				$this->view->data = $user->getData();
				
				$this->view->hasContactAddress = false;
				
				$this->view->deliveryAddress = $this->view->contactAddress = null;
				
				foreach ($user->getAddresses() as $value) /* @var $value Art_Model_Address */ 
				{
					if ( Art_Model_Address_Type::getDeliveryId() === $value->id_address_type )
					{
						$this->view->deliveryAddress = $value;
					}
					
					if ( Art_Model_Address_Type::getContactId() === $value->id_address_type )
					{
						$this->view->contactAddress = $value;
						$this->view->hasContactAddress = true;
					}
				}

				$this->view->countryList = Art_Model_Country::fetchAll();

				//Edit User
				$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
				$request->setRedirect('/'.Art_Router::getLayer().'/users/detail/'.Art_Router::getId());
				$this->view->request = $request;
			}
			else
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
		}
	}
	
	function editcompanyAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$user = new Art_Model_User(Art_Router::getId());	
			
			if ( !$user->isLoaded() )
			{
				$response->addAlert(__('module_users_not_found'));
			}
			
			$representant = Helper_TBDev::getCompanyRepresentant($user);

			$hasRepresentant = false;

			if ( $representant->id !== $user->id )
			{
				$hasRepresentant = true;
			}
			//p($data);
			$userDataFields = Art_Model_User_Data::getCols('update');
			$addressFields = Art_Model_Address::getCols('update');

			//Data to insert to database
			$sql_authUserData = Helper_Default::getValidatedSQLData($userDataFields, self::getUserDataFieldsValidators(Module_Users::AUTH_PERSON), $data, $response, Module_Users::AUTH_PERSON);
			$sql_authDeliveryAddress = Helper_Default::getValidatedSQLData($addressFields, self::getDeliveryFieldsValidators(Module_Users::AUTH_PERSON), $data, $response, Module_Users::AUTH_PERSON);
			$sql_companyAddress = Helper_Default::getValidatedSQLData($addressFields, self::getCompanyFieldsValidators(), $data, $response, Art_Model_Address::COMPANY_PREFIX);

			if ( $hasRepresentant )			
			{
				$sql_userData = Helper_Default::getValidatedSQLData($userDataFields, self::getUserDataFieldsValidators(), $data, $response);
				$sql_deliveryAddress = Helper_Default::getValidatedSQLData($addressFields, self::getDeliveryFieldsWithoutPhoneValidators(Art_Model_Address::DELIVERY_PREFIX), $data, $response, Art_Model_Address::DELIVERY_PREFIX);
			}	
			//p($sql_authUserData);d();
			//Check email validity
			if ( !is_email($data['email']) )
			{
				$response->addAlert(__('module_registration_v_wrong_email'));
			}
			
			//Check duplicate email address
			$users = Art_Model_User_Data::fetchAll(array('email'=>$data['email']));
			if ( !empty($users) && $users[0]->id_user !== $representant->id )
			{
				$response->addAlert(__('module_registration_v_email_duplication'));
			}
			
			if ( !isset($data['ico']) || !Helper_Default::verifyICO($data['ico']) )
			{
				$response->addAlert(__('module_registration_v_ico_not_valid'));
			}
			
			if ( !empty($data['dic']) && !Helper_Default::verifyDIC($data['dic']) )
			{
				$response->addAlert(__('module_registration_v_dic_not_valid'));
			}

			//Everything is valid
			if( $response->isValid() )
			{
				$userId = $user->id;
				
				$userData = $user->getData();
				
				if ( $userData->isLoaded() )
				{
					//*************************//
					//******* USER COMPANY *******//	

					$insertUserCompany = new User_X_Company(array('id_user'=>$userId,'id_company_user'=>$representant->id));
					$insertUserCompany->function = $data['person-function'];
					$insertUserCompany->save();	
						
					//************************************//
					//******* COMPANY ADDRESS *******//				

					$companyAddress = Art_Model_Address_Type::getCompany();

					$insertCompanyAddress = Helper_TBDev::getCompanyAddress($user);

					if ( $insertCompanyAddress->isLoaded() )
					{
						$insertCompanyAddress->setDataFromArray($sql_companyAddress);
						$insertCompanyAddress->setUser($representant);
						$insertCompanyAddress->setType($companyAddress);
						$insertCompanyAddress->id_country = $data[Art_Model_Address::COMPANY_PREFIX.'country'];
						$insertCompanyAddress->dic = $data['dic'];
						$insertCompanyAddress->ico = $data['ico'];
						$insertCompanyAddress->company_name = $data['company_name'];
						$insertCompanyAddress->save();
					}

					//*************************************//
					//******* AUTH USER *******//

					$userData->setDataFromArray($sql_authUserData);
					$userData->email = $data['email'];
					$userData->save();

					$deliveryAddress = Art_Model_Address_Type::getDelivery();

					$insertUserAddress = new Art_Model_Address(array('id_user'=>$userId, 'id_address_type'=>Art_Model_Address_Type::getDeliveryId()));

					if( $insertUserAddress->isLoaded() )
					{
						$insertUserAddress->setDataFromArray($sql_authDeliveryAddress);
						$insertUserAddress->setUser($user);
						$insertUserAddress->setType($deliveryAddress);
						$insertUserAddress->id_country = $data[Module_Users::AUTH_PERSON.'country'];
						$insertUserAddress->save();
					}	
						
					if ( $hasRepresentant )
					{
						$representantData = $representant->getData();
						$representantData->setDataFromArray($sql_userData);
						$representantData->save();

						//*************************************//
						//******* USER DELIVERY ADDRESS *******//

						$deliveryAddress = Art_Model_Address_Type::getDelivery();

						$insertUserAddress = new Art_Model_Address(array('id_user'=>$representant->id, 'id_address_type'=>Art_Model_Address_Type::getDeliveryId()));

						if( $insertUserAddress->isLoaded() )
						{
							$insertUserAddress->setDataFromArray($sql_deliveryAddress);
							$insertUserAddress->setUser($representant);
							$insertUserAddress->setType($deliveryAddress);
							$insertUserAddress->id_country = $data[Art_Model_Address::DELIVERY_PREFIX.'country'];
							$insertUserAddress->save();
						}
					}
					
					$response->addMessage(__('module_users_edit_success'));
					$response->willRedirect();
				}
			}
			
			$response->execute();
		}
		else
		{	
			$user = new Art_Model_User(Art_Router::getId());

			if ( $user->isLoaded() && $user->user_number < 1000000 && Helper_TBDev::isUserRepresentsCompany($user) )
			{
				$this->view->days = Helper_TBDev::getDayRange();
				$this->view->months = Helper_TBDev::getMonthRange();
				$this->view->years = Helper_TBDev::getBornYearRange();
				
				$this->view->user = $user; 

				$this->view->data = $user->getData();
				
				$this->view->deliveryAddress = null;
				
				foreach ($user->getAddresses() as $value) /* @var $value Art_Model_Address */ 
				{
					if ( Art_Model_Address_Type::getDeliveryId() === $value->id_address_type )
					{
						$this->view->deliveryAddress = $value;
					}
				}

				$this->view->companyAddress = Helper_TBDev::getCompanyAddress($user);
				
				$representant = Helper_TBDev::getCompanyRepresentant($user);
			
				$this->view->isRepresentantSame = true;

				if ( NULL !== $representant && $representant->id !== $user->id )
				{
					$this->view->representantData = $representantData = $representant->getData();
					$this->view->representantAddress = $representantAddress = Helper_TBDev::getDeliveryAddress($representant);

					$this->view->representantFunction = Helper_TBDev::getUserFunctionInCompany($user, $representant);
					$this->view->isRepresentantSame = false;
				}
				else
				{
					$this->view->representantFunction = Helper_TBDev::getUserFunctionInCompany($user, $user);
				}
				
				$this->view->countryList = Art_Model_Country::fetchAll();

				//Edit User
				$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
				$request->setRedirect('/'.Art_Router::getLayer().'/users/detailcompany/'.Art_Router::getId());
				$this->view->request = $request;
			}
			else
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
		}
	}
	
	function detailAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT_SALUTATION))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$user = new Art_Model_User(Art_Router::getId());	
			
			if ( !$user->isLoaded() )
			{
				$response->addAlert(__('module_users_not_found'));
			}
			else
			{
				$userData = $user->getData();
				
				Helper_Default::getValidatedSQLData(array('salutation'), static::getSalutationValidator(), $data, $response);
				
				if ( $response->isValid() )
				{
					$userData->salutation = $data['salutation'];
					$userData->save();
					
					$response->addMessage(__('module_users_edit_salutation_success'));
					$response->willRedirect();
				}
			}
			
			$response->execute();
		}
		else
		{
			$user = new Art_Model_User(Art_Router::getId());

			if ( $user->isLoaded() )
			{
				$this->view->user = $user; 

				$this->view->data = $data = $user->getData();

				$this->view->telephone = Helper_TBDev::getTelephoneForUser($user);

				$this->view->gender = Helper_TBDev::getGender($data->gender);
				
				$bankAccountNumber = Helper_TBDev::getBankAccountNumbers($user);
				
				$bankAccount = null;
				 
				if ( !empty($bankAccountNumber) )
				{
					foreach ($bankAccountNumber as $value) /* @var $value User_X_Bank_Account */ 
					{
						$bankAccount .= Helper_TBDev::getBankAccountNumber($value) . ', ';
					}
				}
				
				$this->view->bank_account = (NULL === $bankAccount) ? '-' : substr($bankAccount,0,-2);
						
				$services = Helper_TBDev::getAllServicesForUser($user);

				$activatedServices = Helper_TBDev::getPropertyFromObjectsInArray(Helper_TBDev::getAllActivatedServicesForUser($user),'name');

				foreach ( $services as $value ) /* @var $value Service */ 
				{
					$value->icon = in_array($value->name, $activatedServices) ? 'thumbs-up' : 'info';

					$value->active_to = Helper_TBDev::getServiceToForUser($user,$value);

					if ( 0 > $value->active_to ) 
					{
						$value->active_to = null; 
					}
					else 
					{
						$value->active_to = $value->active_to ? __('to').': '.Helper_TBDev::renderTrueFalseDateTo(dateSQL($value->active_to) < dateSQL(), nice_date($value->active_to)) : null;
					}

					$value->a_service = '/'.Art_Router::getLayer().'/users/service/'.$user->id.'-'.$value->id;
			
					$value->isActivated = Helper_TBDev::isServiceActivatedForUser($value, $user);
				}

				$this->view->services = $services;

				$payments = Helper_TBDev::getAllPaymentsForUser($user);

				$this->view->payments = $payments;

				$this->view->deliveryAddress = $this->view->contactAddress = null;
				$this->view->deliveryState = $this->view->contactState = null;

				foreach ($user->getAddresses() as $value) /* @var $value Art_Model_Address */ 
				{
					if ( Art_Model_Address_Type::getDeliveryId() === $value->id_address_type )
					{
						$this->view->deliveryAddress = $value;
						$this->view->deliveryState = $value->getCountry()->name;
					}

					if ( Art_Model_Address_Type::getContactId() === $value->id_address_type )
					{
						$this->view->contactAddress = $value;
						$this->view->contactState = $value->getCountry()->name;
					}
				}
//d(User_X_Service::fetchAll(array('id_user'=>$user->id)));
				$manager = Helper_TBDev::getManagerForUser($user);

				$this->view->manager = $manager;

				$this->view->invitedBy = Helper_TBDev::getFullnameOrCompanyName(Helper_TBDev::getUserInvitedBy($user));
				$this->view->invitedUsers = Helper_TBDev::getAllInvitedUsers($user);

				$this->view->lastLogins = Helper_Default::getLastLoginsForUser($user);

				$this->view->isAuthenticated = Helper_TBDev::isUserAuthenticated($user); 
				
				//Edit Salutation
				$request = Art_Ajax::newRequest(self::REQUEST_EDIT_SALUTATION);
				$request->setRefresh();
				$this->view->request = $request;

				//Remove activated service
				$remove_service = Art_Ajax::newRequest(self::REQUEST_DEACTIVATE_SERVICE_FOR_USER); 
				$remove_service->setAction('/'.Art_Router::getLayer().'/service/deactivate/$id'); 
				$remove_service->setRefresh();
				$remove_service->setConfirmWindow(__('module_users_remove_service_confirm')); 
				$this->view->remove_service = $remove_service;
				
				//Unmember user by button
				$unmember_user_request = Art_Ajax::newRequest(self::REQUEST_UNMEMBER_USER); 
				$unmember_user_request->setAction('/'.Art_Router::getLayer().'/users/unmemberUser/$id'); 
				$unmember_user_request->setRedirect('/'.Art_Router::getLayer().'/users'); 
				$unmember_user_request->setConfirmWindow(__('module_users_unmember_user_confirm')); 
				$this->view->unmember_user_request = $unmember_user_request;

				//Delete user by button
				$delete_user_request = Art_Ajax::newRequest(self::REQUEST_DELETE_USER); 
				$delete_user_request->setAction('/'.Art_Router::getLayer().'/users/delete/$id'); 
				$delete_user_request->setRedirect('/'.Art_Router::getLayer().'/users'); 
				$delete_user_request->setConfirmWindow(__('module_users_delete_user_confirm')); 
				$this->view->delete_user_request = $delete_user_request;
			}
			else
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
		}
	}
	
	function detailcompanyAction()
	{		
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT_SALUTATION))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$user = new Art_Model_User(Art_Router::getId());	
			
			if ( !$user->isLoaded() )
			{
				$response->addAlert(__('module_users_not_found'));
			}
			else
			{
				$userData = $user->getData();
				
				Helper_Default::getValidatedSQLData(array('salutation'), static::getSalutationValidator(), $data, $response);
				
				if ( $response->isValid() )
				{
					$userData->salutation = $data['salutation'];
					$userData->save();
					
					$response->addMessage(__('module_users_edit_salutation_success'));
					$response->willRedirect();
				}
			}
			
			$response->execute();
		}
		else
		{
			$user = new Art_Model_User(Art_Router::getId());

			if ( $user->isLoaded() && Helper_TBDev::isUserRepresentsCompany($user) )
			{
				$this->view->user = $user; 

				$this->view->data = $data = $user->getData();

				$this->view->telephone = Helper_TBDev::getTelephoneForUser($user);

				$this->view->gender = Helper_TBDev::getGender($data->gender);

				$services = Helper_TBDev::getAllServicesForUser($user);

				$activatedServices = Helper_TBDev::getPropertyFromObjectsInArray(Helper_TBDev::getAllActivatedServicesForUser($user),'name');

				foreach ( $services as $value ) /* @var $value Service */ 
				{
					$value->icon = in_array($value->name, $activatedServices) ? 'thumbs-up' : 'info';

					$value->active_to = Helper_TBDev::getServiceToForUser($user,$value);
					
					if ( 0 > $value->active_to )  
					{
						$value->active_to=null; 
					}
					else 
					{
						$value->active_to = $value->active_to ? __('to').': '.Helper_TBDev::renderTrueFalseDateTo(dateSQL($value->active_to) < dateSQL(), nice_date($value->active_to)) : null;
					}
					
					$value->a_service = '/'.Art_Router::getLayer().'/users/service/'.$user->id.'-'.$value->id;
					
					$value->isActivated = Helper_TBDev::isServiceActivatedForUser($value, $user);
				}

				$this->view->services = $services;

				$payments = Helper_TBDev::getAllPaymentsForUser($user);

				$this->view->payments = $payments;

				$representant = Helper_TBDev::getCompanyRepresentant($user);

				$this->view->isRepresentantDiff = false;

				if ( NULL !== $representant && $representant->id !== $user->id )
				{
					$this->view->representantData = $representantData = $representant->getData();
					$this->view->representantGender = Helper_TBDev::getGender($representantData->gender);
					$this->view->representantAddress = $representantAddress = Helper_TBDev::getDeliveryAddress($representant);
					$this->view->representantState = $representantAddress->getCountry()->name;
					$this->view->representantFunction = Helper_TBDev::getUserFunctionInCompany($user, $representant);
					$this->view->isRepresentantDiff = true;
				}
				else
				{
					$this->view->representantFunction = Helper_TBDev::getUserFunctionInCompany($user, $user);
				}

				$this->view->deliveryAddress = $this->view->contactAddress = null;
				$this->view->deliveryState = $this->view->contactState = null;

				foreach ($user->getAddresses() as $value) /* @var $value Art_Model_Address */ 
				{
					if ( Art_Model_Address_Type::getDeliveryId() === $value->id_address_type )
					{
						$this->view->deliveryAddress = $value;
						$this->view->deliveryState = $value->getCountry()->name;
					}

					if ( Art_Model_Address_Type::getContactId() === $value->id_address_type )
					{
						$this->view->contactAddress = $value;
						$this->view->contactState = $value->getCountry()->name;
					}
				}

				$this->view->company = $company = Helper_TBDev::getCompanyAddress($user);

				$this->view->companyState = $company->getCountry()->name;

				$manager = Helper_TBDev::getManagerForUser($user);

				$this->view->manager = $manager;

				$this->view->invitedBy = Helper_TBDev::getFullnameOrCompanyName(Helper_TBDev::getUserInvitedBy($user));
				$this->view->invitedUsers = Helper_TBDev::getAllInvitedUsers($user);

				$this->view->lastLogins = Helper_Default::getLastLoginsForUser($user);

				$this->view->isAuthenticated = Helper_TBDev::isUserAuthenticated($user);
				
				//Edit Salutation
				$request = Art_Ajax::newRequest(self::REQUEST_EDIT_SALUTATION);
				$request->setRefresh();
				$this->view->request = $request;
				
				//Remove activated service
				$remove_service = Art_Ajax::newRequest(self::REQUEST_DEACTIVATE_SERVICE_FOR_USER); 
				$remove_service->setAction('/'.Art_Router::getLayer().'/service/deactivate/$id'); 
				$remove_service->setRefresh();
				$remove_service->setConfirmWindow(__('module_users_remove_service_confirm')); 
				$this->view->remove_service = $remove_service;
				
				//Unmember user by button
				$unmember_user_request = Art_Ajax::newRequest(self::REQUEST_UNMEMBER_USER); 
				$unmember_user_request->setAction('/'.Art_Router::getLayer().'/users/unmemberUser/$id'); 
				$unmember_user_request->setRedirect('/'.Art_Router::getLayer().'/users'); 
				$unmember_user_request->setConfirmWindow(__('module_users_unmember_user_confirm')); 
				$this->view->unmember_user_request = $unmember_user_request;

				//Delete user by button
				$delete_user_request = Art_Ajax::newRequest(self::REQUEST_DELETE_USER); 
				$delete_user_request->setAction('/'.Art_Router::getLayer().'/users/delete/$id'); 
				$delete_user_request->setRedirect('/'.Art_Router::getLayer().'/users'); 
				$delete_user_request->setConfirmWindow(__('module_users_delete_user_confirm')); 
				$this->view->delete_user_request = $delete_user_request;
			}
			else
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
		}
	}
	
	function newAction()
	{
		$this->showTo(Art_User::NO_ACCESS);
		
		if( Art_Ajax::isRequestedBy(self::REQUEST_ADD) )
		{			
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$userDataFields = Art_Model_User_Data::getCols('insert');
			$addressFields = Art_Model_Address::getCols('insert');

			//Data to insert to database
			$sql_userData = Helper_Default::getValidatedSQLData($userDataFields, self::getFieldsValidators(), $data, $response);
			$sql_deliveryAddress = Helper_Default::getValidatedSQLData($addressFields, self::getDeliveryFieldsValidators(), $data, $response, Art_Model_Address::DELIVERY_PREFIX);
			
			if ( Helper_Default::isPropertyChecked($data,Art_Model_Address::CONTACT_PREFIX.'active') )
			{
				$sql_contactAddress = Helper_Default::getValidatedSQLData($addressFields, self::getContactFieldsValidators(), $data, $response, Art_Model_Address::CONTACT_PREFIX);
			}	
			
			//Check email validity
			if ( !is_email($data['email']) )
			{
				$response->addAlert(__('module_registration_v_wrong_email'));
			}
			
			//Check duplicate email address
			$users = Art_Model_User_Data::fetchAll(array('email'=>$data['email']));
			if ( !empty($users) )
			{
				$response->addAlert(__('module_registration_v_email_duplication'));
			}
			
			if ( !isset($data['reg-code']) || !Helper_TBDev::isInviteCodeValid($data['reg-code']) )
			{
				$response->addAlert(__('module_registration_v_reg_code_not_valid'));
			}
			
			if ( $response->isValid() )
			{
				//********************//
				//******* USER *******//

				//Insert new user
				$insertUser = new Art_Model_User();
				$insertUser->save();

				//Select row containing state according to CURRENCY_FROM_ADDRESS_STATE
				$country = Art_Model_Country::fetchAll(array('id'=>$data[Helper_TBDev::CURRENCY_FROM_ADDRESS_STATE.'country']));

				if ( !empty($country) )
				{
					//Get currency from address country currency
					$insertUser->id_currency = $country[0]->id_currency;
				}
				else
				{
					$insertUser->id_currency = Art_User::DEFAULT_CURRENCY_ID;
				}			

				$insertUser->user_number = Helper_TBDev::generateUserNumber();
				$insertUser->active = 1;
				$insertUser->save();

				//*************************//
				//******* USER DATA *******//			

				$passwd = Art_User::generatePassword();
				$salt = Art_User::generateSalt();

				$sql_userData['password'] = Art_User::hashPassword($passwd,$salt);
				$sql_userData['salt'] = $salt;

				$insertUserData = new Art_Model_User_Data();
				$insertUserData->setDataFromArray($sql_userData);
				$insertUserData->setUser($insertUser);
				$insertUserData->save();

				//*************************//
				//****** INVITE CODE ******//

				$inviteCode = new User_X_Invite_Code();
				$inviteCode->setUser($insertUser);
				$inviteCode->id_invite_code = Helper_TBDev::getInvitedCodeId($data['reg-code']);
				$inviteCode->save();
				
				//*************************//
				//******* USER GROUP *******//

				$insertUserToGroup = new Art_Model_User_X_User_Group();
				$insertUserToGroup->setUser($insertUser);
				$insertUserToGroup->id_user_group = Art_Model_User_Group::getRegisteredId();
				$insertUserToGroup->save();

				if ( self::ENABLE_ADDRESS_DELIVERY )
				{
					//*************************************//
					//******* USER DELIVERY ADDRESS *******//			

					$deliveryAddress = Art_Model_Address_Type::getDelivery();

					$insertDeliveryUserAddress = new Art_Model_Address();
					$insertDeliveryUserAddress->setDataFromArray($sql_deliveryAddress);
					$insertDeliveryUserAddress->setUser($insertUser);
					$insertDeliveryUserAddress->setType($deliveryAddress);
					$insertDeliveryUserAddress->id_country = $data[Art_Model_Address::DELIVERY_PREFIX.'country'];
					$insertDeliveryUserAddress->save();
				}

				if ( self::ENABLE_ADDRESS_CONTACT )
				{	
					//************************************//
					//******* USER CONTACT ADDRESS *******//					

					if ( Helper_Default::isPropertyChecked($data,Art_Model_Address::CONTACT_PREFIX.'active') )
					{
						$contactAddress = Art_Model_Address_Type::getContact();

						$insertContactUserAddress = new Art_Model_Address();
						$insertContactUserAddress->setDataFromArray($sql_contactAddress);
						$insertContactUserAddress->setUser($insertUser);
						$insertContactUserAddress->setType($contactAddress);
						$insertContactUserAddress->id_country = $data[Art_Model_Address::CONTACT_PREFIX.'country'];
						$insertContactUserAddress->save();
					}
				}

				$contactAddr = ( empty($insertContactUserAddress) ) ? 
								$insertDeliveryUserAddress->stringify : $insertContactUserAddress->stringify;

				$resource = Helper_TBDev_PDF::registrationDocForPerson(
					$insertUserData->fullnameWithDegree,
					$insertUser->user_number,
					$insertUserData->born,
					$contactAddr,
					$insertDeliveryUserAddress->stringify,				
					$insertUserData->email,
					$insertDeliveryUserAddress->phone
					);
				
				$url = Art_Server::getHost().'/resource/'.$resource->hash;
				
				//Send registraion mail
				Helper_Email::sendRegistrationMail($data['email'], $url);
				
				$response->addMessage(sprintf(__('module_users_add_success'),$insertUserData->fullname));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$this->view->days = Helper_TBDev::getDayRange();
			$this->view->months = Helper_TBDev::getMonthRange();
			$this->view->years = Helper_TBDev::getBornYearRange();

			$this->view->countryList = Art_Model_Country::fetchAllPrivileged();
				
			$request = Art_Ajax::newRequest(self::REQUEST_ADD);
			$request->setRedirect('/'.Art_Router::getLayer().'/users');
			$this->view->request = $request;
		}
	}
	
	function authorizationAction() {	
		//Get all user data restricted by not set pass_changed_date
		// $usersData = Art_Model_User_Data::fetchAllPrivileged(array('pass_changed_date' => ''));
		$usersData = Art_Model_User_Data::fetchAllPrivileged(array('verif' => 0));
		
		//Get all user data restricted by not be part of authorized group
//		$users = Helper_TBDev::getPropertyFromObjectsInArray(Art_Model_User_Data::fetchAllPrivileged(),'id_user');
//		$usersUserGroup = Helper_TBDev::getPropertyFromObjectsInArray(Art_Model_User_X_User_Group::fetchAll(array('id_user_group'=>Art_Model_User_Group::getAuthorizedId())),'id_user');
//		
//		$usersData = array();
//		
//		foreach (array_diff($users, $usersUserGroup) as $value) /* @var $value  */ 
//		{
//			$usersData[] = (new Art_Model_User($value))->getData();
//		} 
		
		$usersToAuth = array();
		$firmsToAuth = array();

		foreach ($usersData as $value) {
			$userData = $value;
			$user = $userData->getUser();
			

			if (intVal($user->getRights()) < 2) {
				continue;
			}

			// p($userData);

			
			//Init Users not to be shown here
			if (strtotime($userData->created_date) < strtotime((new Art_Model_User(1))->created_date) + 600) {
				continue;
			}

			if (!$user->active) {
				continue;
			}
			
			$userData->p_userId = $user->id;
			$userData->user_number = $user->user_number;
			$userData->p_created_date = $user->created_date;
			
			$userData->a_edit = '/'.Art_Router::getLayer().'/users/edit/'.$user->id;
			
			if (Helper_TBDev::isUserRepresentsCompany($user)) {
				$userData->a_auth = '/'.Art_Router::getLayer().'/users/authorizefirm/'.$user->id;
				$userData->a_completeReg = '/'.Art_Router::getLayer().'/users/completefirmregistration/'.$user->id;
				
				$companyAddress = Helper_TBDev::getCompanyAddress($user);
				$userData->isRegCompleted = !empty(Art_Model_User_Group::fetchAll(array('name'=>Helper_TBDev::GROUP_COMPANY.$companyAddress->company_name)));

				$userData->a_detail = '/'.Art_Router::getLayer().'/users/detailcompany/'.$user->id;
				$userData->company = $companyAddress;
				
				$firmsToAuth[] = $userData;
			}
			else if ($user->user_number < 50000) {

				$userData->a_auth = '/'.Art_Router::getLayer().'/users/authorizeuser/'.$user->id;
				$userData->a_detail = '/'.Art_Router::getLayer().'/users/detail/'.$user->id;
				
				$usersToAuth[] = $userData;

			}
				
			$userData->emailGotApp = null;
			$userData->emailNotGotApp = null;

			foreach (User_X_Email::fetchAllPrivileged(array('id_user'=>$user->id)) as $userEmail) {
				if (Helper_TBDev::EMAIL_TYPE_GOT_APP == $userEmail->email_type) {
					$userData->emailGotApp = $userEmail;
				}
				else if (Helper_TBDev::EMAIL_TYPE_NOT_GOT_APP == $userEmail->email_type) {
					$userData->emailNotGotApp = $userEmail;
				}
			} 

			$userData->p_phone = Helper_TBDev::getTelephoneForUser($user);
		}
		
		$this->view->usersToAuth = $usersToAuth;
		$this->view->firmsToAuth = $firmsToAuth;
		
		//Send got application by button
		$requestGotApp = Art_Ajax::newRequest(self::REQUEST_GOT_APPLICATION);
		$requestGotApp->setAction('/'.Art_Router::getLayer().'/users/gotApplication/$id');
		$requestGotApp->addUpdate('content', '.module_users_authorization');
		$this->view->requestGotApp = $requestGotApp;
		
		//Send got application by button
		$requestGotAppCompany = Art_Ajax::newRequest(self::REQUEST_GOT_APPLICATION_COMPANY);
		$requestGotAppCompany->setAction('/'.Art_Router::getLayer().'/users/gotApplicationCompany/$id');
		$requestGotAppCompany->addUpdate('content', '.module_users_authorization');
		$this->view->requestGotAppCompany = $requestGotAppCompany;
		
		//Send not got application by button
		$requestNotGotApp = Art_Ajax::newRequest(self::REQUEST_NOT_GOT_APPLICATION);
		$requestNotGotApp->setAction('/'.Art_Router::getLayer().'/users/notGotApplication/$id');
		$requestNotGotApp->addUpdate('content', '.module_users_authorization');
		$this->view->requestNotGotApp = $requestNotGotApp;
		
		//Resend registration email by button
		$requestResendReg = Art_Ajax::newRequest(self::REQUEST_RESEND_REG);
		$requestResendReg->setAction('/'.Art_Router::getLayer().'/users/resendRegistrationMail/$id');
		$requestResendReg->addUpdate('content', '.module_users_authorization');
		$this->view->requestResendReg = $requestResendReg;
			
		//Resend authorization email by button
		$requestResendAuth = Art_Ajax::newRequest(self::REQUEST_RESEND_AUTH);
		$requestResendAuth->setAction('/'.Art_Router::getLayer().'/users/resendAuthorizationMail/$id');
		$requestResendAuth->addUpdate('content', '.module_users_authorization');
		$this->view->requestResendAuth = $requestResendAuth;
		
		//Delete user by button
		$requestDelete = Art_Ajax::newRequest(self::REQUEST_DELETE_USER); 
		$requestDelete->setAction('/'.Art_Router::getLayer().'/users/delete/$id'); 
		$requestDelete->setRefresh();
		$requestDelete->setConfirmWindow(__('module_users_delete_user_confirm')); 
		$this->view->requestDelete = $requestDelete;
	}
	
	function authorizefirmAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_AUTHORIZE))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$user = new Art_Model_User(Art_Router::getId());

			Helper_Default::getValidatedSQLData(array('salutation'), static::getSalutationValidator(), $data, $response);
			Helper_Default::getValidatedSQLData(User_Group_X_Service_Price::getCols('insert'), self::getAuthorizeFieldsValidators(), $data, $response);
			
			//Everything is valid
			if( $response->isValid() )
			{
				if ( $user->isLoaded() )
				{
					if( !$user->isPrivileged() )
					{
						$this->allowTo(Art_User::NO_ACCESS);
					}	

					$userData = $user->getData();
					
					//Authorization
					$userData->verif = 1;
					$userData->verif_date = dateSQL();
					$userData->verif_id = Art_User::getId();
					$userData->salutation = $data['salutation'];
					$userData->save();

					//Add to group Authorized
					$userUserGroup = new Art_Model_User_X_User_Group();
					$userUserGroup->id_user_group = Art_Model_User_Group::getAuthorizedId();
					$userUserGroup->setUser($user);
					$userUserGroup->save();

					$companyAddress = Helper_TBDev::getCompanyAddress($user);	/* @var $companyAddress Art_Model_Address */
					
					$membershipGroup = Helper_TBDev::GROUP_COMPANY.$companyAddress->company_name;
					
					$foundServicePrice = null;
					
					foreach ( Helper_TBDev::getAllServicePricesForUser($user) as $value) /* @var $value Service_Price */ 
					{
						if ( $value->getService()->type == Helper_TBDev::MEMBERSHIP_TYPE )
						{
							$foundServicePrice = $value->id;
							break;
						}
					}
			
					if ( NULL !== $foundServicePrice )
					{
						$foundGroup = null;
						
						foreach ($user->getGroups() as $value) /* @var $value Art_Model_User_X_User_Group */ 
						{
							$group = $value->getGroup();

							if ( $group->name == $membershipGroup )
							{
								$foundGroup = $group->id;
								break;
							}
						}

						if ( NULL !== $foundGroup )
						{
							$userGroupServicePrice = new User_Group_X_Service_Price(array('id_user_group'=>$foundGroup,'id_service_price'=>$foundServicePrice));
						}
					}
					
					if ( NULL !== $userGroupServicePrice && $userGroupServicePrice->isLoaded() )
					{
						$activatedDate = $userGroupServicePrice->time_from;
					}
					else
					{
						$activatedDate = dateSQL();
					}
					
					//Activate Membership
					$userService = new User_X_Service();
					$userService->setService(new Service(array('type'=>Helper_TBDev::MEMBERSHIP_TYPE)));
					$userService->setUser($user);
					$userService->activated = 1;
					$userService->activated_date = $activatedDate;
					$userService->save();
					
					$userInvitedBy = Helper_TBDev::getUserInvitedBy($user);
					
					$id_manager = Helper_TBDev::getManagerForUser($userInvitedBy)->id;

					$userManager = new User_X_Manager(array('id_user'=>$user->id));
					
					//Add Manager to User
					if ( NULL === $userManager )
					{		
						$userManager = new User_X_Manager();
						$userManager->setUser($user);
						$userManager->id_manager = $id_manager;
						$userManager->save();
					}
					else if ( $id_manager !== Art_User::getCurrentUser()->id )
					{
						$userManager->id_manager = Art_User::getCurrentUser()->id;
						$userManager->save();
					}

					//Get Ids
					$ids = $this->_getIdsFromCheckboxes(self::CHECKBOXES_SERVICE_PREFIX);			

					if ( !empty($ids) )
					{
						foreach ( $ids as $id )
						{
							$userUserGroup = new Art_Model_User_X_User_Group();
							$userUserGroup->id_user_group = $id;
							$userUserGroup->setUser($user);
							$userUserGroup->save();
						}
					}
					
					Helper_Email::sendAuthorizationMail($user);

					$response->addMessage(__('module_users_authorize_company_success'));
					$response->willRedirect();
				}
				else
				{
					$response->addAlert(__('module_users_authorize_company_not_found'));
				}
			}

			$response->addVariable('content', Art_Module::createAndRenderModule('users','authorization'));
			$response->execute();
		}
		else
		{						
			$this->view->days = Helper_TBDev::getDayRange();
			$this->view->months = Helper_TBDev::getMonthRange();
			$this->view->years = Helper_TBDev::getServiceYearRange();
			
			$services = Service::fetchAllPrivileged();

			foreach ( $services as $key => $value ) /* @var $value Service */ 
			{
				if ( $value->type === Helper_TBDev::MEMBERSHIP_TYPE )
				{
					unset($services[$key]);
					break;
				}
			}
			
			$this->view->services = $services;
			
			$where = new Art_Model_Db_Where(array('name'=>'name', 'value'=>Helper_TBDev::PROTECTED_GROUPS, 'relation'=>Art_Model_Db_Where::REL_IN));
			$groupsNotToShow = Helper_TBDev::getPropertyFromObjectsInArray(Art_Model_User_Group::fetchAllPrivileged($where),'id');
			
			$serviceGroups = Helper_TBDev::getPropertyFromObjectsInArray(Art_Model_User_Group::fetchAllPrivileged(
									new Art_Model_Db_Where(array('name'=>'name', 'value'=>'%service%', 'relation'=>Art_Model_Db_Where::REL_LIKE))), 'id');
			
			$groups = Art_Model_User_Group::fetchAllPrivileged();
			
			$groupServices = array();
			
			foreach ( $groups as $key => $value ) /* @var $value Art_Model_User_Group */ 
			{
				if ( in_array($value->id,$groupsNotToShow) )
				{
					unset($groups[$key]);
				}
				else if ( in_array($value->id,$serviceGroups) )
				{			
					$userGroupServicePrice = User_Group_X_Service_Price::fetchAllPrivileged(array('id_user_group'=>$value->id));
					if ( !empty($userGroupServicePrice) )
					{
						$value->service = $userGroupServicePrice[0]->getServicePrice()->getService();
					}
					$groupServices[] = $value;
					unset($groups[$key]);					
				}
			}
			
			$this->view->groups = $groups;
			
			$this->view->groupServices = $groupServices;
			
			$user = new Art_Model_User(Art_Router::getId());
			
			$userInvitedBy = Helper_TBDev::getUserInvitedBy($user);
			
			$this->view->servicesToBe = Helper_TBDev::getPropertyFromObjectsInArray(Helper_TBDev::getAllServicesForUser($userInvitedBy),'id');			
			
			$this->view->groupsToBe = array_diff(Helper_TBDev::getPropertyFromObjectsInArray(
										Art_Model_User_X_User_Group::fetchAllPrivileged(array('id_user'=>$userInvitedBy->id)),'id_user_group'), $groupsNotToShow);

			$this->view->membershipPrice = 1200;
			
			$this->view->rights = Art_Model_Rights::fetchAllNotHigher();
			
			$request = Art_Ajax::newRequest(self::REQUEST_AUTHORIZE);
			$request->setRedirect('/'.Art_Router::getLayer().'/users/authorization');
			$this->view->request = $request;
		}
	}
	
	function authorizeuserAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_AUTHORIZE))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
					
			$user = new Art_Model_User(Art_Router::getId());
	
			if ( $user->isLoaded() )
			{
				if( !$user->isPrivileged() )
				{
					$this->allowTo(Art_User::NO_ACCESS);
				}	
				
				Helper_Default::getValidatedSQLData(array('salutation'), static::getSalutationValidator(), $data, $response);
				
				if ($response->isValid())
				{
					$userData = $user->getData();

					//Authorization
					$userData->verif = 1;
					$userData->verif_date = dateSQL();
					$userData->verif_id = Art_User::getId();
					$userData->salutation = $data['salutation'];
					$userData->save();

					//Add to group Authorized
					$userUserGroup = new Art_Model_User_X_User_Group();
					$userUserGroup->id_user_group = Art_Model_User_Group::getAuthorizedId();
					$userUserGroup->setUser($user);
					$userUserGroup->save();

					//Add to group Membership
					$userUserGroup = new Art_Model_User_X_User_Group();
					$userUserGroup->setGroup(new Art_Model_User_Group(array('name'=>Helper_TBDev::MEMBERSHIP_MEMBERS_GROUP)));
					$userUserGroup->setUser($user);
					$userUserGroup->save();

					//Activate Membership
					$userService = new User_X_Service();
					$userService->setService(new Service(array('type'=>Helper_TBDev::MEMBERSHIP_TYPE)));
					$userService->setUser($user);
					$userService->activated = 1;
					$userService->activated_date = dateSQL();
					$userService->save();

					$userInvitedBy = Helper_TBDev::getUserInvitedBy($user);

					$id_manager = Helper_TBDev::getManagerForUser($userInvitedBy)->id;

					$userManager = new User_X_Manager(array('id_user'=>$user->id));
					
					//Add Manager to User
					if ( NULL === $userManager )
					{		
						$userManager = new User_X_Manager();
						$userManager->setUser($user);
						$userManager->id_manager = $id_manager;
						$userManager->save();
					}
					else if ( $id_manager !== Art_User::getCurrentUser()->id )
					{
						$userManager->id_manager = Art_User::getCurrentUser()->id;
						$userManager->save();
					}

					//Get Ids
					$ids = $this->_getIdsFromCheckboxes(self::CHECKBOXES_GROUP_PREFIX);			

					if ( !empty($ids) )
					{
						foreach ( $ids as $id )
						{
							$userUserGroup = new Art_Model_User_X_User_Group();
							$userUserGroup->id_user_group = $id;
							$userUserGroup->setUser($user);
							$userUserGroup->save();
						}
					}

					//Get Ids
					$ids = $this->_getIdsFromCheckboxes(self::CHECKBOXES_SERVICE_PREFIX);			

					if ( !empty($ids) )
					{
						foreach ( $ids as $id )
						{
							$userUserGroup = new Art_Model_User_X_User_Group();
							$userUserGroup->id_user_group = $id;
							$userUserGroup->setUser($user);
							$userUserGroup->save();
						}
					}

					Helper_Email::sendAuthorizationMail($user);

					$response->addVariable('content', Art_Module::createAndRenderModule('users','authorization'));
					
					$response->addMessage(__('module_users_authorize_success'));
					$response->willRedirect();
				}
			}
			else
			{
				$response->addAlert(__('module_users_authorize_not_found'));
			}
			
			$response->execute();
		}
		else
		{
			$services = Service::fetchAllPrivileged();
			
			$this->view->services = $services;
			
			$where = new Art_Model_Db_Where(array('name'=>'name', 'value'=>Helper_TBDev::PROTECTED_GROUPS, 'relation'=>Art_Model_Db_Where::REL_IN));
			$groupsNotToShow = Helper_TBDev::getPropertyFromObjectsInArray(Art_Model_User_Group::fetchAllPrivileged($where),'id');
			
			$serviceGroups = Helper_TBDev::getPropertyFromObjectsInArray(Art_Model_User_Group::fetchAllPrivileged(
									new Art_Model_Db_Where(array('name'=>'name', 'value'=>'%service%', 'relation'=>Art_Model_Db_Where::REL_LIKE))), 'id');
			
			$groups = Art_Model_User_Group::fetchAllPrivileged();
			
			$groupServices = array();
			
			foreach ( $groups as $key => $value ) /* @var $value Art_Model_User_Group */ 
			{
				if ( in_array($value->id,$groupsNotToShow) )
				{
					unset($groups[$key]);
				}
				else if ( in_array($value->id,$serviceGroups) )
				{			
					$userGroupServicePrice = User_Group_X_Service_Price::fetchAllPrivileged(array('id_user_group'=>$value->id));
					if ( !empty($userGroupServicePrice) )
					{
						$value->service = $userGroupServicePrice[0]->getServicePrice()->getService();
					}
					$groupServices[] = $value;
					unset($groups[$key]);					
				}
			}
			
			$this->view->groups = $groups;
			
			$this->view->groupServices = $groupServices;
			
			$user = new Art_Model_User(Art_Router::getId());
			
			$userInvitedBy = Helper_TBDev::getUserInvitedBy($user);
			
			$this->view->servicesToBe = Helper_TBDev::getPropertyFromObjectsInArray(Helper_TBDev::getAllServicesForUser($userInvitedBy),'id');			
			
			$this->view->groupsToBe = array_diff(Helper_TBDev::getPropertyFromObjectsInArray(
										Art_Model_User_X_User_Group::fetchAllPrivileged(array('id_user'=>$userInvitedBy->id)),'id_user_group'), $groupsNotToShow);

			$this->view->membershipPrice = 1200;
			
			$request = Art_Ajax::newRequest(self::REQUEST_AUTHORIZE);
			$request->setRedirect('/'.Art_Router::getLayer().'/users/authorization');
			$this->view->request = $request;
		}
	}
	
	function completefirmregistrationAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_COMPLETE_FIRM_REGISTRATION))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();

			$fields = array('membership_fee','from_day','from_month','from_year','to_day','to_month','to_year');
										
			Helper_Default::getValidatedSQLData($fields, self::getCompleteFirmRegValidators(), $data, $response);
			
			$user = new Art_Model_User(Art_Router::getId());
			
			//Everything is valid
			if( $response->isValid() )
			{
				if ( $user->isLoaded() )
				{
					if( !$user->isPrivileged() )
					{
						$this->allowTo(Art_User::NO_ACCESS);
					}	
				
					$userData = $user->getData();
					
					$representant = Helper_TBDev::getCompanyRepresentant($user);
					$representantDeliveryAddress = Helper_TBDev::getDeliveryAddress($representant);
					$companyAddress = Helper_TBDev::getCompanyAddress($user);	/* @var $companyAddress Art_Model_Address */
					
					$deliveryAddress = Helper_TBDev::getDeliveryAddress($user);	/* @var $deliveryAddress Art_Model_Address */
					
					//Create group for company
					$userGroup = new Art_Model_User_Group();
					$userGroup->id_rights = 2;
					$userGroup->name = Helper_TBDev::GROUP_COMPANY.$companyAddress->company_name;
					$userGroup->description = null;
					$userGroup->save();

					$userUserGroup = new Art_Model_User_X_User_Group();
					$userUserGroup->setGroup($userGroup);
					$userUserGroup->setUser($user);
					$userUserGroup->save();
					
					$memService = new Service(array('type'=>Helper_TBDev::MEMBERSHIP_TYPE));
					
					$servicePrice = new Service_Price(array('price'=>$data['membership_fee'],'time_interval'=>'1r','id_service'=>$memService->id));
					
					if ( NULL === $servicePrice || !$servicePrice->isLoaded() )
					{
						$servicePrice = new Service_Price();
						$servicePrice->price = $data['membership_fee'];
						$servicePrice->time_interval = '1r';
						$servicePrice->setService($memService);
						$servicePrice->save();
					}
					
					$from = Helper_TBDev::getDate($data["from_year"], $data["from_month"], $data["from_day"]);
					$to = Helper_TBDev::getDate($data["to_year"], $data["to_month"], $data["to_day"]);

					$userGroupServicePrice = new User_Group_X_Service_Price();
					$userGroupServicePrice->setUserGroup($userGroup);
					$userGroupServicePrice->setServicePrice($servicePrice);
					$userGroupServicePrice->time_from = dateSQL($from);
					$userGroupServicePrice->time_to = dateSQL($to);
					$userGroupServicePrice->save();
					
					//Create registration document
					$resource = Helper_TBDev_PDF::registrationDocForCompany(
							$companyAddress->company_name,
							$user->user_number,
							$companyAddress->ico,
							$companyAddress->stringify,
							$representantDeliveryAddress->stringify,
							$representant->getData()->fullnameWithDegree,
							Helper_TBDev::getUserFunctionInCompany($user, $representant),
							$userData->email,
							Helper_TBDev::getTelephoneForUser($user),
							$userData->fullnameWithDegree.', '.$deliveryAddress->stringify,
							$data['membership_fee']
							);

					$url = Art_Server::getHost().'/resource/'.$resource->hash;

					//Send registration mail
					Helper_Email::sendRegistrationMail($userData->email, $url);

					$response->addMessage(__('module_users_complete_firm_registration_success'));
					$response->willRedirect();
				}
				else
				{
					$response->addAlert(__('module_users_complete_firm_registration_not_found'));
				}
			}

			$response->addVariable('content', Art_Module::createAndRenderModule('users','authorization'));
			$response->execute();
		}
		else
		{
			$this->view->days = Helper_TBDev::getDayRange();
			$this->view->months = Helper_TBDev::getMonthRange();
			$this->view->years = Helper_TBDev::getServiceYearRange();

			$this->view->currentDay = Helper_TBDev::getCurrentDay();
			$this->view->currentMonth = Helper_TBDev::getCurrentMonth();
			$this->view->currentYear = Helper_TBDev::getCurrentYear();
			$this->view->nextYear = $this->view->currentYear + 1;
			
			$request = Art_Ajax::newRequest(self::REQUEST_COMPLETE_FIRM_REGISTRATION);
			$request->setRedirect('/'.Art_Router::getLayer().'/users/authorization');
			$this->view->request = $request;
		}
	}
	
	function embeddAction() {		
		$user = Art_User::getCurrentUser();

		$this->view->user = $user;
		$this->view->user->isManager = intVal($user->getRights()) === 20 ? true : false;
		$this->view->user->isSupervisor = intVal($user->getRights()) === 50 ? true : false;
		$this->view->user->gender = intVal($user->getData()->gender) === 1 ? "male" : "female";
	}

	function sendMailAction() 
	{	
		if( Art_Ajax::isRequestedBy(self::REQUEST_SEND_SELECTED) )
		{	
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			//Get Ids
			$ids = $this->_getIdsFromCheckboxes(self::CHECKBOXES_USER_PREFIX);
	
			if ( empty($ids) )
			{
				$response->addAlert(__('module_users_send_mail_none_user'));
			}
			
			$emails = array();
			
			//Get emails from users id
			foreach($ids AS $id)
			{
				$user = new Art_Model_User($id);
				
				$emails[] = $user->getData()->email;
			}
		
			$fields_validators = self::getMailFieldsValidators();
			$fields = array('body', 'subject');
					
			//Validate all fields
			foreach($fields AS $field_name)
			{				
				//If validator is set
				if( isset($fields_validators[$field_name]) )
				{
					$response->validateField($field_name, $data[$field_name], $fields_validators[$field_name]);
				}
			}
			
			if ( $response->isValid() )
			{
				//Send mail
				Helper_Email::sendMail($emails, $data['subject'], $data['body']);

				$response->addMessage(__('module_users_send_mail_success'));
			}
			
			$response->execute();
		}
		else
		{
			$sortById = Art_Router::getFromURI('id');
			$sortByFirstname = Art_Router::getFromURI('firstname');
			$sortBySurname = Art_Router::getFromURI('surname');
			$sortByMembershipFrom = Art_Router::getFromURI('membership_from');
			$sortByMembershipTo = Art_Router::getFromURI('membership_to');

			$this->view->sortBy = $sortBy = Helper_TBDev::getSortBy($sortById, $sortByFirstname, $sortBySurname, $sortByMembershipFrom, $sortByMembershipTo);
			
			$authenticatedUsersData = $this->_getAllAuthenticatedUserDataForTable($sortBy);
			
			$activeUsers = array();
		
			$nonactiveUsers = array();

			foreach ( $authenticatedUsersData as $value ) /* @var $value Art_Model_User_Data */
			{		
				if ( $value->getUser()->active )
				{
					$activeUsers[] = $value;
				}
				else
				{
					$nonactiveUsers[] = $value;
				}
			}

			$allUsers = array_merge($activeUsers,$nonactiveUsers);

			$this->view->usersData = $allUsers;

			$this->view->count = count($allUsers);

			//Send to all selected users by selected checkboxes
			$send_request = Art_Ajax::newRequest(self::REQUEST_SEND_SELECTED);
			$this->view->send_request = $send_request;
		}
	}
	
	function sendimportAction() 
	{	
		if( Art_Ajax::isRequestedBy(self::REQUEST_SEND_SELECTED) )
		{	
			$response = Art_Ajax::newResponse();
			
			//Get Ids
			$ids = $this->_getIdsFromCheckboxes(self::CHECKBOXES_USER_PREFIX);
	
			if ( empty($ids) )
			{
				$response->addAlert(__('module_users_send_mail_none_user'));
			}
			
			$users = array();
			
			//Get emails from users id
			foreach($ids AS $id)
			{
				$users[] = $id;
			}
			
			if ( $response->isValid() )
			{
				//Send mail
				Helper_TBDev::sendImportMail($users);

				$response->addMessage(__('module_users_send_mail_success'));
			}
			
			$response->execute();
		}
		else
		{
			$users = $this->_getAllAuthenticatedUserDataForTable(-1);
			
			$this->view->usersData = $users;
			$this->view->count = count($users);

			//Send to all selected users by selected checkboxes
			$send_request = Art_Ajax::newRequest(self::REQUEST_SEND_SELECTED);
			$this->view->send_request = $send_request;
		}
	}
	
	function resendAuthorizationMailAction()
	{		 
		if(Art_Ajax::isRequestedBy(self::REQUEST_RESEND_AUTH))
		{
			$response = Art_Ajax::newResponse();
			
			$user = new Art_Model_User(Art_Router::getId());

			if( $user->isLoaded() )
			{			
				if ( $time_rem = $this->_timeRemaining( static::SESSION_AUTH_MAIL.$user->id ) )
				{
					$response->addAlert(sprintf(__('module_users_spam_alert'),$time_rem));
				}
				
				if ( $response->isValid() )
				{
					Helper_Email::sendAuthorizationMail($user);

					$response->addMessage(__('module_users_resend_auth_email_success'));

					$response->addVariable('content', Art_Module::createAndRenderModule('users','authorization'));
					
					Art_Session::set(self::SESSION_AUTH_MAIL.$user->id, time());
				}
			}
			else
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
							
			$response->execute();
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}			
	
	function resendRegistrationMailAction() {
		if (Art_Ajax::isRequestedBy(self::REQUEST_RESEND_REG)) {
			
			$response = Art_Ajax::newResponse();
			$user = new Art_Model_User(Art_Router::getId());

			if ($user->isLoaded()) {

				$userData = $user->getData();
				$resource = Art_Model_Resource_Db::fetchAll(array('name' => Helper_TBDev_PDF::RESOURCE_CONTRACT.$user->user_number.Helper_TBDev_PDF::RESOURCE_EXT_PDF));
				
				if (empty($resource)) {
					$response->addAlert(__('module_users_resend_reg_email_error'));
				}

				if ($time_rem = $this->_timeRemaining(static::SESSION_REG_MAIL.$user->id)) {
					$response->addAlert(sprintf(__('module_users_spam_alert'), $time_rem));
				}

				if ($response->isValid()) {

					$resource = $resource[0];
					$url = Art_Server::getHost().'/resource/'.$resource->hash;
					Helper_Email::sendRegistrationMail($userData->email, $url);
					$response->addMessage(__('module_users_resend_reg_email_success'));
					$response->addVariable('content', Art_Module::createAndRenderModule('users', 'authorization'));
					
					Art_Session::set(self::SESSION_REG_MAIL.$user->id, time());
				}
				
				$response->execute();
			}
			else {
				$this->showTo(Art_User::NO_ACCESS);
			}
		}
		else {
			$this->showTo(Art_User::NO_ACCESS);
		}
	}		

	function gotApplicationAction()
	{		
		if(Art_Ajax::isRequestedBy(self::REQUEST_GOT_APPLICATION))
		{
			$response = Art_Ajax::newResponse();
			
			$user = new Art_Model_User(Art_Router::getId());

			if( $user->isLoaded() )
			{
				$userData = $user->getData();
		
				Helper_Email::sendGotApplicationMail($userData->email, $user->user_number, 1200);
				
				$userEmail = new User_X_Email();
				$userEmail->setUser($user);
				$userEmail->email_type = Helper_TBDev::EMAIL_TYPE_GOT_APP;
				$userEmail->save();
				
				$response->addMessage(__('module_users_got_application_sent'));
				
				$response->addVariable('content', Art_Module::createAndRenderModule('users','authorization'));
				
				$response->execute();
			}
			else
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function gotApplicationCompanyAction()
	{		
		if(Art_Ajax::isRequestedBy(self::REQUEST_GOT_APPLICATION_COMPANY))
		{
			$response = Art_Ajax::newResponse();
			
			$user = new Art_Model_User(Art_Router::getId());

			if( $user->isLoaded() )
			{
				$userData = $user->getData();

				$servicePrice = Helper_TBDev::getAllServicePricesForUser($user);

				if ( !empty($servicePrice) )
				{
					$value = $servicePrice[0]->price;
				}
				else 					
				{
					$value = '-';
				}
	
				Helper_Email::sendGotApplicationCompanyMail($userData->email, $user->user_number, $value);
							
				$userEmail = new User_X_Email();
				$userEmail->setUser($user);
				$userEmail->email_type = Helper_TBDev::EMAIL_TYPE_GOT_APP;
				$userEmail->save();
				
				$response->addMessage(__('module_users_got_application_sent'));
				
				$response->addVariable('content', Art_Module::createAndRenderModule('users','authorization'));
				
				$response->execute();
			}
			else
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function notGotApplicationAction()
	{		
		if(Art_Ajax::isRequestedBy(self::REQUEST_NOT_GOT_APPLICATION))
		{
			$response = Art_Ajax::newResponse();
			
			$user = new Art_Model_User(Art_Router::getId());

			if( $user->isLoaded() )
			{
				$userData = $user->getData();
		
				$pdf = Art_Model_Resource_Db::fetchAll(array('name'=>Helper_TBDev_PDF::RESOURCE_CONTRACT.$user->user_number.Helper_TBDev_PDF::RESOURCE_EXT_PDF));
				
				if ( !empty($pdf) )
				{
					Helper_Email::sendNotGotApplicationMail($userData->email,$user->created_date,$pdf[0]->hash);
						
					$userEmail = new User_X_Email();
					$userEmail->setUser($user);
					$userEmail->email_type = Helper_TBDev::EMAIL_TYPE_NOT_GOT_APP;
					$userEmail->save();

					$response->addVariable('content', Art_Module::createAndRenderModule('users','authorization'));
					
					$response->addMessage(__('module_users_not_got_application_sent'));
				}
				else
				{
					$response->addAlert(__('module_users_not_got_application_error'));
				}
				
				$response->execute();
			}
			else
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}	
	
	function unmemberUserAction()
	{
  		if( Art_Ajax::isRequestedBy(self::REQUEST_UNMEMBER_USER) )
  		{
  			$response = Art_Ajax::newResponse();			
  
			$user = new Art_Model_User(Art_Router::getId());

			if( $user->isLoaded() )
  			{
  				if( !$user->isPrivileged() )
  				{
  					$this->allowTo(Art_User::NO_ACCESS);
  				}

				$user->active = 0;	
				$user->save();
  
				if ( Helper_TBDev::isUserAuthenticated($user) )
				{
					Helper_Email::sendUnmemberMail($user);
				}
				
				$response->addMessage(__('module_users_unmember_success'));
				
				$response->willRedirect();
			}
  			else
 			{
				$response->addAlert(__('module_users_unmember_not_found'));
  			}
 
			$response->execute();
  		}
  		else
  		{
  			$this->showTo(Art_User::NO_ACCESS);
  		}
  	}
	
	function deleteAction() {
  		if (Art_Ajax::isRequestedBy(self::REQUEST_DELETE_USER)) {

  			$response = Art_Ajax::newResponse();			
			$user = new Art_Model_User(Art_Router::getId());

			if ($user->isLoaded()) {
				if (!$user->isPrivileged()) {
  					$this->allowTo(Art_User::NO_ACCESS);
  				}

				if (	!empty(Service_Payment::fetchAll(array('id_user'=>$user->id))) || 	
						!empty(Service_Payment::fetchAll(array('id_user_paid_by'=>$user->id)))) {
					$response->addAlert(__('module_users_delete_contained_in_payments'));
				} 
				elseif (!empty(Helper_TBDev::getAllInvitedUsers($user))) {
					$response->addAlert(__('module_users_delete_contained_invited_users'));
				}
				else  {
					if (Helper_TBDev::isUserAuthenticated($user)) {
						Helper_Email::sendUnmemberMail($user);
					} else {
						Helper_Email::sendTerminateApplicationMail($user);
					}

					foreach ( Art_Model_User_X_User_Group::fetchAll(array('id_user' => $user->id)) as $userUserGroup) {
						$userUserGroup->delete();
					}
					
					foreach (User_X_Invite_Code::fetchAll(array('id_user' => $user->id)) as $userInvCode) {
						$userInvCode->delete();
					}
					
					foreach (Invite_Code::fetchAll(array('id_user' => $user->id)) as $invCode) {
						$invCode->delete();
					}
					
					foreach (User_X_Service::fetchAll(array('id_user' => $user->id)) as $userService) {
						$userService->delete();
					}
					
					foreach (Service_Investment_Value::fetchAll(array('id_user' => $user->id)) as $investment) {
						$investment->delete();
					}
					
					foreach (Service_Investment_Deposit::fetchAll(array('id_user' => $user->id)) as $investmentDeposit) {
						$investmentDeposit->delete();
					}
					
					foreach (User_X_Request::fetchAll(array('id_user' => $user->id)) as $request) {
						$request->delete();
					}
					
					if (Helper_TBDev::isUserRepresentsCompany($user)) {
						$representant = Helper_TBDev::getCompanyRepresentant($user);						
						if ($representant->id !== $user->id) {
							foreach ($representant->getAddresses() as $value) {
								$value->delete();
							}

							foreach ($representant->getGroups() as $value) {
								$value->delete();
							}
							$representant->getData()->delete();
							$representant->delete();
						}
					}
					
					foreach (User_X_Company::fetchAll(array('id_user' => $user->id)) as $userCompany) {
						$userCompany->delete();
					}
					
					foreach (User_X_Email::fetchAll(array('id_user' => $user->id)) as $userEmail) {
						$userEmail->delete();
					}
					
					foreach (User_X_Request::fetchAll(array('id_user' => $user->id)) as $userRequest) {
						$userRequest->delete();
					}
					
					(new User_X_Manager(array('id_user' => $user->id)))->delete();
					
					foreach ($user->getAddresses() as $value) {
						$value->delete();
					}
					
					$resource =  new Art_Model_Resource_Db(Helper_TBDev_PDF::RESOURCE_CONTRACT.$user->user_number.Helper_TBDev_PDF::RESOURCE_EXT_PDF);
					if ( NULL !== $resource && $resource->isLoaded()) {
						unlink($resource->path);
						$resource->delete();
					}
					
					$user->getData()->delete();
					$user->delete();
					
					$response->addMessage(__('module_users_delete_success'));
				}
			}
  			else {
				$response->addAlert(__('module_users_delete_not_found'));
  			}
			
			$response->willRedirect();
			$response->execute();
  		}
  		else {
  			$this->showTo(Art_User::NO_ACCESS);
  		}
  	}

	function requestsAction ()
	{	//TODO
		$userRequests = User_X_Request::fetchAllPrivileged(null, array(
			'name' => 'created_date',
			'type' => Art_Model_Db_Order::TYPE_DESC)
		);

		foreach ($userRequests as $value) /* @var $value User_X_Request */ 
		{
			$user = $value->getUser();
			$value->user = $user;
			$value->userData = $user->getData();
			$value->userPhone = Helper_TBDev::getTelephoneForUser($user);
			
			$service = $value->getService();
			$value->service = $service;
			
			$value->a_detail = '/'.Art_Router::getLayer().'/users/detail/'.$user->id;
			$value->a_sendEmail = '/'.Art_Router::getLayer().'/users/sendRequestEmail/'.$value->id;
			
			$value->servicePrice = Helper_TBDev::getMinimalServicePriceForServiceForUser($user, $service);	
		}
		
		$this->view->userRequests = $userRequests;
		
		//Delete request by button
		$requestDelete = Art_Ajax::newRequest(self::REQUEST_DELETE_REQUEST); 
		$requestDelete->setAction('/'.Art_Router::getLayer().'/users/deleteRequest/$id'); 
		$requestDelete->setRefresh();
		$requestDelete->setConfirmWindow(__('module_users_request_delete_request_confirm')); 
		$this->view->requestDelete = $requestDelete;
	}

	function sendrequestemailAction()
	{	
		if( Art_Ajax::isRequestedBy(self::REQUEST_SEND_REQUEST) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$fields_validators = self::getMailFieldsValidators();
			$fields = array('body', 'subject');
					
			//Validate all fields
			foreach($fields AS $field_name)
			{				
				//If validator is set
				if( isset($fields_validators[$field_name]) )
				{
					$response->validateField($field_name, $data[$field_name], $fields_validators[$field_name]);
				}
			}
			
			if ( $response->isValid() )
			{
				$email = $data['email'];
				
				//Send mail
				Helper_Email::sendMail($email, $data['subject'], $data['body']);

				$response->addMessage(__('module_users_send_mail_success'));
			}
  			
			$response->execute();
		}
		else
		{
			$userRequest = new User_X_Request(Art_Router::getId());

			$user = $userRequest->getUser();
			$userData = $user->getData();
			$service = $userRequest->getService();
			$userPhone = Helper_TBDev::getTelephoneForUser($user);

			$this->view->userRequest = $userRequest;
			$this->view->userData = $userData;
			$this->view->userPhone = $userPhone;
			$this->view->service = $service;
			
			$this->view->email = $userData->email;

			$sendRequest = Art_Ajax::newRequest(self::REQUEST_SEND_REQUEST);
			$this->view->sendRequest = $sendRequest;
		}
	}
	
	function deleteRequestAction()
	{
  		if( Art_Ajax::isRequestedBy(self::REQUEST_DELETE_REQUEST) )
  		{
  			$response = Art_Ajax::newResponse();			
  
			$userRequest = new User_X_Request(Art_Router::getId());

			if( $userRequest->isLoaded() )
  			{
  				if( !$userRequest->isPrivileged() )
  				{
  					$this->allowTo(Art_User::NO_ACCESS);
  				}

				$userRequest->delete();
				
				$response->addMessage(__('module_users_request_delete_success'));
			}
  			else
 			{
				$response->addAlert(__('module_users_request_delete_not_found'));
  			}
			
			$response->willRedirect();
			$response->execute();
  		}
  		else
  		{
  			$this->showTo(Art_User::NO_ACCESS);
  		}
  	}
	
	function changemanagerAction()
	{
  		if( Art_Ajax::isRequestedBy(self::REQUEST_CHANGE_MANAGER) )
  		{
  			$response = Art_Ajax::newResponse();			
			$data = Art_Ajax::getData();
			
			$userManager = new User_X_Manager(array('id_user'=>Art_Router::getId()));

			if( $userManager->isLoaded() )
  			{
  				if( !$userManager->isPrivileged() )
  				{
  					$this->allowTo(Art_User::NO_ACCESS);
  				}

				$userManager->id_manager = $data['id_manager'];
				$userManager->save();
				
				$response->addMessage(__('module_users_change_manager_success'));
			}
  			else
 			{
				$response->addAlert(__('module_users_change_manager_not_found'));
  			}
			
			$response->willRedirect();
			$response->execute();
  		}
  		else
  		{
  			$user = new Art_Model_User(Art_Router::getId());
			
			$userManager = new User_X_Manager(array('id_user'=>Art_Router::getId()));
			
			$managers = array();
			
			foreach (Art_Model_User_X_User_Group::fetchAllPrivileged(array('id_user_group'=>(new Art_Model_User_Group(array('name'=>Helper_TBDev::MANAGER_GROUP)))->id)) as $value) 
				/* @var $value Art_Model_User_X_User_Group */ 
			{
				$managers[] = $value->getUser();
			}
			
			if ( Helper_TBDev::isUserRepresentsCompany($user) )
			{
				$user->fullname = Helper_TBDev::getCompanyAddress($user)->company_name;
			}
			
			$this->view->user = $user;		
			$this->view->userManager = $userManager;
			$this->view->managers = $managers;
			
			//Change manager request
			$request = Art_Ajax::newRequest(self::REQUEST_CHANGE_MANAGER);
			$request->setRedirect('/'.Art_Router::getLayer().'/users/detail/'.Art_Router::getId());
			$this->view->request = $request;
		}
  	}
	
	function invCodesAction() {
		$codes = Invite_Code::fetchAllPrivileged();
		
		foreach ($codes as $value){
			$value->URL = Helper_TBDev::getInvitedCodeURL($value);

			$userData = new Art_Model_User_Data(array('id_user' => $value->id_user));
			$value->fullname = Art_Model_User_Data::getFullname($userData);
			
			$userData = new Art_Model_User_Data(array('id_user' => $value->created_by));
			$value->gen_by_fullname = Art_Model_User_Data::getFullname($userData);
		}
		
		$this->view->codes = $codes;
		
		$usersData = Art_Model_User_Data::fetchAllPrivileged();
		
		$this->view->usersData = Helper_TBDev::getSortedArray($usersData, 'surname');
		
		//Add invite code for user by button
		$add_invite_code_request = Art_Ajax::newRequest(self::REQUEST_ADD_INV_CODE); 
		$add_invite_code_request->setAction('/'.Art_Router::getLayer().'/users/genInvCode'); 
		$add_invite_code_request->addUpdate('content', '.module_users_invcodes');
		$this->view->add_invite_code_request = $add_invite_code_request;
	}
	
	function genInvCodeAction ()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ADD_INV_CODE) )
		{	
			$response = Art_Ajax::newResponse();			
			$data = Art_Ajax::getData();
			
			$codes = Invite_Code::fetchAllPrivileged(array('id_user'=>$data['user-id']));

			foreach ($codes as $key => $value) /* @var $value Invite_Code */ 
			{
				if ( $value->created_by === $data['user-id'] )
				{
					unset($codes[$key]);
				}
			}
			
			$fields_validators = self::getCodeFieldsValidators();
			$field_name = 'note';
					
			//Validate field
			if( isset($fields_validators[$field_name]) )
			{
				$response->validateField($field_name, $data[$field_name], $fields_validators[$field_name]);
			}
			
			if ( $response->isValid() )
			{
				if ( count($codes) < Helper_Default::getDefaultValue(Helper_TBDev::MAX_INV_CODES_PER_USER) )
				{					
					$inviteCode = new Invite_Code();
					$inviteCode->id_user = $data['user-id'];
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

	function serviceAction()
	{
		$separator = strpos(Art_Router::getId(), '-');
		
		if ( false !== $separator )
		{
			$user = new Art_Model_User(substr(Art_Router::getId(),0,$separator+1));
			$service = new Service(substr(Art_Router::getId(),$separator+1));

			if ( $user->isLoaded() && $service->isLoaded() && in_array($service->id,Helper_TBDev::getPropertyFromObjectsInArray(Helper_TBDev::getAllServicesForUser($user),'id')) )
			{
				$this->view->user = $user; 

				$this->view->data = $user->getData();
				
				$this->view->phone = Helper_TBDev::getTelephoneForUser($user);

				$service->active_from = Helper_TBDev::getServiceFromForUser($user,$service);
				
				$service->active_from = ((0 < $service->active_from) && $service->active_from) ? $service->active_from : '-';
				
				$service->active_to = Helper_TBDev::getServiceToForUser($user,$service);

				$service->active_to = ((0 < $service->active_to) && $service->active_to) ? Helper_TBDev::renderTrueFalseDateTo(dateSQL($service->active_to) < dateSQL(), $service->active_to) : '-';
				
				$servicePrice = Helper_TBDev::getMinimalServicePriceForServiceForUser($user, $service);
				
				if ( NULL !== $servicePrice )
				{
					$service->actual_price = $servicePrice->price;

					$service->time_interval = $servicePrice->time_interval;
				}
				else
				{
					$service->actual_price = null;

					$service->time_interval = null;
				}
				
				$this->view->service = $service;
				
				$payments = Helper_TBDev::getServicePaymentsForUser($user, $service);
			
				$this->view->payments = $payments;
				
				$isInvestment = false;
				
				if ( Helper_TBDev::INVESTMENT_TYPE == $service->type && Helper_TBDev::isServiceActivatedForUser($service, $user) )
				{	
					$investmentValues = array();
					
					foreach (Service_Investment_Value::fetchAllPrivileged(array('id_user'=>$user->id)) as $value) /* @var $value Service_Investment_Value */ 
					{
						$value->investment = $value->getService_investment();
						$investmentValues[] = $value;
					}
					
					$this->view->investmentValues = $investmentValues;
					
					$this->view->deposits = Service_Investment_Deposit::fetchAllPrivileged(array('id_user'=>$user->id));
					$isInvestment = true;
				}
				
				$this->view->isInvestment = $isInvestment;
			}
			else
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	
	/**
	 *	Filter Users by filter data
	 * 
	 *  @access protected
	 *	@return Art_Model_Db_Where
	 */	
	protected function _filterUsers()
	{
		$data = Art_Main::getPost();
		
		//Filter is active and send data via POST
		if ( NULL === $data )
		{		
			return null;
		}

		$where = new Art_Model_Db_Where();

		foreach ( $data as $key => $value )
		{
			if ( !empty($value) && ($key == "name" || $key == "surname") )
			{
				$where->add(array('name'=>$key, 'value'=>'%'.$value.'%', 'relation'=>Art_Model_Db_Where::REL_LIKE));
			}
		}

		return $where;
	}

	/**
	 *	Get all authenticated users data for table output
	 *	@param int		$sortBy
	 *	@param boolean $onlyCompany
	 * 
	 *  @access protected
	 *	@return Art_Model_User_Data[]
	 */	
	protected function _getAllAuthenticatedUserDataForTable($sortBy, $onlyCompany = false) {
		//Filter Users by Name, Surname and Service
		$where = $this->_filterUsers();
		$serviceFilterSet = false;
		$data = Art_Main::getPost();
		
		if (!empty($data) && isset($data['service-type']) && $data['service-type'] != 0) {
			$serviceFilterSet = true;
			$usersIdWithService = Helper_TBDev::getPropertyFromObjectsInArray(Helper_TBDev::getAllUsersForActivatedService(new Service($data['service-type'])),'id');
		}
		//~Filter

		//Get all user data restricted by filter
		$usersData = Art_Model_User_Data::fetchAllPrivileged($where);

		$authenticatedUsers = array();	

		//For every User add properties and filter out not authenticated users
		foreach ($usersData as $value) /* @var $value Art_Model_User_Data */ {
			$user = $value->getUser();

			if (!Helper_TBDev::isUserAuthenticated($user)) {
				continue;
			}

			if ((Helper_TBDev::isUserRepresentsCompany($user) && !$onlyCompany) ||
				(!Helper_TBDev::isUserRepresentsCompany($user) && $onlyCompany) ) {
				continue;
			}
			
			//Filter by Service
			if ($serviceFilterSet && !in_array($user->id, $usersIdWithService)) {
				continue;
			}

			$value->p_userId = $user->id;
			$value->user_number = $user->user_number;
			
			$value->p_phone = Helper_TBDev::getTelephoneForUser($user);
			$services = Helper_TBDev::getAllServicesForUser($user);

			foreach ($services as $service)  {
				$settings = json_decode($service->settings, true);

				$service->fa_icon = $settings["icon"];
			}

			$value->services = $services;

			$activatedServices = Helper_TBDev::getAllActivatedServicesForUser($user);
			$actServices = array();
			foreach ($activatedServices as $service) {
				$actServices[] = $service->type;
			}

			$value->actServices = $actServices;
			$value->a_service = '/' . Art_Router::getLayer() . '/users/service/' . $user->id . '-';
			$membership_from = Helper_TBDev::getMembershipFromForUser($user);
			$value->membership_from = !is_null($membership_from) ? nice_date($membership_from) : '';

			if (NULL === $value->membership_from) {
				$value->membership_from = null; 
				$value->membership_to = null;
				$value->membership_to_colored = null;
			} else {	
				$value->membership_to = Helper_TBDev::getMembershipToForUser($user);
				// p($value->membership_to);
				if ($value->membership_to) {
					$value->membership_to_colored = Helper_TBDev::renderTrueFalseDateTo(dateSQL($value->membership_to) < dateSQL(), nice_date($value->membership_to));
				}
			}
			// p($value->membership_to);

			if ($onlyCompany && Helper_TBDev::isUserRepresentsCompany($user)) {
				$value->company_name = Helper_TBDev::getCompanyAddress($user)->company_name;
				$value->a_edit = '/'.Art_Router::getLayer().'/users/editcompany/'.$user->id;
				$value->a_detail = '/'.Art_Router::getLayer().'/users/detailcompany/'.$user->id;
			} else {
				$value->a_edit = '/'.Art_Router::getLayer().'/users/edit/'.$user->id;
				$value->a_detail = '/'.Art_Router::getLayer().'/users/detail/'.$user->id;
			}				
		
			$authenticatedUsers[] = $value;
		}

		if (-1 !== $sortBy) {
			// p($sortBy);
			switch ($sortBy) {
				case 0: $param = 'id'; break;
				case 1:	$param = 'idR'; break;
				case 2: $param = 'firstname'; break;
				case 3:	$param = 'firstnameR'; break;
				case 4:	$param = 'surname'; break;
				case 5:	$param = 'surnameR'; break;
				case 6:	$param = 'membership_from'; break;
				case 7:	$param = 'membership_fromR'; break;
				case 8:	$param = 'membership_to'; break;
				case 9:	$param = 'membership_toR'; break;	
				case 10: $param = 'company_name'; break;
				case 11: $param = 'company_nameR'; break;
			}

			$authenticatedUsers = Helper_TBDev::getSortedArray($authenticatedUsers, $param);
		}
		
		return $authenticatedUsers;
	}
	
	
	/**
	 *	Returns 0 if not spam, number if spam (number of minutes till next allowed sent)
	 */
	protected function _timeRemaining( $sessionName )
	{
		$time_rem = 0;
		
		if( Art_Session::get($sessionName) )
		{
			$time_mnts = ( time() - Art_Session::get($sessionName) ) / 60;

			if( $time_mnts < 2 )
			{
				$time_rem = 2 - floor($time_mnts);
			}
		}

		return $time_rem;
	}
	
	/**
	 *	Load AJAX data and get ids from checkboxes
	 * 
	 *	@access protected
	 *	@param string $prefix
	 *	@return array
	 */
	protected function _getIdsFromCheckboxes($prefix)
	{
		$data = Art_Ajax::getData();
		$prefix_length = strlen($prefix);
		
		$ids = array();
		foreach($data AS $item => $st)
		{
			if( strpos($item,$prefix) === 0 )
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
	
	static function getUserDataFieldsValidators( $prefix = null )
	{
		return	array(
	$prefix.'degree'			=> array( 
		Art_Validator::MAX_LENGTH => ['value' => 20,'message'	=> __('module_registration_v_degree_max')]),
	$prefix.'name'				=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_name_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_name_max')]),
	$prefix.'surname'			=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_surname_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_surname_max')]),
	$prefix.'gender'			=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_gender_not_integer')]),
	$prefix.'born_day'			=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_born_day_not_integer')]),
	$prefix.'born_month'		=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_born_month_not_integer')]),
	$prefix.'born_year'			=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_born_year_not_integer')]),
	$prefix.'reg-code'			=> array(
		Art_Validator::MIN_LENGTH => ['value' => 8,'message'	=> __('module_registration_v_reg_code_min')]),
			);
	}
		
	static function getSalutationValidator()
	{
		return array(
	'salutation'			=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_salutation_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_salutation_max')]),	
		);
	}
	
	static function getDeliveryFieldsValidators( $prefix = 'delivery-' )
	{
		return	array(
	$prefix.'country'	=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_delivery_country_not_integer')]),
	$prefix.'city'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_delivery_city_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_delivery_city_max')]),
	$prefix.'street'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_delivery_street_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_delivery_street_max')]),
	$prefix.'housenum'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_delivery_housenum_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_delivery_housenum_max')]),
	$prefix.'zip'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_delivery_zip_min')],
		Art_Validator::MAX_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_delivery_zip_max')]),
	$prefix.'area_code'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 2,'message'	=> __('module_registration_v_delivery_area_code_min')],
		Art_Validator::MAX_LENGTH => ['value' => 4,'message'	=> __('module_registration_v_delivery_area_code_max')]),
	$prefix.'phone'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 9,'message'	=> __('module_registration_v_delivery_phone_min')],
		Art_Validator::MAX_LENGTH => ['value' => 9,'message'	=> __('module_registration_v_delivery_phone_max')],
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_delivery_phone_not_integer')]),
			);
	}
	
	static function getDeliveryFieldsWithoutPhoneValidators( $prefix = null )
	{
		return	array(
	$prefix.'country'	=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_delivery_country_not_integer')]),
	$prefix.'city'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_delivery_city_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_delivery_city_max')]),
	$prefix.'street'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_delivery_street_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_delivery_street_max')]),
	$prefix.'housenum'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_delivery_housenum_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_delivery_housenum_max')]),
	$prefix.'zip'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_delivery_zip_min')],
		Art_Validator::MAX_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_delivery_zip_max')]),
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
	
	static function getMailFieldsValidators()
	{
		return	array(
	'subject'			=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_users_v_mail_subject_min')],
		Art_Validator::MAX_LENGTH => ['value' => 100,'message'	=> __('module_users_v_mail_subject_max')]),
	'body'				=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_users_v_mail_body_min')],
		Art_Validator::MAX_LENGTH => ['value' => 5000,'message' => __('module_users_v_mail_body_max')]),
			);
	}
	
	static function getCodeFieldsValidators()
	{
		return	array(
	'note'			=> array(
		Art_Validator::MAX_LENGTH => ['value' => 40,'message'	=> __('module_users_v_code_note_max')]),
			);
	}
	
	static function getAuthorizeFieldsValidators()
	{
		return	array(
	'name'				=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message' => __('module_user_group_v_name_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message' => __('module_user_group_v_name_max')]),			
	'description'		=> array(
		Art_Validator::MAX_LENGTH => ['value' => 60,'message' => __('module_user_group_v_description_max')]),
	'id_rights'			=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_user_group_v_id_rights_not_integer')]),
			);
	}
	
	static function getCompleteFirmRegValidators()
	{
		return	array(
	'membership_fee'	=> array(
		Art_Validator::MIN_VALUE => ['message'	=> __('module_registration_v_membership_fee_min')],
		Art_Validator::IS_INTEGER => ['message'	=> __('module_registration_v_membership_fee_not_integer')]),
	'from_day'			=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_user_group_v_from_day_not_integer')]),
	'from_month'		=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_user_group_v_from_month_not_integer')]),
	'from_year'			=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_user_group_v_from_year_not_integer')]),
	'to_day'			=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_user_group_v_to_day_not_integer')]),
	'to_month'			=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_user_group_v_to_month_not_integer')]),
	'to_year'			=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_user_group_v_to_year_not_integer')]),
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
}