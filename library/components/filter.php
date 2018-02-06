<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Filter extends Art_Abstract_Component {
	
	/**
     *  @var bool True if was initialized 
     */
    protected static $_initialized = false;
	
	
	/**
	 *	Converts regular name in url name	
	 *	Foo Bar -> foo-bar
	 * 
	 *	@param string $name
	 *	@return string
	 */
	static function urlName($name)
	{
		$url_name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
		$url_name = strtolower($url_name);
		$url_name = preg_replace("/[^A-Za-z0-9 ]/", '', $url_name);
		$url_name = preg_replace('!\s+!', ' ', $url_name);
		$url_name = str_replace(' ', '-', $url_name);

		return $url_name;
	}
	
	
	/**
	 *	Removes whitespaces and translates comma to dot
	 *	10 000,5 => 10000.5
	 * 
	 *	@param string $price
	 *	@return string
	 */
	static function price($price)
	{
		return strtr($price, array(' ' => '', ',' => '.'));	
	}
	
	
	/**
	 *	Filter date-like string to date by given format
	 *	
	 *	10-12-2015 => 10.12.2015
	 * 
	 *	@param string $date
	 *	@param string $format
	 *	@return string
	 */
	static function date($date, $format = 'j.n.Y')
	{
		return date($format, strtotime($date));
	}
	
	
	/**
	 *	Convert whatever the date to SQL date format
	 * 
	 *	@static
	 *	@param string|int $date
	 *	@return string
	 */
	static function dateSQL($date)
	{
		if( is_int($date) )
		{
			return date('Y-m-d',$date);
		}
		else
		{
			return date('Y-m-d',strtotime($date));
		}
	}
	
	
	/**
	 *	Convert module class name or type in class name
	 * 
	 *	article			=> Module_article
	 *	Module_Article	=> Module_artice
	 * 
	 *	@param string $name
	 *	@return string
	 */
	static function moduleClassName( $name )
	{
		//If class prefix not found
		if( strpos($name, Art_Module::CLASS_PREFIX) !== 0)
		{
			$name = Art_Module::CLASS_PREFIX.$name;
		}
		
		return $name;
	}
	
	
	/**
	 *	Convert module class name or type in type
	 * 
	 *	article			=> article
	 *	Module_Article	=> artice
	 * 
	 *	@param string $name
	 *	@return string
	 */
	static function moduleType( $name )
	{
		//If class prefix is found
		if( strpos($name, Art_Module::CLASS_PREFIX) === 0)
		{
			$name = strtolower(substr($name,strlen(Art_Module::CLASS_PREFIX)));
		}
		
		return $name;
	}
	
	
	/**
	 *	Convert module short action name to long name
	 * 
	 *	index			=> indexAction
	 *	indexAction		=> indexAction
	 * 
	 *	@param string $name
	 *	@return string
	 */
	static function moduleAction( $name )
	{
		if( strrpos($name, Art_Module::ACTION_POSTFIX) !== strlen($name) - strlen(Art_Module::ACTION_POSTFIX) )
		{
			$name = $name.Art_Module::ACTION_POSTFIX;
		}		
		
		return $name;
	}
	
	
	/**
	 *	Convert module long action name to short name
	 * 
	 *	index			=> index
	 *	indexAction		=> index
	 * 
	 *	@param string $name
	 *	@return string
	 */
	static function moduleActionShort( $name )
	{
		if( strrpos($name, Art_Module::ACTION_POSTFIX) === strlen($name) - strlen(Art_Module::ACTION_POSTFIX) )
		{
			$name = substr($name, 0, -strlen(Art_Module::ACTION_POSTFIX));
		}		
		
		return $name;
	}
	
	
	
	/**
	 *	Convert short template name to long
	 * 
	 *	default			=> defaultTemplate
	 *	defaultTemplate	=> defaultTemplate
	 * 
	 *	@param string $name
	 *	@return string
	 */
	static function templateName( $name )
	{
		if( strrpos($name, Art_Template::TEMPLATE_NAME_POSTFIX) !== strlen($name) - strlen(Art_Template::TEMPLATE_NAME_POSTFIX) )
		{
			$name = $name.Art_Template::TEMPLATE_NAME_POSTFIX;
		}		
		
		return $name;
	}
	
	
	/**
	 *	Convert long template name to short
	 * 
	 *	default			=> default
	 *	defaultTemplate => default
	 * 
	 *	@param string $name
	 *	@return string
	 */
	static function templateNameShort( $name )
	{
		if( strrpos($name, Art_Template::TEMPLATE_NAME_POSTFIX) === strlen($name) - strlen(Art_Template::TEMPLATE_NAME_POSTFIX) )
		{
			$name = substr($name, 0, -strlen(Art_Template::TEMPLATE_NAME_POSTFIX));
		}		
		
		return $name;
	}
	
	
	/**
	 *	Convert module class name or type in class name
	 * 
	 *	article			=> Module_Bootstrap_article
	 *	Module_Article	=> Module_Bootstrap_artice
	 * 
	 *	@param string $name
	 *	@return string
	 */
	static function moduleBootstrapClassName( $name )
	{
		//If class prefix not found
		if( strpos($name, Art_Module::BOOTSTRAP_CLASS_PREFIX) !== 0 )
		{
			$name = Art_Module::BOOTSTRAP_CLASS_PREFIX.$name;
		}
		
		return $name;
	}
	
	
	/**
	 *	Convert helper class name or type in class name
	 * 
	 *	article			=> Helper_article
	 *	Helper_Article	=> Helper_artice
	 * 
	 *	@param string $name
	 *	@return string
	 */
	static function helperClassName( $name )
	{
		//If class prefix not found
		if( strpos($name, Art_Main::HELPER_CLASS_PREFIX) !== 0 )
		{
			$name = Art_Main::HELPER_CLASS_PREFIX.$name;
		}
		
		return $name;
	}
	
	
	/**
	 *	Converts string|int to bool
	 * 
	 *	@param string|int $statement
	 *	@return bool
	 */
	static function toBool( $statement )
	{
		return filter_var($statement, FILTER_VALIDATE_BOOLEAN);
	}
	
	
	/**
	 *	Converts string|int to bool integer ( 1 / 0 )
	 * 
	 *	@param string|int $statement
	 *	@return int
	 */
	static function toBoolInt( $statement )
	{
		if( self::toBool($statement) )
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	
	/**
	 *	Sort array of objects by their values
	 * 
	 *	@static
	 *	@param array $array
	 *	@param $childs_relation [optional] bool|string|array
	 *	@param $childs_var [optional] string
	 *	@return array
	 */
	static function parentStructure( array $array, $childs_relation = true, $childs_var = 'childs' )
	{
		//Get primary and parent variable name
		switch( gettype($childs_relation) )
		{
			case 'array':
				$primary = reset($childs_relation);
				$parent = key($childs_relation);
				break;
			case 'boolean':
				$primary = Art_Abstract_Model_Db::PRIMARY_COL_NAME;
				$parent = Art_Abstract_Model_Db::PARENT_COL_NAME;
				break;
			case 'string':
				$parent = $childs_relation;
				$primary = Art_Abstract_Model_Db::PRIMARY_COL_NAME;
				break;
			default:
				trigger_error('Invalid order childs argument supplied to Art_Filter::sortObjectSet()',E_USER_ERROR);
		}

		//Get items in parent structure
		$array = Art_Filter::_parentStructureLayer($array, 0, $primary, $parent, $childs_var);
		
		return $array;
	}
	
	
	/**
	 *	Return one layer of structured data
	 * 
	 *	@static
	 *	@access protected
	 *	@param array $array
	 *	@param string $parent_id
	 *	@param string $primary
	 *	@param string $parent
	 *	@param string $childs_var
	 *	@return array
	 */
	static protected function _parentStructureLayer( array $array, $parent_id, $primary, $parent, $childs_var)
	{
		$output = array();
		
		//For each item in arary
		foreach( $array AS $item )
		{
			//If is item with set parent id
			if( $parent_id == $item->$parent )
			{
				//Load all subitems
				$item->$childs_var = Art_Filter::_parentStructureLayer($array, $item->$primary, $primary, $parent, $childs_var);
				
				//Add to output
				$output[] = $item;
			}
		}
		
		return $output;
	}
	
	
	/**
	 *	Group objects by their parameter
	 * 
	 *	@param array $array
	 *	@param string $group_by
	 *	@return array
	 */
	static function groupObjects( array $array, $group_by = 'parent_id')
	{
		$output = array();
		
		//For each input item
		foreach( $array AS $item )
		{
			//Initialize output array
			if( !isset($output[$item->$group_by]) )
			{
				$output[$item->$group_by] = array();
			}
			
			//Put in array
			$output[$item->$group_by][] = $item;
		}
		
		return $output;
	}
	
	
	/**
	 *	Sort set of object by their value
	 *	
	 *	@static
	 *	@param array $array
	 *	@param string [optional] $order_by
	 *	@param string [optional] $order
	 *	@return array
	 */
	static function sortObjects( array $array, $order_by = 'sort', $order = 'ASC' )
	{
		//Reset array keys
		$array = Art_Filter::resetKeys($array);
		
		//Sort by order_by
		if( NULL !== $order_by )
		{
			//Get order comparator character
			switch( strtolower($order) )
			{
				case 'asc':
				case '>':
				{
					$order = '>';
					break;
				}
				case 'desc':
				case '<':
				{
					$order = '<';
					break;
				}
				default :
				{
					$order = '>';
				}
			}
			
			$length = count($array);

			//For each item
			for($i = 0; $i < $length; ++$i)
			{
				//If order value is set
				if( isset($array[$i]->$order_by) )
				{
					//Save to temp
					$tmp = &$array[$i];

					//Order descending
					if( '>' == $order )
					{
						//For each previous items lower than compared one
						for($j = $i; $j && $tmp->$order_by > $array[$j - 1]->$order_by; --$j)
						{
							//Move item
							$array[$j] = &$array[$j - 1];
						}		
					}
					//Order ascending
					else
					{
						//For each previous items lower than compared one
						for($j = $i; $j && $tmp->$order_by < $array[$j - 1]->$order_by; --$j)
						{
							//Move item
							$array[$j] = &$array[$j - 1];
						}						
					}
					
					//Save current compared item as the highest of compared by previous for
					$array[$j] = &$tmp;
				}
			}
		}
		
		return $array;
	}
	
	
	/**
	 *	Reset array keys
	 * 
	 *	@static
	 *	@param array $array
	 *	@return array
	 */
	static function resetKeys( array $array )
	{
		$output = array();
		
		$i = 0;
		foreach($array AS &$value)
		{
			$output[$i] = $value;
			++$i;
		}
		
		return $output;
	}
	
	
	/**
	 *	Cut string and add postfix
	 * 
	 *	@static
	 *	@param string $string
	 *	@param int $length
	 *	@param string [optional] $postfix
	 *	@return string
	 */
	static function cutString( $string, $length, $postfix = '...')
	{
		if( strlen($string) > $length )
		{
			return mb_substr($string, 0, $length - strlen($postfix) ).$postfix;
		}
		else
		{
			return $string;
		}
	}
	
	
	/**
	 *	Make ITEM unique compared to OTHERS by adding integer postfix
	 * 
	 *	@static
	 *	@param string|int $item
	 *	@param array $others
	 *	@return string|int
	 */
	static function makeUnique( $item, array $others, $postfix_separator = '_' )
	{
		$i = 0;

		while( ++$i )
		{
			foreach($others AS $other)
			{
				if( 1 === $i )
				{
					if( $other == $item)
					{
						continue 2;
					}
				}
				else
				{
					if( $other == $item.'_'.$i)
					{
						continue 2;
					}
				}
			}

			break;
		}

		if( 1 !== $i )
		{
			$item .= $postfix_separator.$i;
		}
		
		return $item;
	}
	
	
	/**
	 *	Filter URL param
	 *	fooBar => foobar
	 * 
	 *	@param string $param
	 *	@return string
	 */
	static function urlParam( $param )
	{
		$param = strtolower( $param );
		
		return $param;
	}
}