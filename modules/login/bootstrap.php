<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/login
 */
class Module_Bootstrap_Login extends Art_Abstract_Module_Bootstrap {
			
	static function initialize()
	{
		Art_Event::on(Art_Event::LABEL_SETUP, function(){
			static::labelSetup();
		});
	}
	
	static function labelSetup() 
	{
		Art_Label::addLabelSet(array(
			'module_login_no_email'				=>	'Zadejte, prosím, email',
			'module_login_no_password'			=>	'Zadejte, prosím, heslo',
			'module_login_email_invalid'		=>	'Email není v platném formátu',
			'module_login_logged_in'			=>	'Uživatel byl úspěšně přihlášen',
			'module_login_logged_out'			=>	'Uživatel byl úspěšně odhlášen',
			'module_login_invalid_creditentals'	=>	'Neplatná kombinace jména a hesla',
			'module_login_need_confirmation'	=>	'Nejprve je nutné aby vás manažer autentizoval',
            'module_login_need_money'           =>  'Přihlášení se nezdařilo!'
			));
	}
}