<?php
/**
 *  @package library/models
 * 
 *	@property int		$id
 *	@property int		$id_rights
 *	@property string	$name
 *	@property string	$description
 * 
 *	@method Art_Model_Rights		getRights()
 *	@method Art_Model_User_X_User_Group[]	getGroupUser()
 *	@method this					setRights(Art_Model_Rights $rights)
 *	@method this					setGroupUser(array $user_x_user_group)
 *	@method this					pushGroupUser(Art_Model_User_X_User_Group $user_x_user_group)
 *	@method this					removeFromGroupUser(Art_Model_User_X_User_Group $user_x_user_group)
 */
class Art_Model_User_Group extends Art_Abstract_Model_DB {

	// protected static $_caching = false;
	
    protected static $_table = 'user_group';
    
	protected static $_foreign = array('id_rights');
	
	protected static $_link = array('rights' => 'Art_Model_Rights');
	
	protected static $_fetch = array('group_user' => 'Art_Model_User_X_User_Group');
	
    protected static $_cols = array('id'			=>	array('select','insert'),
                                    'id_rights'		=>	array('select','insert','update'),
                                    'name'			=>	array('select','insert','update'),
                                    'description'	=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

	/**
	 *	@static
	 *	@access protected
	 *	@var Art_Model_User_Group
	 */
	protected static $_authorized = null;

	
	/**
	 *	@static
	 *	@access protected
	 *	@var Art_Model_User_Group
	 */
	protected static $_registered = null;
	

	/**
	 *	@static
	 *	@access protected
	 *	@var Art_Model_User_Group
	 */
	protected static $_manager = null;
	
	
	/**
	 *	Get authorized group type.
	 * 
	 *	@static
	 *	@return Art_Model_User_Group
	 */
	static function getAuthorized()
	{
		if ( NULL === static::$_authorized )
		{
			static::$_authorized = new Art_Model_User_Group(array('name'=>'Authorized'));
		}

		return static::$_authorized;
	}
	
		
	/**
	 *	Get registered group type.
	 * 
	 *	@static
	 *	@return Art_Model_User_Group
	 */
	static function getRegistered()
	{
		if ( NULL === static::$_registered )
		{
			static::$_registered = new Art_Model_User_Group(array('name'=>'Registered'));
		}

		return static::$_registered;
	}
	
	
	/**
	 *	Get manager group type.
	 * 
	 *	@static
	 *	@return Art_Model_User_Group
	 */
	static function getManager()
	{
		if ( NULL === static::$_manager )
		{
			static::$_manager = new Art_Model_User_Group(array('name'=>'Manager'));
		}

		return static::$_manager;
	}
	
	
	/**
	 *	Get authorized group id.
	 * 
	 *	@static
	 *	@return int
	 */
	static function getAuthorizedId()
	{
		return static::getAuthorized()->id;
	}

	
	/**
	 *	Get registered group id.
	 * 
	 *	@static
	 *	@return int
	 */
	static function getRegisteredId()
	{
		return static::getRegistered()->id;
	}
	
	
	/**
	 *	Get manager group id.
	 * 
	 *	@static
	 *	@return int
	 */
	static function getManagerId()
	{
		return static::getManager()->id;
	}
}
