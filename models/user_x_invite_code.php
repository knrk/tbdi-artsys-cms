<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$id_user
 *	@property int		$id_invite_code
 *	@property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Art_Model_User	getUser()
 *	@method Invite_Code		getInviteCode()
 *	@method this			setUser(Art_Model_User $user)
 *	@method this			setInviteCode(Invite_Code $invite_code)
 */
class User_X_Invite_Code extends Art_Abstract_Model_DB {
    
    protected static $_table = 'user_x_invite_code';
	
	protected static $_foreign = array('id_user','id_invite_code');
	
	protected static $_link = array('user'			=> 'Art_Model_User',
									'inviteCode'	=> 'Invite_Code');
    
    protected static $_cols =  array('id'			=>	array('select','insert'),
									'id_user'		=>	array('select','insert','update'),
									'id_invite_code'=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

}