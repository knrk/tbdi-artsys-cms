<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package modules/node/admin
 */
class Module_Node extends Art_Abstract_Module {
	
	const REQUEST_GETITEMLIST = 'KidamswMu9';
	
	function indexAction()
	{
		if( Art_Ajax::isRequestedBy(static::REQUEST_GETITEMLIST) )
		{
			$section = Art_Main::getPost('section');
			$action = Art_Main::getPost('action');
			
			$response = Art_Ajax::newResponse();

			if( !$section || !$action || !Art_Module::exists($section, $action) )
			{
				$response->addMessage('Někde nastala chyba', Art_Main::ERROR);
				$response->setExitStatus(Art_Main::ERROR);
				$response->execute();
			}
			
			//Get items list
			
			$module = Art_Filter::moduleClassName($section);
			$response->addVariable('list', $module::getNodeItemsList($action));
			$response->execute();
		}
		else
		{
			Art_Main::includeJavaScript('var nodeable_actions = '.static::getNodeableActionsJSON().';');
			$this->view->rights = Art_Model_Rights::fetchAllNotHigher();
			$this->view->request_getitemlist = self::REQUEST_GETITEMLIST;
		}
		
		
		
		
		//Nodeable_action
		//Route name
		//Action name
		
		//
		//
		////Route params
		//URL
		//Name
		//Sort
		//Active
		//Rights
		
	}
	
	
	
	/**
	 *	Get all nodeable actions as JSON
	 * 
	 *	@static
	 *	@return string
	 */
	static function getNodeableActionsJSON()
	{
		$nodeable_actions = Art_Register::in('nodeable_actions')->get();
		
		$data = array();
		foreach($nodeable_actions AS $section => $nodeable_section)
		{
			$data[$section] = array('name' => $section, 'label' => __('module_'.$section), 'nodes' => array());
			foreach($nodeable_section AS $nodeable_action)/* @var $nodeable_action Art_Model_Nodeable_Action */
			{
				$data[$section]['nodes'][$nodeable_action->getName()] = array('name' => $nodeable_action->getName(), 'label' => __('module_'.$section.'_action_'.$nodeable_action->getName()), 'route_name' => $nodeable_action->getRouteName(), 'route' => $nodeable_action->getRoute()->getURLMask());
			}
		}
		
		return json_encode($data);
	}
}