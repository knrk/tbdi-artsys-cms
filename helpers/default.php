<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package helperss
 * 
 *	Default helper used as template
 */
class Helper_Default extends Art_Abstract_Helper {

	static protected $_indexSortBy = 0;

	/**
	 *	Get current month
	 * 
	 *	@static
	 *	@return int
	 */
	static function getCurrentMonth( )
	{
		return date("m");
	}
	
	/**
	 *	Get current year
	 * 
	 *	@static
	 *	@return int
	 */
	static function getCurrentYear( )
	{
		return date("Y");
	}
	
	
	/**
	 *	Get precedent month
	 * 
	 * 	@param	$shift
	 *	@static
	 *	@return int
	 */
	static function getPrecedentMonth( $shift = 1 )
	{
		return date("m",strtotime('-'.$shift.' month'));
	}
	
	/**
	 *	Get year for precedent month
	 * 
	 *	@static
	 *	@param	$shift
	 *	@return int
	 */
	static function getYearForPrecedentMonth( $shift = 1 )
	{
		return date("Y",strtotime('-'.$shift.' month'));
	}

	
	/**
	 *	Default function used as template
	 * 
	 *	@static
	 *	@param string $name
	 *	@param string|int $content
	 * 	@param string	$id
	 * 	@param string	$class
	 * 	@param string	$style
	 *	@return string
	 */
	static function elementPaired($name, $content, $id = NULL, $class = NULL, $style = NULL) {
		$other = '';
		
		if (NULL !== $id) {
			$other .= " id=\"$id\"";
		}
		if (NULL !== $class) {
			$other .= " class=\"$class\"";
		}
		if (NULL !== $style) {
			$other .= " style=\"$style\"";
		}

		return "<$name$other>$content</$name>";
	}
	
	
	/**
	 *	Get validate SQL data for all fields
	 * 
	 *	@static
	 *	@param array $fields
	 *	@param array $fields_validators
	 *	@param array $data
	 *	@param Art_Model_Ajax_Response $response
	 *	@param string $prefix
	 *	@param boolean $forced
	 *	@return array
	 */
	static function getValidatedSQLData( $fields, $fields_validators, $data, &$response, $prefix = NULL, $forced = false )
	{
		$sql_data = array();
		
		foreach($fields AS $field_name)
		{
			$db_field_name = $field_name;
			$field_name = $prefix.$field_name;
			
			//If field is not set
			if( !isset($data[$field_name]) )
			{
				if ( $forced )
				{
					$sql_data[$db_field_name] = NULL;
				}
			}
			else
			{
				$sql_data[$db_field_name] = $data[$field_name];
			}

			//If validator is set
			if( isset($fields_validators[$field_name]) )
			{
				$response->validateField($field_name, $sql_data[$db_field_name], $fields_validators[$field_name]);
			}
		}
		
		return $sql_data;
	}
	
	
	/**
	 *	Is checkbox checked
	 * 
	 *	@static
	 *	@param string $checkbox
	 *	@return boolean
	 */
	static function isCheckboxChecked( $checkbox )
	{
		if ( isset($checkbox) && ( $checkbox === 'on' || $checkbox === 1 || $checkbox === true ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 *	Is object property represents checkbox checked
	 * 
	 *	@static
	 *	@param object|array $object
	 *	@param string $property
	 *	@return boolean
	 */
	static function isPropertyChecked( $object, $property )
	{
		if ( is_string($property) )
		{
			if ( is_object($object) )
			{			
				if ( property_exists($object, $property) )
				{
					return static::isCheckboxChecked($object->$property);
				}
			}
			else if ( is_array($object) )
			{
				if ( array_key_exists($property, $object) )
				{
					return static::isCheckboxChecked($object[$property]);
				}
			}
		}
		
		return false;
	}
	
	
	/**
	 *	Get property or null
	 * 
	 *	@static
	 *	@param object|array $object
	 *	@param string $property
	 *	@return boolean
	 */
	static function getPropertyOrNull( $object, $property )
	{
		return static::getPropertyOrValue($object, $property, null);
	}
	
	
	/**
	 *	Get property or value
	 * 
	 *	@static
	 *	@param object|array $object
	 *	@param string $property
	 *	@param string|int $value
	 *	@return boolean
	 */
	static function getPropertyOrValue( $object, $property, $value )
	{
		if ( is_string($property) )
		{
			if ( is_object($object) )
			{			
				if ( property_exists($object, $property) )
				{
					return $object->$property;
				}
			}
			else if ( is_array($object) )
			{
				if ( array_key_exists($property, $object) )
				{
					return $object[$property];
				}
			}
		}
		
		return $value;
	}
	
	
	
	/**
	 *	Get checked atribute or null
	 * 
	 *	@static
	 *	@param object|array $object
	 *	@param string $property
	 *	@param string|int $matchingValue
	 *	@return boolean
	 */
	static function getCheckedOrNull( $object, $property, $matchingValue )
	{	
		return static::_getSelectedCheckedOrNull($object, $property, $matchingValue, ' checked="checked"');
	}
	
	/**
	 *	Get selected atribute or null
	 * 
	 *	@static
	 *	@param object|array $object
	 *	@param string $property
	 *	@param string|int $matchingValue
	 *	@return boolean
	 */
	static function getSelectedOrNull( $object, $property, $matchingValue )
	{	
		return static::_getSelectedCheckedOrNull($object, $property, $matchingValue, ' selected="selected"');
	}
	
	/**
	 *	Get selected | checked atribute or null
	 * 
	 *	@static
	 *	@param object|array $object
	 *	@param string $property
	 *	@param string|int $matchingValue
	 *	@return boolean
	 */
	static private function _getSelectedCheckedOrNull( $object, $property, $matchingValue, $value )
	{	
		if ( is_string($property) )
		{
			if ( is_object($object) )
			{			
				if ( property_exists($object, $property) )
				{
					if ( $matchingValue == $object->$property )
					{
						return $value;
					}
				}
			}
			else if ( is_array($object) )
			{
				if ( array_key_exists($property, $object) )
				{
					if ( $matchingValue == $object[$property] )
					{
						return $value;
					}
				}
			}
		}
		
		return null;
	}
	
	
	/**
	 *	Verify ICO
	 * 
	 *	@static
	 *	@param int $ico
	 *	@return boolean
	 */
	static function verifyICO ( $ico )
	{
		// má požadovaný tvar?
		if (!preg_match('#^\d{8}$#', $ico)) {
			return FALSE;
		}

		// kontrolní součet
		$a = 0;
		for ($i = 0; $i < 7; $i++) {
			$a += $ico[$i] * (8 - $i);
		}

		$a = $a % 11;
		if ($a === 0) {
			$c = 1;
		} elseif ($a === 1) {
			$c = 0;
		} else {
			$c = 11 - $a;
		}

		return (int) $ico[7] === $c;
	}
	
	
	/**
	 *	Verify DIC
	 * 
	 *	@static
	 *	@param int $dic
	 *	@return boolean
	 */
	static function verifyDIC ( $dic )
	{
		$state = substr($dic, 0, 2);

		$num = substr($dic, 2);

		if ( !is_string($state) || !is_numeric($num) )
		{
			return false;
		}
		
		return true;
	}
	
	
	/**
	 *	Get last logins for user
	 * 
	 *	@static
	 *	@param Art_Model_User	$user
	 *	@return array of dates
	 */
	static function getLastLoginsForUser ( $user )
	{
		if ( $user->isLoaded() )
		{
			$userId = $user->id;
			
			$separator = '#';
			
			$logins = array();
			
			$file = Art_Log::LOGINLOG . '.log';

			$filepath = 'logs/' . $file;
			
			if (file_exists($filepath))
			{
				$handler = fopen($filepath, "r+");

				if ($handler) 
				{
					while (($line = fgets($handler)) !== false) 
					{
						$sepOrder = strpos($line,$separator);
						if ( $userId == substr($line, 0, $sepOrder) )
						{
							$logins[] = date('Y-m-d H:i:s',trim(substr($line, $sepOrder+1)));
						}
					}

					fclose($handler);
				}
			}
		}

		return array_reverse($logins);
	}
	
	/**
	 *	Get all files from directory
	 * 
	 *	@static
	 *	@param string	$directory
	 *	@return array of files
	 */
	static function getFilesFromDirectory ( $directory )
	{
		if (is_dir($directory))
		{
		return array_diff(scandir($directory), array('..', '.'));
		}
		else
		{
			return null;
		}
	}
	
	/**
	 *	Render sort headers up or down arrow plus link
	 * 
	 *	@static
	 *	@param int $sortBy
	 *	@param string $variable
	 *	@return 
	 *
	 *  @deprecated
	 */
	static function renderSortUpDown($sortBy, $variable, $insert = '') {
		// p($sortBy);
		$up = static::$_indexSortBy + 1;
		$down = static::$_indexSortBy;
		static::$_indexSortBy += 2;

		return $sortBy == $up ? '<a href="?'.$variable.'=0">' . $insert . '<i class="fa fa-angle-down"></i>' :
				($sortBy == $down ? '<a href="?'.$variable.'=1">' . $insert . '<i class="fa fa-angle-up"></i>' : 
				'<a href="?'.$variable.'=0">' . $insert);
	}
	
		
	/**
	 *	Get value from Art_Model_Default_Value table or return default
	 *	LOL
	 * 
	 *	@static
	 *	@param string $name
	 *	@param mixed [optional] $default
	 *	@return mixed
	 */
	static function getDefaultValue( $name, $default = NULL )
	{		
		$value = new Art_Model_Default_Value(array('name' => $name));

		if ($value->isLoaded())
		{
			return $value->value;
		}
		else
		{
			return $default;
		}
	}
	
	/**
	 *	Get czech months name
	 * 
	 *	@static
	 *	@return string[]
	 */
	static function getCzechMonthsName( )
	{
		return array(
				1 => 'Leden',
				2 => 'Únor',
				3 => 'Březen',
				4 => 'Duben',
				5 => 'Květen',
				6 => 'Červen',
				7 => 'Červenec',
				8 => 'Srpen',
				9 => 'Září',
				10 => 'Říjen',
				11 => 'Listopad',
				12 => 'Prosinec'
			);
	}
	
	/**
	 *	Get czech name for month
	 * 
	 *	@static
	 *	@param int $month
	 *	@return string
	 */
	static function getCzechMonthName( $month )
	{		
		$months = static::getCzechMonthsName();
		
		if ( (int)$month >= 1 && (int)$month <= 12 )
		{
			return $months[(int)$month];
		}
		else
		{
			return 'UNDEF';
		}
	}
}