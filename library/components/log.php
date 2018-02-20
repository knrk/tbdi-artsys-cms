<?php
/**
 *  @author Pastuszek Jakub <pastuszek@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Log extends Art_Abstract_Component {
    
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
		
	/**
	 *	@static
	 *	@access protected
	 *  @var FileDescriptor Descriptor of default file
	 */
	protected static $_syslogHandler;
	
	/**
	 *	Directory for logs
	 */
	const ROOT = 'logs';
	
	/**
	 *	Syslog file name
	 */
	const SYSLOG = 'syslog';
	
	/**
	 *	Maillog file name
	 */
	const MAILLOG = 'mail';
	
	/**
	 *	Loginlog file name
	 */
	const LOGINLOG = 'login';
	
	/**
	 *	Errorlog file name
	 */
	const ERRORLOG = 'error';
	
	/**
	 *	Cronlog file name
	 */
	const CRONLOG = 'cronlog';

	/**
	 *	Short data dump
	 */
	const SHORT = true;
	
	/**
	 *	Add user identification
	 */
	const USERIDENTIF = true;
	
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
			//Create root if not exists
			if( !file_exists(self::ROOT) )
			{
				mkdir( self::ROOT, 0755, true );
			}

			//Open handler
			self::$_syslogHandler = fopen(self::ROOT.'/'.self::SYSLOG.".log", "a+");

			//Rotate on utility cron
			Art_Event::on(Art_Event::CRON_UTILITY, function(){
				Art_Log::rotate();
			});

			//Log on model update/insert
			Art_Event::on(Art_Event::MODEL_INSERT_AFTER, function($event){
				$data = $event->getData();
				if ( $data instanceof Art_Abstract_Model_DB  )
				{
					Art_Log::log($data->dump(Art_Log::SHORT), Art_Log::SYSLOG, get_class($data).': insert');
				}
			});
			Art_Event::on(Art_Event::MODEL_UPDATE_AFTER, function($event){
				$data = $event->getData();
				if ( $data instanceof Art_Abstract_Model_DB  )
				{
					Art_Log::log($data->dump(Art_Log::SHORT), Art_Log::SYSLOG, get_class($data).': update');
				}
			});			

			//Log on model delete
			Art_Event::on(Art_Event::MODEL_DELETE, function($event){
				$data = $event->getData();
				if ( $data instanceof Art_Abstract_Model_DB  )
				{
					Art_Log::log($data->dump(), Art_Log::SYSLOG, get_class($data).': delete', Art_Log::USERIDENTIF);
				}
			});
			
			//Log CRON
			Art_Event::on(Art_Event::CRON, function($event){
				$data = $event->getData();
				if ( is_array($data)  )
				{
					$name = ri($data['name'], 'unknown');
					$guid = ri($data['guid'], 'unknown');
					
					Art_Log::log(date('j.n.y H:i:s') . ' - Executing cron '.$name.' from '.Art_Server::getIp().' with guid: '.$guid, Art_Log::CRONLOG, 'cron: '.$name);
				}
			});
			
			//Log on log in
			Art_Event::on(Art_Event::USER_LOG_IN, function($event){
				$data = $event->getData();

				Art_Log::log($data, Art_Log::LOGINLOG);
			});
			
			self::$_initialized = true;
		}
    }
	
	
	/**
	 *	Log into file
	 * 
	 *  @param string $message Data for logging
	 *  @param string $file Name of file (default: 'syslog')
	 *  @param string $action Name of action (default: '')
	 *  @param boolean $userIdentification Log user identification (default: false) 
	 *  @param boolean $force Force logging (default: false) 
	 *	@static
	 *	@return void
	 */
	static function log($message, $file = self::SYSLOG, $action = '', $userIdentification = false)
	{
		if ( empty($file) )
		{
			$file = static::SYSLOG;
		}
				
		if( !self::isInitialized() )
		{
			self::init();
		}
		
		if ( $file != self::SYSLOG )
		{			
			$file = trim($file, '/');

			//name of file contain directory/ies - have to create it/them
			if ( strpos( $file, '/') !== false )
			{
				$dir = substr($file, 0, strrpos($file, '/'));

				mkdir(self::ROOT.'/'.$dir, 0755, true);
			}

			$file = self::ROOT.'/'.$file.".log";

			$handler = fopen($file, "a+");
		}
		else
		{
			$handler = self::$_syslogHandler;
		}

		
		//Prepare message
		$data = $message.' ';

		if ( !empty($action) )
		{
			$action = '['.$action.'] ';
		}

		$user = '';

		//If user was initialized and is allowed user logging
		if( class_exists('Art_User') && Art_User::isInitialized() && $userIdentification !== false )
		{
			$user = '['.$_SERVER['REMOTE_ADDR'].' - '.Art_User::getId().' - '.Art_User::getRights().'] ';
		}

		//String to be written to file
		if ( $file === self::SYSLOG )
		{
			$content = date('Y-m-d H:i:s').' '.$action.$data.$user.Art_Router::dumpRouteStr()."\n";
		}
		else 	
		{
			$content = $data."\n"."---LOG DEBUG: ".Art_Router::dumpRouteStr()."\n";
		}

		//Save to file
		fwrite($handler,$content);
	}

	
	/**
	 *	Log rotation
	 * 
	 *	@static
	 *	@return void
	 */
	static function rotate() {		
		//Max file size in bytes
		$threshold = 1000 * LOG_SIZE;	
		$directories = array(self::ROOT);
		
		for ($i = 0; $i < count($directories); $i++) {
			$dir = $directories[$i].'/';
			
			foreach (new DirectoryIterator($dir) as $file) {
				if ($file->isDot()) {
					continue;
				}
				else if (!$file->isFile()) {
					$directories[] = $dir.$file->getFilename();
				}

				$name = $file->getFilename();		
				
				//find only files which are *.log
				if ('log' == $file->getExtension()) {
					if (filesize($dir.$file) >= $threshold) {
						$num_map = array();
						$num_map[-1] = $name;	//save *.log file

						//again iterate through whole directory to find similar files
						foreach (new DirectoryIterator($dir) as $log) {
							if ($log->isDot() || !$log->isFile()) {
								continue;
							}

							//Prepare array
							$matches = array();
							
							//find any file *-[0-9].gz that has same name *.log file
							if (preg_match('/^'.$name.'-?([0-9]){1}\.gz$/', $log->getFilename(), $matches)) {
								$num = $matches[1];	//save number of file
								$file2move = $log->getFilename();
								$num_map[$num] = $file2move;
							}
						}

						krsort($num_map);	//sort by key desc

						//shift from back 8=>9; 7=>8 etc. and *.log=>*-0.gz
						foreach ($num_map as $num => $file2move) {
							$targetN = $num+1;

							//*.log file to .gz
							if ($targetN === 0) {
								$gz = gzopen($dir.$name.'-0.gz','w9');
								$str = file_get_contents($dir.$file);
								fclose(fopen($dir.$file, "w"));	//empty *.log file
								gzwrite($gz, $str);
								gzclose($gz);
							}
							//delete last file (*-9.gz)
							else if ( $targetN === 10 ) {
								unlink($dir.$file2move);
							}
							//other shift
							else {
								rename($dir.$file2move, $dir.$name.'-'.$targetN.'.gz');
							}
						}
					}
				}
			}
		}			
	}
}