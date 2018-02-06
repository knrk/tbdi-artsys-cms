<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$id_user
 *	@property string	$code
 *	@property string	$note
 *  @property int		$active
 *	@property stdClass	$settings		Object stored as JSON
 *	@property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Art_Model_User	getUser()
 *	@method this			setUser(Art_Model_User $user)
 */
class Invite_Code extends Art_Abstract_Model_DB {
    
    protected static $_table = 'invite_code';
	
	protected static $_foreign = array('id_user');
	
	protected static $_link = array('user'		=> 'Art_Model_User');
    
    protected static $_cols =  array('id'			=>	array('select','insert'),
									'id_user'		=>	array('select','insert','update'),
									'code'			=>	array('select','insert','update'),
									'note'			=>	array('select','insert','update'),
									'active'		=>	array('select','insert','update'),
									'settings'		=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

}