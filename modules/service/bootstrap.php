<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/service
 */
class Module_Bootstrap_Service extends Art_Abstract_Module_Bootstrap {
	
	static function initialize()
	{
		Art_Event::on(Art_Event::LABEL_SETUP,function(){
			static::labelSetup();
		});
	}
	
	static function labelSetup() 
	{
		Art_Label::addLabelSet(array(
			'module_service_header'			=> 'Služby',
			'module_service_detail_header'	=> 'Služby - Detail',
			'module_service_new_service'	=> 'Nová služba',
			'module_service_edit_service'	=> 'Úprava služby',
			'module_service_type'			=> 'Typ',
			'module_service_name'			=> 'Název',
			'module_service_settings'		=> 'Nastavení',
			'module_service_price'			=> 'Cena/Čas. int.',
			'module_service_time_interval'	=> 'Časový interval',
			'module_service_is_default'		=> 'Výchozí',
			'module_service_header'			=> 'Služby',
			'module_service_type_service'	=> 'Typ služby:',
			'module_service_icon'			=> 'Ikona:',
			'module_service_article'		=> 'Článek:',
			'module_service_price_price'	=> 'Cena služby',
			'module_service_price_time_interval_value'	=> 'Časový interval',
			'module_service_price_edit'		=> 'Úprava ceny služby',
			'module_service_price_default'	=> 'Výchozí cena služby',
			'module_service_price_new_price'=> 'Nová cena služby',
			'module_service_price_default'	=> 'Výchozí cena služby',
			'module_service_from'			=> 'Od',
			'module_service_to'				=> 'Do',
			
			'module_service_v_type_min'		=> 'Typ je příliš krátký',
			'module_service_v_type_max'		=> 'Typ je příliš dlouhý',
			'module_service_v_name_min'		=> 'Jméno je příliš krátké',
			'module_service_v_name_max'		=> 'Jméno je příliš dlouhé',
			'module_service_v_price_min'	=> 'Cena je příliš nízká',
			'module_service_v_price_not_integer'				=> 'Cena musí být číslo',
			'module_service_v_time_interval_value_min'			=> 'Časový interval je příliš malý',
			'module_service_v_time_interval_value_not_integer'	=> 'Časový interval musí bát číslo',
			'module_service_v_time_interval_type_not_string'	=> 'Typ intervalu není řetězec',
			'module_service_v_filename_min'	=> 'Název souboru je příliš krátký',
			'module_service_v_filename_max'	=> 'Název souboru je příliš dlouhý',
			
			'module_service_payments'		=> 'Platby za službu',
			'module_service_add_success'	=> 'Služba byla úspěšně přidána',
			'module_service_not_found'		=> 'Služba nebyla nalezena',
			'module_service_delete_success'	=> 'Služba byla úspěšně vymazána',
			'module_service_delete_contains_prices'	=> 'Služba obasahuje ceny',
			'module_service_delete_not_found'		=> 'Služba nebyla nalezena',
			'module_service_delete_success'			=> 'Služba byla úspěšně vymazána',
			'module_service_conditions_delete_success'	=> 'Podmínky služby byly úspěšně vymazány',
			'module_service_conditions_delete_contains_services'=> 'Podmínky jsou obsaženy ve službě',
			'module_service_conditions_delete_contains_charter' => 'Podmínky jsou hlavními stanovami',
			'module_service_price_delete_success'			=> 'Cena služby byla úspěšně vymazána',
			'module_service_price_delete_not_found'			=> 'Cena služby nebyla nalezena',
			'module_service_price_delete_contained_in_group'=> 'Cena služby je obsažena ve skupině',
			'module_service_price_add_success'		=> 'Cena služby byla úspěšně přidána',
			'module_service_price_not_found'		=> 'Cena služby nebyla nalezena',
			'module_service_price_edit_success'		=> 'Cena služby byla úspěšně upravena',
			'module_service_activate_success'		=> 'Aktivace služby byla úspěšně provedena',
			'module_service_deactivate_success'		=> 'Deaktivace služby byla úspěšná',
			'module_service_deactivate_error'		=> 'Deaktivace služby nebyla možna',
			'module_service_manager_will_contact'	=> 'Váš manažer vás bude v nejbližsí době kontaktovat.',
			'module_service_no_service_to_activate'	=> 'Uživatel nemá žádnou službu, kterou by mohl aktivovat.',
			'module_service_user_has_all'			=> 'Uživatel vidí všechny dostupné služby.',
			'module_service_request_complete_success'=> 'Žádost byla úspěšně schválena',
			'module_service_request_complete_not_found'=> 'Žádost nebyla nalezena',
			'module_service_add_not_found'			=> 'Služba nebyla nalezena',
			'module_service_add_success'			=> 'Služba byla úspěšně přidána',
			'module_service_pdf_conditions'			=> 'PDF podmínky',
			'module_service_conditions_header'		=> 'Podmínky služeb',
			'module_service_conditions_new_header'	=> 'Nahrát nové podmínky',
			'module_service_promo_interested_to_join_successful_send'=> 'Žádost o služby byla úspěšně odeslána',
			'module_service_pdf_conditions'			=> 'Podmínky užívání',
			'module_service_interested_to_join'		=> 'Mám zájem o službu',
			'module_service_manager_will_contact'	=> 'Žádost byla přijata, manažer se vám v nejbližší době ozve.',
			'module_service_investment_value'		=> 'Výchozí % investice',
			'module_service_conditions_upload_success'	=> 'Soubor byl úspěšně nahrán',
			'module_service_conditions_upload_filesize_min'	=> 'Soubor je příliš malý',
			'module_service_pdf_application'		=> 'Přihláška',
			
			'module_service_conditions'				=> 'Podmínky',
			'module_service_conditions_delete_confirm'	=> 'Opravdu chcete smazat tyto podmínky?',
			'module_service_delete_price_single_confirm'=> 'Opravdu chcete smazat tuto cenu?',
			'module_service_delete_single_confirm'	=> 'Opravdu chcete smazat tuto službu?',
			'module_service_price_is_default'		=> 'Výchozí cena',
			'module_service_wrong_icon'				=> 'Vybraná ikona není platná',
			'module_service_investment'				=> 'Vklad'
			));
	}
}