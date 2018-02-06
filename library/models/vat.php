<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *	@property string	$name
 *	@property string	$description
 *	@property float		$value
 *	@property int		$sort
 */
class Art_Model_Vat extends Art_Abstract_Model_DB {
    
    protected static $_table = 'vat';
	    
    protected static $_cols = array('id'			=>  array('select','insert'),
                                    'name'			=>  array('select','insert','update'),
                                    'description'	=>  array('select','insert','update'),
                                    'value'			=>  array('select','insert','update'),
                                    'sort'			=>  array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

	/**
	 *	Get coefficient (for using to multiply with prices)
	 * 
	 *	@return float
	 */
	function getCoeff()
	{
		return $this->value/100;
	}
}