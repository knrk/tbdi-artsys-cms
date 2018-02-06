<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/contact
 */
class Module_Bootstrap_Contact extends Art_Abstract_Module_Bootstrap {
	
	static function initialize()
	{
		Art_Event::on(Art_Event::LABEL_SETUP, function(){
			static::labelSetup();
		});
		
	Art_Event::on(Art_Event::ROUTER_SETUP,function(){
			
		Art_Router::addRoute(
				'contact', 
				'/contact/contactformshort', 
				array ('layer'=> Art_Router::LAYER_FRONTEND, 'section'=>'contact', 'action'=>'contactFormShort'), 
				'tbdevelopment.cz');
		});
	}
	
	static function labelSetup() 
	{
		Art_Label::addLabelSet(array(
			'module_contact'							=>	'Kontakt',
			'module_contact_saved'						=>	'Nastavení bylo uloženo',
			'module_contact_title'						=>	'Editace kontaktu',
			'module_contact_placeholder_name'			=>	'Jméno',
			'module_contact_placeholder_surname'		=>	'Příjmení',
			'module_contact_placeholder_company_name'	=>	'Název společnosti',
			'module_contact_placeholder_phone'			=>	'Telefon',
			'module_contact_placeholder_mobile'			=>	'Mobil',
			'module_contact_placeholder_email'			=>	'Email',
			'module_contact_placeholder_street'			=>	'Ulice',
			'module_contact_placeholder_city'			=>	'Město',
			'module_contact_placeholder_zip'			=>	'PSČ',
			'module_contact_placeholder_country'		=>	'Stát',
			'module_contact_placeholder_fb_link'		=>	'Facebook profil/stránka',
			'module_contact_placeholder_tw_link'		=>	'Twitter kanál',
			'module_contact_placeholder_gp_link'		=>	'Google Plus profil',
			'module_contact_placeholder_yt_link'		=>	'Youtube kanál',
			'module_contact_placeholder_map'			=>	'Mapa (embedd kód)',
			'module_contact_placeholder_mail_to'		=>	'Adresa pro příjem konktaktních emailů',
			'module_contactform_email_wrong'			=>	'Emailová adresa má nesprávný formát',
			'module_contactform_email_empty'			=>	'Zadejte, prosím, emailovou adresu',
			'module_contactform_message_short'			=>	'Zpráva je příliš krátká',
			'module_contactform_message_empty'			=>	'Vyplňte, prosím, obsah zprávy',
			'module_contactform_form_empty'				=>	'Vyplňte, prosím, všechna povinná pole',
			'module_contactform_spam_alert'				=>	'Odesláno příliš mnoho zpráv. Další může být odeslána až po uplynutí %s minut',
			'module_contactform_thank_you'				=>	'Děkujeme',
			'module_contactform_will_contact_you'		=>	'Budeme Vás kontaktovat co nejdříve to bude možné'
		));
	}
}