<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_File {
	
	/**
	 *	Full file path (foo/foo/bar.baz)
	 * 
	 *	@var string
	 */
	protected $_path;
	
	/**
	 *	File dir name (foo/foo)
	 * 
	 *	@var string
	 */
	protected $_dirname;
	
	/**
	 *	File base name (bar.baz)
	 * 
	 *	@var type
	 */
	protected $_basename;
	
	/**
	 *	File name without extension (bar)
	 * 
	 *	@var type 
	 */
	protected $_filename;
	
	/**
	 *	File extension (baz)
	 * 
	 *	@var string
	 */
	protected $_extension;
	
	/**
	 *	@var string File type
	 */
	protected $_type;
	
	/**
	 *	@var bool True if file not exists or was uploading error
	 */
	protected $_error = false;
	
	/**
	 *	@var float File size in MB
	 */
	protected $_size;
		
	/**
	 *	@var bool If true, file is still in temporary folder (used for uploaded files)
	 */
	protected $_uploaded_in_temp = false;
		
	/**
	 *	Extensions used for declaring if file is image
	 */
	const IMAGE_EXTENSIONS = array('jpg','jpeg','png','bmp','png','gif','svg');
	
	/**
	 *	Extensions used for declaring if file is document
	 */	
	const DOCUMENT_EXTENSIONS = array('pdf','doc','docx','txt','odt','rtf','xls','xlsx');
	
	
	/**
	 *	Create new file by loading from path
	 * 
	 *	@param string $path
	 */
	function __construct( $path = NULL )
	{
		if( NULL === $path )
		{
			$this->_updateInfo($path);
		}
		else
		{
			$this->_error = true;
		}
	}
	
	
	/**
	 *	Create new instsance from HTTP file upload
	 * 
	 *	@param string|array $file_info (Can be POST name or file info array)
	 *	@return this
	 */
	static function fromPost( $file_info )
	{
		if( is_string($file_info) )
		{
			$file_info = Art_Main::getPostFile( $file_info );
		}

		$instance = new static;
		
		if( NULL != $file_info && !empty($file_info) )
		{
			$pathinfo = pathinfo($file_info['name']);
			
			$instance->_error = $file_info['error'];
			$instance->_updateInfo($file_info['tmp_name']);
			$instance->_basename = $pathinfo['basename'];
			$instance->_extension = $pathinfo['extension'];
			$instance->_filename = $pathinfo['filename'];
		}
		else
		{
			$instance->_error = true;
		}
		
		if( empty($instance->_path) )
		{
			$instance->_error = true;
		}
		
		return $instance;
	}
	
	
	/**
	 *	Get file base name (bar.baz)
	 * 
	 *	@return string
	 */
	function getBasename()
	{
		return $this->_basename;
	}
	
	
	/**
	 *	Get file name without extension (bar)
	 * 
	 *	@return string
	 */
	function getFilename()
	{
		return $this->_filename;
	}
	
	
	/**
	 *	Get file dir name (foo/foo)
	 * 
	 *	@return string
	 */
	function getDirname()
	{
		return $this->_dirname;
	}
	
	
	/**
	 *	@return string
	 */
	function getType()
	{
		return $this->_type;
	}
	
	
	/**
	 *	@return bool
	 */
	function isError()
	{
		return $this->_error;
	}
	
	
	/**
	 *	@return bool True if file eixsts
	 */
	function isExisting()
	{
		return $this->isError();
	}
	
	
	/**
	 *	Returns size in MB
	 * 
	 *	@return float
	 */
	function getSize()
	{
		return $this->_size;
	}
	
	
	/**
	 *	@return string
	 */
	function getExtension()
	{
		return $this->_extension;
	}
	
	
	/**
	 *	Returns timestamp of last access time
	 * 
	 *	@return int
	 */
	function getAccessTime()
	{
		if( !$this->isError() )
		{
			return fileatime($this->_path);
		}
		else
		{
			return -1;
		}
	}
	
	
	/**
	 *	Returns timestamp of last modify time
	 * 
	 *	@return int
	 */
	function getModifyTime()
	{
		if( !$this->isError() )
		{
			return filemtime($this->_path);
		}
		else
		{
			return -1;
		}
	}
	
	
	/**
	 *	Returns timestamp of last change time
	 * 
	 *	@return int
	 */
	function getChangeTime()
	{
		if( !$this->isError() )
		{
			return filectime($this->_path);
		}
		else
		{
			return -1;
		}
	}
	
	
	/**
	 *	Returns contents of file
	 * 
	 *	@return string
	 */
	function getContents()
	{
		if( !$this->isError() )
		{
			return file_get_contents($this->_path);
		}
		else
		{
			return NULL;
		}
	}
	
	
	/**
	 *	Returns true if this file is readable
	 * 
	 *	@return bool
	 */
	function isReadable()
	{
		if( !$this->isError() )
		{
			return is_readable($this->_path);
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 *	Returns true if this file is writeable
	 * 
	 *	@return bool
	 */
	function isWriteable()
	{
		if( !$this->isError() )
		{
			return is_writeable($this->_path);
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * 
	 * @return bool
	 */
	function isImage()
	{
		return in_array($this->getExtension(), self::IMAGE_EXTENSIONS);
	}

	
	/**
	 * 
	 * @return bool
	 */
	function isDocument()
	{
		return in_array($this->getExtension(), self::DOCUMENT_EXTENSIONS);
	}
	
	
	/**
	 *	Move file to another location
	 * 
	 *	@param string $dir_name
	 *	@param bool $safe_mode When true, an numeric postfix will be added, if file name is already used (eg. foo_2.bar)
	 *	@return this
	 */
	function moveTo( $dir_name, $safe_mode = true )
	{
		if( $this->isError() )
		{
			return $this;
		}
				
		//Create directory if not exists
		if( !is_dir($dir_name) )
		{
			mkdir($dir_name, 0777, true);
		}
		
		$new_path = $dir_name.'/'.$this->_basename;
		
		//If safe mode and target file already exists
		if( $safe_mode && file_exists($new_path) )
		{
			$file_name = $this->_getNextFilename($new_path);
			$new_path = $dir_name.'/'.$file_name;
		}
		
		//If is uploaded file still in temp
		if( $this->_uploaded_in_temp )
		{
			move_uploaded_file( $this->_path, $new_path );
			$this->_uploaded_in_temp = false;
		}
		else
		{
			rename( $this->_path, $new_path );
		}
		
		//Update this instance with new path
		$this->_updateInfo($new_path);
		
		return $this;
	}
	
	
	/**
	 *	Get next filename of this instance for target directory
	 *	(Returns foobar_2.baz, foobar_3.baz ... )
	 * 
	 *	@param string $new_path
	 *	@return string
	 */
	protected function _getNextFilename( $new_path )
	{
		if( !file_exists($new_path) )
		{
			return basename($new_path);
		}
		elseif( !$this->isError() )
		{
			$new_info = pathinfo($new_path);
			
			$b_num = 2;
			$b_filename = $new_info['filename'];
			
			//Find numeric postfix
			if( ( $pos = strrpos($new_info['filename'], '_') ) !== false && $pos < 5 )
			{
				$sub_num = substr($new_info['filename'], -$pos);
				if( $sub_num === (int)$sub_num )
				{
					$b_num = $sub_num;
					$b_filename = substr($new_info['filename'], 0, -$pos + 1);
				}
			}
				
			for( $i = $b_num; $i < 10000; $i++ )
			{
				if( !file_exists($new_info['dirname'].'/'.$b_filename.'_'.$i.'.'.$new_info['extension']) )
				{
					return $b_filename.'_'.$i.'.'.$new_info['extension'];
				}
			}
		}
		
		return basename($new_path);
	}
	
	
	/**
	 *	Update info of this instance by target path
	 * 
	 *	@param string $path
	 *	@param bool $meta_info [optional] Update meta info too (size & type)
	 *	@return this
	 */
	protected function _updateInfo( $path = NULL, $meta_info = true )
	{
		if( NULL === $path )
		{
			$path = $this->_path;
		}
		
		if( is_readable($path) && is_file($path) )
		{
			$info = pathinfo($path);
			$this->_path = $path;
			$this->_dirname = $info['dirname'];
			$this->_basename = $info['basename'];
			$this->_filename = $info['filename'];
			if( isset($info['extension']) )
			{
				$this->_extension = $info['extension'];
			}
			
			if( $meta_info )
			{
				$this->_size = filesize($this->_path);
				$this->_type = mime_content_type($this->_path);
			}
		}
		else
		{
			$this->_error = true;
		}
		
		return $this;
	}
	
	
	/**
	 *	Set and return random name - file extension is unchanged
	 * 
	 *	@param int [optional] $length
	 *	@return string
	 */
	function randomizeName( $length = 32 )
	{
		if( $this->isError() )
		{
			return $this;
		}		
		
		$name = rand_str( $length ).'.'.$this->getExtension();
		$this->rename($name);
		return $name;
	}
	
	
	/**
	 *	Rename a file
	 * 
	 *	@param string $name
	 *	@param bool $safe_mode [optional] When true, an numeric postfix will be added, if file name is already used (eg. foo_2.bar)
	 *	@return this
	 */
	function rename( $name, $safe_mode = false )
	{
		if( $this->isError() )
		{
			return $this;
		}		
		
		$new_path = $this->_dirname.'/'.$name;
		
		//If safe mode and target file already exists
		if( $safe_mode && file_exists($new_path) )
		{
			$file_name = $this->_getNextFilename($new_path);
			$new_path = $this->_dirname.'/'.$file_name;
		}
		
		rename( $this->_path, $new_path );
		
		$this->_updateInfo($new_path);
		
		return $this;
	}
	
	
	/**
	 *	Rename file without changing its extension
	 * 
	 *	@param string $name
	 *	@param bool $safe_mode When true, an numeric postfix will be added, if file name is already used (eg. foo_2.bar)
	 *	@return this
	 */
	function renameWithSameExtension( $name, $safe_mode = false )
	{
		$ext = $this->getExtension();
		if( !empty($ext) )
		{
			return $this->rename($name.'.'.$ext, $safe_mode);
		}
		else
		{
			return $this->rename($name, $safe_mode);
		}
	}
	
	
	/**
	 *	Deletes a file. Seriously.
	 * 
	 *	@return this
	 */
	function delete()
	{
		unlink($this->_path);
		$this->_updateInfo();
		
		return $this;
	}
}