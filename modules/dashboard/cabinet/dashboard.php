<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/dashboard/cabinet
 */
class Module_Dashboard extends Art_Abstract_Module {
	
	function indexAction() 
	{						
		$limit = Helper_Default::getDefaultValue('dashboard-max-results', Helper_TBDev::MAX_DASHBOARD_RESULTS);

		$dashboard = Dashboard::fetchAllPrivileged(NULL,array('name'=>'id','type'=>Art_Model_Db_Order::TYPE_DESC), $limit);

		$this->view->dashboard = $dashboard;
	}
	
	function embeddAction() 
	{			
		$limit = Helper_Default::getDefaultValue('dashboard-max-results-embedd', Helper_TBDev::MAX_DASHBOARD_RESULTS_EMBEDD);
				
		$where = new Art_Model_Db_Where(array('name'=>'created_date','value'=>dateSQL('-'.Helper_Default::getDefaultValue('dashboard-max-age',Helper_TBDev::DASHBOARD_TIME_FROM).' months'),'relation'=>Art_Model_Db_Where::REL_GREATER));
		
		$dashboard = Dashboard::fetchAllPrivileged($where,array('name'=>'id','type'=>Art_Model_Db_Order::TYPE_DESC), $limit);
		
		$this->view->dashboard = $dashboard;
		
		$this->view->link = Art_Server::getHost().'/'.Art_Router::getLayer().'/dashboard';
	}
}