<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property string	$header
 *	@property string	$body
 *	@property boolean	$important
 *  @property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 */
class Dashboard extends Art_Abstract_Model_DB {
    
    protected static $_table = 'dashboard';
    
    protected static $_cols =  array('id'			=>	array('select','insert'),
									'header'		=>	array('select','insert','update'),
									'body'			=>	array('select','insert','update'),
									'important'		=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

}