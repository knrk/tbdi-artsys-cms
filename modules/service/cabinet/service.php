<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/service/cabinet
 */
class Module_Service extends Art_Abstract_Module {
	
	const REQUEST_INTERESTED_TO_JOIN	= 'RyULyzMFmf';
	
	function indexAction() 
	{		
		$user = Art_User::getCurrentUser();
		
		$servicePrices = Helper_TBDev::getAllMinimalServicePricesForUser($user);
		
		$services = array();
		
		foreach ( $servicePrices as $servicePrice )
		{
			$services[$servicePrice->id_service] = $servicePrice->getService();
		}
		
		$this->view->services = $services;
		$this->view->servicePrices = $servicePrices;
		
		//-----------------------------------------//

		$services = Helper_TBDev::getAllActivatedServicesForUser($user);
		
		foreach ( $services as $value ) 
		{
			$value->active_to = Helper_TBDev::getServiceToForUser($user, $value);

			$value->active_to = Helper_TBDev::renderTrueFalseDateTo(dateSQL($value->active_to) < nice_date(dateSQL()), nice_date($value->active_to));
			
			Helper_TBDev::parseServiceSettings($value);
			
			$value->fa_icon = $value->icon;
		}
		
		Helper_TBDev::sortServices($services);
		
		$this->view->activatedServices = $services;
		
		$payments = Helper_TBDev::getAllPaymentsForUser($user);
			
		$this->view->payments = $payments;
		
		$serviceMembership = Helper_TBDev::getServiceByType(Helper_TBDev::MEMBERSHIP_TYPE);
		
		Helper_TBDev::parseServiceSettings($serviceMembership);
		
		$this->view->url_name = $url_name = $serviceMembership->article;
		
		$hasApplication = false;
		
		$contractName = Helper_TBDev_PDF::RESOURCE_CONTRACT.Art_User::getCurrentUser()->user_number.Helper_TBDev_PDF::RESOURCE_EXT_PDF;
		
		$contract = new Art_Model_Resource_Db($contractName);
				
		if ( $contract->isLoaded() )
		{
			$application = Art_Server::getHost().'/resource/'.$contract->hash;
			$hasApplication = true;
		}
		
		$this->view->hasApplication = $hasApplication;
		
		$this->view->application = isset($application) ? $application : null;
	}
	
	function embeddAction() 
	{		
		$user = Art_User::getCurrentUser();
		
		$services = Helper_TBDev::getAllActivatedServicesForUser($user);
		
		foreach ( $services as $value ) 
		{
			Helper_TBDev::parseServiceSettings($value);
			
			$value->fa_icon = $value->icon;
		}
		
		Helper_TBDev::sortServices($services);
		
		$this->view->services = $services;
	}

	function embeddMenuAction() 
	{		
		$user = Art_User::getCurrentUser();
		
		$isManager = Helper_TBDev::isManager($user);
		
		if ( $isManager )
		{
			$services = Service::fetchAllPrivileged();
		}
		else
		{
			$services = Helper_TBDev::getAllServicesForUser($user);
		}
		
		foreach ( $services as $value ) 
			/* @var $value Service */
		{
			Helper_TBDev::parseServiceSettings($value);
			
			$value->fa_icon = $value->icon;
			$value->article = new Article(array('url_name'=>$value->article));
			$value->promo = new Article(array('url_name'=>$value->promo));
			$value->active = Helper_TBDev::isServiceActivatedForUser($value, $user);
		}
		
		Helper_TBDev::sortServices($services);
		
		$this->view->services = $services;
		$this->view->isManager = $isManager;
	}
	
	function articleAction ()
	{
		$type = Art_Router::getId();

		if (is_numeric($type) )
		{
			$service = new Service($type);
		}
		else
		{
			$service = Helper_TBDev::getServiceByType($type);
		}
		
		if ( $service->isLoaded() )
		{
			Helper_TBDev::parseServiceSettings($service);

			$this->view->url_name = $url_name = $service->article;

			$isInvestment = false;

			if ( Helper_TBDev::INVESTMENT_TYPE == $service->type )
			{
				$this->view->investments = $investments = Service_Investment::fetchAllPrivilegedActive(array('visible'=>1));

				$investmentsId = array();

				foreach ($investments as $value) /* @var $value Service_Investment */ 
				{
					$investmentsId[] = $value->id;
				}

				$investmentValues = null;

				if ( !empty($investmentsId) )
				{	
					$where = new Art_Model_Db_Where(array('name'=>'id_service_investment', 'value'=>$investmentsId, 'relation'=>Art_Model_Db_Where::REL_IN));
					$where->add(array('name'=>'id_user','value'=>Art_User::getCurrentUser()->id));

					$investmentValues = Service_Investment_Value::fetchAllPrivilegedActive($where);
					$this->view->deposits = Service_Investment_Deposit::fetchAllPrivilegedActive(array('id_user'=>Art_User::getCurrentUser()->id));

					$hasCommission = false;

					foreach ($investmentValues as $value) /* @var $value Service_Investment_Value */ 
					{
						$value->investment = $value->getService_investment();
						if ( NULL != $value->commission && 0 != $value->commission ) 
						{
							$hasCommission = true;
						}
					}

					$this->view->hasCommission = $hasCommission;
				}

				$this->view->investmentValues = $investmentValues;

				$isInvestment = true;
			}

			$this->view->isInvestment = $isInvestment;

			$this->view->conditions = Art_Server::getHost().'/resource/'.$service->conditions;
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function promoAction ()
	{
		$type = Art_Router::getId();

		if (is_numeric($type) )
		{
			$service = new Service($type);
		}
		else
		{
			$service = Helper_TBDev::getServiceByType($type);
		}
		
		if ( $service->isLoaded() )
		{
			$user = Art_User::getCurrentUser();

			Helper_TBDev::parseServiceSettings($service);

			$this->view->url_name = $url_name = $service->promo;

			$this->view->conditions = Art_Server::getHost().'/resource/'.$service->conditions;

			$isActivated = Helper_TBDev::isServiceActivatedForUser($service, $user);

			$isManager = false;

			if ( Helper_TBDev::isManager($user) )
			{
				$isManager = true;
				$isActivated = true;
			}

			$this->view->isManager = $isManager;

			$this->view->isActivated = $isActivated;

			$isRequested = User_X_Request::fetchAllPrivileged(array('id_user'=>$user->id,'id_service'=>$service->id,'accepted'=>0));
			$this->view->isRequested = !empty($isRequested);

			if ( !$isActivated )
			{
				$this->view->isActivated = $isActivated;

				//Interested to join service by button
				$requestInterestedToJoin = Art_Ajax::newRequest(self::REQUEST_INTERESTED_TO_JOIN);
				$requestInterestedToJoin->setAction('/'.Art_Router::getLayer().'/service/interestedToJoin/'.$service->id);
				$requestInterestedToJoin->addUpdate('content', '.module_service_promo');
				$this->view->requestInterestedToJoin = $requestInterestedToJoin;		
			}
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function interestedToJoinAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_INTERESTED_TO_JOIN))
		{
			$service = new Service(Art_Router::getId());

			$user = Art_User::getCurrentUser();

			$userRequest = new User_X_Request();
			$userRequest->setUser($user);
			$userRequest->setService($service);
			$userRequest->save();
			
			Helper_Email::sendManServiceInterestedMail($user,$service);
			
			$response = Art_Ajax::newResponse();
			$response->addMessage(__('module_service_promo_interested_to_join_successful_send'));
			
			$response->addVariable('content', Art_Module::createAndRenderModule('service','promo'));
			
			$response->execute();
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
		
	function paymentsAction() 
	{		
		$user = Art_User::getCurrentUser();
		
		$type = Art_Router::getId();

		if (is_numeric($type) )
		{
			$service = new Service($type);
		}
		else
		{
			$service = Helper_TBDev::getServiceByType($type);
		}
		
		if ( $user->isLoaded() && $service->isLoaded() )
		{
			$payments = Helper_TBDev::getServicePaymentsForUser($user, $service);

			$this->view->payments = $payments;
		}
		else
		{
			redirect_to('/');
		}
	}
}