<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$id_user
 *	@property int		$id_service_investment
 *	@property int		$invested
 *	@property int		$interest
 *	@property int		$commission
 *  @property string	$note
 *  @property date		$payment_date
 *	@property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Art_Model_User	getUser()
 *	@method this			setUser(Art_Model_User $user)
 *	@method Service_Investment	getService_investment()
 *	@method this			setService_investment(Service_Investment $investment)
 */
class Service_Investment_Value extends Art_Abstract_Model_DB {
    
    protected static $_table = 'service_investment_value';
	
	protected static $_foreign = array('id_user','id_service_investment');
	
	protected static $_link = array('user'			=> 'Art_Model_User', 
									'service_investment'	=> 'Service_Investment');
    
    protected static $_cols =  array('id'			=>	array('select','insert'),
									'id_user'		=>	array('select','insert','update'),
									'id_service_investment'	=>	array('select','insert','update'),
									'invested'		=>	array('select','insert','update'),
									'interest'		=>	array('select','insert','update'),
									'commission'	=>	array('select','insert','update'),
									'note'			=>	array('select','insert','update'),
									'payment_date'	=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

}