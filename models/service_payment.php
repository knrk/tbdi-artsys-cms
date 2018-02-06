<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$id_user
 *  @property int		$id_user_paid_by
 *	@property int		$id_user_group_x_service_price
 *  @property int		$value
 *	@property date		$received_date
 *  @property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Art_Model_User				getUser()
 *	@method Art_Model_User				getUserPaidBy()
 *	@method User_Group_X_Service_Price	getUserGroupXServicePrice()
 *	@method this		setUser(Art_Model_User $user)
 *	@method this		setUserPaidBy(Art_Model_User $user)
 *	@method this		setUserGroupXServicePrice(User_Group_X_Service_Price $user_group_x_service_price)
 */
class Service_Payment extends Art_Abstract_Model_DB {
    
    protected static $_table = 'service_payment';
	    
	protected static $_foreign = array('id_user','id_user_paid_by','id_user_group_x_service_price');
	
	protected static $_link = array('user'						=> 'Art_Model_User', 
									'userPaidBy'				=> 'Art_Model_User',
									'userGroupXServicePrice'	=> 'User_Group_X_Service_Price');
    
    protected static $_cols =  array('id'						=>	array('select','insert'),
									'id_user'					=>	array('select','insert','update'),
									'id_user_paid_by'			=>	array('select','insert','update'),
									'id_user_group_x_service_price'	=>	array('select','insert','update'),
									'value'						=>	array('select','insert','update'),
									'received_date'				=>	array('select','insert','update'),
									'created_by'				=>	array('select','insert'),
									'modified_by'				=>	array('select','update'),
									'created_date'				=>	array('select'),
									'modified_date'				=>	array('select'));

	/**
	 *	Override get method 
	 * 
	 *	@param string $name
	 *	@return string
	 */
	function __get($name) {
		switch($name) {
			case 'date': 
				return static::gePaymentDate($this);
			default:
				return $this->{$name};
		}
	}
	
	
	/**
     *  Get date of payment - received date or created date
	 * 
     *  @static
     *  @param Service_Payment $servicePayment
     *  @return date
     */
    static function gePaymentDate ( $servicePayment )
    {
		$date = '';
		
		if ( $servicePayment->isLoaded() )
		{
			$date = $servicePayment->received_date;
			
			if ( NULL === $date || '0000-00-00' == $date )
			{
				$date = $servicePayment->created_date;
			}
		}
		
		return date($date);
    }	
}

