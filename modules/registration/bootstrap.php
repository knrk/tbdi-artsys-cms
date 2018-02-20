<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/registration
 */
class Module_Bootstrap_Registration extends Art_Abstract_Module_Bootstrap {
	
	static function initialize()
	{
		// Art_Event::on(Art_Event::LABEL_SETUP,function(){
		// 	static::labelSetup();
		// });
		Art_Event::on(Art_Event::ROUTER_SETUP,function(){
			static::routerSetup();
		});
	}
	
	static function routerSetup()
	{
		Art_Router::addRoute('route_forgotten_login', '/forgotten', [
			'layer' => Art_Router::LAYER_FRONTEND,
			'section' => 'registration',
			'action' => 'forgotten'
		]);
		Art_Router::addRoute('route_forgotten_login_acknowledge', '/forgottenAck/$1', [
			'layer' => Art_Router::LAYER_FRONTEND,
			'section' => 'registration',
			'action' => 'forgottenAck',
			'id' => '$1'
		]);
		Art_Router::addRoute('route_register_via_inv_code', '/invitation', [
			'layer' => Art_Router::LAYER_FRONTEND,
			'section' => 'registration',
			'action' => 'index',
			'id' => '$1'
		]);
	}
	
	// static function labelSetup() 
	// {
	// 	Art_Label::addLabelSet(array(
	// 		'module_registration_information_check'		=> 'Kontrola údajů',
	// 		'module_registration_register'				=> 'Registrovat',
	// 		'module_registration_register_company'		=> 'Požádat o registraci',
	// 		'module_registration_registration_complete'	=> 'Registrace byla úspěšná',
	// 		'module_registration_back_to_edit'			=> 'Zpět na úpravy',
	// 		'module_registration_send_again'			=> 'Poslat email znovu',
	// 		'module_registration_sended_again'			=> 'Email byl znovu odeslán',
	// 		'module_registration_sended_again_failed'	=> 'Nastala chyba při znovuodesílání emailu',
			
	// 		'module_registration_v_degree_max'			=> 'Titul je příliš dlouhý',
	// 		'module_registration_v_name_min'			=> 'Jméno je příliš krátké',
	// 		'module_registration_v_name_max'			=> 'Jméno je příliš dlouhé',
	// 		'module_registration_v_surname_min'			=> 'Příjmení je příliš krátké',
	// 		'module_registration_v_surname_max'			=> 'Příjmení je příliš dlouhé',
	// 		'module_registration_v_gender_not_integer'	=> 'Pohlaví není platné',
	// 		'module_registration_v_born_day_not_integer'=> 'Den narození není platný',
	// 		'module_registration_v_born_month_not_integer'=> 'Měsíc narození není platný',
	// 		'module_registration_v_born_year_not_integer'=> 'Rok narození není platný',
	// 		'module_registration_v_reg_code_min'		=> 'Registrační kód je příliš krátký',
			
	// 		'module_registration_v_delivery_country_not_integer'=> 'Stát trvalého pobytu není platný',
	// 		'module_registration_v_delivery_city_min'		=> 'Město trvalého pobytu je příliš krátké',
	// 		'module_registration_v_delivery_city_max'		=> 'Město trvalého pobytu je příliš dlouhé',
	// 		'module_registration_v_delivery_street_min'		=> 'Ulice trvalého pobytu je příliš krátká',
	// 		'module_registration_v_delivery_street_max'		=> 'Ulice trvalého pobytu je příliš dlouhá',
	// 		'module_registration_v_delivery_housenum_min'	=> 'Číslo popisné trvalého pobytu je příliš krátké',
	// 		'module_registration_v_delivery_housenum_max'	=> 'Číslo popisné trvalého pobytu je příliš dlouhé',
	// 		'module_registration_v_delivery_zip_min'		=> 'PSČ trvalého pobytu je příliš krátké',
	// 		'module_registration_v_delivery_zip_max'		=> 'PSČ trvalého pobytu je příliš dlouhé',
	// 		'module_registration_v_delivery_zip_not_integer'=> 'PSČ trvalého pobytu musí být číslo',
	// 		'module_registration_v_delivery_area_code_min'	=> 'Předčíslí je příliš krátké',
	// 		'module_registration_v_delivery_area_code_max'	=> 'Předčíslí je příliš dlouhé',
	// 		'module_registration_v_delivery_phone_min'		=> 'Telefonní číslo je příliš krátké',
	// 		'module_registration_v_delivery_phone_max'		=> 'Telefonní číslo je příliš dlouhé',
	// 		'module_registration_v_delivery_phone_not_integer'=> 'Telefonní číslo musí být číslo',
			
	// 		'module_registration_v_contact_country_not_integer'=> 'Stát kontaktní adresy není platný',
	// 		'module_registration_v_contact_city_min'		=> 'Město kontaktní adresy je příliš krátké',
	// 		'module_registration_v_contact_city_max'		=> 'Město kontaktní adresy je příliš dlouhé',
	// 		'module_registration_v_contact_street_min'		=> 'Ulice kontaktní adresy je příliš krátká',
	// 		'module_registration_v_contact_street_max'		=> 'Ulice kontaktní adresy je příliš dlouhá',
	// 		'module_registration_v_contact_housenum_min'	=> 'Číslo popisné kontaktní adresy je příliš krátké',
	// 		'module_registration_v_contact_housenum_max'	=> 'Číslo popisné kontaktní adresy je příliš dlouhé',
	// 		'module_registration_v_contact_zip_min'			=> 'PSČ kontaktní adresy je příliš krátké',
	// 		'module_registration_v_contact_zip_max'			=> 'PSČ kontaktní adresy je příliš dlouhé',
	// 		'module_registration_v_contact_zip_not_integer'	=> 'PSČ kontaktní adresy musí být číslo',
			
	// 		'module_registration_v_company_country_not_integer'=> 'Stát adresy společnosti není platný',
	// 		'module_registration_v_company_city_min'		=> 'Město adresy společnosti je příliš krátké',
	// 		'module_registration_v_company_city_max'		=> 'Město adresy společnosti je příliš dlouhé',
	// 		'module_registration_v_company_street_min'		=> 'Ulice adresy společnosti je příliš krátká',
	// 		'module_registration_v_company_street_max'		=> 'Ulice adresy společnosti je příliš dlouhá',
	// 		'module_registration_v_company_housenum_min'	=> 'Číslo popisné adresy společnosti je příliš krátké',
	// 		'module_registration_v_company_housenum_max'	=> 'Číslo popisné adresy společnosti je příliš dlouhé',
	// 		'module_registration_v_company_zip_min'			=> 'PSČ adresy společnosti je příliš krátké',
	// 		'module_registration_v_company_zip_max'			=> 'PSČ adresy společnosti je příliš dlouhé',
	// 		'module_registration_v_company_zip_not_integer'	=> 'PSČ adresy společnosti musí být číslo',
			
	// 		'module_registration_v_company_name_min'	=> 'Název společnosti je příliš krátký',
	// 		'module_registration_v_company_name_max'	=> 'Název společnosti je příliš dlouhý',
	// 		'module_registration_v_company_name_duplication' => 'Název společnosti je již používaný',
	// 		'module_registration_v_ico_min'				=> 'IČO je příliš krátké',
	// 		'module_registration_v_ico_max'				=> 'IČO je příliš dlouhé',
	// 		'module_registration_v_dic_min'				=> 'DIČ je příliš krátké',
	// 		'module_registration_v_dic_max'				=> 'DIČ je příliš dlouhé',
	// 		'module_registration_v_person_function_min'	=> 'Funkce zástupce je příliš krátká',
	// 		'module_registration_v_person_function_max'	=> 'Funkce zástupce je příliš dlouhá',
			
	// 		'module_registration_v_wrong_email'			=> 'Emailová adresa není platná',
	// 		'module_registration_v_email_duplication'	=> 'Emailová adresa je již používaná',
	// 		'module_registration_v_charter_confirm'		=> 'Stanovy klubu musí být potvrzeny',
	// 		'module_registration_v_reg_code_not_valid'	=> 'Registrační kód není platný',
	// 		'module_registration_v_ico_not_valid'		=> 'IČO není platné',
	// 		'module_registration_v_dic_not_valid'		=> 'DIČ není platné',			
			
	// 		'module_registration_v_not_existed_email'	=> 'Tento email neexistuje',
	// 		'module_registration_v_not_authorized_email'=> 'Tento email není autorizovaný',
	// 		'module_registration_v_change_password_not_same'=> 'Zadaná hesla se neshodují',
	// 		'module_registration_v_set_password_not_same'	=> 'Zadaná hesla se neshodují',
			
	// 		'module_registration_done_sended_email'			=> 'Email byl odeslán na adresu: ',
	// 		'module_registration_done_company_send_email'	=> 'Email byl odeslán na adresu: ',
	// 		'module_registration_header'				=> 'Registrace',
	// 		'module_registration_forgotten_header'		=> 'Zapomenuté heslo',
	// 		'module_registration_confirm_header'		=> 'Kontrola registračních údajů',
	// 		'module_registration_forgotten_send'		=> 'Odeslat zapomenuté heslo',
	// 		'module_registration_forgotten_sended'		=> 'Email byl odeslán',
			
	// 		'module_registration_contact_address_different'=> 'Odlišná kontaktní adresa',
	// 		'module_registration_change_password_header'=> 'Změna zapomenutého hesla',
	// 		'module_registration_change_password_changed'=> 'Heslo bylo úspěšně změněno',
	// 		'module_registration_set_password_header'	=> 'Vytvoření nového hesla',
	// 		'module_registration_set_password_new'		=> 'Nové heslo',
	// 		'module_registration_set_password_new_again'=> 'Nové heslo znovu',
	// 		'module_registration_set_password_send'		=> 'Nastavit',
	// 		'module_registration_set_password_setted'	=> 'Nové heslo bylo úspěšně nastaveno',
	// 		'module_registration_set_password_not_found'=> 'Uživatel nenalezen',
	// 		'module_registration_firm_address'			=> 'Adresa společnosti',
	// 		'module_registration_back_to_login'			=> 'Návrat na přihlášení',
	// 		'module_registration_new_password_header'	=> 'Zvolte si nové heslo',
	// 		'module_registration_forgotten_email'		=> 'Zadejte svojí emailovou adresu, kterou jste použili pro registraci',
	// 		'module_registration_done_message'			=> 'Zkontrolujte svoji emailovou schránku včetně složek se spamem. V případě že Vám email nedorazil, kliknutím na tlačitko níže Vám bude registrační email zaslán znovu.',
	// 		'module_registration_done_company_message'	=> 'Obdrželi jsme Vaši žádost o registraci do klubu. Jakmile náš manažer připraví Vaši přihlášku, obdržíte ji na e-mail uvedený při registraci.',
	// 		'module_registration_area_number_title'		=> '420 CZE, 421 SVK',
	// 		'module_registration_representant_equal_authorized'	=> 'Oprávněná osoba je shodná se zástupcem společnosti',
	// 		'module_registration_authorized_person_contact_data'=> 'Kontaktní údaje oprávněné osoby',
	// 		'module_registration_authorized_person_adress'	=> 'Adresa oprávněné osoby',
	// 		'module_registration_authorized_person'		=> 'Osoba oprávněná fyzicky využívat členství',
	// 		'module_registration_representant_adress'	=> 'Zástupce společnosti - Adresa trvalého pobytu',
	// 		'module_registration_representant'			=> 'Zástupce společnosti',
	// 		'module_registration_header_company'		=> 'Registrace právnické osoby',

	// 		));
	// }
}