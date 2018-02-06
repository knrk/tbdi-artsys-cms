<?php
/**
 *  @author Robin Zon <zon@itart.cz>
 *  @package modules/emaildump
 */
class Module_Bootstrap_Emaildump extends Art_Abstract_Module_Bootstrap {	
	
	static function initialize()
	{
		Art_Event::on(Art_Event::LABEL_SETUP, function(){
			static::labelSetup();
		});
	}

	
	static function labelSetup() 
	{
		Art_Label::addLabelSet(array(
			'module_emaildump'							=> 'Historie emailových zpráv',
			'module_emaildump_name'						=> 'Název',
			'module_emaildump_subject'					=> 'Předmět',
			'module_emaildump_detail'					=> 'Detail emailu',
			'module_emaildump_to'						=> 'Adresát',
			'module_emaildump_reply_to'					=> 'Adresa pro odpověď',
			'module_emaildump_date'						=> 'Datum odeslání',
			'module_emaildump_body'						=> 'Tělo zprávy',
			'module_emaildump_bcc'						=> 'Skryté kopie',
			));
	}
}