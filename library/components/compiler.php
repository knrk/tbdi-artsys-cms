<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Compiler extends Art_Abstract_Component {
	
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
	
	/**
	 *	Contains true/false info of caching by script type
	 * 
	 *	@static
	 *	@access protected
	 *	@var array
	 */
	protected static $_caching = array( 'css' => true, 'js' => true );
	
	/**
	 *	Register namespace name prefix
	 */
	const REGISTER_PREFIX = 'compiler';
	
	/**
	 *	Folder where compiled files are stored
	 */
	const COMPILE_ROOT_FOLDER = 'tmp/compiler';
	
	
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
			//Create compile root folder
			if( !file_exists(self::COMPILE_ROOT_FOLDER) )
			{
				mkdir( self::COMPILE_ROOT_FOLDER, 0777, true );
			}
		}
	}		
	
	
	/**	
	 *	Returns true if compiler is caching its output for script type
	 * 
	 *	@static
	 *	@param string $script_type css/js
	 *	@return bool
	 */
	static function isCaching( $script_type )
	{		
		if( isset(static::$_caching[$script_type]) )
		{
			return static::$_caching[$script_type];
		}
		else
		{
			return NULL;
		}
	}
	
	
	/**
	 *	Disable compiler cache for script type
	 * 
	 *	@static
	 *	@param string [optional] $script_type css/js
	 *	@return void
	 */
	static function disableCache( $script_type = NULL )
	{
		if( NULL === $script_type )
		{
			foreach( static::$_caching AS &$item )
			{
				$item = false;
			}
		}
		else
		{
			static::$_caching[$script_type] = false;
		}
	}
	
	
	/**
	 *	Enable compiler cache for script type
	 * 
	 *	@static
	 *	@param string [optional] $script_type css/js
	 *	@return void
	 */
	static function enableCache( $script_type = NULL )
	{
		if( NULL === $script_type )
		{
			foreach( static::$_caching AS &$item )
			{
				$item = true;
			}
		}
		else
		{
			static::$_caching[$script_type] = true;
		}
	}
	
	
	/**
	 *	Get register namespace root name
	 * 
	 *	@param string $script_type
	 *	@return string
	 */
	static function getRegisterRootName( $script_type )
	{
		return self::REGISTER_PREFIX.'_'.$script_type.'_'.Art_Router::getLayer().'_'.Art_Template::getTemplateFolder().'_'.Art_Template::getTemplateName();
	}
	
	
	/**
	 *  Returns true if cache for script type is old
	 *	Based on current layer and template
	 * 
	 *	@param string $script_type
	 *	@param array $files
	 *	@param array $scripts
	 *	@return boolean
	 */
	static function isCacheOld( $script_type, $files, $scripts )
	{		
		//Check if cached file name exists
		$file_path = Art_Register::in( self::getRegisterRootName($script_type) )->get('file_path', false);
		if( !$file_path || !is_file($file_path) )
		{
			return true;
		}		

		//Get cached files info
		$cached_files = Art_Register::in( self::getRegisterRootName($script_type).'_files' )->get();

		//Compare files count
		if( count($cached_files) !== count($files) )
		{
			return true;
		}

		//Compare modify time
		foreach( $files AS $file )
		{
			if( !isset($cached_files[$file]) ||  filemtime($file) > $cached_files[$file] )
			{
				return true;
			}
		}
		
		//Compare cached scripts
		$cached_scripts = Art_Register::in( self::getRegisterRootName($script_type).'_scripts' )->get();
		foreach( $scripts AS $key => $value )
		{
			if( !isset($cached_scripts[$key]) || strlen($value) != $cached_scripts[$key] )
			{
				return true;
			}
		}
		
		return false;
	}

	
	/**
	 *	Save compiled script and returns file path
	 * 
	 *	@param string $script_type
	 *	@param string $script
	 *	@return string File path
	 */
	static function saveCompiled( $script_type, $script )
	{			
		$folder = static::COMPILE_ROOT_FOLDER.'/'.$script_type;
		$file_path = $folder.'/'.rand_str(16).'.'.$script_type;
		
		if( !file_exists( $folder ) )
		{
			mkdir($folder, 0777, true);
		}
		
		$file = fopen( $file_path, 'w' );
		fwrite( $file, $script );
		fclose( $file );
		
		return $file_path;
	}
	
	
	/**
	 *	Save cache info to registers
	 * 
	 *	@static
	 *	@param string $script_type css/js
	 *	@param string $file_path
	 *	@param array $files
	 *	@param array $scripts
	 *	@return void
	 */
	static function saveCacheInfo( $script_type, $file_path, $files, $scripts )
	{
		//Save files info
		$files_register = Art_Register::in( self::getRegisterRootName($script_type).'_files' );
		$files_register->purge();

		foreach( $files AS $file )
		{
			$files_register->set($file, filemtime($file), true);
		}

		//Save scripts info
		$scripts_register = Art_Register::in( self::getRegisterRootName($script_type).'_scripts' );
		$scripts_register->purge();
		if( NULL !== $scripts )
		{
			foreach( $scripts AS $key => $value )
			{
				$scripts_register->set($key, strlen($value), true);
			}
		}
		
		//Save file name and time
		$register = Art_Register::in(self::getRegisterRootName($script_type));
		$old_file = $register->get('file_path','');
		$register->purge()
						->set('file_path', $file_path, true)
						->set('cached_time', dateSQL(), true);
		
		//Remove old file
		if( $old_file != '.' && is_readable($old_file) )
		{
			unlink($old_file);
		}
	}
	
	
	/**
	 *	Clean cached files and registers
	 * 
	 *	@static
	 *	@param string $script_type
	 *	@return void
	 */
	static function purgeCache( $script_type )
	{
		Art_Register::in( self::getRegisterRootName($script_type).'_files' )->purge();
		Art_Register::in( self::getRegisterRootName($script_type).'_scripts' )->purge();
		Art_Register::in( self::getRegisterRootName($script_type) )->purge();
		
		//Delete cached files
		$iterator = new FilesystemIterator(self::COMPILE_ROOT_FOLDER.'/'.$script_type);
		foreach ($iterator AS $file_info) 
		{
			if( $file_info->isFile() )
			{
				unlink( $file_info );
			}
		}
	}
	
	
	/**
	 *	Get cached file name
	 * 
	 *	@static
	 *	@param string $script_type
	 *	@return string
	 */
	static function getCachedFileName( $script_type )
	{
		return Art_Register::in( self::getRegisterRootName($script_type) )->get('file_path', '');
	}
}