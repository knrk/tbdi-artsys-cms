<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *	@property string	$layer		Layer which this meta belongs to (public/admin)
 *	@property string	$key		Key or name this meta is using (title, keywords, description...)
 *	@property string	$content_cs	Value of meta (in czech locale)
 */
class Art_Model_Meta extends Art_Abstract_Model_DB {
	    
    protected static $_table = 'meta';
	
    protected static $_cols = array('id'		=>  array('select','insert'),
									'layer'		=>	array('select','insert'),
									'key'		=>	array('select','insert'),
                                    'content_cs'=>  array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

	protected static $_current_locale;
	
	
	/**
	 *	Get content of this META tag
	 * 
	 *	@param string [optional] $locale
	 *	@return string
	 */
	function getContent( $locale = NULL)
	{
		//If locale is set
		if( NULL !== $locale )
		{
			return $this->{'content_'.$locale};
		}
		else
		{
			//If locale wasn't loaded
			if( NULL === static::$_current_locale )
			{
				static::$_current_locale = Art_Main::getDefaultLocale();
			}
			
			return $this->{'content_'.static::$_current_locale};
		}
	}
	
	
	/**
	 *	Set content of this META
	 * 
	 *	@param string $content
	 *	@return \Art_Model_Meta
	 */
	function setContent( $content )
	{
		$this->{'content_'.static::$_current_locale} = $content;
		
		return $this;
	}
	
	
	/**
	 *	Fetch all META tags for current locale and layer
	 * 
	 *	@return array
	 *	@see Art_Model_Meta::fetchAll()
	 */
	static function fetchAllCurrLayer()
	{
		$where_stmt = new Art_Model_Db_Where(array('name'=>'layer','value'=>Art_Router::getLayer()));
		
		return static::fetchAll($where_stmt);
	}
	
	
	/**
	 *	Get default META as associative array
	 * 
	 *	@static
	 *	@return array
	 */
	static function getDefaults()
	{
		return	array(
					// 'title'				=> Art_Register::in('meta')->get('title'),
					// 'description'		=> Art_Register::in('meta')->get('description'),
					// 'keywords'			=> Art_Register::in('meta')->get('keywords'),
					// 'copyright'			=> Art_Register::in('meta')->get('copyright'),
					// 'application-name'	=> Art_Register::in('meta')->get('application_name'),
					// 'robots'			=> Art_Register::in('meta')->get('robots'),
					// 'author'			=> Art_Register::in('meta')->get('author'),
					// 'og:type'			=> Art_Register::in('meta')->get('og_type'),
					// 'og:image'			=> Art_Server::getHost().'/'.Art_Register::in('meta')->get('og_image'),
					// 'og:site_name'		=> Art_Register::in('meta')->get('og_site_name'),
					// 'og:url'			=> Art_Server::getHost()
					);		
	}
}