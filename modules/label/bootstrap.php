<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/label
 */
class Module_Bootstrap_Label extends Art_Abstract_Module_Bootstrap {
	
	static function initialize()
	{
		Art_Event::on(Art_Event::LABEL_SETUP, function(){
			static::labelSetup();
		});
	}
	
	static function labelSetup() 
	{
		Art_Label::addLabelSet(array(
			'module_label_labels'			=> 'Překlady',
			'module_label_new_label'		=> 'Nový překlad',
			'module_label_edit_label'		=> 'Editace překladu',
			'module_label_choose'			=> 'Nabídka',
			'module_label_key'				=> 'Klíč',
			'module_label_group'			=> 'Skupina',
			'module_label_cs'				=> 'Čeština',
			'module_label_en'				=> 'Angličtina',
			'module_meta_self'				=> 'Vlastní META popisek',
			'module_label_q_delete'			=> 'Opravdu chcete odebrat zvolené položky?',
			'module_label_v_key_min'		=> 'Klíč je příliš krátký',
			'module_label_v_key_max'		=> 'Klíč musí být kratší než 60 znaků',
			'module_label_editted'			=> 'Překlad byl úspěšně upraven',
			'module_label_deleted'			=> 'Překlad byl odstraněn',
			'module_label_deleted_more'		=> 'Překlady (%d) byly odstraněny',
			'module_label_deleted_none'		=> 'Žádný překlad nebyl odstraněn',
			'module_label_added'			=> 'Překlad %s byl úspěšně přidán',
			'module_label_added_alert'		=> 'Překlad %s je již vytvořen',
			'module_label_not_found'		=> 'Překlad nebyl nalezen. Pravděpodobně byl smazán jiným uživatelem'
			));
	}
}