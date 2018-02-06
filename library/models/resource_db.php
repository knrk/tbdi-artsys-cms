<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 *	@final
 */
final class Art_Model_Resource_Db extends Art_Abstract_Model_Db {
	
    protected static $_table = 'resource';
	
    protected static $_cols = array('id'			=>	array('select','insert'),
									'hash'			=>	array('select','insert'),
                                    'name'			=>	array('select','insert','update'),
                                    'path'			=>	array('select','insert','update'),
                                    'size'			=>	array('select','insert','update'),
                                    'rights_read'	=>	array('select','insert','update'),
                                    'rights_write'	=>	array('select','insert','update'),
                                    'added_time'	=>	array('select'),
									'modified_time'	=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
	
	/**
	 *	@var bool If true, handler is opened
	 */
	protected $_handler_opened;
	
	/**
	 *	@var resource File resource handler
	 */
	protected $_handler;
	
	/**
	 *	@var resource
	 */
	protected static $_finfo_mime_type;
	
	/**
	 *	Default chunk size for streming (in bytes)
	 */
	const DEFAULT_CHUNK_SIZE = 16384;
	
	
	/**
	 *	Create new instance of resource file
	 *	Hash or path name can be supplied as the first argument
	 *	
     *  @param string|int|array|Art_Model_Db_Select|Art_Abstract_Model_Db $where Identifier or array of identifiers with they respective column names
	 *	@param bool|User $privileged
	 *	@param bool $active_only
	 */
	function __construct($where = NULL, $privileged = NULL, $active_only = false) 
	{
		//Is hash
		if( strlen($where) == 32 )
		{
			$where = array( 'hash' => $where );
		}
		elseif( !is_numeric($where) )
		{
			if ( false !== strpos($where, '/') )
			{
				$where = array( 'path' => $where );
			}
			else
			{
				$where = array( 'name' => $where );
			}
		}
		
		parent::__construct($where, $privileged, $active_only);

		if( NULL === static::$_finfo_mime_type )
		{
			static::$_finfo_mime_type = finfo_open(FILEINFO_MIME_TYPE);
		}
	}
	
	
	/**
	 *	Returns true if handler is opened
	 * 
	 *	@return bool
	 */
	function isOpened()
	{
		return $this->_handler_opened;
	}
	
	
	/**
	 *	Open file resource handler
	 * 
	 *	@param string [optional] $mode
	 */
	function open( $mode = 'r' )
	{
		$this->_handler = fopen( $this->path , $mode);
		$this->_handler_opened = true;		
	}
	
	
	/**
	 *	Get MIME type of file
	 * 
	 *	@return string
	 */
	function getMIME()
	{
		return finfo_file( static::$_finfo_mime_type, $this->path );
	}
	
	
	/**
	 *	Get file size
	 * 
	 *	@param string $unit
	 *	@return float
	 */
	function getSize( $unit = 'b' )
	{
		switch( $unit )
		{
			case '':
			case 'b':
			{
				return filesize( $this->path );
			}
			case 'k':
			{
				return filesize( $this->path ) / 1024;
			}
			case 'm':
			{
				return filesize( $this->path ) / 1048576;
			}
			case 'g':
			{
				return filesize( $this->path ) / 1073741824;
			}
		}
	}
	
	
	/**
	 *	Read next chunk of file (as generator)
	 * 
	 *	@param int $chunk_size
	 *	@return Generator
	 */
	function getChunk( $chunk_size = self::DEFAULT_CHUNK_SIZE )
	{
		if( !$this->isOpened() )
		{
			$this->open();
		}
		
		while( !feof( $this->_handler) )
		{
			yield fread( $this->_handler, $chunk_size );
		}
	}
	
	
	/**
	 *	Rewind file pointer to beginning
	 * 
	 *	@return bool
	 */
	function rewind()
	{
		if( !$this->isOpened() )
		{
			$this->open();
		}
		
		return rewind( $this->_handler );
	}
	
	
	/**
	 *	Close file handler
	 * 
	 *	@return boolean
	 */
	function close()
	{
		if( $this->isOpened() )
		{
			$this->_handler_opened = false;
			return fclose( $this->_handler );
		}
		else
		{
			return true;
		}
	}

	
	/**
	 *	Returns true, if file is readable by user
	 *
	 *	@param Art_Model_User|int $user
	 *	@return boolean
	 */
	function isReadableByUser( $user = NULL )
	{
		if( NULL === $user )
		{
			$rights = Art_User::getRights();
		}
		elseif( $user instanceof Art_Model_User )
		{
			$rights = $user->getRights();
		}
		elseif( Art_Validator::validate($user, array( Art_Validator::IS_RIGHTS )))
		{
			$rights = $user;
		}
		else
		{
			trigger_error('Invalid argument supplied to '.get_called_class.'->isReadable()');
		}
		
		if( $this->rights_read <= $rights )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 *	Send file to client
	 * 
	 *	@param string [optional] $filename Filename to send as
	 *	@param bool [optional] $as_attachment Force downloading of file
	 *	@return boolean
	 */
	function sendToClient( $filename = NULL, $as_attachment = false )
	{
		if( !$this->isOpened() )
		{
			$this->open();
		}
		
		if( NULL === $filename )
		{
			$filename = $this->name;
		}
		
		//Rewind file
		$this->rewind();		
		
		//Send headers
		header('Content-Type: '.$this->getMIME());
		
		if( $as_attachment )
		{
			header('Content-Disposition: attachment; filename='.$filename);
		}
		
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . $this->getSize());
		
		//Clean output
		ob_clean();
		flush();
		
		//Echo file
		foreach($this->getChunk() AS $chunk)
		{
			echo $chunk;
		}
		
		return true;
	}
	
}