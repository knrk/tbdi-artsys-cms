<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Uploader {
	
	/**
	 *	GUID of AJAX request
	 * 
	 *	@var string 
	 */
	protected $_request_guid;
	
	/**
	 *	Uploaded files
	 * 
	 *	@var array 
	 */
	protected $_files = array();
	
	/**
	 *	Temp folder - based on GUID and root
	 * 
	 *	@var string 
	 */
	protected $_temp_folder;
	
	/**
	 *	Root for temp folders
	 */
	const TEMP_ROOT = './tmp';
	
	
	function __construct() 
	{
		if( !Art_Server::isAjax() )
		{
			trigger_error('Cannot instantiate uploader when not in AJAX mode');
		}
		elseif( !Art_Ajax::getRequestGUID() )
		{
			trigger_error('Form GUID wasnt found');
		}
		else
		{
			$this->_request_guid = Art_Ajax::getRequestGUID();
			$this->_temp_folder = static::TEMP_ROOT.'/'.$this->_request_guid;
			$this->_loadFilesFromPost();
		}
	}
	
	
	/**
	 *	Load files from $_FILES array
	 * 
	 *	@return this
	 */
	protected function _loadFilesFromPost()
	{
		$data = Art_Ajax::getData();

		foreach( $data AS $item )
		{
			if( is_array($item) && isset($item['name']) && isset($item['error']) )
			{
				$this->_files[] = new Art_Uploaded_File($item);
			}
		}
		
		return $this;
	}
	
	
	/**
	 *	Move files to temp
	 * 
	 *	@return this
	 */
	function moveToTemp()
	{
		if( !is_dir($this->_temp_folder) )
		{
			mkdir($this->_temp_folder,0777,true);
		}
		
		foreach($this->_files AS $file/* @var $file Art_Uploaded_file */)
		{
			$file->moveTo($this->_temp_folder);
		}
		
		return $this;
	}
	
	
	/**
	 *	Remove temporary folder
	 * 
	 *	@return this
	 */
	function removeTemp()
	{
		rmdirr($this->_temp_folder);
		
		return $this;
	}
	
	
	/**
	 *	Get all uploaded files
	 * 
	 *	@return array
	 */
	function getFiles()
	{
		return $this->_files;
	}
	
	
	/**
	 *	Get temporary folder name
	 * 
	 *	@return string
	 */
	function getTempFolder()
	{
		return $this->_temp_folder;
	}
	
	
	/**
	 *	@return int
	 */
	function getFilesCount()
	{
		return count($this->_files);
	}
	
	
	/**
	 *	Move all files to directory
	 * 
	 *	@param string $dir_name
	 *	@return this
	 */
	function moveTo( $dir_name )
	{
		foreach( $this->_files AS $file )
		{
			$file->moveTo($dir_name);
		}
		
		return $this;
	}
}