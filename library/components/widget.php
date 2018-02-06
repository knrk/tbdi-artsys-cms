<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Widget extends Art_Abstract_Component {
	
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;	
	
	/**
	 *	Widget opening tag
	 */
	const OPENING_TAG = '#[';
	
	/**
	 *	Widget closing tag
	 */
	const CLOSING_TAG = ']#';
	
	/**
	 *	Default widget action
	 */
	const DEFAULT_ACTION = 'embedd';
	
	
	/**
	 *	Render widget by module type and action name
	 * 
	 *	@static
	 *	@param string $type
	 *	@param string $action_name
	 *	@param array $params
	 *	@return string
	 */
	static function render($type, $action_name = self::DEFAULT_ACTION, array $params = array())
	{
		return Art_Module::createAndRenderModule($type, $action_name, $params, true);
	}
	
	
	/**
	 *	Replace widget tags in content with widgets
	 * 
	 *	@static
	 *	@param string $content
	 *	@return void
	 */
	static function replaceTagsWithWidgets( &$content )
	{
		//Get widget tags positions
		$tags_positions = static::_getTagsPosition($content);
		
		//Get widgets data
		$widgets_data = static::_getWidgetData($content, $tags_positions);
	
		//Replace tags with widgets
		foreach( $widgets_data AS $widget )
		{
			//If module exists
			if( Art_Module::exists($widget['section'], $widget['action']) )
			{
				$content = str_replace(self::OPENING_TAG.$widget['code'].self::CLOSING_TAG, Art_Widget::render($widget['section'], $widget['action'], $widget['data']), $content);
			}
		}
	}
	
	
	/**
	 *	Get widget tags position from content
	 * 
	 *	@static
	 *	@access protected
	 *	@param string $content
	 *	@return string
	 */
	protected static function _getTagsPosition( &$content )
	{
		$content_length = strlen($content);
		
		$matches = array();
		
		//For each letter
		for($i = 0; $i < $content_length; ++$i)
		{
			//If start of opening tag found
			if( '#' == $content[$i] )
			{
				if( '[' == $content[$i + 1] )
				{
					$matches[$i++] = 'o';
				}
			}
			//If start of closing tag found
			elseif( ']' == $content[$i] )
			{
				if( '#' == $content[$i + 1] )
				{
					$matches[$i++] = 'c';
				}
			}
		}
		
		//Filter matches
		$currently_oppened = false;
		foreach($matches AS $pos => $type)
		{
			if( $currently_oppened === false )
			{
				if( 'o' == $type )
				{
					$currently_oppened = $pos;
				}
				else
				{
					unset( $matches[$pos] );
				}
			}
			else
			{
				if( 'c' == $type )
				{
					$currently_oppened = false;
				}
				else
				{
					unset( $matches[$currently_oppened] );
					$currently_oppened = $pos;
				}
			}
		}
		//If ends with opening tag
		if( 'o' == end($matches) )
		{
			array_pop($matches);
		}
		
		return $matches;
	}
	
	
	/**
	 *	Get widget data from content and tags positions
	 * 
	 *	@param string $content
	 *	@param array $tags_positions
	 *	@return array
	 */
	protected static function _getWidgetData( &$content, &$tags_positions )
	{
		//Get only positins
		$positions = array_keys($tags_positions);

		$widgets = array();
		
		//Read each widget content
		while( !empty($positions) )
		{
			$start = array_shift($positions) + 2;
			$stop = array_shift($positions);

			if( $stop - $start > 2 )
			{
				//Get code
				$widget = array('code' => substr($content, $start, $stop - $start));
				
				//Explode to words
				$widget['parts'] = explode(' ', $widget['code']);

				//Get first part as section:action
				if( false !== ( $pos = strpos($widget['parts'][0], ':') ) )
				{
					$widget['section'] = substr($widget['parts'][0], 0, $pos);
					$widget['action'] = substr($widget['parts'][0], $pos + 1 );
				}
				else
				{
					$widget['section'] = $widget['parts'][0];
					$widget['action'] = self::DEFAULT_ACTION;
				}
				
				//Unset section:actions from parts
				unset($widget['parts'][0]);
				
				//Prepare data
				$widget['data'] = array();
				foreach($widget['parts'] AS &$part)
				{
					if( false !== ( $pos = strpos($part, ':') ) )
					{
						$widget['data'][substr($part, 0, $pos)] = substr($part, $pos + 1 );
					}
					else
					{
						$widget['data'][$part] = '';
					}
				}
				
				$widgets[$start] = $widget;
			}
		}
		
		return $widgets;
	}
	
}