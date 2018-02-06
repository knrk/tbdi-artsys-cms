<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/investment/admin
 */
class Module_Investment extends Art_Abstract_Module {
	
	const REQUEST_ADD			= 'fE5gI1apB1';
	const REQUEST_DELETE_SINGLE	= 'g4Kn2mXd4r';
    const REQUEST_TERMINATE_SINGLE = 'f2cEh4arD6';
	const REQUEST_EDIT			= 'f1rE2cGSew';
	const REQUEST_NEW_MONTH		= 'R4vjW8s6bA';
	const REQUEST_DELETE_INVESTMENT	= 'g4Ev1hSwr2g';
	const REQUEST_PUBLISH_INVESTMENT	= 'f5rD1awiB1';
	const REQUEST_UNPUBLISH_INVESTMENT	= 'uz1F1eH2dw';
	const REQUEST_EDIT_SINGLE			= '5Ffse1jZ1q';
	
	const SESSION_PREFIX	= 'invest-';
	
	function indexAction() 
	{	
		$this->view->investment = $investment = Service_Investment::fetchAllPrivileged(NULL,array('year','month'));
		$this->view->investmentValue = Service_Investment_Value::fetchAllPrivileged();
		$deposit = Service_Investment_Deposit::fetchAllPrivileged(NULL,new Art_Model_Db_Order(array('name' => 'expiry_date', 'type' => 'ASC')));
		$this->view->defaultInterest = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_INVESTMENT_INTEREST);
		
		$sortBySurname = Art_Router::getFromURI('surname');
		$sortByValue = Art_Router::getFromURI('value');
		$sortByDate = Art_Router::getFromURI('date');
		$sortByExpiryDate = Art_Router::getFromURI('expiry_date');
		
		$this->view->sortBy = $sortBy = Helper_TBDev::getSortBy($sortBySurname, $sortByValue, $sortByDate, $sortByExpiryDate);
		
		$dataset = array();
		
		foreach ($investment as $value)
		{
			$dataset[$value->year][$value->month] = true;
		}
		
		$links = array();
		
		foreach ($dataset as $keyYear => $year)
		{
			foreach ($year as $keyMonth => $month)
			{
				$links[$keyMonth.'-'.$keyYear] = Helper_Default::getCzechMonthName($keyMonth).' '.$keyYear;
			}
		}

		$this->view->links = $links;
		
		$defaultInterest = new Art_Model_Default_Value(array('name'=>Helper_TBDev::DEFAULT_INVESTMENT_INTEREST));
		
		$this->view->defaultInterestId = $defaultInterest->isLoaded() ? $defaultInterest->id : null;
		
		$this->view->deposit_edit = '/'.Art_Router::getLayer().'/investment/edit/';
		
		foreach ($deposit as $value) /* @var $value Service_Investment_Deposit */ 
		{
			$value->surname = $value->getUser()->getData()->surname;				//HACK to confuse sorting by surname!!!
            $value->terminable = (strtotime($value->expiry_date) <= strtotime("now")) ? true : false;
		}
		
		if ( -1 !== $sortBy )
		{
			switch ( $sortBy )
			{
				case 0: $param = 'surname'; break;
				case 1:	$param = 'surnameR'; break;
				case 2: $param = 'value'; break;
				case 3:	$param = 'valueR'; break;
				case 4:	$param = 'date'; break;
				case 5:	$param = 'dateR'; break;
				case 6:	$param = 'expiry_date'; break;
				case 7:	$param = 'expiry_dateR'; break;			
			}

			$deposit = Helper_TBDev::getSortedArray($deposit, $param);
		}
		
		$this->view->deposit = $deposit;
		
		//Delete item by button
        $delete_single_request = Art_Ajax::newRequest(self::REQUEST_DELETE_SINGLE);
        $delete_single_request->setAction('/'.Art_Router::getLayer().'/investment/deleteSingle/$id');
        $delete_single_request->addUpdate('content','.module_investment_index');
        $delete_single_request->setConfirmWindow(__('module_investment_delete_single_confirm'));
        $this->view->delete_single_request = $delete_single_request;

        //Terminate item by button
        $terminate_single_request = Art_Ajax::newRequest(self::REQUEST_TERMINATE_SINGLE);
        $terminate_single_request->setAction('/'.Art_Router::getLayer().'/investment/terminateSingle/$id');
        $terminate_single_request->addUpdate('content','.module_investment_index');
        $terminate_single_request->setConfirmWindow(__('module_investment_terminate_single_confirm'));
        $this->view->terminate_single_request = $terminate_single_request;
	}

    function terminatedAction()
    {
        $deposit = Service_Investment_Deposit::fetchAllPrivileged(NULL,new Art_Model_Db_Order(array('name' => 'expiry_date', 'type' => 'ASC')));

        $sortBySurname = Art_Router::getFromURI('surname');
        $sortByValue = Art_Router::getFromURI('value');
        $sortByDate = Art_Router::getFromURI('date');
        $sortByExpiryDate = Art_Router::getFromURI('expiry_date');

        $this->view->sortBy = $sortBy = Helper_TBDev::getSortBy($sortBySurname, $sortByValue, $sortByDate, $sortByExpiryDate);

        $this->view->deposit_edit = '/'.Art_Router::getLayer().'/investment/edit/';

        foreach ($deposit as $value) /* @var $value Service_Investment_Deposit */
        {
            $value->surname = $value->getUser()->getData()->surname;				//HACK to confuse sorting by surname!!!
        }

        if ( -1 !== $sortBy )
        {
            switch ( $sortBy )
            {
                case 0: $param = 'surname'; break;
                case 1:	$param = 'surnameR'; break;
                case 2: $param = 'value'; break;
                case 3:	$param = 'valueR'; break;
                case 4:	$param = 'date'; break;
                case 5:	$param = 'dateR'; break;
                case 6:	$param = 'expiry_date'; break;
                case 7:	$param = 'expiry_dateR'; break;
            }

            $deposit = Helper_TBDev::getSortedArray($deposit, $param);
        }

        $this->view->deposit = $deposit;
    }

	function detailAction()
	{
		$key = Art_Router::getId();
		
		$separator = strpos($key, '-');
				
		if ( false !== $separator )
		{
			$this->view->month = $month = substr($key, 0, $separator);
			$this->view->year = $year = substr($key, $separator+1);
		
			$investments = array();
			$investmentIds = array();
			
			$hasThatMonthAndYear = false;
			
			foreach (Service_Investment::fetchAllPrivileged(array('month'=>$month,'year'=>$year)) as $value) /* @var $value Service_Investment */ {
				$investments[$value->id] = $value;
				$investmentIds[] = $value->id;
				$hasThatMonthAndYear = true;
			}
			
			if ( $hasThatMonthAndYear )
			{
				$investment = Service_Investment_Value::fetchAllPrivileged(new Art_Model_Db_Where(array('name'=>'id_service_investment', 'value'=>$investmentIds, 'relation'=>Art_Model_Db_Where::REL_IN)));

				$dataset = array();

				foreach ($investment as $value) /* @var $value Service_Investment_Value */ 
				{
					$dataset[$value->id_service_investment][] = $value;
				}

				$this->view->dataset = $dataset;

				$this->view->investments = $investments;

				$this->view->a_edit = '/'.Art_Router::getLayer().'/investment/edit/';

				//Publish investment by button
				$publish_request = Art_Ajax::newRequest(self::REQUEST_PUBLISH_INVESTMENT); 
				$publish_request->setAction('/'.Art_Router::getLayer().'/investment/publishInvestment/$id'); 
				//$publish_request->setRedirect('/'.Art_Router::getLayer().'/investment'); 
				$publish_request->setRefresh();
				$this->view->publish_request = $publish_request;

				//Unpublish investment by button
				$unpublish_request = Art_Ajax::newRequest(self::REQUEST_UNPUBLISH_INVESTMENT); 
				$unpublish_request->setAction('/'.Art_Router::getLayer().'/investment/unpublishInvestment/$id'); 
				$unpublish_request->setRefresh(); 
				$this->view->unpublish_request = $unpublish_request;

				//Delete investment by button
				$delete_request = Art_Ajax::newRequest(self::REQUEST_DELETE_INVESTMENT); 
				$delete_request->setAction('/'.Art_Router::getLayer().'/investment/deleteInvestment/$id'); 
				$delete_request->setRedirect('/'.Art_Router::getLayer().'/investment'); 
				$delete_request->setConfirmWindow(__('module_investment_delete_investment_confirm')); 
				$this->view->delete_request = $delete_request;
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
			
	function newMonthAction() 
	{		
		if( Art_Ajax::isRequestedBy(self::REQUEST_NEW_MONTH) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
		
			Helper_Default::getValidatedSQLData(array('month','year','target'), self::getNewMonthFieldsValidators(), $data, $response);

			if ( !empty(Service_Investment::fetchAll(array('month'=>$data['month'],'year'=>$data['year'],'target'=>$data['target']))) )
			{
				$response->addAlert(__('module_investment_new_month_already_exists'));
			}
			
			//Everything is valid
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
			$this->view->defaultInterest = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_INVESTMENT_INTEREST);
			
			$this->view->months = Helper_Default::getCzechMonthsName();

			$request = Art_Ajax::newRequest(self::REQUEST_NEW_MONTH);
			$request->setRedirect('/'.Art_Router::getLayer().'/investment/newInvestment');
			$this->view->request = $request; 
		}
	}

	function newInvestmentAction() 
	{		
		if( Art_Ajax::isRequestedBy(self::REQUEST_NEW_MONTH) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
						
			$users = array();
			
			foreach ($data as $key => $value)
			{
				$separator = strpos($key, '-');
				
				if ( false !== $separator )
				{
					$users[substr($key, $separator+1)][substr($key, 0, $separator)] = $value;
				}
			}
			
			$anyUser = false;
			
			foreach ($users as $value)
			{
				if ( Helper_Default::isPropertyChecked($value,'investment') )
				{
					$anyUser = true;
					break;
				}
			}
			
			if ( false == $anyUser )
			{
				$response->addAlert(__('module_investment_new_investment_no_user'));
			}
			
			//Everything is valid
			if( $response->isValid() )
			{	
				$investment = new Service_Investment;
				$investment->month = $data['month'];
				$investment->year = $data['year'];
				$investment->target = $data['target'];
				$investment->save();
				
				foreach ($users as $key => $value)
				{
					if ( Helper_Default::isPropertyChecked($value,'investment') )
					{
						if ( NULL == $value['payment_date'] )
						{
							$value['payment_date'] = $data['date'];
						}

						$investmentValue = new Service_Investment_Value();
						$investmentValue->invested = $value['invested'];
						$investmentValue->interest = $value['interest'];
						$investmentValue->commission = $value['commission'];
						$investmentValue->note = $value['note'];
						$investmentValue->id_user = $key;
						$investmentValue->setService_investment($investment);
						$investmentValue->payment_date = dateSQL($value['payment_date']);
						$investmentValue->save();	
					}
				}
				
				$response->addMessage(__('module_investment_new_month_success'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			//Get data from SESSION
			foreach(Art_Session::get() AS $field_name => $field_value)
			{		
				if ( 0 === strpos($field_name, self::SESSION_PREFIX) )
				{
					$data[substr($field_name, strlen(self::SESSION_PREFIX))] = $field_value;
				}
			}
			
			$this->view->investment = Service_Investment_Value::fetchAllPrivileged();
			$this->view->deposit = Service_Investment_Deposit::fetchAllPrivileged();
			$this->view->defaultInterest = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_INVESTMENT_INTEREST);

			$this->view->date = date("d.m.Y");
			
			$this->view->month = $data['month'];
			$this->view->year = $data['year'];
			$this->view->target = $data['target'];

			$users = Helper_TBDev::getAllUsersForActivatedService(new Service(array('type'=>Helper_TBDev::INVESTMENT_TYPE)));

			foreach ($users as $value) /* @var $value Art_Model_User */ 
			{
				$deposits = 0;

				foreach ( Service_Investment_Deposit::fetchAllPrivileged(array('id_user'=>$value->id))
						as $deposit )	/* @var $deposit Service_Investment_Deposit */ 
				{
					$time = strtotime('1.'.$data['month'].'.'.$data['year']);
					
					if ( strtotime($deposit->expiry_date) > $time && strtotime($deposit->date) <= $time )
					{
						$deposits += $deposit->value;
					}
				}

				$value->deposit = $deposits;
				$value->interest = Helper_Default::getDefaultValue(Helper_TBDev::DEFAULT_INVESTMENT_INTEREST);
			}

			$this->view->users = $users;

			$request = Art_Ajax::newRequest(self::REQUEST_NEW_MONTH);
			$request->setRedirect('/'.Art_Router::getLayer().'/investment');
			$this->view->request = $request; 
		}
	}
	
	function newAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_ADD) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			//Set each field validation options
			$fields = Service_Investment_Deposit::getCols('insert');
			
			//Data to insert to database
			$sql_data = Helper_Default::getValidatedSQLData($fields, self::getFieldsValidators(), $data, $response);
			
			//Everything is valid
			if( $response->isValid() )
			{		
				$investmentDeposit = new Service_Investment_Deposit();
				$investmentDeposit->setDataFromArray($sql_data);
				$investmentDeposit->save();
				
				$response->addMessage(__('module_investment_add_success'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$this->view->currentDate = date("Y-m-d");
			$this->view->afterYearDate = date("Y-m-d", strtotime("+1 year"));
			$this->view->actualMonth = date("m");
			$this->view->actualYear = date("Y");
			
			$service = new Service(array('type'=>Helper_TBDev::INVESTMENT_TYPE));
			
			$users = Helper_TBDev::getAllUsersForActivatedService($service);

			foreach ($users as $value) /* @var $value Art_Model_User */ 
			{
				$value->surname = $value->getData()->surname;
			}
			
			$this->view->users = Helper_TBDev::getSortedArray($users,'surname');
			
			$request = Art_Ajax::newRequest(self::REQUEST_ADD);
			$request->setRedirect('/'.Art_Router::getLayer().'/investment');
			$this->view->request = $request; 
		}
	}
	
	function editAction()
	{
		if( Art_Ajax::isRequestedBy(self::REQUEST_EDIT) )
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$id = Art_Router::getId();
			
			Helper_Default::getValidatedSQLData(array('expiry_date'), self::getFieldsValidators(), $data, $response);
			
			$investmentDeposit = new Service_Investment_Deposit($id);

			if ( strtotime($investmentDeposit->date) > strtotime($data['expiry_date']) )
			{
				$response->addAlert(__('module_investment_deposit_v_date_not_before'));
			}
			
			if ( !$investmentDeposit->isLoaded() )
			{
				$response->addAlert(__('module_investment_deposit_edit_not_found'));
			}
			
			//Everything is valid
			if( $response->isValid() )
			{		
				$investmentDeposit->expiry_date = dateSQL($data['expiry_date']);
                $investmentDeposit->note = $data['note'];
				$investmentDeposit->save();

				$response->addMessage(__('module_investment_edit_success'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{			
			$id = Art_Router::getId();
			
			$this->view->deposit = $deposit = new Service_Investment_Deposit($id);
			
			$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
			$request->setRedirect('/'.Art_Router::getLayer().'/investment');
			$this->view->request = $request; 
		}
	}
	
	function editinvestmentAction()
	{
		if(Art_Ajax::isRequestedBy(self::REQUEST_EDIT))
		{
			$response = Art_Ajax::newResponse();
			$data = Art_Ajax::getData();
			
			$users = array();
			
			foreach ($data as $key => $value)
			{
				$separator = strpos($key, '-');
				
				if ( false !== $separator )
				{
					$users[substr($key, $separator+1)][substr($key, 0, $separator)] = $value;
				}
			}
			
			//Everything is valid
			if( $response->isValid() )
			{	
				foreach ($users as $key => $value)
				{
					$investment = new Service_Investment($data['investmentId']);
					
					$investment->target = $data['target'];
					$investment->save();
					
					$investmentValue = new Service_Investment_Value(array('id_user'=>$key,'id_service_investment'=>$data['investmentId']));
					
					if ( $investmentValue->isLoaded() )
					{
						if ( Helper_Default::isPropertyChecked($value,'investment') )
						{
							$investmentValue->invested = $value['invested'];
							$investmentValue->interest = $value['interest'];
							$investmentValue->commission = $value['commission'];
							$investmentValue->note = $value['note'];
							$investmentValue->payment_date = dateSQL($value['payment_date']);
							$investmentValue->save();
						}
						else						
						{
							$investmentValue->delete();					
						}
					}
				}

				$response->addMessage(__('module_investment_edit_month_success'));
				$response->willRedirect();
			}
			
			$response->execute();
		}
		else
		{
			$id = Art_Router::getId();

			$investment = new Service_Investment($id);
			
			$this->view->investmentId = $investment->id;
			
			if ( $investment->isLoaded() )
			{
				$this->view->month = $investment->month;
				$this->view->year = $investment->year;
				$this->view->target = $investment->target;

				$this->view->investmentValues = Service_Investment_Value::fetchAllPrivileged(array('id_service_investment'=>$investment->id));
				
				$request = Art_Ajax::newRequest(self::REQUEST_EDIT);
				$request->setRedirect('/'.Art_Router::getLayer().'/investment');
				$this->view->request = $request;
			} 
			else
			{
				$this->showTo(Art_User::NO_ACCESS);
			}
		}
	}

    function terminateSingleAction()
    {
        if( Art_Ajax::isRequestedBy(self::REQUEST_TERMINATE_SINGLE) )
        {
            $response = Art_Ajax::newResponse();

            $investmentDeposit = new Service_Investment_Deposit(Art_Router::getId());

            if( $investmentDeposit->isLoaded() )
            {
                $investmentDeposit->terminated = 1;
                $investmentDeposit->save();
                $response->addMessage(__('module_investment_terminate_success'));
            }
            else
            {
                $response->addAlert(__('module_investment_terminate_not_found'));
            }

            $response->addVariable('content', Art_Module::createAndRenderModule('investment'));

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
  
			$investmentDeposit = new Service_Investment_Deposit(Art_Router::getId());
 
			if( $investmentDeposit->isLoaded() )
  			{
				$investmentDeposit->delete();
				$response->addMessage(__('module_investment_delete_success'));
			}
  			else
 			{
				$response->addAlert(__('module_investment_delete_not_found'));
  			}
 
			$response->addVariable('content', Art_Module::createAndRenderModule('investment'));

			$response->execute();
  		}
  		else
  		{
  			$this->showTo(Art_User::NO_ACCESS);
  		}
  	}
		
	function deleteInvestmentAction()
	{
  		if( Art_Ajax::isRequestedBy(self::REQUEST_DELETE_INVESTMENT) )
  		{
  			$response = Art_Ajax::newResponse();			
  
			$investment = new Service_Investment(Art_Router::getId());
 
			if( $investment->isLoaded() )
  			{
				$investmentValues = $investment->getInvestmentValues();
				
				foreach ($investmentValues as $value) /* @var $value Service_Investment_Value */ 
				{
					$value->delete();
				}
				
				$investment->delete();
				$response->addMessage(__('module_investment_delete_success'));
			}
  			else
 			{
				$response->addAlert(__('module_investment_delete_not_found'));
  			}
 
			$response->willRedirect();
			
			$response->execute();
  		}
  		else
  		{
  			$this->showTo(Art_User::NO_ACCESS);
  		}
  	}
	
	function publishInvestmentAction()
	{
  		if( Art_Ajax::isRequestedBy(self::REQUEST_PUBLISH_INVESTMENT) )
  		{
  			$response = Art_Ajax::newResponse();			
  
			$investment = new Service_Investment(Art_Router::getId());
 
			if( $investment->isLoaded() )
  			{
				$investment->visible = 1;
				$investment->save();
				$response->addMessage(__('module_investment_publish_success'));
			}
  			else
 			{
				$response->addAlert(__('module_investment_publish_not_found'));
  			}
 
			$response->willRedirect();
			
			$response->execute();
  		}
  		else
  		{
  			$this->showTo(Art_User::NO_ACCESS);
  		}
  	}
	
		function unpublishInvestmentAction()
	{
  		if( Art_Ajax::isRequestedBy(self::REQUEST_UNPUBLISH_INVESTMENT) )
  		{
  			$response = Art_Ajax::newResponse();			
  
			$investment = new Service_Investment(Art_Router::getId());
 
			if( $investment->isLoaded() )
  			{
				$investment->visible = 0;
				$investment->save();
				$response->addMessage(__('module_investment_publish_success'));
			}
  			else
 			{
				$response->addAlert(__('module_investment_publish_not_found'));
  			}
 
			$response->willRedirect();
			
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
	'id_user'				=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_investment_v_id_user_not_integer')]),
	'value'	=> array(
		Art_Validator::MIN_VALUE => ['value' => 0,'message'		=> __('module_investment_v_value_min')],
		Art_Validator::IS_INTEGER => ['message'					=> __('module_investment_v_value_not_integer')]),
		);
	}
	
	static function getNewMonthFieldsValidators()
	{
		return	array(
	'month'				=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_investment_v_month_not_integer')]),
	'year'				=> array(
		Art_Validator::IS_INTEGER => ['message' => __('module_investment_v_year_not_integer')]),
	'target'				=> array(
		Art_Validator::IS_STRING => ['message' => __('module_investment_v_target_not_string')],
		Art_Validator::REGEX	=>	['value' => "/^[a-zA-Z-\s_\d]+$/",'message'		=> __('module_investment_v_target_not_right')]),
	/*'date'	=> array(
		Art_Validator::IS_STRING => ['message'		=> __('module_investment_v_date_not_string')]),
	'interest'	=> array(
		Art_Validator::MIN_VALUE => ['value' => 0,'message'		=> __('module_investment_v_interest_min')],
		Art_Validator::IS_NUMERIC => ['message'					=> __('module_investment_v_interest_not_integer')]),*/
		);
	}
}