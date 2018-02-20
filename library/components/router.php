<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Router extends Art_Abstract_Component {
    
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
   	
	/**
	 *	@static
	 *	@access protected
	 *	@var array All params from $_GET
	 *	@exampe array('param1' => 'foo','param2' => 'bar')
	 */
	protected static $_get = array();
	
	/**
	 *	@static
	 *	@access protected
	 *	@var Art_Model_Route[] All router routes
	 */
	protected static $_routes = array();
	
	/**
	 *	@static
	 *	@access protected
	 *	@var bool True if default routes were added
	 */
	protected static $_defaultRoutesAdded = false;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array All output (ROUTED) params
	 */
	protected static $_params = array();
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array All params from MOD_REWRITE
	 */
	protected static $_rewrite_params = array();
	
	/**
	 *	@static
	 *	@access protected
	 *	@var Art_Model_Route
	 */
	protected static $_matching_route = NULL;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string Layer type (frontend|backend) 
	 */
	protected static $_layer = self::LAYER_FRONTEND;
	
	/**
	 *	Layer type constant
	 */
	const LAYER_BACKEND = 'admin';
	const LAYER_FRONTEND = 'public';
	
	/**
	 *	No access router options
	 */
	const NO_ACCESS_LAYER = self::LAYER_FRONTEND;
	const NO_ACCESS_PARAMS = array('section' => 'error', 'action' => 'noAccess');
	
	/**
	 *	Not found router options
	 */
	const NOT_FOUND_LAYER = self::LAYER_FRONTEND;
	const NOT_FOUND_PARAMS = array('section' => 'error', 'action' => 'notFound');
		
	
	/**
	 *	Return URL GET value by identifier
	 * 
	 *	@static
	 *	@param string $identifier
	 *	@return string
	 */
	static function get( $identifier = NULL )
	{
		if( NULL !== $identifier )
		{
			if( isset(self::$_params[$identifier]) )
			{
				return self::$_params[$identifier];
			}
			else
			{
				return NULL;
			}
		}
		else
		{
			return self::$_params;
		}
	}
	
	
	/**
	 *	Return URL GET from URI value by identifier
	 * 
	 *	@static
	 *	@param string $identifier
	 *	@return string
	 */
	static function getFromURI( $identifier = NULL )
	{
		if( NULL !== $identifier )
		{
			if( isset(self::$_get[$identifier]) )
			{
				return self::$_get[$identifier];
			}
			else
			{
				return NULL;
			}
		}
		else
		{
			return self::$_get;
		}
	}
	
	
	/**
	 *	@static
	 *	@return string Return URL $_GET section
	 */
	static function getSection()
	{
		return self::get('section');
	}
	
	
	/**
	 *	@static
	 *	@return string Return URL $_GET action
	 */
	static function getAction()
	{
		return self::get('action');
	}
	
	
	/**
	 *	@static
	 *	@return string Return URL $_GET id
	 */
	static function getId()
	{
		return self::get('id');
	}	
	
	
	/**
	 *	@static
	 *	@return string Layer type
	 */
	static function getLayer()
	{
		return self::$_layer;
	}
	

	/**
	 *	@static
	 *	@return array All layers
	 */
	static function getLayersList()
	{
		return array(self::LAYER_FRONTEND, self::LAYER_BACKEND);
	}
	
	
	/**
	 *	@static
	 *	@return bool True if layer is backend
	 */
	static function isBackend()
	{
		return self::$_layer === self::LAYER_BACKEND;
	}
	
	
	/**
	 *	@static
	 *	@return bool True if layer is frontend
	 */
	static function isFrontend()
	{
		return self::$_layer === self::LAYER_FRONTEND;
	}
	
	
	/**
	 *	Set params to NO ACCESS
	 * 
	 *	@param bool [optional] $change_layer
	 *	@static
	 *	@return void
	 */
	static function setNoAccess( $change_layer = false )
	{
		http_response_code(401);
		
		if( $change_layer )
		{
			self::$_layer = self::NO_ACCESS_LAYER;
		}
		
		self::$_params = array_merge(array(), self::NO_ACCESS_PARAMS);
	}
	
	
	/**
	 *	Set params to NOT FOUND
	 * 
	 *	@param bool [optional] $change_layer
	 *	@static
	 *	@return void
	 */
	static function setNotFound( $change_layer = false )
	{
		http_response_code(404);
		
		if( $change_layer )
		{		
			self::$_layer = self::NOT_FOUND_LAYER;
		}
		
		self::$_params = array_merge(array(), self::NOT_FOUND_PARAMS);
	}
	
	
	/**
	 *	Dump routes on screen
	 *
	 *	@static
	 *	@return void
	 */
	static function dumpRoute()
	{		
		$out =  '<pre>
					<table>
					<tr>
						<td colspan="3">------------- ROUTE DUMP -------------</td>
					</tr>';
		
		foreach(self::$_params AS $name => $value)
		{
			$out .=  '<tr>
						<td>'.$name.'</td>
						<td>:</td>
						<td>'.$value.'</td>
					  </tr>';
		}
		
		$out  .= '<tr>
					<td colspan="3">------------- ROUTE DUMP -------------</td>
				   </tr>
				</table>
			</pre>';
		
		echo $out;
	}
	
	
	/**
	 *	Dump route to str as JSON
	 * 
	 *	@static
	 *	@return string
	 */
	static function dumpRouteStr()
	{
		return json_encode(self::$_params);
	}
	
	
	/**
	 *	Get route by name
	 * 
	 *	@static
	 *	@param string $name
	 *	@return Art_Model_Route
	 */
	static function getRoute( $name )
	{
		if( isset(self::$_routes[$name]) )
		{
			return self::$_routes[$name];
		}
		else
		{
			return NULL;
		}
	}
	
	
    /**
     *  Initialize the component
     *  @static
     *  @return void
     */
    static function init()
    {
        if(parent::init())
        {            	
			self::loadParams();	
			
			if( !self::$_defaultRoutesAdded )
			{
				self::_addDefaultRoutes();			
			}			
        }
    }
	
	
	/**
	 *	Add default routes to router
	 * 
	 *	@access protected
	 *	@static
	 *	@return void
	 */
	static protected function _addDefaultRoutes() {
		self::addRoute('default_route_section_action_id', '/$1/$2/$3', [
			'section' => '$1', 'action' => '$2', 'id' => '$3'
		]);
		self::addRoute('default_route_section_action', '/$1/$2', [
			'section' => '$1', 'action'=>'$2'
		]);
		self::addRoute('default_route_section', '/$1', [
			'section' => '$1'
		]);
		self::addRoute('default_route', '/', [
			'section' => DEFAULT_MODULE, 
			'action' => DEFAULT_ACTION
		]);
		self::addRoute('default_route_backend_section_action_id', '/'.self::LAYER_BACKEND.'/$1/$2/$3', [
			'layer' => self::LAYER_BACKEND,
			'section' => '$1',
			'action' => '$2',
			'id' => '$3'
		]);
		self::addRoute('default_route_backend_section_action', '/'.self::LAYER_BACKEND.'/$1/$2', [
			'layer' => self::LAYER_BACKEND,
			'section' => '$1',
			'action' => '$2'
		]);
		self::addRoute('default_route_backend-section', '/'.self::LAYER_BACKEND.'/$1', [
			'layer' => self::LAYER_BACKEND,
			'section' => '$1'
		]);
		self::addRoute('default_route_backend', '/'.self::LAYER_BACKEND, [
			'layer' => self::LAYER_BACKEND,
			'section' => 'admin'
		]);
		
		self::$_defaultRoutesAdded = true;
	}

	
	/**
	 * Create URL from route
	 * 
	 *	@param string $route_name
	 *	@param array [optional] $params
	 *	@param string [optional] $locale
	 *	@return string
	 */
	static function createURL( $route_name, array $params = array(), $locale = NULL )
	{
		$route = self::getRoute($route_name);
		if( NULL !== $route )
		{
			return $route->createURL($params, $locale);
		}
		else
		{
			trigger_error('Route '.$route_name.' not found', E_USER_ERROR);
		}
	}
	
	
	/**
	 *	Match all routes in router
	 * 
	 *	@static
	 *	@param string|array $url [optional]  URL to compare with
	 *	@param string $domain [optional]  Domain to compare with
	 *	@return void
	 *	@example matchRoutes('/product/42') Compares this URL to all router rules
	 *	@example matchRoutes(array('product','42')
	 */
	static function matchRoutes($url = NULL, $domain = NULL)
	{
		//Default URL is the current url from rewrite
		if( NULL === $url )
		{
			$url = implode('/', self::$_rewrite_params);
		}
		
		//Default domain is current
		if( NULL === $domain )
		{
			$domain = Art_Server::getDomain();
		}
		
		//Get matching params and route
		$matched = static::getMatchingData($url, $domain);

		//If no matching route was found
		if( NULL === $matched )
		{
			self::setNotFound();
		}
		else
		{
			self::$_params = $matched['params'];
			self::$_matching_route = $matched['route'];

			//Put layer to its own variable
			if( isset(self::$_params['layer']) )
			{
				self::$_layer = self::$_params['layer'];
			}
			else
			{
				self::$_params['layer'] = self::$_layer;
			}
		}
	}
	
	
	/**
	 *	Get matching route and params for URL and domain
	 * 
	 *	@static
	 *	@param string|array $url URL to compare with
	 *	@param string $domain [optional]  Domain to compare with
	 *	@return array
	 */
	static function getMatchingData($url, $domain = NULL)
	{
		//For each route
		foreach(array_reverse(self::$_routes) AS $route) /* @var $route Art_Model_Route */
		{
			//If route match
			if( NULL !== ($params = $route->match($url, $domain)) )
			{
				return array( "params" => $params, "route" => $route );
			}
		}
		
		return NULL;
	}
	
	
	/**
	 *	Load $_GET to static variable and clear $_GET
	 * 
	 *	@static
	 *	@access protected
	 *	@return void
	 */
	static protected function loadParams()
	{
		self::$_get = $_GET;
		$_GET = array();
		
		//Store params from rewrite to special array
		foreach(self::$_get AS $key => $value)
		{
			//If is lang
			if( 'param1' == $key && strlen($value) == 2 )
			{
				if( in_array($value, Art_Main::getLocales()) )
				{
					Art_Main::setLocale($value);
				}
			}
			elseif( strpos($key,'param') === 0 )
			{
				self::$_rewrite_params[$key] = Art_Filter::urlParam( $value );
			}
		}
	}
	
		
	/**
	 *	Add new route to router
	 * 
	 *	@static
	 *	@param string Route rule name
	 *	@param string|array $url_mask URL mask
	 *	@param array $output_mask Output mask
	 *	@param string|int [optional] $domain
	 *	@return void
	 *	@example addRoute('/product/&1',['section'=>'product','action'=>'show','id'=>'$1'], 'itart.cz')
	 */
	static function addRoute($name, $url_mask, $output_mask, $domain = NULL)
	{
		self::$_routes[$name] = new Art_Model_Route($name, $url_mask, $output_mask, $domain);
	}

	static function getLayerAccess($access) {
		switch ($access) {
			case 'admin':
				return 50;
				break;
			case 'cabinet':
				return 2;
				break;
			case 'public':
				return 0;
				break;
			default:
				return Art_User::NO_ACCESS;
				break;
		}
	}
	
	
	/**
	 *	Restrict users from accessing specific layers
	 * 
	 *	@static
	 *	@return void
	 */
	static function doFirewall() {
		$access = false;
		
		//Get current layer rights - if not set, layer has no access
		// $rights = Art_Register::in('layer_rights')->get(self::getLayer(), Art_User::NO_ACCESS);
		$rights = self::getLayerAccess(self::getLayer());
		
		if (Art_User::hasPrivileges($rights)) {
			$access = true;
		}
		
		//Check for token validity if AJAX
		if (Art_Server::isAjax()) {
			if (Art_Session::getToken() === Art_Main::getPost(Art_Session::TOKEN_NAME, NULL)) {
				$access = true;
			} else {
				$access = false;
			}
		}
		
		//No access if active
		if (!Art_User::getCurrentUser()->isActive()) {
			$access = false;
		}

		//245
		// if (!(Art_Server::getIp() === '81.200.53.245' || Art_Server::getIp() === '81.19.13.54') && 
		// 	Art_Server::getDomain() == APP_DOMAIN && 
		// 	!(Art_Router::getSection() == 'registration' || Art_Router::getSection() == 'cabinet')) {
		// 	//self::setNotFound(true);
		// 	//$access = false;
		// }
		
		$userData = Art_User::getCurrentUser()->getData();
		
		//If pass changed before year of 2010
		if (Art_User::isRegistered() && !Art_Server::isAjax() && 
			(strtotime($userData->pass_changed_date) < 1262300400 || NULL == $userData->gender)) {
			$access = false;
		}
		
		if (!$access) {
			self::setNoAccess(true);
		}
	}
}
