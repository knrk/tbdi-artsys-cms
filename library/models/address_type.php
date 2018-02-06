<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *	@property string	$name
 *	@property string	$description
 */
class Art_Model_Address_Type extends Art_Abstract_Model_DB {
	
    protected static $_table = 'address_type';
    
    protected static $_cols = array('id'				=>	array('select','insert'),
                                    'name'				=>	array('select','insert','update'),
                                    'description'		=>	array('select','insert','update'),
									'created_by'		=>	array('select','insert'),
									'modified_by'		=>	array('select','update'),
									'created_date'		=>	array('select'),
									'modified_date'		=>	array('select'));
	
		
	/**
	 *	@static
	 *	@access protected
	 *	@var Art_Model_Address_Type
	 */
	protected static $_residental = null;
	
	
	/**
	 *	@static
	 *	@access protected
	 *	@var Art_Model_Address_Type
	 */
	protected static $_contact = null;
	
	
	/**
	 *	@static
	 *	@access protected
	 *	@var Art_Model_Address_Type
	 */
	protected static $_delivery = null;
	
	
	/**
	 *	@static
	 *	@access protected
	 *	@var Art_Model_Address_Type
	 */
	protected static $_invoicing = null;	
	
	
	/**
	 *	@static
	 *	@access protected
	 *	@var Art_Model_Address_Type
	 */
	protected static $_company = null;
	
	
	/**
	 *	Get residental address type.
	 * 
	 *	@static
	 *	@return Art_Model_Address_Type
	 */
	static function getResidental()
	{
		if ( NULL === static::$_residental )
		{
			static::$_residental = new Art_Model_Address_Type(array('name'=>'residental'));
		}

		return static::$_residental;
	}
	
		
	/**
	 *	Get contact address type.
	 * 
	 *	@static
	 *	@return Art_Model_Address_Type
	 */
	static function getContact()
	{
		if ( NULL === static::$_contact )
		{
			static::$_contact = new Art_Model_Address_Type(array('name'=>'contact'));
		}

		return static::$_contact;
	}
	
	
	/**
	 *	Get delivery address type.
	 * 
	 *	@static
	 *	@return Art_Model_Address_Type
	 */
	static function getDelivery()
	{
		if ( NULL === static::$_delivery )
		{
			static::$_delivery = new Art_Model_Address_Type(array('name'=>'delivery'));
		}

		return static::$_delivery;
	}
	
		
	/**
	 *	Get invoicing address type.
	 * 
	 *	@static
	 *	@return Art_Model_Address_Type
	 */
	static function getInvoicing()
	{
		if ( NULL === static::$_invoicing )
		{
			static::$_invoicing = new Art_Model_Address_Type(array('name'=>'invoicing'));
		}

		return static::$_invoicing;
	}	
	
		
	/**
	 *	Get company contact address type.
	 * 
	 *	@static
	 *	@return Art_Model_Address_Type
	 */
	static function getCompany()
	{
		if ( NULL === static::$_company )
		{
			static::$_company = new Art_Model_Address_Type(array('name'=>'company'));
		}

		return static::$_company;
	}
	
	
	/**
	 *	Get delivery address id.
	 * 
	 *	@static
	 *	@return int
	 */
	static function getResidentalId()
	{
		return static::getResidental()->id;
	}

	
	/**
	 *	Get contact address id.
	 * 
	 *	@static
	 *	@return int
	 */
	static function getContactId()
	{
		return static::getContact()->id;
	}
	
	
	/**
	 *	Get delivery address id.
	 * 
	 *	@static
	 *	@return int
	 */
	static function getDeliveryId()
	{
		return static::getDelivery()->id;
	}

	
	/**
	 *	Get contact address id.
	 * 
	 *	@static
	 *	@return int
	 */
	static function getInvoicingId()
	{
		return static::getInvoicing()->id;
	}
	
	
	/**
	 *	Get company contact address id.
	 * 
	 *	@static
	 *	@return int
	 */
	static function getCompanyId()
	{
		return static::getCompany()->id;
	}
}
