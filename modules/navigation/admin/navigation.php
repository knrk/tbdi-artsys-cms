<?php

class Module_Navigation extends Art_Abstract_Module {
	
	function embeddAction()
	{
		//Get module bootstraps and their administration setup
		$nodes = Art_Model_Node::fetchAllPrivileged(NULL,'sort');
		
		$url = strtolower('/'.implode('/', Art_Router::getFromURI()));
		
		$active_nodes_ids = array();

		$best_match = NULL;
		foreach( $nodes AS $node )
		{
			if( strtolower($node->url) === $url )
			{
				$best_match = $node;
				$nodef = $node;
				$active_nodes_ids[] = $nodef->id;
				$i = 0;
				while( $nodef = $nodef->getParent() )
				{
					if( !$nodef->isLoaded() )
					{
						break 2;
					}
					
					//Safety guard
					if( $i++ > 10 )
					{
						break 2;
					}
					$active_nodes_ids[] = $nodef->id;
				}
			}
		}
		
		if( NULL === $best_match )
		{
			foreach ( $nodes as &$node ) /* @var $node Art_Model_Node */
			{
				if( !empty($node->url) && strpos($url, strtolower($node->url)) === 0 )
				{
					$nodef = $node;
					$active_nodes_ids[] = $nodef->id;
					$i = 0;
					while( $nodef = $nodef->getParent() )
					{
						if( !$nodef->isLoaded() )
						{
							break 2;
						}

						//Safety guard
						if( $i++ > 10 )
						{
							break 2;
						}
						$active_nodes_ids[] = $nodef->id;
					}
					break;
				}
			}
		}
				
		$nodes = Art_Filter::parentStructure($nodes, true, 'childs');
		
		$this->view->active_nodes_ids = $active_nodes_ids;
		$this->view->nodes = $nodes;
	}
}
