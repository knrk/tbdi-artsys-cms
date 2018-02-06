<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/error
 */
class Module_Bootstrap_Error extends Art_Abstract_Module_Bootstrap {
	
	static function initialize()
	{
		Art_Event::on(Art_Event::LABEL_SETUP, function(){
			static::labelSetup();
		});
	}
	
	static function labelSetup() 
	{
		Art_Label::addLabelSet(array(
			'module_error_no_access'				=>	'Do této sekce nemáte přístup',
			'module_error_no_access_try_login'		=>	'Do této sekce nemáte přístup, zkuste se přihlásit',
			'module_error_not_found'				=>	'Sekce nebyla nalezena',
			
			'module_error_membership_ended'			=> 'Vaše členství v klubu bylo ukončeno dne %s. Pro další informace kontaktujte předsedu klubu.'
		));
	}
}