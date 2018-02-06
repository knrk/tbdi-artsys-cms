<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Uploaded_Fileset
{
	/**
	 *	Files in fileset
	 * 
	 *	@var array
	 */
	protected $_files = array();
	
	/**
	 *	Number of files
	 * 
	 *	@var int 
	 */
	protected $_count = 0;
	
	/**
	 *	Number of files with errors
	 * 
	 *	@var array
	 */
	protected $_error_files = array();
	
	
	/**
	 *	@param array $files_info Info from $_FILES[] array
	 */
	function __construct( $files_info )
	{
		if( is_string($files_info) )
		{
			$files_info = Art_Main::getPostFile($files_info);
		}
		
		if( isset($files_info['name']) )
		{
			$max = count($files_info['name']);
			for( $i = 0; $i < $max; $i++ )
			{
				$temp_file = array();
				foreach( $files_info AS $key => $values )
				{
					$temp_file[$key] = $values[$i];
				}
				
				if( $temp_file['error'] == 0 )
				{
					$this->_files[] = Art_Model_File::fromPost($temp_file);
					$this->_count++;
				}
				else
				{
					$this->_error_files[] = $temp_file;
				}
			}
		}
	}

	
	/**
	 *	Get all files from fileset
	 * 
	 *	@return array
	 */
	function getAll()
	{
		return $this->_files;
	}
	
	
	/**
	 *	Move all files to new directory
	 * 
	 *	@param string $dir_name
	 */
	function moveTo( $dir_name )
	{
		foreach( $this->_files AS $file )
		{
			$file->moveTo($dir_name);
		}
	}
}