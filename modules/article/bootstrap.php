<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/article
 */
class Module_Bootstrap_Article extends Art_Abstract_Module_Bootstrap {	
	
	static function initialize()
	{
		Art_Event::on(Art_Event::LABEL_SETUP, function(){
			static::labelSetup();
		});
				
		Art_Event::on(Art_Event::ROUTER_SETUP, function(){
			static::routerSetup();
		});
		
		Art_Event::on(Art_Event::NODEABLE_ACTIONS_SETUP, function(){
			static::nodeableActionsSetup();
		});
	}
	
	static function nodeableActionsSetup()
	{
		Art_Register::in('nodeable_actions')->set('article', array(
			new Art_Model_Nodeable_Action('index', 'module_article_detail')
		));
		Art_Register::in('nodeable_actions')->set('article_2', array(
			new Art_Model_Nodeable_Action('index2', 'module_article_detail')
		));
		Art_Register::in('nodeable_actions')->set('article_3', array(
			new Art_Model_Nodeable_Action('index3', 'module_article_detail')
		));		
	}
	
	static function routerSetup() 
	{
		Art_Router::addRoute('module_article_detail' ,'/article/$1', array ('layer'=> Art_Router::LAYER_FRONTEND, 'section'=>'article', 'action'=>'index', 'id'=>'$1'));
	}	
	
	static function labelSetup() 
	{
		Art_Label::addLabelSet(array(
			'module_article'							=> 'Články',
			'module_article_action_index'				=> 'Jeden článek',
			
			'module_article_edit_article'				=> 'Úprava článku',
			'module_article_new_article'				=> 'Nový článek',
			'module_article_articles'					=> 'Články',
			'module_article_published'					=> 'Publikován',
			'module_article_category'					=> 'Kategorie',
			'module_article_not_found'					=> 'Článek nebyl nalezen',
			'module_article_title'						=> 'Titulek',
			'module_article_perex'						=> 'Perex',
			'module_article_url'						=> 'URL',
			'module_article_content'					=> 'Obsah',
			'module_article_meta_title'					=> 'META titulek',
			'module_article_meta_keywords'				=> 'META klíčová slova',
			'module_article_meta_description'			=> 'META popis',
			'module_article_delete_selected_confirm'	=> 'Opravdu chcete odebrat zvolené položky?',
			'module_article_delete_single_confirm'		=> 'Opravdu chcete odebrat článek {$name}?',
			'module_article_deleted_not_found'			=>	'Článek byl již smazán, nebo nemohl být nalezen',
			'module_article_url_used_already'			=> 'Tento odkaz URL je již použit pro jiný článek',
			'module_article_added_success'				=> 'Článek %s byl úspěšně přidán',
			'module_article_deleted_when_edit'			=> 'Článek nebyl nalezen. Pravděpodobně byl smazán jiným uživatelem',
			'module_article_no_rights_when_edit'		=> 'Nemáte dostatečná oprávnění pro editaci tohoto článku',
			'module_article_edited_success'				=> 'Článek %s byl úspěšně upraven',
			'module_article_invalid_name_short'			=> 'Název je příliš krátký',
			'module_article_invalid_name_long'			=> 'Název musí být kratší než 100 znaků',
			'module_article_invalid_perex_long'			=> 'Perex nesmí být delší než 500 znaků',
			'module_article_invalid_url_long'			=> 'URL nesmí být delší než 100 znaků',
			'module_article_invalid_bad_cat'			=> 'Zvolte správnou kategorii',
			'module_article_invalid_content_short'		=> 'Obsah je příliš krátký',
			'module_article_invalid_meta_title_long'	=> 'META titulek nesmí být delší než 70 znaků',
			'module_article_invalid_meta_kw_long'		=> 'META klíčová slova nesmí být delší než 100 znaků',
			'module_article_invalid_meta_desc'			=> 'META popisek nesmí být delší než 300 znaků',
			));
	}
}