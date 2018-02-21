<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Cron extends Art_Abstract_Component {
    
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array
	 */
	protected static $_timestamps = array();
	
	/**
	 *  Cron URI param name
	 */
	const CRON_URI_PARAM_NAME = '_cron';

	/**
	 *	Cron timestamps cache folder
	 */
	const CACHE_FOLDER = 'tmp/cron';
	
	/**
	 *	Cron timestamps cache file
	 */
	const CACHE_FILE = 'cron.txt';
	
	/**
	 *	Timestamp for utility cron
	 */
	const UTILITY_TIMESTAMP_NAME = '_utility';
	
    /**
     *  Initialize the component
	 * 
     *  @static
     *  @return void
     */
    static function init()
    {
        if(parent::init())
        {      
			//Load timestamps from cache
			static::_loadCache();			
			
			$uri_guid = Art_Router::getFromURI(static::CRON_URI_PARAM_NAME);

			//Is requested by CRON
			if( $uri_guid )
			{
				//Call utility cron at 3-6 AM
				Art_Event::on(Art_Event::CRON, function($event) {
					$time = (int)date('G');
					//If is between 3-6 AM
					if( $time >= 3 && $time < 6 )
					{
						//If last call was at least 12 hours ago
						$last = static::getDiffTimestampsByName(static::UTILITY_TIMESTAMP_NAME);
						if( $last > 12 )
						{
							$cron_data = $event->getData();
							$cron_data['name'] = str_replace('cron_', '', Art_Event::CRON_UTILITY);
							
							//Save timestamp
							static::saveTimestamp(static::UTILITY_TIMESTAMP_NAME);
							
							//Call utility event
							Art_Event::trigger(Art_Event::CRON, $cron_data);
							Art_Event::trigger(Art_Event::CRON_UTILITY, $cron_data);
						}
					}
				});
				
				$is_cron = false;
				//For each cron
				foreach (CRON_TASKS as $name => $guid) {
					if ($uri_guid == $guid) {
						$cron_data = array('guid' => $guid, 'name' => $name);								
						$is_cron = true;

						Art_Main::startExecTime('cron_'.$name);
						Art_Event::trigger(Art_Event::CRON, $cron_data);
						Art_Event::trigger('cron_'.$name, $cron_data);

						echo('Cron '.$name.' successfully run: '.Art_Main::getExecTime('cron_'.$name,2).'ms'."\n");
					}
				}
				
				//If cron was triggered
				if ($is_cron) {
					exit();
				} else {
					http_response_code(400);
					exit('Unknown cron id');
				}
			}
		}
	}
		
	
	/**
	 *	Load timestamps from cache
	 * 
	 *	@static
	 *	@access protected
	 */
	protected static function _loadCache()
	{
		//Create cron tmp file
		if( !file_exists(static::CACHE_FOLDER) )
		{
			mkdir(static::CACHE_FOLDER, 0777, true);
		}		
		
		if( file_exists(static::CACHE_FOLDER.'/'.static::CACHE_FILE) )
		{
			$cache = file_get_contents(static::CACHE_FOLDER.'/'.static::CACHE_FILE);
		}
		else
		{
			$cache = '';
		}
		
		static::$_timestamps = (array)json_decode($cache);
	}
	
	
	/**
	 *	Save timestamp
	 * 
	 *	@static
	 *	@param string $name
	 *	@param int [optional] $value
	 *	@return void
	 */
	static function saveTimestamp( $name, $value = NULL )
	{
		if( NULL === $value )
		{
			$value = time();
		}
		
		static::$_timestamps[$name] = $value;
		
		file_put_contents(static::CACHE_FOLDER.'/'.static::CACHE_FILE, json_encode(static::$_timestamps));
	}
	
	
	/**
	 *	Get timestamp
	 * 
	 *	@static
	 *	@return int
	 */
	static function getTimestamp( $name )
	{
		if( isset(static::$_timestamps[$name]) )
		{
			return static::$_timestamps[$name];
		}
		else
		{
			return 0;
		}
	}
	
	
	/**
	 *	Diff two timestamps
	 *	Returns by default in hours (h)
	 *	Can also return in days (d), minutes(m) and seconds(s)
	 *	If second parameter NULL, diff will be compared with now
	 * 
	 *	@param int $time1
	 *	@param int [optional] $time2
	 *	@param string [optional]$unit
	 *	@return float
	 */
	static function getDiffTimestamps( $time1, $time2 = NULL, $unit = 'h' )
	{
		if( NULL === $time2 )
		{
			$time2 = time();
		}

		$interval =  $time2 - $time1;
		
		switch( $unit )
		{
			case 's':
				return $interval;
			case 'm':
				return $interval / 60;
			case 'h':
				return $interval / 3600;
			case 'd':
				return $interval / 86400;
			default: 
				return NULL;
		}
	}
	
	
	/**
	 *	Diff two timestamps from cache
	 *	Returns by default in hours (h)
	 *	Can also return in days (d), minutes(m) and seconds(s)
	 *	If second parameter NULL, diff will be compared with now
	 * 
	 *	@param string $name1
	 *	@param string [optional] $name2
	 *	@param string [optional] $unit
	 *	@return float
	 */
	static function getDiffTimestampsByName( $name1, $name2 = NULL, $unit = 'h' )
	{
		if( NULL !== $name2 )
		{
			return static::getDiffTimestamps(static::getTimestamp($name1), static::getTimestamp($name2), $unit);
		}
		else
		{
			return static::getDiffTimestamps(static::getTimestamp($name1), NULL, $unit);
		}
	}
}