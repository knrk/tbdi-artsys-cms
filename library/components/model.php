<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Model extends Art_Abstract_Component {

    /**
     *  @static
     *  @access private
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
    
	/**
	 *	@static
	 *	@access protected
	 *	@var array Array of all loaded models from /models folder
	 */	
	protected static $_availableModels = [];
    
    /**
	 *	Index used in arrays when getting sort buttons
	 */
	const SORT_UP = 'up';
	
	/**
	 *	Index used in arrays when getting sort buttons
	 */
	const SORT_DOWN = 'down';
	
	/**
	 *	Path to folder with all the models
	 */
	const MODELS_PATH = 'models';
	
	
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
            self::_loadModels();
        }
    }
    
    
	/**
	 *	Load all models from /models folder
     * 
	 *	@static
	 *	@access private
	 *	@return void
	 */
	protected static function _loadModels()
	{
		//Search for models in the models folder		
		$iterator = new FilesystemIterator(self::MODELS_PATH);
		foreach ($iterator AS $file_info) 
		{
			require( $file_info->getPathname() );
		}
	}
	
	
	/**
	 *	Get sort buttons for model set
	 * 
	 *	@param array $model_set
	 *	@param string $group_by
	 *	@return array
	 */
	static function sortButtons( $model_set, $group_by )
	{
		//Get counts
		$count = array();
		foreach( $model_set AS $model )
		{
			if( !isset($count[$model->$group_by]) )
			{
				$count[$model->$group_by] = 1;
			}
			else
			{
				++$count[$model->$group_by];
			}
		}

		$curr_sort = NULL;
		
		$output = array();
		foreach( $model_set AS $model )
		{
			$primary = $model->getPrimaryName();
			$setting = array();
			
			if( $curr_sort != $model->{$group_by} )
			{
				$curr_sort = $model->{$group_by}; 
				$count_sort = 1;
				
				$setting[self::SORT_UP] = false;
			}
			else
			{
				$setting[self::SORT_UP] = true;
			}

			if( $count_sort++ != $count[$model->$group_by] )
			{
				$setting[self::SORT_DOWN] = true;
			}
			else
			{
				$setting[self::SORT_DOWN] = false;
			}
			
			$output[$model->$primary] = $setting;
		}
		
		return $output;
	}
}