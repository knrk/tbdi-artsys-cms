<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Validator extends Art_Abstract_Component {

    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
    
    /**
     *  @static
     *  @access protected
     *  @var    array   All class constants
     */
	protected static $_optionList;
    
    /** Max length of string */
    const MAX_LENGTH = 'max_length';
	/** Min length of string */
    const MIN_LENGTH = 'min_length';
    
	/** Max numeric value */
    const MAX_VALUE = 'max_value';
	/** Min length of string */
    const MIN_VALUE = 'min_value';
    
    const IS_INTEGER = 'is_integer';
	const IS_NUMERIC = 'is_numeric';
    const IS_STRING = 'is_string';
    const IS_BOOLEAN = 'is_boolean';
    const IS_ARRAY = 'is_array';
    const IS_OBJECT = 'is_object';
    const IS_EMAIL = 'is_email';
	
	const IS_RIGHTS = 'is_rights';
	const IS_LOG_TAG = 'is_logtag';
    const IS_CHECKED = 'is_checked';
	
	const GREATER_THAN = 'greater';
	const GREATER_EQUALS_THAN = 'greater-equals';
	const LESS_THAN = 'less';
	const LESS_EQUALS_THAN = 'less-equals';
	
	const EQUALS = 'equals';
	const IN_ARRAY = 'in_array';
	const NOT_EMPTY = 'not_empty';
	
    const NO_WHITESPACE = 'allow_whitespace';
	
	const REGEX = 'regex';
	
	
    /**
     *  Initialize the component
     *  @static
     *  @return void
     */
    static function init()
    {
        if(parent::init())
        {
            //Get all class constants - options
            $rc = new ReflectionClass(__CLASS__);
            self::$_optionList = $rc->getConstants();
        }
    }
    
    
    /**
     *  Validate variable by given options
	 *	Alias for Art_Validator::validate()
     *
	 *	@see Art_Validator::validate()
     *  @static
     *  @param  mixed   $variable   Variable to be validated
     *  @param  array   $options    Validation options (class constants)
     *  @param  bool    $types      If true - function will return array of failed validators
     *  @return bool|array
     *
     *  @example Validator::isValid('foo bar 123',array(Validator::IS_STRING,Validator::MAX_LENGTH => 3))
     */
	static function isValid($value, $options)
	{
		return static::validate($value, $options);
	}
	
	
    /**
     *  Validate variable by given options
     *
     *  @static
     *  @param  mixed   $variable   Variable to be validated
     *  @param  array   $options    Validation options (class constants)
     *  @param  bool    $types      If true - function will return array of failed validators
     *  @return bool|array
     *
     *  @example Validator::validate('foo bar 123',array(Validator::IS_STRING,Validator::MAX_LENGTH => 3))
     */
    static function validate($variable, $options, $types = false)
    {
        //Convert to array
        if( !is_array($options) )
        {
            $options = array($options);
        }
         
        //Prepare options
        foreach($options as $key => $value)
        {
            if(in_array($key,self::$_optionList,true) !== false)
            {
            }
            elseif(in_array($value,self::$_optionList,true) !== false)
            {
                $options[$value] = true;
                unset($options[$key]);
            }
            else
            {
                unset($options[$key]);
            }
        }

        $resultBoolean = true;
        $resultTypes = array();
       
        //Validate
        foreach($options as $option => $value)
        {
            switch($option)
            {
				case self::NOT_EMPTY:
                    if(empty($variable))
                    {
                        if($types)
                        {
                            $resultTypes[] = self::NOT_EMPTY;
                        }
                        else
                        {
                            $resultBoolean = false;
                        }
						break 2;
                    }
					break;
                case self::IS_INTEGER:
                    if( filter_var($variable, FILTER_VALIDATE_INT) === false )
                    {
                        if($types)
                        {
                            $resultTypes[] = self::IS_INTEGER;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
                    break;
				case self::IS_NUMERIC:
					if(!is_numeric($variable))
					{
                        if($types)
                        {
                            $resultTypes[] = self::IS_NUMERIC;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
					}
					break;
                case self::IS_STRING:
                    if(!is_string($variable))
                    {
                        if($types)
                        {
                            $resultTypes[] = self::IS_STRING;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
                    break;
                case self::IS_BOOLEAN:
                    if(!is_bool($variable))
                    {
                        if($types)
                        {
                            $resultTypes[] = self::IS_BOOLEAN;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
                    break;
                case self::IS_ARRAY:
                    if(!is_array($variable))
                    {
                        if($types)
                        {
                            $resultTypes[] = self::IS_ARRAY;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
                    break;
                case self::IS_OBJECT:
                    if(!is_object($variable))
                    {
                        if($types)
                        {
                            $resultTypes[] = self::IS_OBJECT;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
                    break;
                case self::IS_EMAIL:
                    if(!is_email($variable))
                    {
                        if($types)
                        {
                            $resultTypes[] = self::IS_EMAIL;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
                    break;
                case self::MAX_LENGTH:
                    if(strlen($variable)>$value)
                    {
                        if($types)
                        {
                            $resultTypes[] = self::MAX_LENGTH;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
                    break;
                case self::MIN_LENGTH:
                    if(strlen($variable)<$value)
                    {
                        if($types)
                        {
                            $resultTypes[] = self::MIN_LENGTH;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
                    break;
                case self::MAX_VALUE:
                    if($variable>=$value)
                    {
                        if($types)
                        {
                            $resultTypes[] = self::MAX_VALUE;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
                    break;
                case self::MIN_VALUE:
                    if($variable<$value)
                    {
                        if($types)
                        {
                            $resultTypes[] = self::MIN_VALUE;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
                    break;
                case self::NO_WHITESPACE:
                    if(!ctype_space($variable))
                    {
                        if($types)
                        {
                            $resultTypes[] = self::NO_WHITESPACE;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
                    break;
				case self::EQUALS:
                    if($variable != $value)
                    {
                        if($types)
                        {
                            $resultTypes[] = self::EQUALS;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
                    break;
				case self::IN_ARRAY:
                    if(!in_array($variable,$value))
                    {
                        if($types)
                        {
                            $resultTypes[] = self::IN_ARRAY;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
                    break;
				case self::IS_CHECKED:
                    if(empty($variable) || !$variable)
                    {
                        if($types)
                        {
                            $resultTypes[] = self::IS_CHECKED;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
					break;
				case self::IS_RIGHTS:
                    if(!is_numeric($variable) || $variable < 0 || $variable > Art_User::MAX_RIGHTS)
                    {
                        if($types)
                        {
                            $resultTypes[] = self::IS_RIGHTS;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
					break;
				case self::IS_LOG_TAG:
					if(!is_string($variable) || strlen($variable) != Art_User::LOG_TAG_LENGTH)
                    {
                        if($types)
                        {
                            $resultTypes[] = self::IS_RIGHTS;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
                    }
					break;
				case self::GREATER_THAN:
					if( $variable <= $value )
					{
                        if($types)
                        {
                            $resultTypes[] = self::GREATER_THAN;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
					}
					break;
				case self::GREATER_EQUALS_THAN:
					if( $variable < $value )
					{
                        if($types)
                        {
                            $resultTypes[] = self::GREATER_EQUALS_THAN;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
					}
					break;
				case self::LESS_THAN:
					if( $variable >= $value )
					{
                        if($types)
                        {
                            $resultTypes[] = self::LESS_THAN;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
					}
					break;
				case self::LESS_EQUALS_THAN:
					if( $variable > $value )
					{
                        if($types)
                        {
                            $resultTypes[] = self::LESS_EQUALS_THAN;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
					}
					break;
				case self::REGEX:
					if( preg_match($value, $variable) !== 1 )
					{
                        if($types)
                        {
                            $resultTypes[] = self::REGEX;
                        }
                        else
                        {
                            $resultBoolean = false;
                            break 2;
                        }
					}
					break;
            }
        }
        
        //Return
        if($types)
        {
            return $resultTypes;
        }
        else
        {
            return $resultBoolean;
        }
    }
}