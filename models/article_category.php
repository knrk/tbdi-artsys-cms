<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property string	$name
 *	@property string	$url_name
 *	@property string	$description
 *  @property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 */
class Article_Category extends Art_Abstract_Model_DB {
	
    protected static $_table = 'article_category';
    
    protected static $_cols = array('id'				=>	array('select','insert'),
                                    'name'			=>	array('select','insert','update'),
                                    'url_name'		=>	array('select','insert','update'),
                                    'description'	=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
	
}