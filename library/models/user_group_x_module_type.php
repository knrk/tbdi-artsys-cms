<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *	@property int		$id_user_group
 *	@property int		$id_module_type
 *	@property int		$read_allowed
 *	@property int		$add_allowed
 *	@property int		$update_allowed
 *	@property int		$delete_allowed
 * 
 *	@method Art_Model_User_Group	getUserGroup()
 *	@method Art_Model_Module_type	getModuleType()
 *	@method	this					setUserGroup( Art_Model_User_Group $user_group )
 *	@method this					setModuleType( Art_Model_Module_Type $module_type )
 */
class Art_Model_User_Group_X_Module_Type extends Art_Abstract_Model_DB {
	
    protected static $_table = 'user_group_x_module_type';
    
	protected static $_foreign = array('id_user_group', 'id_module_type');
	
	protected static $_link = array('user_group' => 'Art_Model_User_Group', 'module_type' => 'Art_Model_Module_Type');
	
	protected static $_dependencies = array('module_type');
	
    protected static $_cols = array('id'				=>	array('select','insert'),
                                    'id_user_group'		=>	array('select','insert'),
                                    'id_module_type'	=>	array('select','insert'),
                                    'read_allowed'		=>	array('select','insert','update'),
                                    'add_allowed'		=>	array('select','insert','update'),
                                    'update_allowed'	=>	array('select','insert','update'),
                                    'delete_allowed'	=>	array('select','insert','update'),
									'created_by'		=>	array('select','insert'),
									'modified_by'		=>	array('select','update'),
									'created_date'		=>	array('select'),
									'modified_date'		=>	array('select'));
}