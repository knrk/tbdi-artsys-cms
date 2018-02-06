<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$id_user
 *	@property int		$value
 *	@property date		$date
 *	@property date		$expiry_date
 *  @property string	$note
 *  @property int		$terminated
 *	@property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Art_Model_User	getUser()
 *	@method this			setUser(Art_Model_User $user)
 */
class Service_Investment_Deposit extends Art_Abstract_Model_DB {
    
    protected static $_table = 'service_investment_deposit';
	
	protected static $_foreign = array('id_user');
	
	protected static $_link = array('user'		=> 'Art_Model_User');
    
    protected static $_cols =  array('id'			=>	array('select','insert'),
									'id_user'		=>	array('select','insert','update'),
									'value'			=>	array('select','insert','update'),
									'date'			=>	array('select','insert','update'),
									'expiry_date'	=>	array('select','insert','update'),
                                    'note'	        =>	array('select','insert','update'),
                                    'terminated'	=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

}