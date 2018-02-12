<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id_user
 *	@property string	$name
 *	@property string	$surname
 *  @property string	$degree
 *	@property string	$username
 *	@property string	$gender
 *	@property string	$salutation
 *	@property string	$email
 *	@property int		$born_day
 *	@property int		$born_month
 *	@property int		$born_year
 *	@property string	$password
 *	@property string	$salt		(binary 32)
 *	@property int		$verif		(0/1) if email was verified
 *	@property string	$verif_id	20 random chars
 *	@property string	$verif_date Verification datetime
 *	@property string	$pass_changed_date Password changed datetime
 *	@property string	$forgotten_pass_hash Forgotten password request value
 *	@property string	$forgotten_pass_IP Forgotten password request by IP
 *  @property string	$forgotten_pass_date Forgotten password request datetime
 * 
 *  @property string	$fullname 
 *  @property string	$fullnameWithDegree 
 *  @property string	$born
 * 
 *	@method		Art_Model_User		getUser()
 *	@method		\Art_Model_Address	setUser( Art_Model_User $user )
 */
class Art_Model_User_Data extends Art_Abstract_Model_DB {
	
    protected static $_table = 'user_data';
	
	protected static $_foreign = array('id_user');
	
	protected static $_dependencies = array('user');
	
	protected static $_link = array('user' => 'Art_Model_User', 'currency' => 'Art_Model_Currency');

    protected static $_cols = array('id'					=>	array('select','insert'),
									'id_user'				=>	array('select','insert'),
                                    'name'					=>	array('select','insert','update'),
                                    'surname'				=>	array('select','insert','update'),
									'degree'				=>	array('select','insert','update'),
                                    'username'				=>	array('select','insert','update'),
									'gender'				=>	array('select','insert','update'),
									'salutation'			=>	array('select','insert','update'),
                                    'email'					=>	array('select','insert','update'),
									'born_day'				=>	array('select','insert','update'),
									'born_month'			=>	array('select','insert','update'),
									'born_year'				=>	array('select','insert','update'),
                                    'password'				=>	array('select','insert','update'),
                                    'salt'					=>	array('select','insert','update'),
                                    'verif'					=>	array('select','insert','update'),
                                    'verif_id'				=>	array('select','insert','update'),
                                    'verif_date'			=>	array('select','insert','update'),
									'pass_changed_date'		=>	array('select','insert','update'),
									'forgotten_pass_hash'	=>	array('select','insert','update'),
									'forgotten_pass_IP'		=>	array('select','insert','update'),
									'forgotten_pass_date'	=>	array('select','insert','update'),
									'created_by'			=>	array('select','insert'),
									'modified_by'			=>	array('select','update'),
									'created_date'			=>	array('select'),
									'modified_date'			=>	array('select'));
	
	/**
	 *	Override get method 
	 * 
	 *	@param string $name
	 *	@return string
	 */
	function __get($name) {
		switch($name) {
			case 'fullname': 
				return static::getFullname($this);
			case 'fullnameWithDegree': 
				return static::getFullnameWithDegree($this);
			case 'born': 
				return static::getBorn($this);
			case 'degreeSA': 
				return $this->degree ? $this->degree : '-';
			default:
				return $this->{$name};
		}
	}
	
	
	/**
     *  Get fullname - concatenated name with surname
	 * 
     *  @static
     *  @param Art_Model_User_Data $userData
     *  @return string
     */
    static function getFullname ( $userData )
    {
		$fullname = '';
		
		if ( $userData->isLoaded() )
		{
			$fullname = !empty($userData->name) ? $userData->name : null;
			if ( !empty($userData->name) && !empty($userData->surname) )
			{
				$fullname .= ' ';
			}
			$fullname .= !empty($userData->surname) ? $userData->surname : null;
		}
		
		return $fullname;
    }
	
	
	/**
     *  Get fullname with degree - concatenated degree with name and surname
	 * 
     *  @static
     *  @param Art_Model_User_Data $userData
     *  @return string
     */
    static function getFullnameWithDegree ( $userData )
    {
		$fullnameWithDegree = '';
		
		if ( $userData->isLoaded() )
		{
			$fullnameWithDegree = !empty($userData->degree) ? $userData->degree.' ' : null;
			$fullnameWithDegree .= static::getFullname($userData);
		}
		
		return $fullnameWithDegree;
    }
	
	
	/**
     *  Get born - concatenated day with month and year
	 * 
     *  @static
     *  @param Art_Model_User_Data $userData
     *  @return string
     */
    static function getBorn ( $userData )
    {
		$born = '';
		
		if ( $userData->isLoaded() )
		{
			$born = isset($userData->born_day) ? $userData->born_day.'. ' : null;
			$born .= isset($userData->born_month) ? $userData->born_month.'. ' : null;
			$born .= isset($userData->born_year) ? $userData->born_year : null;
		}
		
		return $born;
	}
}
