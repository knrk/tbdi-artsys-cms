<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/cron
 */
class Module_Bootstrap_Cron extends Art_Abstract_Module_Bootstrap {
		

	static function initialize() 
	{
		Art_Event::on('cron_daily', function(){
			Module_Cron::testMembership();
		});
	}
}