<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/uploader/admin
 */
class Module_Uploader extends Art_Abstract_Module {
	
	const CKEDITOR_ID = 'STFGvfqd3FEeJBiXzNZEu8SBIpQRsdUpJTYMbM39';
	
	function getCKEditorId()
	{
		return self::CKEDITOR_ID;
	}
	
	function ckeditorAction()
	{
		$id = Art_Router::getFromURI('id');
		if( $id == $this->getCKEditorId() )
		{
			$files = Art_Main::getPostFiles();
			$file = new Art_Uploaded_File($files['upload']);
			if( $file->isImage() )
			{
				$file->moveTo('files/article');
				$name = $file->randomizeName();
			}
			
			exit('{"uploaded": 1,"fileName": "'.$name.'","url": "/files/article/'.$name.'"}');
		}
		
		
		$this->setView();
	}
}