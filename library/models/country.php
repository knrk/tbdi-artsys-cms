<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *	@property string	$name			Full country name
 *	@property string	$abbr			ISO name (CZE, USA, RUS)
 *	@property int		$id_currency	Id of currency used in the country
 */
class Art_Model_Country extends Art_Abstract_Model_DB {
	
    protected static $_table = 'country';
    
	protected static $_foreign = array('id_currency');

    protected static $_cols = array('id'			=>	array('select','insert'),
                                    'name'			=>	array('select','insert','update'),
                                    'abbr'			=>	array('select','insert','update'),
                                    'id_currency'	=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
}