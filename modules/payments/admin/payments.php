<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/payments/admin
 */
class Module_Payments extends Art_Abstract_Module {
	
	const REQUEST_EDIT	= 'TXLbCeEups';
	const REQUEST_ADD	= 'yFAkrkYfFb';
	const REQUEST_DELETE_SINGLE	= 'iRnSMflnEp';
	
	function indexAction() {
		$payments = Service_Payment::fetchAllPrivileged(null, array(array('name' => 'id', 'type' => Art_Model_Db_Order::TYPE_DESC)));
		$lastPayments = array();
		
		foreach ($payments as $key => $value) {
			$user = new Art_Model_User($value->id_user);
			$userData = $user->getData();
			
			$userPaid = new Art_Model_User($value->id_user_paid_by);
			$userPaidData = $userPaid->getData();
			
			if (Helper_TBDev::isUserRepresentsCompany($user)) {
				$payments[$key]->user_fullname = Helper_TBDev::getCompanyAddress($user)->company_name;
			} else {
				$payments[$key]->user_fullname = $userData->fullname;
			}
			
			if (Helper_TBDev::isUserRepresentsCompany($userPaid)) {
				$payments[$key]->user_paid_by_fullname = Helper_TBDev::getCompanyAddress($userPaid)->company_name;
			} else {
				$payments[$key]->user_paid_by_fullname = $userPaidData->fullname;
			}	
			
			$userGroupServicePrice = new User_Group_X_Service_Price($value->id_user_group_x_service_price);
			$servicePrice = $userGroupServicePrice->getServicePrice();
			$service = $servicePrice->getService();
			$payments[$key]->service_name = $service->name;
			$payments[$key]->service_id = $service->id;
			$payments[$key]->user_id = $user->id;
			$payments[$key]->user_number = $user->user_number;
			
			if (!isset($lastPayments[$value->id_user][$service->id])) {
				$lastPayments[$value->id_user][$service->id] = $key;
			}	
		}

		$this->view->lastPayments = $lastPayments;
		$this->view->payments = $payments;
		
		//Delete item by button
		$delete_single_request = Art_Ajax::newRequest(self::REQUEST_DELETE_SINGLE); 
		$delete_single_request->setAction('/' . Art_Router::getLayer() . '/payments/deleteSingle/$id'); 
		$delete_single_request->addUpdate('content','.module_payments_index'); 
		$delete_single_request->setConfirmWindow(__('module_payments_delete_single_confirm')); 
		$this->view->delete_single_request = $delete_single_request;
	}
	
	function deleteSingleAction() {
		if (Art_Ajax::isRequestedBy(self::REQUEST_DELETE_SINGLE)) {
			
			$response = Art_Ajax::newResponse();	
			$payment = new Service_Payment(Art_Router::getId());
			
			if ($payment->isLoaded()) {
				$payment->delete();
				
				$response->addMessage(__('module_payments_delete_success'));
				$response->addVariable('content', Art_Module::createAndRenderModule('payments'));
			} else {
				$response->addAlert(__('module_payments_delete_fail'));
			}

			$response->execute();
		} else {
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
	
	function newAction() {
		if (Art_Ajax::isRequestedBy(self::REQUEST_ADD)) {
			
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			//Set each field validation options
			$fields = Service_Payment::getCols('insert');
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
					
			//Everything is valid
			if ($response->isValid()) {
				$servicePayment = new Service_Payment();
				$servicePayment->setDataFromArray($sql_data);

				$servicePayment->received_date = dateSQL($data['recieved_date_submit']);
				$servicePayment->save();
				
				$response->addMessage(__('module_payments_new_success'));
				$response->willRedirect();
			}
			
			$response->execute();

		} else {

			$this->view->currentDate = date("Y-m-d");
			
			$usersData = Art_Model_User_Data::fetchAllPrivileged();
			// $usersData = Art_Model_User_Data::fetchSelected(array('id', 'id_user', 'name', 'surname'));
			
			$users = array();
			$usersWithActService = array();
			$companies = array();
			$companiesWithActService = array();
			$managers = array();
			$servicesPerUser = array();
			
			foreach ($usersData as $userData) {

				$user = $userData->getUser();
				$isCompany = Helper_TBDev::isUserRepresentsCompany($user);
				$servicePrices = Helper_TBDev::getServicePricesForServiceForUser($user, new Service(array('type' => Helper_TBDev::MEMBERSHIP_TYPE)));
				$actServices = Helper_TBDev::getPropertyFromObjectsInArray(Helper_TBDev::getAllActivatedServicesForUser($user), 'name');
				$hasUserActService = false;
				$services = array();
				
				if ($isCompany) {
					$user->fullname = Helper_TBDev::getCompanyAddress($user)->company_name;
				}

				$user->surname = $userData->surname;
				
				foreach ($servicePrices as $value) {
					$service['id_user_group_x_service_price'] = $value->id_user_group_x_service_price;
					$service['name'] = $value->getService()->name;
					$service['price'] = $value->price;
					$service['time_interval'] = $value->time_interval;
					
					//Separate activated services
					if (in_array($service['name'], $actServices)) {

						$services[] = $service;
						
						if (!$hasUserActService) {
							if ($isCompany) {
								$companiesWithActService[] = $user;
							} else {
								$usersWithActService[] = $user;
							}
							$hasUserActService = true;
						}
					}
				}
				
				$servicesPerUser[$user->id] = $services;
				$services = Helper_TBDev::getAllActivatedServicesForUser($user);
				$user->servicePrices = $servicePrices;
				
				if (Helper_TBDev::isManager($user)) {
					//nerobrazeni Admin uctu :)
					if ($user->user_number != 10) {
						$managers[] = $user;
					}
				}
				elseif ($isCompany) {
					$companies[] = $user;
				}
				else
				{
					$users[] = $user;
				}
			}

			$this->view->users = array_merge(Helper_TBDev::getSortedArray($users, 'surname'), $companies, $managers);
			$this->view->usersWithActService = array_merge(Helper_TBDev::getSortedArray($usersWithActService, 'surname'), $companiesWithActService);
					
			$this->view->servicesPerUser = json_encode($servicesPerUser);
	
			$request = Art_Ajax::newRequest(self::REQUEST_ADD);
			$request->setRedirect('/' . Art_Router::getLayer() . '/payments');
			$this->view->request = $request; 
		}
	}
		
	static function getFieldsValidators()
	{
		return	array(
	'value'				=> array(
		Art_Validator::MIN_VALUE => ['value' => 1,'message' => __('module_payments_v_value_min')],
		Art_Validator::IS_INTEGER => ['message' => __('module_payments_v_value_not_integer')]),
	'id_user'				=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_payments_v_id_user_not_integer')]),
	'id_user_paid_by'		=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_payments_v_id_user_paid_by_not_integer')]),
	'id_user_group_x_service_price'		=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_payments_v_id_user_group_x_service_price_not_integer')]),
			);
	}
}