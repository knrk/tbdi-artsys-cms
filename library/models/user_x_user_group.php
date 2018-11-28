<?php
/**
 *  @package library/models
 * 
 *	@property int		$id
 *	@property int		$id_user
 *	@property int		$id_user_group
 * 
 *	@method Art_Model_User			getUser()
 *	@method Art_Model_User_Group	getGroup()
 *	@method	this						setUser( Art_Model_User $user )
 *	@method this						setGroup( Art_Model_User_Group $user_group )
 */
class Art_Model_User_X_User_Group extends Art_Abstract_Model_DB {

	protected static $_caching = true;
	
    protected static $_table = 'user_x_user_group';
    
	protected static $_foreign = array('id_user', 'id_user_group');
	
	protected static $_link = array('user' => 'Art_Model_User', 'group' => 'Art_Model_User_Group');
	
	protected static $_dependencies = array('user', 'group');
	
    protected static $_cols = array('id'			=>	array('select','insert'),
                                    'id_user'		=>	array('select','insert'),
                                    'id_user_group'	=>	array('select','insert'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
}