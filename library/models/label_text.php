<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property string $id
 *	@property string $key	Unique label identifier (dashed_lower_case_form)
 *	@property string $note	Used only for marking labels, can be blank (not recommended)
 *	@property string $cs
 *	@property string $en
 */
class Art_Model_Label_Text extends Art_Abstract_Model_DB {
    
    protected static $_table = 'label_text';
    
    protected static $_cols = array('id'			=>  array('select','insert'),
                                    'key'			=>  array('select','insert'),
                                    'note'			=>  array('select','insert','update'),
                                    'cs'			=>  array('select','insert','update'),
                                    'en'			=>  array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
	
	
	/**
	 *	Fetch all labels for locale as array(array('key' => ..., 'value' => ...)...)
	 * 
	 *	@static
	 *	@param string $locale
	 *	@return array
	 */
	static function fetchAllLocalSimple( $locale )
	{
		$select_stmt = new Art_Model_Db_Select(self::$_table, array('key',array('name' => $locale, 'alias' => 'value')));
		$query = Art_Main::db()->select($select_stmt);
		$query->execute();
		$data = $query->fetchAll(PDO::FETCH_ASSOC);
		
		$output = array();
		foreach($data AS $item)
		{
			$output[$item['key']] = $item['value'];
		}

		return $output;
	}
}