<?php
/**
 *  @author Robin Zon <zon@itart.cz>
 *  @package modules/emailtemplate
 */
class Module_Bootstrap_Emailtemplate extends Art_Abstract_Module_Bootstrap {	
	
	static function initialize()
	{
		Art_Event::on(Art_Event::LABEL_SETUP, function(){
			static::labelSetup();
		});
	}

	
	static function labelSetup() 
	{
		Art_Label::addLabelSet(array(
			'module_emailtemplate'							=> 'Šablony emailů',
			'module_emaildump_delete_single_confirm'		=> 'Opravdu chcete odebrat šablonu {$name}?',
			'module_emaildump_deleted_not_found'			=> 'Šablona byla již smazána, nebo nemohla být nalezena',
			'module_emaildump_added_success'				=> 'Šablona byla úspěšně přidána',
			'module_emailtemplate_deleted_when_edit'		=> 'Šablona nemohla být nalezena, pravděpodobně je již smazána',
			'module_emailtemplate_no_rights_when_edit'		=> 'Nemáte oprávnění pro editaci šablony',
			'module_emailtemplate_edited_success'			=> 'Šablona byla úspěšně upravena',
			'module_emailtemplate_invalid_name_short'		=> 'Název šablony je příliš krátký',
			'module_emailtemplate_invalid_name_long'		=> 'Název šablony je příliš dlouhý',
			'module_emailtemplate_invalid_subject_long'		=> 'Předmět emailu je příliš dlouhý',
			'module_emailtemplate_invalid_from_name_long'	=> 'Jméno odesílatele je příliš dlouhé',
			'module_emailtemplate_invalid_from_email'		=> 'Email odesílatele není platný',
			'module_emailtemplate_invalid_reply_to_name_long' => 'Jméno pro odpověď je příliš dlouhé',
			'module_emailtemplate_invalid_reply_to_email'   => 'Email pro odpověď není platný',
			'module_emailtemplate_invalid_body_short'		=> 'Tělo emailu je příliš krátké',
			'module_emailtemplate_add_emailtemplate'		=> 'Přidat šablonu emailu',
			'module_emailtemplate_edit_emailtemplate'		=> 'Upravit šablonu emailu',
			'module_emailtemplate_name'						=> 'Název',
			'module_emailtemplate_subject'					=> 'Předmět',
			'module_emailtemplate_from_name'				=> 'Jméno odesílatele',
			'module_emailtemplate_from_email'				=> 'Email odesílatele',
			'module_emailtemplate_reply_to_name'			=> 'Jméno pro odpověď',
			'module_emailtemplate_reply_to_email'			=> 'Email pro odpověď',
			'module_emailtemplate_body'						=> 'Tělo emailu',
			
			'module_emailtemplate_delete_single_confirm'	=> 'Opravdu chcete smazat tuto šablonu emailu?',
			'module_emailtemplate_deleted_not_found'		=> 'Šablona emailu nebyla nalezena',
			));
	}
}