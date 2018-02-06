<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$id_dashboard
 *	@property int		$id_user_group
 *  @property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Dashboard				getDashboard()
 *	@method Art_Model_User_Group	getUserGroup()
 *	@method this					setDashboard(Dashboard $dashboard)
 *	@method this					setUserGroup(Art_Model_User_Group $user_group)
 */
class Dashboard_X_User_Group extends Art_Abstract_Model_DB {
    
    protected static $_table = 'dashboard_x_user_group';
	  
	protected static $_foreign = array('id_dashboard', 'id_user_group');
	
	protected static $_link = array('dashboard'		=> 'Dashboard', 
									'userGroup'	=> 'Art_Model_User_Group');
	    
    protected static $_cols =  array('id'			=>	array('select','insert'),
									'id_dashboard'	=>	array('select','insert','update'),
									'id_user_group'	=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

}