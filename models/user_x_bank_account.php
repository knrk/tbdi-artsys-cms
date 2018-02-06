<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$id_user
 *	@property string	$prefix
 *	@property string	$basic
 *	@property string	$bank_code
 *	@property string	$note
 *  @property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Art_Model_User	getUser()
 *	@method this			setUser(Art_Model_User $user)
 */
class User_X_Bank_Account extends Art_Abstract_Model_DB {
    
    protected static $_table = 'user_x_bank_account';
	
	protected static $_foreign = array('id_user');
	
	protected static $_link = array('user'		=> 'Art_Model_User');
			
	protected static $_dependencies = array('user');
    
    protected static $_cols =  array('id'			=>	array('select','insert'),
									'id_user'		=>	array('select','insert','update'),
									'prefix'		=>	array('select','insert','update'),
									'basic'			=>	array('select','insert','update'),
									'bank_code'		=>	array('select','insert','update'),
									'note'			=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

}