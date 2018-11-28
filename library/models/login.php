<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *	@property int		$id_user
 *	@property string	$log_tag		Random 32 chars string
 *	@property int		$login_expire	Timestamp of login expiration
 *	@property string	$login_date
 *	@property string	$ip
 *	@property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Art_Model_User		getUser()						Returns owner of this login
 *	@method Art_Model_Login		setUser(Art_Model_User $user)	Sets owner of this login
 */
class Art_Model_Login extends Art_Abstract_Model_DB {

	// protected static $_caching = false;
	
    protected static $_table = 'login';
    
	protected static $_foreign = array('id_user');
	
	protected static $_link = array('user' => 'Art_Model_User');
	
    protected static $_cols = array('id'			=>	array('select','insert'),
									'id_user'		=>	array('select','insert'),
                                    'log_tag'		=>	array('select','insert','update'),
                                    'login_expire'	=>	array('select','insert','update'),
                                    'login_date'	=>	array('select','insert','update'),
									'ip'			=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
}