<?php
/**
 *	@author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/public/cron
 */
class Module_Cron extends Art_Abstract_Module {
	
	private static $local_DEBUG = false;
	
	//daily test
	static function testMembership() 
	{		
		$object = new stdClass();
		$object->data = null;
		
		foreach (Helper_TBDev::getAllAuthenticatedUsers() as $value) 
			/* @var $value Art_Model_User */ 
		{
			if ( !$value->active )
			{
				continue;
			}
			
			$membershipTo = Helper_TBDev::getMembershipToForUser($value);

                $secMembershipTo = strtotime($membershipTo);

                $remainingDays = floor(($secMembershipTo - time()) / (60 * 60 * 24) ) + 1;

                if ( $secMembershipTo > 0 )
                {
                    //10 days before membership ends
                    if ( $remainingDays == 10 )
                    {
                        if (!self::$local_DEBUG) :
                            Helper_Email::sendMembershipEarlyEndMail($value);
                        endif;
                        $object->data .= '10 days before membership ends - '.$membershipTo.' - '.$value->getData()->email.'<br>';
                    }

                    //6 days after membership ends
				if ( $remainingDays == -6 )
				{
					if (!self::$local_DEBUG) {
						Helper_Email::sendForfeitedMembershipMail($value);
					}

					$object->data .= '6 days after membership ends - '.$membershipTo.' | '.$value->getData()->email.'<br>';
				}
			}
			
			$gotApp = User_X_Email::fetchAll(array('id_user'=>$value->id,'email_type'=>Helper_TBDev::EMAIL_TYPE_GOT_APP));
				
			//Unpaid membership - 7 days after approval
			if ( !empty($gotApp) && !$value->getData()->verif )
			{
				$daysAfterApproval = floor((time() - strtotime($gotApp[0]->created_date)) / (60 * 60 * 24) );

				if ( $daysAfterApproval == 7 )
				{
					if (!self::$local_DEBUG) {
						Helper_Email::sendUnpaidMembershipMail($value);
					}

					$object->data .= 'Unpaid membership - 7 days after approval - '.$gotApp[0]->created_date.' : '.$value->getData()->email.'<br>';
				}
			}

			$notGotApp = User_X_Email::fetchAll(array('id_user'=>$value->id,'email_type'=>Helper_TBDev::EMAIL_TYPE_NOT_GOT_APP));

			//Terminate application - 14 days after not got application
			if ( !empty($notGotApp) && empty($gotApp) )
			{
				$daysAfterNotGotApp = floor((time() - strtotime($notGotApp[0]->created_date)) / (60 * 60 * 24) );

				if ( $daysAfterNotGotApp == 14 )
				{
					//$value->active = 0;
					//$value->save();
					if (!self::$local_DEBUG) {
						Helper_Email::sendTerminateApplicationMail($value);
					}
					$object->data .= 'Terminate application - 14 days after not got application - '.$notGotApp[0]->created_date.' / '.$value->getData()->email.'<br>';
				}
			}
		}	
		
		if (!self::$local_DEBUG) {
			Helper_Email::sendReportMail($object);
		}
		else {
			p($object->data);
		}
	}
}