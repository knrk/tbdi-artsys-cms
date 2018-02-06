<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/resource/admin
 */
class Module_Resource extends Art_Abstract_Module {
	
	function indexAction()
	{
		$id = Art_Router::getId();
		$file = new Art_Model_Resource_Db($id);
		
		if( NULL !== Art_Router::getFromURI('d') )
		{
			$to_download = true;
		}
		else
		{
			$to_download = false;
		}
		
		if( $file->isLoaded() )
		{
			if( $file->isReadableByUser() )
			{
				$file->sendToClient( NULL, $to_download );
				exit;
			}
			else
			{
				$this->allowTo(Art_User::NO_ACCESS);
			}
		}
		else
		{
			$this->showTo(Art_User::NO_ACCESS);
		}
	}
}