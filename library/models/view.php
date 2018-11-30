<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_View {
	
	/**
	 *	@var string View path
	 */
	protected $_path;
	

	/**
	 *	Override get method - omitt the "Variable is not defined" error on all variables
	 * 
	 *	@param string $name
	 *	@return string
	 */
	function &__get( $name )
	{
		//Set variable to empty
		$this->{$name} = '';
		
		return $this->{$name};
	}
	
	
	/**
	 *	@param string $path
	 */
	function __construct($path)
	{
		if(is_readable($path))
		{
			$this->_path = $path;
		}
		else
		{
			trigger_error('View in '.$path.' not found',E_USER_ERROR);
			return false;
		}
	}
	
	
	/**
	 *	Parse values and render view
	 * 
	 *	@param array|object $values
	 *	@param Art_Abstract_Module
	 *	@return string
	 */
	function render($values = array(),$module = NULL)
	{
		//Pass view values to this instance
		if(is_object($values) || is_array($values))
		{
			foreach($values AS $name => $value)
			{
				$this->$name = $value;
			}
		}

		$output = '';
		
		if( NULL !== $module )
		{
			$type = $module->getType();
			$showName = $module->getShowName();
			// $output = '<div class="module_container module_'.$type.'_'.$module->getActionName().' '.$type.'_container">'.
			$output = '<div class="module_container module_'.$type.'_'.$module->getActionName().' ' . $type . '-container">'.
						( $showName ? '<h3 class="module_header '.$type.'_header">'.$module->getName().'</h3>' : '' ).
						// '<div class="module_content '.$type.'_content ' .$type.'-content">';
						'<div class="module_content ' . $type . '-content">';
		}
		
		$output .= call_user_func(function($path) 
					{
						ob_start();
						require($path);
						return ob_get_clean();
					},$this->_path);
		
		if( NULL !== $module )
		{	
			$output .= '  </div>
					</div>';
		}
		
		Art_Widget::replaceTagsWithWidgets($output);
		
		return $output;
	}
	
	
	/**
	 *	Render anti-spam protected email
	 * 
	 *	@param string $email
	 *	@param bool [optional] $mail_to
	 *	@return string
	 */
	function emailProtection( $email, $mail_to = true )
	{
		if( Art_Validator::validate($email, Art_Validator::IS_EMAIL) )
		{
			$pos1 = strpos($email,'@');
			$pos2 = strrpos($email,'.');
			$n = substr($email,0,$pos1);
			$d = substr($email,$pos1+1,$pos2-$pos1-1);
			$c = substr($email,$pos2+1);
			
			return '<span class="art-email-protected" data-mail_to="'.(int)$mail_to.'" data-n="'.$n.'" data-d="'.$d.'" data-c="'.$c.'">'.rand_str(strlen($n)).'@'.rand_str(strlen($d)).'.'.$c.'</span>';
		}
		else
		{
			return $email;
		}
	}
}