<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$id_service_payment_template
 *	@property int		$id_user_group_x_service_price
 *  @property int		$value
 *  @property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Service_Payment_Template	getServicePaymentTemplate()
 *	@method User_Group_X_Service_Price	getUserGroupXServicePrice()
 *	@method this		setServicePaymentTemplate(Service_Payment_Template $service_payment_template)
 *	@method this		setUserGroupXServicePrice(User_Group_X_Service_Price $user_group_x_service_price)
 */
class Service_Payment_Template_X_User_Group extends Art_Abstract_Model_DB {
    
    protected static $_table = 'service_payment_template_x_user_group';
	  
	protected static $_foreign = array('id_service_payment_template', 'id_user_group_x_service_price');
	
	protected static $_link = array('servicePaymentTtemplate'	=> 'Service_Payment_Template', 
									'userGroupXServicePrice'	=> 'User_Group_X_Service_Price');
    
    protected static $_cols =  array('id'							=>	array('select','insert'),
									'id_service_payment_template'	=>	array('select','insert','update'),
									'id_user_group_x_service_price'	=>	array('select','insert','update'),
									'value'							=>	array('select','insert','update'),
									'created_by'					=>	array('select','insert'),
									'modified_by'					=>	array('select','update'),
									'created_date'					=>	array('select'),
									'modified_date'					=>	array('select'));

}

