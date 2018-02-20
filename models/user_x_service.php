<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$id_user
 *	@property int		$id_service
 *	@property int		$activated
 *	@property date		$activated_date
 *	@property date		$deactivated_date
 *	@property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Art_Model_User	getUser()
 *	@method Service			getService()
 *	@method this			setUser(Art_Model_User $user)
 *	@method this			setService(Service $service)
 */
class User_X_Service extends Art_Abstract_Model_DB {

	protected static $_caching = false;
    
    protected static $_table = 'user_x_service';
	
	protected static $_foreign = array('id_user','id_service');
	
	protected static $_link = array('user'		=> 'Art_Model_User',
									'service'	=> 'Service');
    
    protected static $_cols =  array('id'			=>	array('select','insert'),
									'id_user'		=>	array('select','insert','update'),
									'id_service'	=>	array('select','insert','update'),
									'activated'		=>	array('select','insert','update'),
									'activated_date'=>	array('select','insert','update'),
									'deactivated_date'=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

}