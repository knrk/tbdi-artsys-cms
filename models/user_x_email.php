<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$id_user
 *	@property string	$email_type
 * 
 *	@method Art_Model_User			getUser()
 *	@method	this					setUser( Art_Model_User $user )
 */
class User_X_Email extends Art_Abstract_Model_DB {
	
    protected static $_table = 'user_x_email';
    
	protected static $_foreign = array('id_user');
	
	protected static $_link = array('user' => 'Art_Model_User');
	
	protected static $_dependencies = array('user');
	
    protected static $_cols = array('id'				=>	array('select','insert'),
                                    'id_user'			=>	array('select','update','insert'),
                                    'email_type'		=>	array('select','update','insert'),
									'created_by'		=>	array('select','insert'),
									'modified_by'		=>	array('select','update'),
									'created_date'		=>	array('select'),
									'modified_date'		=>	array('select'));
}