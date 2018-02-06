<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/user_group/admin
 */
class Module_User_Group extends Art_Abstract_Module {
	
	const REQUEST_EDIT			= 'ScSgaCzWtb';
	const REQUEST_ADD			= 'JcafhjSuPM';
	const REQUEST_ADD_SERVICE	= 'ZHwaXaVMxv';
	const REQUEST_ADD_USER		= 'FrzeJRyHHq';
	const REQUEST_TAKE_AWAY_USER_SINGLE		= 'fdAUpgVLGb';
	const REQUEST_TAKE_AWAY_SERVICE_SINGLE	= 'PFDLmdUvLF';
	const REQUEST_DELETE_SINGLE	= 'YRtSyqjDdC';
	
	const CHECKBOXES_PREFIX	= 'user_';	
	
	function indexAction()
	{
		$userGroups = Helper_TBDev::getAllPermittedUserGroupsForUser(Art_User::getCurrentUser());
		
		foreach ( $userGroups as $value ) /* @var $value Art_Model_User_Group */
		{
			$value->services = array();
			$value->extendedServicePrices = array();
			
			$value->a_detail = '/'.Art_Router::getLayer().'/user_group/detail/'.$value->id;
			$value->a_edit = '/'.Art_Router::getLayer().'/user_group/edit/'.$value->id;	
		}

		$this->view->userGroups = $userGroups;

		$rights = array();
		
		foreach ( Art_Model_Rights::fetchAllPrivileged() as $right ) 
		{
			$rights[$right->id] = $right;
		}
		
		$this->view->rights = $rights;
					
		//Delete item by button
		$delete_single_request = Art_Ajax::newRequest(self::REQUEST_DELETE_SINGLE); 
		$delete_single_request->setAction('/'.Art_Router::getLayer().'/user_group/deleteSingle/$id'); 
		$delete_single_request->addUpdate('content','.module_user_group_index');
		$delete_single_request->setConfirmWindow(__('module_user_group_delete_single_confirm')); 
		$this->view->delete_single_request = $delete_single_request;
	}
	
	function detailAction()
	{
		$id = $this->getParams("id", Art_Router::getId());

		$this->view->id = $id;
		
		$userGroup = new Art_Model_User_Group($id);

		if ( $userGroup->isLoaded() )
		{
			$userGroup->users = array();

			//Get all Users included in Group
			foreach ( Art_Model_User_X_User_Group::fetchAllPrivileged(array('id_user_group'=>$userGroup->id)) as $userUserGroup ) 
				/* @var $userUserGroup Art_Model_User_X_User_Group */
			{
				 $user = $userUserGroup->getUser();
				 $user->user_x_user_group = $userUserGroup;
				 $userGroup->users[] = $user;
			}
			
			$userGroup->services = array();
			$userGroup->extendedServicePrices = array();
			
			//Get all Services and Service Prices included in Group
			foreach ( User_Group_X_Service_Price::fetchAllPrivileged(array('id_user_group'=>$userGroup->id)) as $userGroupServicePrice )
				/* @var $userGroupServicePrice User_Group_X_Service_Price */
			{
				$servicePrice = $userGroupServicePrice->getServicePrice();
					
				$extendedServicePrice = $servicePrice;
				$extendedServicePrice->time_from = $userGroupServicePrice->time_from;
				$extendedServicePrice->time_to = $userGroupServicePrice->time_to;
				$extendedServicePrice->user_group_x_service_price = $userGroupServicePrice;
				
				$userGroup->services[$servicePrice->id_service] = $servicePrice->getService();
				$userGroup->extendedServicePrices[] = $extendedServicePrice;
			}
			
			$this->view->userGroup = $userGroup;
			
			$this->view->right = new Art_Model_Rights($userGroup->id_rights);
			
			$this->view->isUserManipulationPermitted = 
				( $id === Art_Model_User_Group::getAuthorizedId() ||
					 $id === Art_Model_User_Group::getRegisteredId() ) ? false : true;
			
			//Taky away service by button
			$taky_away_service_single_request = Art_Ajax::newRequest(self::REQUEST_TAKE_AWAY_SERVICE_SINGLE); 
			$taky_away_service_single_request->setAction('/'.Art_Router::getLayer().'/user_group/takeAwayServiceSingle/$id'); 
			$taky_away_service_single_request->addUpdate('content','.module_user_group_detail'); 
			$taky_away_service_single_request->setConfirmWindow(__('module_user_group_take_away_service_single_confirm')); 
			$this->view->taky_away_service_single_request = $taky_away_service_single_request;
			
			//Taky away user by button
			$taky_away_user_single_request = Art_Ajax::newRequest(self::REQUEST_TAKE_AWAY_USER_SINGLE); 
			$taky_away_user_single_request->setAction('/'.Art_Router::getLayer().'/user_group/takeAwayUserSingle/$id'); 
			$taky_away_user_single_request->addUpdate('content','.module_user_group_detail'); 
			$taky_away_user_single_request->setConfirmWindow(__('module_user_group_take_away_user_single_confirm')); 
			$this->view->taky_away_user_single_request = $taky_away_user_single_request;
		}
		else 
		{
			$this->showTo(Art_User::NO_ACCESS);
		}	
	}
	
	function newAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ADD) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			//Set each field validation options
			$fields = Art_Model_User_Group::getCols('insert');
			
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
					
			//Everything is valid
			if( $response->isValid() )
			{
				$userGroup = new Art_Model_User_Group();
				$userGroup->setDataFromArray($sql_data);
				$userGroup->save();
				
				$response->addMessage(__('module_user_group_add_success'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$this->view->rights = Art_Model_Rights::fetchAllNotHigher();
			
			$request = Art_Ajax::newRequest(self::REQUEST_ADD);
			$request->setRedirect('/'.Art_Router::getLayer().'/user_group');
			$this->view->request = $request; 
		}
	}
	
	function editAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$userGroup = new Art_Model_User_Group(Art_Router::getId());		
			
			if ( !$userGroup->isLoaded() )
			{
				$response->addAlert(__('module_user_group_not_found'));
			}
			
			$fields = Art_Model_User_Group::getCols('update');
			
			//Data to insert to database
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
			
			//Everything is valid
			if( $response->isValid() )
			{
				$userGroup->setDataFromArray($sql_data);
				$userGroup->save();
				
				$response->addMessage(__('module_user_group_edit_success'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{		
			$this->view->rights = Art_Model_Rights::fetchAllNotHigher();
			
			$userGroup = new Art_Model_User_Group(Art_Router::getId());

			if ( !$userGroup->isLoaded() )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
	
			$this->view->userGroup = $userGroup; 
			
			$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
			$request->setRedirect('/'.Art_Router::getLayer().'/user_group');
			$this->view->request = $request;
		}
	}
	
	function addServiceAction()
	{	
		if( Art_Ajax::isRequestedBy(self::REQUEST_ADD_SERVICE) )
		{				
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			//Set each field validation options
			$fields = User_Group_X_Service_Price::getCols('insert');
			
			//Data to insert to database
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
					
			//Everything is valid
			if( $response->isValid() )
			{
				$from = Helper_TBDev::getDate($data["from_year"], $data["from_month"], $data["from_day"]);
				$to = Helper_TBDev::getDate($data["to_year"], $data["to_month"], $data["to_day"]);
				
				$userGroupServicePrice = new User_Group_X_Service_Price();
				$userGroupServicePrice->setDataFromArray($sql_data);
				$userGroupServicePrice->id_user_group = Art_Router::getId();
				$userGroupServicePrice->time_from = dateSQL($from);
				$userGroupServicePrice->time_to = dateSQL($to);
				$userGroupServicePrice->save();
				
				$response->addMessage(__('module_user_group_add_service_success'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$userGroup = new Art_Model_User_Group(Art_Router::getId());

			if ( !$userGroup->isLoaded() )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			
			$this->view->userGroup = $userGroup;
						
			$services = Service::fetchAllPrivileged();
			
			$this->view->services = $services;
			
			$servicePricesPerService = array();
			
			$servicePrices = Service_Price::fetchAllPrivileged();
			
			foreach ($servicePrices as $value) /* @var $value Service_Price */ 
			{
				$servicePrice = array();
				
				$servicePrice['id'] = $value->id;
				$servicePrice['price'] = $value->price;
				$servicePrice['time_interval'] = $value->time_interval;
				
				$servicePricesPerService[$value->id_service][] = $servicePrice;
			}				
				
			$this->view->servicePrices = $servicePrices;
			
			$this->view->servicePricesPerService = json_encode($servicePricesPerService);
			
			$this->view->days = Helper_TBDev::getDayRange();
			$this->view->months = Helper_TBDev::getMonthRange();
			$this->view->years = Helper_TBDev::getServiceYearRange();
			
			$this->view->currentMonth = Helper_TBDev::getCurrentMonth();
			$this->view->currentYear = Helper_TBDev::getCurrentYear();
			$this->view->nextYear = Helper_TBDev::getNextYear();
			
			$request = Art_Ajax::newRequest(self::REQUEST_ADD_SERVICE);
			$request->setRedirect('/'.Art_Router::getLayer().'/user_group');
			$this->view->request = $request; 
		}
	}
	
	function addUserAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ADD_USER) )
		{
			$response = Art_Ajax::newResponse();

			if ( Art_Router::getId() === Art_Model_User_Group::getAuthorizedId() ||
					 Art_Router::getId() === Art_Model_User_Group::getRegisteredId() )
			{
				$response->addAlert(__('module_user_group_add_cannot_be'));
			}	
			
			if ($response->isValid() )
			{
				//Get Ids
				$ids = $this->_getIdsFromCheckboxes();			

				if ( !empty($ids) )
				{
					foreach ( $ids as $id )
					{
						$userUserGroup = new Art_Model_User_X_User_Group();
						$userUserGroup->id_user_group = Art_Router::getId();
						$userUserGroup->id_user = $id;
						$userUserGroup->save();
					}

					$response->addMessage(__('module_user_group_add_success'));
					$response->willRedirect();
				}
				else 
				{
					$response->addAlert(__('module_user_group_add_none_selected'));
				}
			}
						
			$response->execute();
		}
		else
		{
			$userGroup = new Art_Model_User_Group(Art_Router::getId());

			if ( !$userGroup->isLoaded() )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			
			$this->view->userGroup = $userGroup;
			
			$request = Art_Ajax::newRequest(self::REQUEST_ADD_USER);
			$request->setRedirect('/'.Art_Router::getLayer().'/user_group');
			$this->view->request = $request; 
		}
	}
		
	function takeAwayUserSingleAction()
	{
  		if( Art_Ajax::isRequestedBy(self::REQUEST_TAKE_AWAY_USER_SINGLE) )
  		{
  			$response = Art_Ajax::newResponse();			
 
			$userUserGroup = new Art_Model_User_X_User_Group(Art_Router::getId());

			$groupId = $userUserGroup->id_user_group;
			
			if ( $groupId === Art_Model_User_Group::getAuthorizedId() ||
					 $groupId === Art_Model_User_Group::getRegisteredId() )
			{
				$response->addAlert(__('module_user_group_take_away_user_cannot'));
			}	
			
			if ($response->isValid() )
			{		
				if( $userUserGroup->isLoaded() )
				{
					if( !$userUserGroup->isPrivileged() )
					{
						$this->allowTo(Art_User::NO_ACCESS);
					}

					$userUserGroup->delete();	

					$response->addMessage(__('module_user_group_take_away_user_success'));
				}
				else
				{
					$response->addAlert(__('module_user_group_take_away_user_not_found'));
				}

				$response->addVariable('content', Art_Module::createAndRenderModule('user_group','detail',array('id'=>$groupId)));
			}

			$response->execute();
  		}
  		else
  		{
  			$this->showTo(Art_User::NO_ACCESS);
  		}
  	}
		
	function takeAwayServiceSingleAction()
	{
  		if( Art_Ajax::isRequestedBy(self::REQUEST_TAKE_AWAY_SERVICE_SINGLE) )
  		{
  			$response = Art_Ajax::newResponse();			
  
			$userGroupServicePrice = new User_Group_X_Service_Price(Art_Router::getId());
 
			$groupId = $userGroupServicePrice->id_user_group;
			
			if( $userGroupServicePrice->isLoaded() )
  			{
  				if( !$userGroupServicePrice->isPrivileged() )
  				{
  					$this->allowTo(Art_User::NO_ACCESS);
  				}

				if ( !empty(Service_Payment::fetchAll(array('id_user_group_x_service_price'=>$userGroupServicePrice->id)))  )
				{
					$response->addMessage(__('module_user_group_take_away_service_contained_in_payments'));
				}
				else 
				{
					$userGroupServicePrice->delete();	
					
					$response->addMessage(__('module_user_group_take_away_service_success'));
				}
			}
  			else
 			{
				$response->addAlert(__('module_user_group_take_away_service_not_found'));
  			}
 
			$response->addVariable('content', Art_Module::createAndRenderModule('user_group','detail',array('id'=>$groupId)));

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
  
			$userGroup = new Art_Model_User_Group(Art_Router::getId());
			
			if( $userGroup->isLoaded() )
  			{
  				if( !$userGroup->isPrivileged() )
  				{
  					$this->allowTo(Art_User::NO_ACCESS);
  				}

				$isIndependent = false;
				
				if ( empty(User_Group_X_Service_Price::fetchAll(array('id_user_group'=>$userGroup->id))) 
						&& empty(Art_Model_User_X_User_Group::fetchAll(array('id_user_group'=>$userGroup->id))) )
				{
					$isIndependent = true;
				}
				
				if ( $isIndependent )
				{
					$userGroup->delete();
					
					$response->addMessage(__('module_user_group_delete_success'));
				}
				else
				{
					$response->addMessage(__('module_user_group_delete_contains_user_or_service'));	
				}
			}
  			else
 			{
				$response->addAlert(__('module_user_group_delete_not_found'));
  			}

			$response->addVariable('content', Art_Module::createAndRenderModule('user_group'));
			
			$response->execute();
  		}
  		else
  		{
  			$this->showTo(Art_User::NO_ACCESS);
  		}
  	}
	
	/**
	 *	Load AJAX data and get ids from checkboxes
	 * 
	 *	@access protected
	 *	@return array
	 */
	protected function _getIdsFromCheckboxes()
	{
		$data = Art_Ajax::getData();
		$prefix_length = strlen(self::CHECKBOXES_PREFIX);
		
		$ids = array();
		foreach($data AS $item => $st)
		{
			if( strpos($item,self::CHECKBOXES_PREFIX) === 0 )
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
	
	static function getFieldsValidators()
	{
		return	array(
	'name'				=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message' => __('module_user_group_v_name_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message' => __('module_user_group_v_name_max')]),
	'description'		=> array(
		Art_Validator::MAX_LENGTH => ['value' => 60,'message' => __('module_user_group_v_description_max')]),
	'id_rights'			=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_user_group_v_id_rights_not_integer')]),
	'id_service'		=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_user_group_v_id_service_not_integer')]),
	'id_service_price'	=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_user_group_v_id_service_price_not_integer')]),
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
}