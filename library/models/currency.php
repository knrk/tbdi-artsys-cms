<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *	@property string	$name	Full name
 *	@property string	$abbr	Short name
 *	@property string	$code	ISO code (CZK, EUR) 
 *	@property string	$rate	Ratio between this currency and first one
 */
class Art_Model_Currency extends Art_Abstract_Model_DB {
    
    protected static $_table = 'currency';
    
    protected static $_cols = array('id'			=>  array('select','insert'),
                                    'name'			=>  array('select','insert','update'),
                                    'abbr'			=>  array('select','insert','update'),
                                    'code'			=>  array('select','insert','update'),
                                    'rate'			=>  array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
}