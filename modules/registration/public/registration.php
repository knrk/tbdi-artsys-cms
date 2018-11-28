<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/registration/public
 */
class Module_Registration extends Art_Abstract_Module {
	
	const REQUEST_REGISTER			= 'xg9UdWMZP3';
	const REQUEST_CONFIRM			= 'r3W3UpsCsx';
	const REQUEST_BACK				= 'm8kX3PwFUN';
	const REQUEST_SEND_AGAIN		= 'A6sQfe2aL4';
	const REQUEST_SEND_FORGOTTEN	= 'WQvEanfGcS';
	const REQUEST_CHANGE_PASSWD		= 'BZzPESCdHh';
	const REQUEST_SET_PASSWD		= 'JdovFmfNds';
	const REQUEST_SET_DATA			= 'fE5dN6gUrV';
	
	const ENABLE_ADDRESS_DELIVERY	= true;
	const ENABLE_ADDRESS_CONTACT	= true;
	const ENABLE_ADDRESS_COMPANY	= true;
	const ENABLE_INVITE_CODE		= true;
	
	const AUTH_PERSON	= 'auth-person-';
	
	const SESSION_PREFIX			= "reg_";
	const SESSION_INVCODE			= "inv-code";
	
	const ADDRESS_FIELDS = array('street','housenum','city','zip','area_code','phone','id_country');
		
	function showAction()	//TODO so far..
	{
		Art_Template::setTemplate('index', 'clubTemplate');
p(Helper_TBDev::generateUserNumber());
p(Helper_TBDev::generateCompanyRepresentantNumber());
p(Helper_TBDev::generateCompanyNumber());
p(Helper_TBDev::generateInviteCode());

/*
$user = new Art_Model_User_Data(array('email'=>'info@itart.cz'));
$pass = Art_User::generatePassword();

$passwd = Art_User::hashPassword($pass, $user->salt);

$user->password = $passwd;

$user->save();

p($pass);*/
//p(Helper_TBDev::generateNonmemberNumber(Helper_TBDev::generateCompanyNumber()));
		//$resource = Helper_TBDev_PDF::registrationDocForPerson('$titul_jmeno_prijmeni', '$klientske_cislo', '$datum_narozeni', '$adresa_trvaleho_pobytu', '$kontaktni_adresa', '$email', '$tel');
		//$this->view->url = Art_Server::getHost().'/resource/'.$resource->hash;
	}
	
	function indexAction() 
	{
		if( !$this->isWidget() )
		{
			Art_Template::setTemplate('index', 'clubTemplate');
		}
		
		$invCode = Art_Router::getFromURI("code");

		if ( NULL !== $invCode )
		{
			Art_Session::set(self::SESSION_PREFIX.self::SESSION_INVCODE, $invCode);
		}
	}
	
	function naturalpersonAction()
	{
		if( !$this->isWidget() )
		{
			Art_Template::setTemplate('index', 'clubTemplate');
		}
		
		if( Art_Ajax::isRequestedBy(self::REQUEST_CONFIRM) )
		{			
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$userDataFields = Art_Model_User_Data::getCols('insert');
			$addressFields = Art_Model_Address::getCols('insert');

if ( !DEBUG ) :
			//Only check input 
			Helper_Default::getValidatedSQLData($userDataFields, self::getUserDataFieldsValidators(), $data, $response);
			Helper_Default::getValidatedSQLData($addressFields, self::getDeliveryFieldsValidators(), $data, $response, Art_Model_Address::DELIVERY_PREFIX);		
			
			if( Helper_Default::isPropertyChecked($data,Art_Model_Address::CONTACT_PREFIX.'active') )
			{
				Helper_Default::getValidatedSQLData($addressFields, self::getContactFieldsValidators(), $data, $response, Art_Model_Address::CONTACT_PREFIX);
			}
			
			//Check email validity
			if ( empty($data['email']) || !is_email($data['email']) )
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
			
			if ( !Helper_Default::isPropertyChecked($data,'charter') )
			{
				$response->addAlert(__('module_registration_v_charter_confirm'));
			}	
endif;

			if( $response->isValid() )
			{
				//Set data to SESSION
				foreach($data AS $field_name => $field_value)
				{				
					Art_Session::set(self::SESSION_PREFIX.$field_name, $field_value);
				}
				
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else 
		{		
			$stanovyName = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_STANOVY);
			
			$stanovyPDF = null;
			
			if ( NULL !== $stanovyName )
			{
				$stanovy = new Art_Model_Resource_Db($stanovyName);
				
				if ( $stanovy->isLoaded() )
				{
					$stanovyPDF = $stanovy->name;
				}
			}
			
			$directory = Art_Server::getHost().'/resource/';
			
			$this->view->stanovy = $directory.$stanovyPDF;
			
			$this->view->days = Helper_TBDev::getDayRange();
			$this->view->months = Helper_TBDev::getMonthRange();
			$this->view->years = Helper_TBDev::getBornYearRange();

			$this->view->countryList = Art_Model_Country::fetchAllPrivilegedActive();

			$invCode = null;
			
			//Get and remove data from SESSION if available
			foreach(Art_Session::get() AS $field_name => $field_value)
			{	
				if ( 0 === strpos($field_name, self::SESSION_PREFIX) )
				{
					if ( 0 === strpos($field_name, self::SESSION_PREFIX.self::SESSION_INVCODE) )
					{
						$invCode = $field_value;
						Art_Session::remove($field_name);
					}
					
					$this->view->data[substr($field_name, strlen(self::SESSION_PREFIX))] = $field_value;
					Art_Session::remove($field_name);
				}
			}

			$this->view->invCode = $invCode;
			
			//Send registration data to check by user himself
			$request = Art_Ajax::newRequest(self::REQUEST_CONFIRM);
			$request->setRedirect('/registration/confirmnatural');
			$this->view->request = $request;
		}
	}
	
	function legalpersonAction()
	{
		if( !$this->isWidget() )
		{
			Art_Template::setTemplate('index', 'clubTemplate');
		}
		
		if( Art_Ajax::isRequestedBy(self::REQUEST_CONFIRM) )
		{		
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();

			$userDataFields = Art_Model_User_Data::getCols('insert');
			$addressFields = Art_Model_Address::getCols('insert');

if ( !DEBUG ) :	
			//Only check input 
			Helper_Default::getValidatedSQLData(array_merge($addressFields,array('person-function')), self::getFirmFieldsValidators(), $data, $response);
			
			if ( !empty(Art_Model_User_Group::fetchAll(array('name'=>Helper_TBDev::GROUP_COMPANY.$data['company_name']))) )
			{
				$response->addAlert(__('module_registration_v_company_name_duplication'));
			}

			if ( !isset($data['ico']) || !Helper_Default::verifyICO($data['ico']) )
			{
				$response->addAlert(__('module_registration_v_ico_not_valid'));
			}
			
			if ( !empty($data['dic']) && !Helper_Default::verifyDIC($data['dic']) )
			{
				$response->addAlert(__('module_registration_v_dic_not_valid'));
			}
			
			Helper_Default::getValidatedSQLData($addressFields, self::getCompanyFieldsValidators(), $data, $response, Art_Model_Address::COMPANY_PREFIX);
			Helper_Default::getValidatedSQLData($userDataFields, self::getUserDataFieldsValidators(), $data, $response);
			Helper_Default::getValidatedSQLData($addressFields, self::getDeliveryFieldsValidators(), $data, $response, Art_Model_Address::DELIVERY_PREFIX);
			if ( !(isset($data[Module_Registration::AUTH_PERSON.'active']) && Helper_Default::isCheckboxChecked($data[Module_Registration::AUTH_PERSON.'active'])) )
			{
				Helper_Default::getValidatedSQLData($userDataFields, self::getUserDataFieldsValidators(Module_Registration::AUTH_PERSON), $data, $response, Module_Registration::AUTH_PERSON);
				Helper_Default::getValidatedSQLData($addressFields, self::getDeliveryFieldsValidators(Module_Registration::AUTH_PERSON), $data, $response, Module_Registration::AUTH_PERSON.Art_Model_Address::DELIVERY_PREFIX);
			}
			
			//Check email validity
			if ( empty($data['email']) || !is_email($data['email']) )
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
			
			if ( !Helper_Default::isPropertyChecked($data,'charter') )
			{
				$response->addAlert(__('module_registration_v_charter_confirm'));
			}	
endif;		
			
			if( $response->isValid() )
			{
				//Set data to SESSION
				foreach($data AS $field_name => $field_value)
				{				
					Art_Session::set(self::SESSION_PREFIX.$field_name, $field_value);
				}
				
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else 
		{		
			$stanovyName = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_STANOVY);
			
			$stanovyPDF = null;
			
			if ( NULL !== $stanovyName )
			{
				$stanovy = new Art_Model_Resource_Db($stanovyName);
				
				if ( $stanovy->isLoaded() )
				{
					$stanovyPDF = $stanovy->name;
				}
			}
			
			$directory = Art_Server::getHost().'/resource/';
			
			$this->view->stanovy = $directory.$stanovyPDF;

			$this->view->days = Helper_TBDev::getDayRange();
			$this->view->months = Helper_TBDev::getMonthRange();
			$this->view->years = Helper_TBDev::getBornYearRange();

			$this->view->countryList = Art_Model_Country::fetchAllPrivilegedActive();

			$invCode = null;
			
			//Get and remove data from SESSION if available
			foreach(Art_Session::get() AS $field_name => $field_value)
			{	
				if ( 0 === strpos($field_name, self::SESSION_PREFIX) )
				{
					if ( 0 === strpos($field_name, self::SESSION_PREFIX.self::SESSION_INVCODE) )
					{
						$invCode = $field_value;
						Art_Session::remove($field_name);
					}
					
					$this->view->data[substr($field_name, strlen(self::SESSION_PREFIX))] = $field_value;
					Art_Session::remove($field_name);
				}
			}

			$this->view->invCode = $invCode;
			
			//Send registration data to check by user himself
			$request = Art_Ajax::newRequest(self::REQUEST_CONFIRM);
			$request->setRedirect('/registration/confirmlegal');
			$this->view->request = $request;
		}
	}
	
	function confirmnaturalAction() {
		if (Art_Ajax::isRequestedBy(self::REQUEST_BACK)) {
			
			$response = Art_Ajax::newResponse();
			$response->willRedirect();
			$response->execute();
		}
		else if (Art_Ajax::isRequestedBy(self::REQUEST_REGISTER)) {
			
			$response = Art_Ajax::newResponse();
			$data = array();
			
			//Get data from SESSION
			foreach(Art_Session::get() as $field_name => $field_value) {		
				if (0 === strpos($field_name, self::SESSION_PREFIX)) {
					$data[substr($field_name, strlen(self::SESSION_PREFIX))] = $field_value;
				}
			}

			//********************//
			//******* USER *******//

			//Insert new user
			$insertUser = new Art_Model_User();
			$insertUser->save();
	
			//Select row containing state according to CURRENCY_FROM_ADDRESS_STATE
			$country = new Art_Model_Country($data[Helper_TBDev::CURRENCY_FROM_ADDRESS_STATE.'country']);

			if ($country->isLoaded()) {
				//Get currency from address country currency
				$insertUser->id_currency = $country->id_currency;
			} else {
				$insertUser->id_currency = Art_User::DEFAULT_CURRENCY_ID;
			}			
			
			$insertUser->user_number = Helper_TBDev::generateUserNumber();
			$insertUser->active = 1;
			$insertUser->save();
			
			//*************************//
			//******* USER DATA *******//

			//Data to insert to database
			$sql_data = array();

			$fields = Art_Model_User_Data::getCols('insert');

			foreach($fields AS $field_name) {
				//If field is not set
				if (!isset($data[$field_name])) {
					$sql_data[$field_name] = null;
				} else {
					$sql_data[$field_name] = $data[$field_name];
				}
			}					

			$passwd = Art_User::generatePassword();
			$salt = Art_User::generateSalt();

			$insertUserData = new Art_Model_User_Data();
			$insertUserData->setDataFromArray($sql_data);
			$insertUserData->setUser($insertUser);
			$insertUserData->password = Art_User::hashPassword($passwd, $salt);
			$insertUserData->salt = $salt;
			$insertUserData->save();
			
			//*************************//
			//******* USER GROUP *******//	

			$insertUserToGroup = new Art_Model_User_X_User_Group();
			$insertUserToGroup->setUser($insertUser);
			$insertUserToGroup->setGroup(Art_Model_User_Group::getRegistered());
			$insertUserToGroup->save();
			
			if (self::ENABLE_INVITE_CODE ) {
				//*************************//
				//****** INVITE CODE ******//

				$inviteCode = new User_X_Invite_Code();
				$inviteCode->setUser($insertUser);
				$inviteCode->id_invite_code = Helper_TBDev::getInvitedCodeId($data['reg-code']);
				$inviteCode->save();
			}
			
			//*************************//
			//******* USER MANAGER *******//	

			$insertUserToGroup = new User_X_Manager();
			$insertUserToGroup->setUser($insertUser);
			$insertUserToGroup->id_manager = Helper_TBDev::getManagerForUser($insertUser)->id;
			$insertUserToGroup->save();
			
			if (self::ENABLE_ADDRESS_DELIVERY) {
				//*************************************//
				//******* USER DELIVERY ADDRESS *******//

				//Data to insert to database
				$sql_data = array();

				$fields = Art_Model_Address::getCols('insert');

				foreach ($fields as $field_name) {
					//If field is not set
					if (!isset($data[Art_Model_Address::DELIVERY_PREFIX.$field_name])) {
						$sql_data[$field_name] = NULL;
					} else {
						$sql_data[$field_name] = $data[Art_Model_Address::DELIVERY_PREFIX.$field_name];
					}
				}					

				$insertDeliveryUserAddress = new Art_Model_Address();
				$insertDeliveryUserAddress->setDataFromArray($sql_data);
				$insertDeliveryUserAddress->setUser($insertUser);
				$insertDeliveryUserAddress->setType(Art_Model_Address_Type::getDelivery());
				$insertDeliveryUserAddress->id_country = $data[Art_Model_Address::DELIVERY_PREFIX.'country'];
				$insertDeliveryUserAddress->save();
			}

			if (self::ENABLE_ADDRESS_CONTACT &&
				Helper_Default::isPropertyChecked($data,Art_Model_Address::CONTACT_PREFIX.'active')) {	
				//************************************//
				//******* USER CONTACT ADDRESS *******//

				//Data to insert to database
				$sql_data = array();

				$fields = Art_Model_Address::getCols('insert');

				foreach ($fields as $field_name) {
					//If field is not set
					if (!isset($data[Art_Model_Address::CONTACT_PREFIX.$field_name])) {
						$sql_data[$field_name] = null;
					} else {
						$sql_data[$field_name] = $data[Art_Model_Address::CONTACT_PREFIX.$field_name];
					}
				}					

				$insertContactUserAddress = new Art_Model_Address();
				$insertContactUserAddress->setDataFromArray($sql_data);
				$insertContactUserAddress->setUser($insertUser);
				$insertContactUserAddress->setType(Art_Model_Address_Type::getContact());
				$insertContactUserAddress->id_country = $data[Art_Model_Address::CONTACT_PREFIX.'country'];
				$insertContactUserAddress->save();
			}
			
			//Create registration document
			$contactAddr = (empty($insertContactUserAddress)) ? 
								$insertDeliveryUserAddress->stringify : $insertContactUserAddress->stringify;

			$resource = Helper_TBDev_PDF::registrationDocForPerson(
					$insertUserData->fullnameWithDegree,
					$insertUser->user_number,
					$insertUserData->born,
					$contactAddr,
					$insertDeliveryUserAddress->stringify,				
					$insertUserData->email,
					Helper_TBDev::getTelephoneForUser($insertUser)
			);
			
			$url = Art_Server::getHost().'/resource/'.$resource->hash;
			
			//Send registraion mail
			if (MAIL) {
				Helper_Email::sendRegistrationMail($data['email'], $url);
			}
			
			//Save email and pdf url to session
			Art_Session::set('resend_email', $data['email']);
			Art_Session::set('pdf_url', $url);
			
			$response->addMessage(__('module_registration_registration_complete'));		
			$response->willRedirect();
			
			$response->execute();

		} else {
			
			if (!$this->isWidget()) {
				Art_Template::setTemplate('index', 'clubTemplate');
			}
	
			//Controlled data
			$data = array();
			
			//Get data from SESSION
			foreach (Art_Session::get() AS $field_name => $field_value) {		
				if (0 === strpos($field_name, self::SESSION_PREFIX)) {
					$data[substr($field_name, strlen(self::SESSION_PREFIX))] = $field_value;
				}
			}

			$this->view->data = $data;

			if (!empty($data)) {
				$deliveryAddressCountry = new Art_Model_Country($data[Art_Model_Address::DELIVERY_PREFIX.'country']);
				
				$this->view->deliveryAddressCountry = (NULL !== $deliveryAddressCountry) ? $deliveryAddressCountry->name : null;

				$contactAddressCountry = new Art_Model_Country($data[Art_Model_Address::CONTACT_PREFIX.'country']);
				
				$this->view->contactAddressCountry = (NULL !== $contactAddressCountry) ? $contactAddressCountry->name : null;

				$this->view->born_date = Helper_TBDev::getDate($data['born_year'], $data['born_month'], $data['born_day'], '.');
				
				$this->view->gender = Helper_TBDev::getGender($data['gender']);
				
				$this->view->hasContactAddress = Helper_Default::isPropertyChecked($data,Art_Model_Address::CONTACT_PREFIX.'active');
				
				//Edit filled registration form
				$requestBack = Art_Ajax::newRequest(self::REQUEST_BACK);
				$requestBack->setRedirect('/registration/naturalperson');
				$this->view->requestBack = $requestBack; 

				//Send registration
				$requestRegister = Art_Ajax::newRequest(self::REQUEST_REGISTER);
				$requestRegister->setRedirect('/registration/donenatural');
				$this->view->requestRegister = $requestRegister; 
			} else {
				redirect_to('/registration');
			}
		}
	}
	
	function confirmlegalAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_BACK) )
		{
			$response = Art_Ajax::newResponse();
			
			$response->willRedirect();

			$response->execute();
		}
		else if( Art_Ajax::isRequestedBy(self::REQUEST_REGISTER) )
		{
			$response = Art_Ajax::newResponse();
			$data = array();
			
			//Get data from SESSION
			foreach(Art_Session::get() AS $field_name => $field_value)
			{		
				if ( 0 === strpos($field_name, self::SESSION_PREFIX) )
				{
					$data[substr($field_name, strlen(self::SESSION_PREFIX))] = $field_value;
				}
			}
	
			if ( Helper_Default::isPropertyChecked($data,Module_Registration::AUTH_PERSON.'active') )
			{
				//********************//
				//******* USER *******//

				//Insert new user
				$insertUser = new Art_Model_User();
				$insertUser->save();

				//Select row containing state according to CURRENCY_FROM_ADDRESS_STATE
				$country = new Art_Model_Country($data[Helper_TBDev::CURRENCY_FROM_ADDRESS_STATE.'country']);

				if ( $country->isLoaded() )
				{
					//Get currency from address country currency
					$insertUser->id_currency = $country->id_currency;
				}
				else
				{
					$insertUser->id_currency = Art_User::DEFAULT_CURRENCY_ID;
				}			

				$insertUser->user_number = Helper_TBDev::generateCompanyNumber();
				$insertUser->active = 1;
				$insertUser->save();

				//*************************//
				//******* USER DATA *******//

				//Data to insert to database
				$sql_data = array();

				$fields = Art_Model_User_Data::getCols('insert');

				foreach($fields AS $field_name)
				{
					//If field is not set
					if( !isset($data[$field_name]) )
					{
						$sql_data[$field_name] = NULL;
					}
					else
					{
						$sql_data[$field_name] = $data[$field_name];
					}
				}					

				$passwd = Art_User::generatePassword();
				$salt = Art_User::generateSalt();

				$insertUserData = new Art_Model_User_Data();
				$insertUserData->setDataFromArray($sql_data);
				$insertUserData->setUser($insertUser);
				$insertUserData->password = Art_User::hashPassword($passwd,$salt);
				$insertUserData->salt = $salt;
				$insertUserData->save();

				//*************************//
				//******* USER GROUP *******//

				$insertUserToGroup = new Art_Model_User_X_User_Group();
				$insertUserToGroup->setUser($insertUser);
				$insertUserToGroup->setGroup(Art_Model_User_Group::getRegistered());
				$insertUserToGroup->save();			

				if ( self::ENABLE_INVITE_CODE )
				{
					//*************************//
					//****** INVITE CODE ******//

					$inviteCode = new User_X_Invite_Code();
					$inviteCode->setUser($insertUser);
					$inviteCode->id_invite_code = Helper_TBDev::getInvitedCodeId($data['reg-code']);
					$inviteCode->save();
				}
				
				//*************************//
				//******* USER MANAGER *******//	

				$insertUserToGroup = new User_X_Manager();
				$insertUserToGroup->setUser($insertUser);
				$insertUserToGroup->id_manager = Helper_TBDev::getManagerForUser($insertUser)->id;
				$insertUserToGroup->save();
				
				//*************************//
				//******* USER COMPANY *******//	

				$insertUserCompany = new User_X_Company();
				$insertUserCompany->setUser($insertUser);
				$insertUserCompany->id_company_user = $insertUser->id;
				$insertUserCompany->function = $data['person-function'];
				$insertUserCompany->save();			

				if ( self::ENABLE_ADDRESS_COMPANY )
				{
					//*************************************//
					//******* USER DELIVERY ADDRESS *******//

					//Data to insert to database
					$sql_data = array();

					$fields = Art_Model_Address::getCols('insert');

					foreach($fields AS $field_name)
					{
						//If field is not set
						if( !isset($data[Art_Model_Address::COMPANY_PREFIX.$field_name]) )
						{
							$sql_data[$field_name] = NULL;
						}
						else
						{
							$sql_data[$field_name] = $data[Art_Model_Address::COMPANY_PREFIX.$field_name];
						}
					}					

					$insertCompanyAddress = new Art_Model_Address();
					$insertCompanyAddress->setDataFromArray($sql_data);
					$insertCompanyAddress->setUser($insertUser);
					$insertCompanyAddress->setType(Art_Model_Address_Type::getCompany());
					$insertCompanyAddress->id_country = $data[Art_Model_Address::COMPANY_PREFIX.'country'];
					$insertCompanyAddress->dic = $data['dic'];
					$insertCompanyAddress->ico = $data['ico'];
					$insertCompanyAddress->company_name = $data['company_name'];
					$insertCompanyAddress->save();
				}
				
				if ( self::ENABLE_ADDRESS_DELIVERY )
				{
					//*************************************//
					//******* USER DELIVERY ADDRESS *******//

					//Data to insert to database
					$sql_data = array();

					$fields = Art_Model_Address::getCols('insert');

					foreach($fields AS $field_name)
					{
						//If field is not set
						if( !isset($data[Art_Model_Address::DELIVERY_PREFIX.$field_name]) )
						{
							$sql_data[$field_name] = NULL;
						}
						else
						{
							$sql_data[$field_name] = $data[Art_Model_Address::DELIVERY_PREFIX.$field_name];
						}
					}					

					$insertDeliveryUserAddress = new Art_Model_Address();
					$insertDeliveryUserAddress->setDataFromArray($sql_data);
					$insertDeliveryUserAddress->setUser($insertUser);
					$insertDeliveryUserAddress->setType(Art_Model_Address_Type::getDelivery());
					$insertDeliveryUserAddress->id_country = $data[Art_Model_Address::DELIVERY_PREFIX.'country'];
					$insertDeliveryUserAddress->save();
				}
			}
			else
			{
				//********************//
				//******* USER REPRESENTANT *******//

				//Insert new user
				$insertUser = new Art_Model_User();
				$insertUser->save();

				$representantUser = $insertUser;
				
				//Select row containing state according to CURRENCY_FROM_ADDRESS_STATE
				$country = new Art_Model_Country($data[Helper_TBDev::CURRENCY_FROM_ADDRESS_STATE.'country']);

				if ( $country->isLoaded() )
				{
					//Get currency from address country currency
					$insertUser->id_currency = $country->id_currency;
				}
				else
				{
					$insertUser->id_currency = Art_User::DEFAULT_CURRENCY_ID;
				}			

				$insertUser->user_number = Helper_TBDev::generateCompanyRepresentantNumber();
				$insertUser->active = 1;
				$insertUser->save();

				//*************************//
				//******* USER DATA REPRESENTANT *******//

				//Data to insert to database
				$sql_data = array();

				$fields = Art_Model_User_Data::getCols('insert');

				foreach($fields AS $field_name)
				{
					//If field is not set
					if( !isset($data[$field_name]) )
					{
						$sql_data[$field_name] = NULL;
					}
					else
					{
						$sql_data[$field_name] = $data[$field_name];
					}
				}					

				$sql_data['email'] = null;
				
				$insertUserData = new Art_Model_User_Data();
				$insertUserData->setDataFromArray($sql_data);
				$insertUserData->setUser($insertUser);
				$insertUserData->save();

				if ( self::ENABLE_ADDRESS_COMPANY )
				{
					//*************************************//
					//******* USER DELIVERY ADDRESS *******//

					//Data to insert to database
					$sql_data = array();

					$fields = Art_Model_Address::getCols('insert');

					foreach($fields AS $field_name)
					{
						//If field is not set
						if( !isset($data[Art_Model_Address::COMPANY_PREFIX.$field_name]) )
						{
							$sql_data[$field_name] = NULL;
						}
						else
						{
							$sql_data[$field_name] = $data[Art_Model_Address::COMPANY_PREFIX.$field_name];
						}
					}					

					$insertCompanyAddress = new Art_Model_Address();
					$insertCompanyAddress->setDataFromArray($sql_data);
					$insertCompanyAddress->setUser($insertUser);
					$insertCompanyAddress->setType(Art_Model_Address_Type::getCompany());
					$insertCompanyAddress->id_country = $data[Art_Model_Address::COMPANY_PREFIX.'country'];
					$insertCompanyAddress->dic = $data['dic'];
					$insertCompanyAddress->ico = $data['ico'];
					$insertCompanyAddress->company_name = $data['company_name'];
					$insertCompanyAddress->save();
				}
				
				if ( self::ENABLE_ADDRESS_DELIVERY )
				{
					//*************************************//
					//******* USER DELIVERY ADDRESS *******//

					//Data to insert to database
					$sql_data = array();

					$fields = Art_Model_Address::getCols('insert');

					foreach($fields AS $field_name)
					{
						//If field is not set
						if( !isset($data[Art_Model_Address::DELIVERY_PREFIX.$field_name]) )
						{
							$sql_data[$field_name] = NULL;
						}
						else
						{
							$sql_data[$field_name] = $data[Art_Model_Address::DELIVERY_PREFIX.$field_name];
						}
					}					

					$sql_data['area_code'] = null;
					$sql_data['phone'] = null;
					
					$insertDeliveryUserAddress = new Art_Model_Address();
					$insertDeliveryUserAddress->setDataFromArray($sql_data);
					$insertDeliveryUserAddress->setUser($insertUser);
					$insertDeliveryUserAddress->setType(Art_Model_Address_Type::getDelivery());
					$insertDeliveryUserAddress->id_country = $data[Art_Model_Address::DELIVERY_PREFIX.'country'];
					$insertDeliveryUserAddress->save();
				}
				
				////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////
				
				//********************//
				//******* USER *******//

				//Insert new user
				$insertUser = new Art_Model_User();
				$insertUser->save();

				//Select row containing state according to CURRENCY_FROM_ADDRESS_STATE
				$country = new Art_Model_Country($data[Helper_TBDev::CURRENCY_FROM_ADDRESS_STATE.'country']);

				if ( $country->isLoaded() )
				{
					//Get currency from address country currency
					$insertUser->id_currency = $country->id_currency;
				}
				else
				{
					$insertUser->id_currency = Art_User::DEFAULT_CURRENCY_ID;
				}			

				$insertUser->user_number = Helper_TBDev::generateCompanyNumber();
				$insertUser->active = 1;
				$insertUser->save();

				//*************************//
				//******* USER DATA *******//

				//Data to insert to database
				$sql_data = array();

				$fields = Art_Model_User_Data::getCols('insert');

				foreach($fields AS $field_name)
				{
					//If field is not set
					if( !isset($data[Module_Registration::AUTH_PERSON.$field_name]) )
					{
						$sql_data[$field_name] = NULL;
					}
					else
					{
						$sql_data[$field_name] = $data[Module_Registration::AUTH_PERSON.$field_name];
					}
				}					

				$sql_data['email'] = $data['email'];
				
				$passwd = Art_User::generatePassword();
				$salt = Art_User::generateSalt();

				$insertUserData = new Art_Model_User_Data();
				$insertUserData->setDataFromArray($sql_data);
				$insertUserData->setUser($insertUser);
				$insertUserData->password = Art_User::hashPassword($passwd,$salt);
				$insertUserData->salt = $salt;
				$insertUserData->save();

				//*************************//
				//******* USER GROUP *******//

				$insertUserToGroup = new Art_Model_User_X_User_Group();
				$insertUserToGroup->setUser($insertUser);
				$insertUserToGroup->setGroup(Art_Model_User_Group::getRegistered());
				$insertUserToGroup->save();			

				if ( self::ENABLE_INVITE_CODE )
				{
					//*************************//
					//****** INVITE CODE ******//

					$inviteCode = new User_X_Invite_Code();
					$inviteCode->setUser($insertUser);
					$inviteCode->id_invite_code = Helper_TBDev::getInvitedCodeId($data['reg-code']);
					$inviteCode->save();
				}
				
				//*************************//
				//******* USER MANAGER *******//	

				$insertUserToGroup = new User_X_Manager();
				$insertUserToGroup->setUser($insertUser);
				$insertUserToGroup->id_manager = Helper_TBDev::getManagerForUser($insertUser)->id;
				$insertUserToGroup->save();
				
				//*************************//
				//******* USER COMPANY *******//	

				$insertUserCompany = new User_X_Company();
				$insertUserCompany->setUser($insertUser);
				$insertUserCompany->id_company_user = $representantUser->id;
				$insertUserCompany->function = $data['person-function'];
				$insertUserCompany->save();	
				
				if ( self::ENABLE_ADDRESS_DELIVERY )
				{
					//*************************************//
					//******* USER DELIVERY ADDRESS *******//

					//Data to insert to database
					$sql_data = array();

					$fields = Art_Model_Address::getCols('insert');

					foreach($fields AS $field_name)
					{
						//If field is not set
						if( !isset($data[Module_Registration::AUTH_PERSON.Art_Model_Address::DELIVERY_PREFIX.$field_name]) )
						{
							$sql_data[$field_name] = NULL;
						}
						else
						{
							$sql_data[$field_name] = $data[Module_Registration::AUTH_PERSON.Art_Model_Address::DELIVERY_PREFIX.$field_name];
						}
					}					

					$sql_data['area_code'] = $data[Art_Model_Address::DELIVERY_PREFIX.'area_code'];
					$sql_data['phone'] = $data[Art_Model_Address::DELIVERY_PREFIX.'phone'];
					
					$insertDeliveryUserAddress = new Art_Model_Address();
					$insertDeliveryUserAddress->setDataFromArray($sql_data);
					$insertDeliveryUserAddress->setUser($insertUser);
					$insertDeliveryUserAddress->setType(Art_Model_Address_Type::getDelivery());
					$insertDeliveryUserAddress->id_country = $data[Art_Model_Address::DELIVERY_PREFIX.'country'];
					$insertDeliveryUserAddress->save();
				}
			}
			
			//Send registraion info mail
			Helper_Email::sendRegInfoMail($data['email']);			
			
			//Save email to session
			Art_Session::set('resend_email', $data['email']);
			
			$response->addMessage(__('module_registration_registration_complete'));		
			$response->willRedirect();
			
			$response->execute();
		}
		else
		{
			if( !$this->isWidget() )
			{
				Art_Template::setTemplate('index', 'clubTemplate');
			}
			
			//Controlled data
			$data = array();
			
			//Get data from SESSION
			foreach(Art_Session::get() AS $field_name => $field_value)
			{		
				if ( 0 === strpos($field_name, self::SESSION_PREFIX) )
				{
					$data[substr($field_name, strlen(self::SESSION_PREFIX))] = $field_value;
				}
			}

			$this->view->data = $data;
			
			if ( !empty($data) )
			{
				$companyAddressCountry = new Art_Model_Country($data[Art_Model_Address::COMPANY_PREFIX.'country']);
				
				$this->view->companyAddressCountry = (NULL !== $companyAddressCountry) ? $companyAddressCountry->name : null;
				
				$deliveryAddressCountry = new Art_Model_Country($data[Art_Model_Address::DELIVERY_PREFIX.'country']);
				
				$this->view->deliveryAddressCountry = (NULL !== $deliveryAddressCountry) ? $deliveryAddressCountry->name : null;
				
				$this->view->born_date = Helper_TBDev::getDate($data['born_year'], $data['born_month'], $data['born_day'], '.');
				
				$this->view->gender = Helper_TBDev::getGender($data['gender']);
				
				$this->view->isAuthPerson = $isAuthPerson = !(isset($data[Module_Registration::AUTH_PERSON.'active']) && Helper_Default::isCheckboxChecked($data[Module_Registration::AUTH_PERSON.'active']));
				
				if ( $isAuthPerson )
				{
					$authPersonDeliveryAddressCountry = new Art_Model_Country($data[Module_Registration::AUTH_PERSON.Art_Model_Address::DELIVERY_PREFIX.'country']);
				
					$this->view->authPersonDeliveryAddressCountry = (NULL !== $authPersonDeliveryAddressCountry) ? $authPersonDeliveryAddressCountry->name : null;
				
					$this->view->authPersonBorn_date = Helper_TBDev::getDate($data[Module_Registration::AUTH_PERSON.'born_year'], $data[Module_Registration::AUTH_PERSON.'born_month'], $data[Module_Registration::AUTH_PERSON.'born_day'], '.');
				
					$this->view->authPersonGender = Helper_TBDev::getGender($data[Module_Registration::AUTH_PERSON.'gender']);
				}
				
				//Edit filled registration form
				$requestBack = Art_Ajax::newRequest(self::REQUEST_BACK);
				$requestBack->setRedirect('/registration/legalperson');
				$this->view->requestBack = $requestBack; 

				//Send registration
				$requestRegister = Art_Ajax::newRequest(self::REQUEST_REGISTER);
				$requestRegister->setRedirect('/registration/donelegal');
				$this->view->requestRegister = $requestRegister; 
			}
			else
			{
				redirect_to('/registration');
			}
		}
	}
	
	function donenaturalAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_SEND_AGAIN) )
		{
			$response = Art_Ajax::newResponse();

			$email = Art_Session::get('resend_email');
			$pdf = Art_Session::get('pdf_url');
			
			if ( empty($email) || empty($pdf) )
			{
				$response->addAlert(__('module_registration_sended_again_failed'));
			}
			else 
			{
				//Resend email
				Helper_Email::sendRegistrationMail($email, $pdf);

				$response->addMessage(__('module_registration_sended_again'));
			}

			$response->execute();
		}
		else 
		{
			if( !$this->isWidget() )
			{
				Art_Template::setTemplate('index', 'clubTemplate');
			}
			
			//Remove data from SESSION
			foreach(Art_Session::get() AS $field_name => $field_value)
			{		
				if ( 0 === strpos($field_name, self::SESSION_PREFIX) )
				{
					Art_Session::remove($field_name);
				}
			}

			$this->view->email = $email = Art_Session::get('resend_email');
			
			if ( !empty($email) )
			{
				//Resend registration email if address is in session
				$request = Art_Ajax::newRequest(self::REQUEST_SEND_AGAIN);
				//$request->setRefresh();
				$this->view->request = $request; 
			}
			else
			{
				redirect_to('/registration');
			}
		}
	}
	
	function donelegalAction()
	{
		if( !$this->isWidget() )
		{
			Art_Template::setTemplate('index', 'clubTemplate');
		}

		//Remove data from SESSION
		foreach(Art_Session::get() AS $field_name => $field_value)
		{		
			if ( 0 === strpos($field_name, self::SESSION_PREFIX) )
			{
				Art_Session::remove($field_name);
			}
		}

		$this->view->email = $email = Art_Session::get('resend_email');

		if ( empty($email) )
		{
			redirect_to('/registration');
		}
	}
	
	function forgottenAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_SEND_FORGOTTEN) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();

			//Check email validity
			if ( !is_email($data['email']) )
			{
				$response->addAlert(__('module_registration_v_wrong_email'));
			}
			
			if ( $response->isValid() )
			{
				$userData = new Art_Model_User_Data(array('email'=>$data['email']));

				if ( !$userData->isLoaded() )
				{
					$response->addAlert(__('module_registration_v_not_existed_email'));
				}
				else
				{	
					$userinfo = Art_Model_User_X_User_Group::fetchAll(array(
						'id_user' => $userData->id_user, 
						'id_user_group' => Art_Model_User_Group::getAuthorizedId()));
					
					if (empty($userinfo)) {
						$response->addAlert(__('module_registration_v_not_authorized_email'));
					}
					else
					{
						$hash = substr(str_replace('/','x',Art_User::hashPassword(Art_User::generatePassword(),Art_User::generateSalt())),7);
						$userData->forgotten_pass_hash = $hash;
						$userData->forgotten_pass_IP = Art_Server::getIp();
						$userData->forgotten_pass_date = dateSQL();
						$userData->save();

						//Send email - forgotten password
						Helper_Email::sendForgottenMail($data['email'], $hash);

						$response->addMessage(__('module_registration_forgotten_sended'));
					}
				}
			}
			
			$response->execute();
		}
		else 
		{
			if( !$this->isWidget() )
			{
				Art_Template::setTemplate('index', 'clubTemplate');
			}
			
			//Send email - forgotten password
			$request = Art_Ajax::newRequest(self::REQUEST_SEND_FORGOTTEN);
			//$request->setRedirect('/');
			$this->view->request = $request; 
			
		}
	}

	function forgottenAckAction()
	{
		$userData = new Art_Model_User_Data(array('forgotten_pass_hash'=>Art_Router::getId()));
	
		if( $userData->isLoaded() )
		{
			if( Art_Ajax::isRequestedBy(self::REQUEST_CHANGE_PASSWD) )
			{
				$response = Art_Ajax::newResponse();
				$data = Art_Ajax::getData();

				$where = new Art_Model_Db_Where();
				$where->add(array('name'=>'forgotten_pass_hash', 'value'=>Art_Router::getId()));
				$where->add(array('name'=>'forgotten_pass_date', 'value'=>dateSQL('-'.Helper_TBDev::FORGOTTEN_VALID_TIME), 'relation'=>Art_Model_Db_Where::REL_GREATER));

				$userData = Art_Model_User_Data::fetchAll($where);

				if( empty($userData) )
				{
					$this->showTo(Art_User::NO_ACCESS);
				}
				else
				{
					$userData = $userData[0];	/* @var $userData Art_Model_User_Data */
					
					if ( $userData->isLoaded() )
					{
						if ( $data['passwd_new1'] !== $data['passwd_new2'] )
						{
							$response->addAlert(__('module_registration_v_change_password_not_same'));
						}
						else 			
						{
							Helper_Default::getValidatedSQLData(array('passwd_new1'), self::getFieldsValidators(), $data, $response);

							if ( $response->isValid() )
							{
								$newhash = Art_User::hashPassword($data['passwd_new1'],$userData->salt);

								$userData->password = $newhash;
								$userData->pass_changed_date = dateSQL();
								$userData->forgotten_pass_hash = null;
								$userData->save();

								$response->addMessage(__('module_registration_change_password_changed'));
								$response->willRedirect();
							}
						}
					}
				}

				$response->execute();
			}
			else
			{
				if( !$this->isWidget() )
				{
					Art_Template::setTemplate('index', 'clubTemplate');
				}
				
				//Change password request
				$request = Art_Ajax::newRequest(self::REQUEST_CHANGE_PASSWD);
				$request->setRedirect('/');
				$this->view->request = $request;
			}
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}

	function setPasswordAction() 
	{	
		if( Art_Ajax::isRequestedBy(self::REQUEST_SET_PASSWD) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();

			$userData = Art_User::getCurrentUser()->getData();

			if ( $userData->isLoaded() )
			{
				if ( $data['passwd_new1'] !== $data['passwd_new2'] )
				{
					$response->addAlert(__('module_registration_v_set_password_not_same'));
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
						
						$response->addMessage(__('module_registration_set_password_setted'));
						$response->willRedirect();
					}
				}
			}
			else 
			{
				$response->addAlert(__('module_registration_set_password_not_found'));
			}

			$response->execute();
		}
		else
		{
			//Set password request
			$request = Art_Ajax::newRequest(self::REQUEST_SET_PASSWD);
			$request->setAction('/registration/setpassword');
			$request->setRedirect('/cabinet');
			$this->view->request = $request;
		}
	}
		
	function setdataAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_SET_DATA) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$dataFields = Art_Model_User_Data::getCols('update');
			$addressFields = Art_Model_Address::getCols('update');

			$data['verif'] = 1;
			$data['verif_id'] = 10;
			
			//Data to insert to database
			$sql_userData = Helper_Default::getValidatedSQLData($dataFields, self::getImportDataFieldsValidators(), $data, $response);			
			$sql_deliveryAddress = Helper_Default::getValidatedSQLData($addressFields, self::getDeliveryFieldsValidators(), $data, $response, Art_Model_Address::DELIVERY_PREFIX);			
			
			if( Helper_Default::isPropertyChecked($data,Art_Model_Address::CONTACT_PREFIX.'active') )
			{
				$sql_contactAddress = Helper_Default::getValidatedSQLData($addressFields, self::getContactFieldsValidators(), $data, $response, Art_Model_Address::CONTACT_PREFIX);
			}
			
			if ( $response->isValid() )
			{
				$user = Art_User::getCurrentUser();

				if ( $user->isLoaded() )
				{	
					$userData = $user->getData();
					
					//*************************************//
					//******* USER DATA *******//
					$userData->setDataFromArray($sql_userData);
					$userData->save();
					
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
			$this->view->days = Helper_TBDev::getDayRange();
			$this->view->months = Helper_TBDev::getMonthRange();
			$this->view->years = Helper_TBDev::getBornYearRange();
						
			$this->view->countryList = Art_Model_Country::fetchAllPrivilegedActive();
			
			$user = Art_User::getCurrentUser();
			
			$data = array();
			
			foreach ( $user->getData() as $key => $value )
			{
				$data[$key] = $value; 
			}

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
			
			$this->view->data = $data;
			
			//Set user data (addresses, date of birth, gender and phone) request
			$request = Art_Ajax::newRequest(self::REQUEST_SET_DATA);
			$request->setAction('/registration/setdata');
			$request->setRedirect('/cabinet');
			$this->view->request = $request;
		}
	}
	
	static function getFieldsValidators()
	{
		return	array(
	'passwd_new1'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 6,'message'	=> __('module_users_v_password_new_min')],
		Art_Validator::MAX_LENGTH => ['value' => 40,'message'	=> __('module_users_v_password_new_max')])
		);
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
	
	static function getFirmFieldsValidators()
	{
		return	array(
	'company_name'			=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_company_name_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_company_name_max')]),
	'ico'			=> array(
		Art_Validator::MIN_LENGTH => ['value' => 6,'message'	=> __('module_registration_v_ico_min')],
		Art_Validator::MAX_LENGTH => ['value' => 10,'message'	=> __('module_registration_v_ico_max')]),
	'person-function'			=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_person_function_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_person_function_max')]),
				);
	}

	static function getDeliveryFieldsValidators( $prefix = null )
	{
		return	array(
	$prefix.Art_Model_Address::DELIVERY_PREFIX.'country'	=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_delivery_country_not_integer')]),
	$prefix.Art_Model_Address::DELIVERY_PREFIX.'city'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_delivery_city_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_delivery_city_max')]),
	$prefix.Art_Model_Address::DELIVERY_PREFIX.'street'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_delivery_street_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_delivery_street_max')]),
	$prefix.Art_Model_Address::DELIVERY_PREFIX.'housenum'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_delivery_housenum_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_delivery_housenum_max')]),
	$prefix.Art_Model_Address::DELIVERY_PREFIX.'zip'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_delivery_zip_min')],
		Art_Validator::MAX_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_delivery_zip_max')]),
	Art_Model_Address::DELIVERY_PREFIX.'area_code'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 3,'message'	=> __('module_registration_v_delivery_area_code_min')],
		Art_Validator::MAX_LENGTH => ['value' => 4,'message'	=> __('module_registration_v_delivery_area_code_max')]),
	Art_Model_Address::DELIVERY_PREFIX.'phone'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 9,'message'	=> __('module_registration_v_delivery_phone_min')],
		Art_Validator::MAX_LENGTH => ['value' => 9,'message'	=> __('module_registration_v_delivery_phone_max')],
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_delivery_phone_not_integer')]),
			);
	}
	
	static function getContactFieldsValidators()
	{
		return	array(
	Art_Model_Address::CONTACT_PREFIX.'country'	=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_contact_country_not_integer')]),
	Art_Model_Address::CONTACT_PREFIX.'city'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_contact_city_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_contact_city_max')]),
	Art_Model_Address::CONTACT_PREFIX.'street'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_contact_street_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_registration_v_contact_street_max')]),
	Art_Model_Address::CONTACT_PREFIX.'housenum'	=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_registration_v_contact_housenum_min')],
		Art_Validator::MAX_LENGTH => ['value' => 20,'message'	=> __('module_registration_v_contact_housenum_max')]),
	Art_Model_Address::CONTACT_PREFIX.'zip'		=> array(
		Art_Validator::MIN_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_contact_zip_min')],
		Art_Validator::MAX_LENGTH => ['value' => 5,'message'	=> __('module_registration_v_contact_zip_max')]),
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
		
	static function getImportDataFieldsValidators( )
	{
		return	array(
	'gender'			=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_gender_not_integer')]),
	'born_day'			=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_born_day_not_integer')]),
	'born_month'		=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_born_month_not_integer')]),
	'born_year'			=> array(
		Art_Validator::IS_INTEGER => ['message'					=> __('module_registration_v_born_year_not_integer')]),
			);
	}
}
