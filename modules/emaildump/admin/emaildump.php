<?php
/**
 *  @author Robin Zon <zon@itart.cz>
 *  @package modules/emaildump/admin
 */
class Module_Emaildump extends Art_Abstract_Module {

	function indexAction() 
	{		
		$orderBy = new Art_Model_Db_Order(array(array('name' => 'created_date', 'type' => 'DESC')));
		$emaildumps = Art_Model_Mail_Dump::fetchAllPrivileged(NULL, $orderBy);
		
		foreach ($emaildumps as $value) /* @var $value Art_Model_Mail_Dump */ 
		{
			$value->countBcc = 0;
			if ( '[]' != $value->bcc_addresses )
			{
				$value->countBcc = count(explode('],[',$value->bcc_addresses));
			}
		}
		
		$this->view->emaildumps = $emaildumps;
	}
	
	function detailAction()
	{
		$emaildump = new Art_Model_Mail_Dump(Art_Router::getId());
		if( !$emaildump->isLoaded() )
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
		elseif( !$emaildump->isPrivileged() )
		{
			$this->allowTo(Art_User::NO_ACCESS);
		}

		$this->view->emaildump = $emaildump;
	}
}