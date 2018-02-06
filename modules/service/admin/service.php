<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/service/admin
 */
class Module_Service extends Art_Abstract_Module {
	
	const REQUEST_ADD			= 'qxBaUBqwCk';
	const REQUEST_EDIT			= 'NaKymEKzqJ';	
	const REQUEST_EDIT_PRICE	= 'aSgmKKhyVC';
	const REQUEST_ADD_PRICE		= 'xXytBMcFhD';
	const REQUEST_MOVE_UP		= 'd1Sd1jB7cE';
	const REQUEST_MOVE_DOWN		= 'Jtv5fFhb6f';
	
	const REQUEST_DELETE_SINGLE			= 'FqLRYpjLMw';
	const REQUEST_DELETE_PRICE_SINGLE	= 'mQreJdwGmj';
	
	const REQUEST_ADD_SERVICE_TO_USER	= 'dWdv54sDsa';
	const REQUEST_ACTIVATE_SERVICE_FOR_USER	= 'ecjFcfgmAp';
	const REQUEST_DEACTIVATE_SERVICE_FOR_USER = 'fEd4uBe9dC';
	
	const REQUEST_UPLOAD_CONDITIONS		= 'hFebskENdk';
	
	const REQUEST_ACTIVATE_REQUEST_FOR_USER	= 'nrDjendDWo';
	
	function indexAction() 
	{		
		$articles = array();
		
		foreach (Article::fetchAllPrivileged() as $article) /* @var $article Article */ 
		{
			$articles[$article->url_name]['perex'] = $article->perex;
			$articles[$article->url_name]['title'] = $article->title;
		}
		
		$services = Service::fetchAllPrivileged(NULL,array('sort'));
		
		foreach ($services as $service) /* @var $service Service */ 
		{
			$service->a_detail = '/'.Art_Router::getLayer().'/service/detail/'.$service->id;
			$service->a_edit = '/'.Art_Router::getLayer().'/service/edit/'.$service->id;
			
			Helper_TBDev::parseServiceSettings($service);
			
			$service->title_article = $articles[$service->article]['title'];
			$service->title_promo = $articles[$service->promo]['title'];
			
			$service->perex_article = $articles[$service->article]['perex'];
			$service->perex_promo = $articles[$service->promo]['perex'];
		}
		
		$this->view->services = $services;
		
		$this->view->sort_buttons = Art_Model::sortButtons($services, 'id_category');
		
		//Delete item by button
		$delete_single_request = Art_Ajax::newRequest(self::REQUEST_DELETE_SINGLE); 
		$delete_single_request->setAction('/'.Art_Router::getLayer().'/service/deleteSingle/$id'); 
		$delete_single_request->addUpdate('content','.module_service_index'); 
		$delete_single_request->setConfirmWindow(__('module_service_delete_single_confirm')); 
		$this->view->delete_single_request = $delete_single_request;
				
		//Move service up
		$move_up_request = Art_Ajax::newRequest(self::REQUEST_MOVE_UP);
		$move_up_request->setAction('/'.Art_Router::getLayer().'/service/moveUp/$id');
		$move_up_request->addUpdate('content','.module_service_index');
		$this->view->move_up_request = $move_up_request;
		
		//Mode service down
		$move_down_request = Art_Ajax::newRequest(self::REQUEST_MOVE_DOWN);
		$move_down_request->setAction('/'.Art_Router::getLayer().'/service/moveDown/$id');
		$move_down_request->addUpdate('content','.module_service_index');
		$this->view->move_down_request = $move_down_request;
	}
	
	function newAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ADD) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			//Set each field validation options
			$fields = array_merge(Service::getCols('insert'), Service_Price::getCols('insert'), User_Group_X_Service_Price::getCols('insert'));
			
			//Data to insert to database
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
					
			//TODO validate settings
			/*if ( !in_array($data['icon'], Helper_TBDev::getAllServicesFaIcons()) )
			{
				$response->addAlert(__('module_service_wrong_icon'));
			}*/
			
			//Everything is valid
			if( $response->isValid() )
			{
				if ( '-----' == $data['conditions_name'] )
				{
					$data['conditions_name'] = null;
				}
				
				$from = Helper_TBDev::getDate($data["from_year"], $data["from_month"], $data["from_day"]);
				$to = Helper_TBDev::getDate($data["to_year"], $data["to_month"], $data["to_day"]);

				$settings = array('icon'=>$data['icon'],'article'=>$data['article_url_name'],'promo'=>$data['promo_url_name'],'conditions'=>$data['conditions_name']);
				$settings = json_encode($settings);
				
				$service = new Service();
				$service->setDataFromArray($sql_data);
				$service->settings = $settings;
				$service->save();
				
				$servicePrice = new Service_Price();
				$servicePrice->setDataFromArray($sql_data);
				$servicePrice->setService($service);
				$servicePrice->is_default = 1;
				$servicePrice->time_interval = Helper_TBDev::setTimeInterval($data['time_interval_value'], $data['time_interval_type']);
				$servicePrice->save();
				
				$userGroup = new Art_Model_User_Group();
				$userGroup->name = $data['name'].Helper_TBDev::GROUP_SERVICE_MEMBERS;
				$userGroup->id_rights = 2;
				$userGroup->save();
				
				$userGroupServicePrice = new User_Group_X_Service_Price();
				$userGroupServicePrice->setDataFromArray($sql_data);
				$userGroupServicePrice->setServicePrice($servicePrice);
				$userGroupServicePrice->setUserGroup($userGroup);
				$userGroupServicePrice->time_from = dateSQL($from);
				$userGroupServicePrice->time_to = dateSQL($to);
				$userGroupServicePrice->save();
				
				$response->addMessage(__('module_service_add_success'));
				$response->willRedirect();
			}
			
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
			
			$this->view->intervalTypes = Helper_TBDev::getIntervalTypeRange();			
			
			$this->view->articles = Article::fetchAllPrivileged();
			
			$icons = Helper_TBDev::getAllServicesFaIcons();
			
			$this->view->icons = $icons;
			
			$directory = Helper_TBDev::CONDITIONS_DIRECTORY;
			$scannedDirectory = array_merge(array('-----'),Helper_Default::getFilesFromDirectory($directory));

			$this->view->scannedDirectory = $scannedDirectory;
			
			$request = Art_Ajax::newRequest(self::REQUEST_ADD);
			$request->setRedirect('/'.Art_Router::getLayer().'/service');
			$this->view->request = $request; 
		}
	}
	
	function editAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();

			$service = new Service(Art_Router::getId());		
			
			if ( !$service->isLoaded() )
			{
				$response->addAlert(__('module_service_not_found'));
			}
			
			$fields = array_merge(Service::getCols('update'));
			
			//Data to insert to database
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
			
			//TODO validate settings
			/*if ( !in_array($data['icon'], Helper_TBDev::getAllServicesFaIcons()) )
			{
				$response->addAlert(__('module_service_wrong_icon'));
			}*/
			
			//Everything is valid
			if( $response->isValid() )
			{
				if ( '-----' == $data['conditions_name'] )
				{
					$data['conditions_name'] = null;
				}
				
				$settings = array('icon'=>$data['icon'],'article'=>$data['article_url_name'],'promo'=>$data['promo_url_name'],'conditions'=>$data['conditions_name']);
				$settings = json_encode($settings);

				$service->setDataFromArray($sql_data);
				$service->settings = $settings;
				$service->save();
				
				$response->addMessage(__('module_service_add_success'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$service = new Service(Art_Router::getId());
		
			if( !$service->isLoaded() )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			elseif( !$service->isPrivileged() )
			{
				$this->allowTo(Art_User::NO_ACCESS);
			}
			
			Helper_TBDev::parseServiceSettings($service);
			
			$this->view->isInvestment = $isInvestment = Helper_TBDev::isServiceInvestment($service);
			
			if ( $isInvestment )
			{
				 $service->defaultValue = Helper_Default::getDefaultValue('investment',0);
			}
			
			$this->view->service = $service;
			
			$directory = Helper_TBDev::CONDITIONS_DIRECTORY;
			$scannedDirectory = array_merge(array('-----'),Helper_Default::getFilesFromDirectory($directory));

			$this->view->scannedDirectory = $scannedDirectory;
			
			$this->view->articles = Article::fetchAllPrivileged();
			
			$icons = Helper_TBDev::getAllServicesFaIcons();
			$this->view->icons = $icons;
			
			$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
			$request->setRedirect('/'.Art_Router::getLayer().'/service');
			$this->view->request = $request;
		}
	}
	
	function detailAction()
	{
		$id = $this->getParams("id", Art_Router::getId());

		$this->view->ID = $id;

		$service = new Service($id);

		if ( $service->isLoaded() )
		{
			Helper_TBDev::parseServiceSettings($service);
			
			$this->view->isInvestment = $isInvestment = Helper_TBDev::isServiceInvestment($service);
			
			if ( $isInvestment )
			{
				 $defValue = Helper_Default::getDefaultValue('investment');
				 if ( !empty($defValue) )
				 {
					 $service->defaultValue = $defValue;
				 }	 
			}
			
			$this->view->service = $service; 
						
			$servicePrices = Service_Price::fetchAllPrivileged(array('id_service'=>$service->id));
			
			foreach ($servicePrices as $value) /* @var $value Service_Price */
			{
				$value->a_edit = '/'.Art_Router::getLayer().'/service/editPrice/'.$value->id;
			}
			
			$this->view->servicePrices = $servicePrices; 
			
			//Delete item by button
			$delete_single_request = Art_Ajax::newRequest(self::REQUEST_DELETE_PRICE_SINGLE); 
			$delete_single_request->setAction('/'.Art_Router::getLayer().'/service/deletePriceSingle/$id'); 
			$delete_single_request->addUpdate('content','.module_service_detail'); 
			$delete_single_request->setConfirmWindow(__('module_service_delete_price_single_confirm')); 
			$this->view->delete_single_request = $delete_single_request;
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function newPriceAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ADD_PRICE) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			//Set each field validation options
			$fields = array_merge(Service_Price::getCols('insert'),array('time_interval_value','time_interval_type'));
			
			Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
					
			//Everything is valid
			if( $response->isValid() )
			{
				$servicePrice = new Service_Price();
				$servicePrice->price = $data["price"];
				$servicePrice->time_interval = Helper_TBDev::setTimeInterval($data["time_interval_value"],$data["time_interval_type"]);
				$servicePrice->id_service = Art_Router::getId();
				$servicePrice->is_default = (Helper_Default::isPropertyChecked($data,"is_default")) ? 1 : 0;
				
				//Only one Service_Price per Service may be a default
				if ( $servicePrice->is_default )
				{
					$sPrices = Service_Price::fetchAll(array('id_service'=>$servicePrice->id_service));
					
					foreach ( $sPrices as $sPrice )
					{
						if ( 0 !== $sPrice->is_default )
						{
							$sPrice->is_default = 0;
							$sPrice->save();
						}
					}
				}
				
				$servicePrice->save();

				$response->addMessage(__('module_service_price_add_success'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$service = new Service(Art_Router::getId());
			
			if ( !$service->isLoaded() )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			
			$this->view->service = $service;
			
			$this->view->intervalTypes = Helper_TBDev::getIntervalTypeRange();
			
			$request = Art_Ajax::newRequest(self::REQUEST_ADD_PRICE);
			$request->setRedirect('/'.Art_Router::getLayer().'/service');
			$this->view->request = $request; 
		}
	}
	
	function editPriceAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT_PRICE))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$servicePrice = new Service_Price(Art_Router::getId());		
			
			if ( !$servicePrice->isLoaded() )
			{
				$response->addAlert(__('module_service_price_not_found'));
			}
			
			//Set each field validation options
			$fields = array_merge(Service_Price::getCols('update'),array('time_interval_value','time_interval_type'));
			
			Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
			
			//Everything is valid
			if( $response->isValid() )
			{					
				$old_is_default = $servicePrice->is_default;

				$servicePrice->price = $data["price"];
				$servicePrice->time_interval = Helper_TBDev::setTimeInterval($data["time_interval_value"],$data["time_interval_type"]);
				$servicePrice->is_default = (Helper_Default::isPropertyChecked($data,"is_default")) ? 1 : 0;

				//Only one Service_Price per Service may be a default
				if ( $servicePrice->is_default !== $old_is_default )
				{
					$sPrices = Service_Price::fetchAll(array('id_service'=>$servicePrice->id_service));
					
					if ( $servicePrice->is_default )
					{
						foreach ( $sPrices as $sPrice )
						{
							if ( $sPrice->id == $servicePrice->id )
							{
								continue;
							}
							if ( 0 !== $sPrice->is_default )
							{
								$sPrice->is_default = 0;
								$sPrice->save();
							}
						}
					}
					else
					{						
						if ( empty($sPrices) )
						{
							$servicePrice->is_default = 1;
							
						}
						else
						{
							$sPrice = new Service_Price($sPrices[0]->id);
							$sPrice->is_default = 1;
							$sPrice->save();
						}
					}
				}
				
				$servicePrice->save();
				
				$response->addMessage(__('module_service_price_edit_success'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$servicePrice = new Service_Price(Art_Router::getId());
		
			if( !$servicePrice->isLoaded() )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			elseif( !$servicePrice->isPrivileged() )
			{
				$this->allowTo(Art_User::NO_ACCESS);
			}
			
			$this->view->servicePrice = $servicePrice;
			
			$service = $servicePrice->getService();
			
			$this->view->service = $service;
			
			$parsedTimeInterval = Helper_TBDev::getParsedTimeInterval($servicePrice->time_interval);
			
			$this->view->intervalValue = (isset($parsedTimeInterval["value"])) ? $parsedTimeInterval["value"] : null;
			$this->view->intervalType = (isset($parsedTimeInterval["type"])) ? $parsedTimeInterval["type"] : null;
			
			$this->view->intervalTypes = Helper_TBDev::getIntervalTypeRange();
						
			$request = Art_Ajax::newRequest(self::REQUEST_EDIT_PRICE);
			$request->setRedirect('/'.Art_Router::getLayer().'/service');
			$this->view->request = $request;
		}
	}
		
	function deleteSingleAction()
	{
  		if( Art_Ajax::isRequestedBy(self::REQUEST_DELETE_SINGLE) )
  		{
  			$response = Art_Ajax::newResponse();			
  
			$service = new Service(Art_Router::getId());
 
			if( $service->isLoaded() )
  			{
  				if( !$service->isPrivileged() )
  				{
  					$this->allowTo(Art_User::NO_ACCESS);
  				}

				$isIndependent = false;
				
				if ( empty(Service_Price::fetchAll(array('id_service'=>$service->id))) )
				{
					$isIndependent = true;
				}
				
				if ( $isIndependent )
				{
					$service->delete();
					$response->addMessage(__('module_service_delete_success'));
				}
				else
				{
					$response->addAlert(__('module_service_delete_contains_prices'));	
				}				
			}
  			else
 			{
				$response->addAlert(__('module_service_delete_not_found'));
  			}
 
			$response->addVariable('content', Art_Module::createAndRenderModule('service'));

			$response->execute();
  		}
  		else
  		{
  			$this->showTo(Art_User::NO_ACCESS);
  		}
  	}
	
	function deleteConditionsAction()
	{
  		if( Art_Ajax::isRequestedBy(self::REQUEST_DELETE_SINGLE) )
  		{
  			$response = Art_Ajax::newResponse();			
  
			$services = Service::fetchAllPrivileged();
			
			$err_type = 0;
			
			$isIndependent = true;
			
			foreach ($services as $value) /* @var $value Service */ 
			{
				Helper_TBDev::parseServiceSettings($value);

				if ( Art_Router::getId() == $value->conditions )
				{
					$isIndependent = false;
					$err_type = 1;
					break;
				}
			}
			
			$stanovyName = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_STANOVY);
			
			if ( NULL !== $stanovyName )
			{
				$stanovyPDF = new Art_Model_Resource_Db($stanovyName);
				
				if ( $stanovyPDF->isLoaded() && Art_Router::getId() == $stanovyPDF->name )
				{
					$isIndependent = false;
					$err_type = 2;
				}
			}

			if ( $isIndependent )
			{
				foreach (Art_Model_Resource_Db::fetchAll(array('name'=>Art_Router::getId())) as $value )
				{
					$value->delete();
				}
				//TODO delete
				unlink(Art_Server::getDocumentRoot().'/'.Helper_TBDev::CONDITIONS_DIRECTORY.'/'.Art_Router::getId());
				$response->addMessage(__('module_service_conditions_delete_success'));
			}
			else
			{
				if ( 1 == $err_type )
				{
					$response->addAlert(__('module_service_conditions_delete_contains_services'));	
				}
				else	
				{
					$response->addAlert(__('module_service_conditions_delete_contains_charter'));	
				}
			}				
 
			$response->addVariable('content', Art_Module::createAndRenderModule('service','conditions'));

			$response->execute();
  		}
  		else
  		{
  			$this->showTo(Art_User::NO_ACCESS);
  		}
  	}
	
	function deletePriceSingleAction()
	{
  		if( Art_Ajax::isRequestedBy(self::REQUEST_DELETE_PRICE_SINGLE) )
  		{
  			$response = Art_Ajax::newResponse();			
  
			$servicePrice = new Service_Price(Art_Router::getId());
			
			$service = $servicePrice->getService();
			
			if( $servicePrice->isLoaded() )
  			{
  				if( !$servicePrice->isPrivileged() )
  				{
  					$this->allowTo(Art_User::NO_ACCESS);
  				}

				$isIndependent = false;
				
				if ( empty(User_Group_X_Service_Price::fetchAll(array('id_service_price'=>$servicePrice->id))) )
				{
					$isIndependent = true;
				}
				
				$wasDefault = $servicePrice->is_default;
				
				if ( $isIndependent )
				{
					$servicePrice->delete();
					$response->addMessage(__('module_service_price_delete_success'));
				}
				else
				{
					$response->addMessage(__('module_service_price_delete_contained_in_group'));	
				}
				
				//Set another default
				if ( $wasDefault )
				{
					$newDefault = Service_Price::fetchAllPrivileged(array('id_service'=>$service->id));
					
					if ( !empty($newDefault) )
					{
						$newDefault[0]->is_default = 1;
						$newDefault[0]->save();
					}
				}
			}
  			else
 			{
				$response->addAlert(__('module_service_price_delete_not_found'));
  			}
 
			$response->addVariable('content', Art_Module::createAndRenderModule('service','detail',array('id'=>$service->id)));

			$response->execute();
  		}
  		else
  		{
  			$this->showTo(Art_User::NO_ACCESS);
  		}
  	}
	
	function activateAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ACTIVATE_SERVICE_FOR_USER) )
  		{
  			$response = Art_Ajax::newResponse();			
			$data = Art_Ajax::getData();

			$user = new Art_Model_User(Art_Router::getId());
			$service = new Service($data['id_service']);
			
			$userService = new User_X_Service();
			$userService->setUser($user);
			$userService->setService($service);
			$userService->activated = 1;
			$userService->activated_date = dateSQL(Helper_TBDev::getDate($data['from_day'], $data['from_month'], $data['from_year']));
			$userService->save();
			
			if ( Helper_TBDev::INVESTMENT_TYPE == $service->type )
			{
				$serviceInvestment = new Service_Investment_Value();
				$serviceInvestment->setUser($user);
				$serviceInvestment->interest = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_INVESTMENT_INTEREST);
				$serviceInvestment->month = $data['from_month'];
				$serviceInvestment->year = $data['from_year'];
				
				if ( !(new Service_Investment_Value(array('id_user'=>$user->id,'month'=>$data['from_month'],'year'=>$data['from_year'])))->isLoaded() )
				{
					$serviceInvestment->save();
				}
				
				$nextMonth = $data['from_month'] + 1;
				$nextYear = $data['from_year'];
				
				if ( $nextMonth > 12 ) 
				{
					$nextMonth = 1;
					$nextYear += 1;
				}
				
				$serviceInvestment = new Service_Investment_Value();
				$serviceInvestment->setUser($user);
				$serviceInvestment->interest = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_INVESTMENT_INTEREST);
				$serviceInvestment->month = $nextMonth;
				$serviceInvestment->year = $nextYear;
				
				if ( !(new Service_Investment_Value(array('id_user'=>$user->id,'month'=>$nextMonth,'year'=>$nextYear)))->isLoaded() )
				{
					$serviceInvestment->save();
				}
			}
			
			$response->addMessage(__('module_service_activate_success'));
			$response->willRedirect();		
			$response->execute();
  		}
  		else
  		{
			$this->view->days = Helper_TBDev::getDayRange();
			$this->view->months = Helper_TBDev::getMonthRange();
			$this->view->years = Helper_TBDev::getServiceYearRange();
			
			$this->view->currentMonth = Helper_TBDev::getCurrentMonth();
			$this->view->currentYear = Helper_TBDev::getCurrentYear();
			$this->view->nextYear = Helper_TBDev::getCurrentYear();
			
			$user = new Art_Model_User(Art_Router::getId());
			
			$activatedServices = Helper_TBDev::getAllActivatedServicesForUser($user);
			
			$userServices = Helper_TBDev::getAllServicesForUser($user);			
			
			foreach ($userServices as $key => $value) /* @var $value Service */ 
			{
				if ( in_array($value, $activatedServices) || $value->type == Helper_TBDev::MEMBERSHIP_TYPE )
				{
					unset($userServices[$key]);
				}
			}
			
  			$this->view->services = $userServices;
			
			$request = Art_Ajax::newRequest(self::REQUEST_ACTIVATE_SERVICE_FOR_USER);
			$request->setRedirect('/'.Art_Router::getLayer().'/users/detail/'.Art_Router::getId());
			$this->view->request = $request;
  		}
	}

	function deactivateAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_DEACTIVATE_SERVICE_FOR_USER) )
  		{
  			$response = Art_Ajax::newResponse();			
			$separator = strpos(Art_Router::getId(), '-');	
			
			if ( false === $separator )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			
			$user = new Art_Model_User(substr(Art_Router::getId(),0,$separator+1));
			$service = new Service(substr(Art_Router::getId(),$separator+1));
			
			$userService = new User_X_Service(array('id_user'=>$user->id,'id_service'=>$service->id,'activated'=>'1'));

			if ( $userService->isLoaded() )
			{
				$userService->activated = 0;
				$userService->deactivated_date = dateSQL();
				$userService->save();
				
				$response->addMessage(__('module_service_deactivate_success'));
			}
			else
			{
				$response->addAlert(__('module_service_deactivate_error'));
			}
		
			$response->execute();
  		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function activaterequestAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ACTIVATE_REQUEST_FOR_USER) )
  		{
  			$response = Art_Ajax::newResponse();			
			$data = Art_Ajax::getData();

			$separator = strpos(Art_Router::getId(), '-');	
			
			if ( false === $separator )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			
			$user = new Art_Model_User(substr(Art_Router::getId(),0,$separator+1));
			$service = new Service(substr(Art_Router::getId(),$separator+1));
			
			$userRequest = new User_X_Request(array('id_user'=>$user->id,'id_service'=>$service->id,'accepted'=>'0'));
			
			if( $userRequest->isLoaded() )
  			{
  				if( !$userRequest->isPrivileged() )
  				{
  					$this->allowTo(Art_User::NO_ACCESS);
  				}
				
				$userService = new User_X_Service();
				$userService->setUser($user);
				$userService->setService($service);
				$userService->activated = 1;
				$userService->activated_date = Helper_TBDev::getDate($data['from_day'], $data['from_month'], $data['from_year']);
				$userService->save();
				
				$userRequest->accepted = 1;
				$userRequest->accepted_date = dateSQL();
				$userRequest->save();
				
				if ( Helper_TBDev::INVESTMENT_TYPE == $service->type )
				{
					$serviceInvestment = new Service_Investment_Value();
					$serviceInvestment->setUser($user);
					$serviceInvestment->interest = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_INVESTMENT_INTEREST);
					$serviceInvestment->month = $data['from_month'];
					$serviceInvestment->year = $data['from_year'];

					if ( !(new Service_Investment_Value(array('id_user'=>$user->id,'month'=>$data['from_month'],'year'=>$data['from_year'])))->isLoaded() )
					{
						$serviceInvestment->save();
					}

					$nextMonth = $data['from_month'] + 1;
					$nextYear = $data['from_year'];

					if ( $nextMonth > 12 ) 
					{
						$nextMonth = 1;
						$nextYear += 1;
					}

					$serviceInvestment = new Service_Investment_Value();
					$serviceInvestment->setUser($user);
					$serviceInvestment->interest = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_INVESTMENT_INTEREST);
					$serviceInvestment->month = $nextMonth;
					$serviceInvestment->year = $nextYear;

					if ( !(new Service_Investment_Value(array('id_user'=>$user->id,'month'=>$nextMonth,'year'=>$nextYear)))->isLoaded() )
					{
						$serviceInvestment->save();
					}
				}
				
				$response->addMessage(__('module_service_request_complete_success'));
				$response->willRedirect();	
			}
  			else
 			{
				$response->addAlert(__('module_service_request_complete_not_found'));
  			}
			
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
			
			$separator = strpos(Art_Router::getId(), '-');

			if ( false === $separator )
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
			
			$user = new Art_Model_User(substr(Art_Router::getId(),0,$separator+1));
			$service = new Service(substr(Art_Router::getId(),$separator+1));
			
			$this->view->user = $user;
			$this->view->service = $service;
			
			$this->view->type = Art_Router::getId();
			
			$request = Art_Ajax::newRequest(self::REQUEST_ACTIVATE_REQUEST_FOR_USER);
			$request->setRedirect('/'.Art_Router::getLayer().'/users/requests/');
			$this->view->request = $request;
  		}
	}	
	
	function addAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ADD_SERVICE_TO_USER) )
  		{
  			$response = Art_Ajax::newResponse();			
			$data = Art_Ajax::getData();

			$user = new Art_Model_User(Art_Router::getId());
			$service = new Service($data['id_service']);
			
			$userGroup = null;
			
			foreach ( Art_Model_User_Group::fetchAllPrivileged() as $value) /* @var $value Art_Model_User_Group */ 
			{			
				$name = strtolower($value->name);
				if ( false !== strstr($name, $service->type) )
				{
					$userGroup = $value;
					break;
				}
			}

			if ( NULL === $userGroup )
			{
				$response->addAlert(__('module_service_add_not_found'));
			}
			else 
			{
				$userUserGroup = new Art_Model_User_X_User_Group();
				$userUserGroup->setGroup($userGroup);
				$userUserGroup->setUser($user);
				$userUserGroup->save();

				$response->addMessage(__('module_service_add_success'));
				$response->willRedirect();
			}
				
			$response->execute();
  		}
  		else
  		{			
			$user = new Art_Model_User(Art_Router::getId());
			
			$services = Service::fetchAllPrivileged();
			
			$userServices = Helper_TBDev::getAllServicesForUser($user);			
			
			foreach ($services as $key => $value) /* @var $value Service */ 
			{
				if ( in_array($value, $userServices) )
				{
					unset($services[$key]);
				}
			}
			
  			$this->view->services = $services;
			
			$request = Art_Ajax::newRequest(self::REQUEST_ADD_SERVICE_TO_USER);
			$request->setRedirect('/'.Art_Router::getLayer().'/users/detail/'.Art_Router::getId());
			$this->view->request = $request;
  		}
	}
	
	function conditionsAction()
	{
		$directory = Helper_TBDev::CONDITIONS_DIRECTORY;
		$scannedDirectory = Helper_Default::getFilesFromDirectory($directory);

		$this->view->scannedDirectory = $scannedDirectory;
		
		$this->view->directory = Art_Server::getHost().'/resource/';
		
		//Delete item by button
		$delete_single_request = Art_Ajax::newRequest(self::REQUEST_DELETE_SINGLE); 
		$delete_single_request->setAction('/'.Art_Router::getLayer().'/service/deleteConditions/$id'); 
		$delete_single_request->addUpdate('content','.module_service_conditions'); 
		$delete_single_request->setConfirmWindow(__('module_service_conditions_delete_confirm')); 
		$this->view->delete_single_request = $delete_single_request;
	}
	
	//TODO !!!
	function conditionscompleteAction()
	{
		$response = Art_Ajax::newResponse();
		$data = Art_Main::getPost();

		Helper_Default::getValidatedSQLData(array('filename'), self::getFieldsValidators(), $data, $response);
		
		$file = Art_Model_File::fromPost('conditions');

		$filename = strtolower($data['filename']);
		
		if ( 0 === $file->getSize() )
		{
			$response->addAlert(__('module_service_conditions_upload_filesize_min'));
		}

		if ( $response->isValid() )
		{		
			$filepath = Helper_TBDev::CONDITIONS_DIRECTORY;
			
			$file->renameWithSameExtension($filename);
	
			$newBasename = $file->getBasename();
			
			if( !file_exists( dirname($filepath.'/'.$newBasename) ) )
			{
				mkdir(dirname($filepath.'/'.$newBasename), 0777, true );
			}

			$file->moveTo($filepath);	
			
			$filehash = rand_str();
			
			$rsrc = new Art_Model_Resource_Db;
			$rsrc->hash = $filehash;
			$rsrc->name = $file->getBasename();
			$rsrc->path = $filepath.'/'.$newBasename;
			$rsrc->size = filesize($rsrc->path);
			$rsrc->rights_read = 0;
			$rsrc->rights_write = 0;
			$rsrc->save();			
			
			$response->addMessage(__('module_service_conditions_upload_success'));
		}
		
		redirect_to(Art_Router::getLayer().'/service/conditions');
	}
	
	function moveUpAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_MOVE_UP) )
		{
			$this->_moveByDirection('up');
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function moveDownAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_MOVE_DOWN) )
		{
			$this->_moveByDirection('down');
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	//Move service up or down
	protected function _moveByDirection( $dir )
	{
		$response = Art_Ajax::newResponse();

		//Load service
		$service_1 = new Service(Art_Router::getId());

		if( $service_1->isLoaded() && $service_1->isPrivileged() )
		{
			switch( strtolower($dir) )
			{
				case 'up':
					$service_2 = $service_1->getUpper();
					break;
				case 'down':
					$service_2 = $service_1->getDowner();
					break;
			}

			if( $service_2->isLoaded() && $service_2->isPrivileged() )
			{
				Service::swapPositions($service_1, $service_2);
			}
		}

		$response->addVariable('content', Art_Module::createAndRenderModule('service'));
		$response->execute();
	}
	
	static function getFieldsValidators()
	{
		return	array(
	'type'				=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_service_v_type_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_service_v_type_max')]),
	'name'				=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_service_v_name_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_service_v_name_max')]),
	'price'				=> array(
		Art_Validator::MIN_VALUE => ['value' => 0,'message'		=> __('module_service_v_price_min')],
		Art_Validator::IS_INTEGER => ['message'					=> __('module_service_v_price_not_integer')]),
	'time_interval_value'	=> array(
		Art_Validator::MIN_VALUE => ['value' => 1,'message'		=> __('module_service_v_time_interval_value_min')],
		Art_Validator::IS_INTEGER => ['message'					=> __('module_service_v_time_interval_value_not_integer')]),
	'time_interval_type'	=> array(
		Art_Validator::IS_STRING => ['message'					=> __('module_service_v_time_interval_type_not_string')]),
	'filename'				=> array(
		Art_Validator::MIN_LENGTH => ['value' => 1,'message'	=> __('module_service_v_filename_min')],
		Art_Validator::MAX_LENGTH => ['value' => 60,'message'	=> __('module_service_v_filename_max')]),
		);
	}
}