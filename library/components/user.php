<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_User extends Art_Abstract_Component {
    
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
    
	/**
	 *	Current user instance
	 * 
	 *	@static
	 *	@access protected
	 *	@var Art_Model_User 
	 */
	protected static $_instance = null;
	
	/**
	 *	Current user login instance
	 *
	 *	@static
	 *	@access protected
	 *	@var Art_Model_Login
	 */
	protected static $_login = null;
	
    /**
     *  Address type constants
     */
    const ADDRESS_CONTACT = 1;
    const ADDRESS_DELIVERY = 2;
    const ADDRESS_BILL = 3;
    
    /** Usr max rights */
    const MAX_RIGHTS = 101;
    
    /**
     *  User rights groups constants
     */
    const NO_ACCESS = 101;
    const SUPERADMIN =  100;
    const ADMIN = 50;
    const EDITOR = 30;
    const MODERATOR = 20;
    const REGISTERED = 1;
    const NONREGISTERED = 0;
    const ALL = 0;
    
	/** strlen of log_tag */
	const LOG_TAG_LENGTH = 32;
	
	/** Cookie logtag name */
	const LOG_TAG_NAME = 'lt';
	
	/** Default user rights */
	const DEFAULT_RIGHTS = 0;
	
	/** Default currency ID */
	const DEFAULT_CURRENCY_ID = 1;
    
	/** If used in createUser, user db tables will be truncated */
	const PURGE_DB_TOKEN = 'IUGF5678KJHY';
	
	/** User number for default user */
	const USER_NUMBER_DEFAULT_USER = 10;
	
	/** User number for cron user */
	const USER_NUMBER_CRON = 2;
	
	/** Default cron user rights */
	const DEFAULT_CRON_RIGHTS = 100;
	
	/**
	 *	Get instance of current logged user
	 * 
	 *	@return Art_Model_User
	 */
	static function getCurrentUser()
	{
		return self::$_instance;
	}
	
	
	/**
	 *	Get instance of current user login
	 * 
	 *	@return User
	 */
	static function getCurrentLogin()
	{
		return self::$_login;
	}
	
	
    /**
     *  @static
     *  @return int User ID
     */
    static function getId()
    {
        return self::$_instance->id;
    }
	
        
    /**
     *  @static
     *  @return int Rights of user (0 - 100)
     */
    static function getRights()
    {
		// print_r(self::$_instance);
        return self::$_instance->getRights();
    }
	

    /**
     *  @static
	 *	@param bool $explicit If true, function will return true only if user is explicitely registered
     *  @return bool True if user is registered
     */
    static function isRegistered($explicit = false)
    {
		if( $explicit )
		{
			return self::$_instance->getRights() == self::REGISTERED;
		}
		else
		{
			return self::$_instance->getRights() >= self::REGISTERED;
		}
    }    
    
	
    /**
     *  @static
	 *	@param bool $explicit If true, function will return true only if user is explicitely a moderator
     *  @return bool True if user is moderaton
     */
    static function isModerator($explicit = false)
    {
		if( $explicit )
		{
			return self::$_instance->getRights() == self::MODERATOR;
		}
		else
		{
			return self::$_instance->getRights() >= self::MODERATOR;
		}
    }    
	
	
    /**
     *  @static
	 *	@param bool $explicit If true, function will return true only if user is explicitely an editor
     *  @return bool True if user is editor
     */
    static function isEditor($explicit = false)
    {
		if( $explicit )
		{
			return self::$_instance->getRights() == self::EDITOR;
		}
		else
		{
			return self::$_instance->getRights() >= self::EDITOR;
		}
    }    
	
	
    /**
     *  @static
	 *	@param bool $explicit If true, function will return true only if user is explicitely an admin
     *  @return bool True if user is admin
     */
    static function isAdmin($explicit = false)
    {
		if( $explicit )
		{
			return self::$_instance->getRights() == self::ADMIN;
		}
		else
		{
			return self::$_instance->getRights() >= self::ADMIN;
		}
    }    
	
	
    /**
     *  @static
	 *	@param bool $explicit If true, function will return true only if user is explicitely a superadmin
     *  @return bool True if user is superadmin
     */
    static function isSuperadmin($explicit = false)
    {
		if( $explicit )
		{
			return self::$_instance->getRights() == self::SUPERADMIN;
		}
		else
		{
			return self::$_instance->getRights() >= self::SUPERADMIN;
		}
    }   
	
	
	/**
	 *	Returns true if user is logged in
	 * 
	 *	@static
	 *	@return bool
	 */
	static function isLogged()
	{
		return self::isRegistered();
	}
	
	
    /**
     *  Initialize the component
     *  @static
     *  @return void
     */
    static function init()
    {
        if(parent::init())
        {			
			//If is requested as CRON
			if( Art_Server::isCron() )
			{
				//User with ID 2 is a CRON user
				$user = new Art_Model_User(array('user_number' => static::USER_NUMBER_CRON));
				
				if( !$user->isLoaded() )
				{
					self::_createCronUser();
				}
				else
				{
					static::$_instance = $user;
				}
				
				static::$_login = new Art_Model_Login;
			}
			else
			{
				$login = new Art_Model_Login();
				
				//If logtag is valid
				if( Art_Validator::validate(ri($_COOKIE[self::LOG_TAG_NAME]), Art_Validator::IS_LOG_TAG) )
				{
					$login->load(array('log_tag' => $_COOKIE[self::LOG_TAG_NAME]));
				}
				
				//If logtag was found
				if( $login->isLoaded() && $login->getUser()->isLoaded() )
				{
					//Save instance
					self::$_instance = $login->getUser();
					self::$_login = $login;		

					//Refresh logtag
					self::_refreshLogTag();
				}
				else
				{
					self::_createNewUser();
				}
			}
		}
	}
		
	
    /**
     *  Refresh user's log_tag
	 * 
     *  @static
     *  @access protected
     *  @return void
     */
    protected static function _refreshLogTag()
    {
        //Refresh log_tag
        $login_expire = time() + AUTH_EXPIRE;
		self::$_login->login_expire = $login_expire;
		self::$_login->save();

        //Refresh logtag in cookie
        cookie_set(self::LOG_TAG_NAME, self::$_login->log_tag, $login_expire);
    }
	
	
    /**
     *  Creates new user, his user_number and log_tag
	 * 
     *  @static
	 *	@access protected
     *  @return void
     */
    protected static function _createNewUser()
    {
		//Create user
		$user = new Art_Model_User();
		$user->id_currency = static::DEFAULT_CURRENCY_ID;
		
		//Save instance
		self::$_instance = $user;
		
		//Save to get id
		$user->save();
		
		//Save user_number
		$user->user_number = self::generateUserNumber($user->id);
		$user->active = 1;
		$user->save();
		
		//Generate log_tag
		$log_tag = self::generateLogTag();
		$login_expire = time() + AUTH_EXPIRE;
		
		//Create login
		$login = new Art_Model_Login();
		$login->setUser($user);
		$login->log_tag = $log_tag;
		$login->login_date = dateSQL();
		$login->login_expire = $login_expire;
		$login->ip = Art_Server::getIp();
		$login->save();
		
		//Reload and save instances
		self::$_instance = new Art_Model_User($user->id);
		self::$_login = new Art_Model_Login($login->id);
		
        //Save log_tag to cookie
        cookie_set(Art_User::LOG_TAG_NAME,$log_tag,$login_expire);
    }
	
	
	/**
	 *	Create user for CRON job
	 * 
	 *	@access protected
	 *	@static
	 *	@return void
	 */
	protected static function _createCronUser()
	{
		//Create user
		$user = new Art_Model_User();
		$user->id_currency = static::DEFAULT_CURRENCY_ID;
		$user->user_number = static::USER_NUMBER_CRON;
		$user->rights = static::DEFAULT_CRON_RIGHTS;
		
		static::$_instance = $user;
		
		$user->save();
		
		//Reload and save instances
		self::$_instance = new Art_Model_User($user->id);	
	}
	
	
    /**
     *  Generates user number based on id_user
	 * 
     *  @static
     *  @return int user_number
     */
    static function generateUserNumber($id_user) {
		return AUTH_USER + ($id_user) * 2;
    }
	
    
    /**
     *  Generates pseudo-random 32byte string
	 * 
     *  @static
	 *	@return string random 32byte
     */
    static function generateLogTag()
    {
        return rand_str(32);
    }
	
	
	/**
	 *	Generate random password string (not hashed) length = 6
	 * 
	 *	@static
	 *	@return string Password (6 chars)
	 */
	static function generatePassword()
	{
		return substr(md5(rand()),0,6);
	}
	
	 
	/**
	 *	Generate salt
	 *	
	 *	@static
	 *	@return string Salt (32 chars)
	 */
	static function generateSalt()
	{
		return mcrypt_create_iv(32, MCRYPT_DEV_URANDOM);
	}
	

	/**
	 *	Match non hashed password with hashed one
	 * 
	 *	@static
	 *	@param string $pass NONhashed password
	 *	@param string $salt Salt used for hashing the second password
	 *	@param string $pass_h HASHED password
	 *	@return bool True if passwords equals
	 */
	static function matchPasswords($pass,$salt,$pass_h)
	{
		$pass_h2 = static::hashPassword($pass,$salt);
		return ($pass_h2 === $pass_h && strlen($pass_h) > 10);
	}
	
	
	/**
	 *	Hash pasword by given password and salt (max_length 60)
	 * 
	 *	@static
	 *	@param string $password Original user password
	 *	@param string $salt Salt to add to password
	 *	@return string Hashed password
	 */
	static function hashPassword($password, $salt) {
		return substr(password_hash($password, PASSWORD_BCRYPT, ['cost' => 8, 'salt' => $salt]), 0, 60);
	}
	
	
    /**
     *  True if user has privileges compared to param
	 * 
     *  @static
     *  @param object $object Rights value or object to compare with user's one
     *  @return bool True if user has privileges
     *  @example User::hasPrivileges({testModule})
     */
    static function hasPrivileges($object)
    {
        return self::getCurrentUser()->hasPrivileges($object);
    }
	
	
	/**
	 *	Create default user
	 *	Purge db if purge_db token is used as second param
	 * 
	 *	@static
	 *	@param string $purge_db_token
	 *	@return void
	 */
	static function createDefaultUser( $purge_db_token = NULL )
	{
		return static::createUser('info@itart.cz', 'abc123', Art_User::SUPERADMIN, static::USER_NUMBER_DEFAULT_USER, $purge_db_token);
	}
	
	
	/**
	 *	Creates new user with creditentals in params
	 * 
	 *	@static
	 *	@param string $login
	 *	@param string $password
	 *	@param int $rights
	 *	@param int $user_number
	 *	@param string $purge_db_token
	 *	@return void
	 */
	static function createUser( $login, $password, $rights, $user_number, $purge_db_token = NULL )
	{
		if( $purge_db_token === static::PURGE_DB_TOKEN )
		{
			Art_Main::db()->query('TRUNCATE TABLE user_group_x_module_type');
			Art_Main::db()->query('TRUNCATE TABLE user_x_user_group');
			Art_Main::db()->query('TRUNCATE TABLE user_group');
			Art_Main::db()->query('TRUNCATE TABLE user_data');
			Art_Main::db()->query('TRUNCATE TABLE login');
			Art_Main::db()->query('TRUNCATE TABLE user');
		}
		
		$user = new Art_Model_User;
		$user->user_number = $user_number;
		$user->active = 1;
		
		$currency = new Art_Model_Currency(self::DEFAULT_CURRENCY_ID);
		if( $currency->isLoaded() )
		{
			$user->setCurrency($currency);
		}
		
		$user->save();
		
		$user_data = new Art_Model_User_Data;
		$user_data->name = 'Admin';
		$user_data->surname = 'IT ART';
		$user_data->email = $login;
		$user_data->salt = Art_User::generateSalt();
		$user_data->password = Art_User::hashPassword($password, $user_data->salt);
		$user_data->verif = 1;
		$user_data->pass_changed_date = dateSQL();
		$user_data->setUser($user);
		$user_data->save();
		
		if( $rights > 0 )
		{
			$rights = new Art_Model_Rights(array('value' => $rights));
			if( $rights->isLoaded() )
			{
				$user_group = new Art_Model_User_Group($rights);
				if( !$user_group->isLoaded() )
				{
					$user_group->setRights($rights);
					$user_group->name = 'Default group';
					$user_group->description = 'Group created by default';
					$user_group->save();					
				}

				$user_x_user_group = new Art_Model_User_X_User_Group();
				$user_x_user_group->setUser($user);
				$user_x_user_group->setGroup($user_group);
				$user_x_user_group->save();
			}
			else
			{
				trigger_error('Rights with SUPERADMIN value was not found, default user was not assigned to group', E_USER_WARNING);
			}
		}
		
		return $user;
	}
	
	
	/**
	 *	Returns true if user is allowed to read from module
	 * 
	 *	@param mixed $module_type
	 *	@return boolean
	 */
	static function readAllowed( $module_type )
	{
		return static::$_instance->readAllowed($module_type);
	}


	/**
	 *	Returns true if user is allowed to add in module
	 * 
	 *	@param mixed $module_type
	 *	@return boolean
	 */	
	static function addAllowed( $module_type )
	{
		return static::$_instance->addAllowed($module_type);
	}
	
	
	/**
	 *	Returns true if user is allowed to update in module
	 * 
	 *	@param mixed $module_type
	 *	@return boolean
	 */	
	static function updateAllowed( $module_type )
	{
		return static::$_instance->updateAllowed($module_type);
	}
	

	/**
	 *	Returns true if user is allowed to delete in module
	 * 
	 *	@param mixed $module_type
	 *	@return boolean
	 */	
	static function deleteAllowed( $module_type )
	{
		return static::$_instance->deleteAllowed($module_type);
	}
}