<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *	@property int		$id_user
 *	@property int		$id_address_type
 *	@property string	$name
 *	@property string	$surname
 *	@property string	$company_name
 *	@property string	$street
 *	@property string	$housenum
 *	@property string	$city
 *	@property string	$zip
 *  @property string	$area_code
 *	@property string	$phone
 *	@property int		$id_country
 *	@property string	$email
 *	@property string	$ico
 *	@property string	$dic
 * 
 *  @property string	$stringify
 * 
 *	@method		Art_Model_Country	getCountry()
 *	@method		\Art_Model_Address	setCountry( Art_Model_Country $country )
 *	@method		Art_Model_Address_Type	getType()
 *	@method		\Art_Model_Address	setType( Art_Model_Address_Type $type )
 *	@method		Art_Model_User		getUser()
 *	@method		\Art_Model_Address	setUser( Art_Model_User $user )
 */
class Art_Model_Address extends Art_Abstract_Model_DB {
	
    protected static $_table = 'address';
    
	protected static $_foreign = array('id_user','id_address_type','id_country');
	
	protected static $_link = array('country'			=> 'Art_Model_Country', 
									'type'				=> 'Art_Model_Address_Type',
									'user'				=> 'Art_Model_User');
	
    protected static $_cols = array('id'				=>	array('select','insert'),
                                    'id_user'			=>	array('select','insert','update'),
                                    'id_address_type'	=>	array('select','insert','update'),
                                    'name'				=>	array('select','insert','update'),
                                    'surname'			=>	array('select','insert','update'),
                                    'company_name'		=>	array('select','insert','update'),
                                    'street'			=>	array('select','insert','update'),
									'housenum'			=>	array('select','insert','update'),                            
									'city'				=>	array('select','insert','update'),
                                    'zip'				=>	array('select','insert','update'),
									'area_code'			=>	array('select','insert','update'),
                                    'phone'				=>	array('select','insert','update'),
                                    'id_country'		=>	array('select','insert','update'),
                                    'email'				=>	array('select','insert','update'),
                                    'ico'				=>	array('select','insert','update'),
                                    'dic'				=>	array('select','insert','update'),
									'created_by'		=>	array('select','insert'),
									'modified_by'		=>	array('select','update'),
									'created_date'		=>	array('select'),
									'modified_date'		=>	array('select'));
	
	const DELIVERY_PREFIX	= 'delivery-';
	const CONTACT_PREFIX	= 'contact-';
	const COMPANY_PREFIX	= 'company-';
	
	/**
	 *	Override get method 
	 * 
	 *	@param string $name
	 *	@return string
	 */
	function __get($name) {
		switch($name) {
			case 'stringify': 
				return static::getStringify($this);
			default:
				return $this->{$name};
		}
	}
		
	
	/**
     *  Get stringify - concatenated street with housenum and city and zip
	 * 
     *  @static
     *  @param Art_Model_Address $address
     *  @return string
     */
    static function getStringify ( $address )
    {
		$stringify = '';
		
		if ( $address->isLoaded() )
		{
			//$country = $address->getCountry();
			
			$stringify = isset($address->street) ? $address->street.' ' : null;
			$stringify .= isset($address->housenum) ? $address->housenum.', ' : null;			
			$stringify .= isset($address->city) ? $address->city.' ' : null;			
			$stringify .= isset($address->zip) ? $address->zip : null;
			
			/*if ( $country->isLoaded() )
			{
				$stringify .= isset($country->name) ? ', '.$country->name : null;
			}*/
		}
		
		return $stringify;
    }
}
