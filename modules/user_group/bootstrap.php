<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/user_group
 */
class Module_Bootstrap_User_Group extends Art_Abstract_Module_Bootstrap {
	
	static function initialize()
	{
		Art_Event::on(Art_Event::LABEL_SETUP,function(){
			static::labelSetup();
		});
	}
	
	static function labelSetup() 
	{
		Art_Label::addLabelSet(array(
			'module_user_group_header'			=> 'Skupiny',
			'module_user_group_detail_header'	=> 'Detail skupiny',
			
			'module_user_group_v_name_min'				=> 'Název je příliš krátký',
			'module_user_group_v_name_max'				=> 'Název je příliš dlouhý',
			'module_user_group_v_description_max'		=> 'Popis je příliš dlouhý',
			'module_user_group_v_id_rights_not_integer'	=> 'Práva nejsou platná',
			'module_user_group_v_id_service_not_integer'=> 'Služba není platná',
			'module_user_group_v_id_service_price_not_integer'=> 'Cena služby není platná',
			'module_user_group_v_from_day_not_integer'	=> 'Den od není platný',
			'module_user_group_v_from_month_not_integer'=> 'Měsíc od není platný',
			'module_user_group_v_from_year_not_integer'	=> 'Rok od není platný',
			'module_user_group_v_to_day_not_integer'	=> 'Den do není platný',
			'module_user_group_v_to_month_not_integer'	=> 'Měsíc do není platný',
			'module_user_group_v_to_year_not_integer'	=> 'Rok do není platný',
			
			'module_user_group_add_service'		=> 'Přidat službu',
			'module_user_group_add_service_success'=> 'Služba byla úspěšně přidána',
			'module_user_group_add_user'		=> 'Přidat uživatele',
			'module_user_group_add_user_success'=> 'Uživatel byl úspěšně přidána',
			'module_user_group_add_cannot_be'	=> 'Uživatel nemůže být přidán',
			'module_user_group_add_none_selected'=> 'Nebyl vybrán žáden uživatel pro přidání',
			
			'module_user_group_take_away_user_cannot'=> 'Uživatel nemůže být odebrán',
			'module_user_group_take_away_user_success'=> 'Uživatel byl úspěšně odebrán',
			'module_user_group_take_away_user_not_found'=> 'Uživatel nebyl nalezen',
			'module_user_group_take_away_service_contained_in_payments'=> 'Služba je obsažena v platbě',
			'module_user_group_take_away_service_success'=> 'Služba byla úspěšně odebrána',
			'module_user_group_take_away_service_not_found'=> 'Služba nebyla nalezena',
			
			'module_user_group_delete_success'	=> 'Skupina byla úspěšně odebrána',
			'module_user_group_delete_not_found'=> 'Skupina nebyla nalezena',
			'module_user_group_delete_contains_user_or_service'=> 'Skupina obsahuje služby nebo uživatele',
			
			'module_user_group_service'			=> 'Typ služby',
			'module_user_group_service_price'	=> 'Cena služby',
			'module_user_group_add_group'		=> 'Přidání skupiny',
			'module_user_group_name'			=> 'Název skupiny',
			'module_user_group_description'		=> 'Popis',
			'module_user_group_rights'			=> 'Práva skupiny',
			'module_user_group_service_price'	=> 'Cena služby',
			'module_user_group_company_name'	=> 'Název firmy (skupiny)',
			'module_user_group_edit_group'		=> 'Úprava skupiny',
			'module_user_group_company_name'	=> 'Název firmy (skupiny)',
			'module_user_group_not_found'		=> 'Skupina nebyla nalezena',
			'module_user_group_add_success'		=> 'Skupina byla úspěšně přidána',
			'module_user_group_edit_success'	=> 'Skupina byla úspěšně upravena',
			
			'module_user_group_delete_single_confirm'		=> 'Opravdu chcete smazat tuto skupinu?',
			'module_user_group_take_away_service_single_confirm'=> 'Opravdu chcete odebrat tuto cenu?',
			'module_user_group_take_away_user_single_confirm'	=> 'Opravdu chcete odebrat tohoto uživatele?',
			));
	}
}