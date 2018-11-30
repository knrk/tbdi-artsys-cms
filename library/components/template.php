<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Template extends Art_Abstract_Component {
	
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string Template name
	 */
    protected static $_templateName;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string Template folder
	 */
	protected static $_templateFolder;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array META container 
	 */
	protected static $_meta = array();
	
	/**
	 *	@static
	 *	@access protected
	 *	@var Module Content module instance
	 */
	protected static $_contentModule;
	
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string Alert HTML, which is shown in content
	 */
	protected static $_alert = '';
    
	/**
	 *	@static
	 *	@access protected
	 *	@var string Title added to base loaded from DB 
	 */
	protected static $_meta_title_added = '';
	
	/**
	 *	Paired META tags - if $value not found, $key will be used
	 * 
	 *	@static
	 *	@access protected
	 *	@var array 
	 */
	protected static $_meta_pair = array(	self::META_TITLE => self::META_OG_TITLE,
											self::META_DESCRIPTION => self::META_OG_DESCRIPTION);
    
	/**
	 *	Array of currently loaded extensions
	 * 
	 *	@static
	 *	@access protected
	 *	@var array
	 */
	protected static $_loaded_extensions = array();
	
	/**
	 *	Template name postfix
	 */
	const TEMPLATE_NAME_POSTFIX = 'Template';
	
	/**
	 *	Separator used between base title and added title
	 */
	const TITLE_SEPARATOR = ' - ';
	
	const META_TITLE = 'title';
	const META_DESCRIPTION = 'description';
	const META_KEYWORDS = 'keywords';
	const META_APPLICATION_NAME = 'application-name';
	const META_AUTHOR = 'author';
	const META_COPYRIGHT = 'copyright';
	const META_ROBOTS =	'robots';
	const META_GOOGLE_SITE_VERIFICATION = 'google-site-verification';
	
	const META_OG_TITLE = 'og:title';
	const META_OG_TYPE = 'og:type';
	const META_OG_URL = 'og:url';
	const META_OG_IMAGE = 'og:image';
	const META_OG_SITE_NAME = 'og:site_name';
	const META_OG_DESCRIPTION = 'og:description';
	const META_FB_ADMINS = 'fb:admins';
	const META_FB_APP_ID = 'fb:app_id';
	const META_FB_PAGE_ID = 'fb:page_id';
	
	
    /**
	 *	@static
	 *	@return string Template name
	 */
	static function getTemplateName()
	{
		return self::$_templateName;
	}
	
	
    /**
	 *	@static
	 *	@return string Template folder
	 */
	static function getTemplateFolder()
	{
		return self::$_templateFolder;
	}
	
	
	/**
	 *	@static
	 *	@return string Template folder relative to script root
	 */
	static function getFolder()
	{
		return 'templates/'.self::getTemplateFolder();
	}
	
	
	/**
	 *	Get META tag or return default value
	 * 
	 *	@static
	 *	@param string $name
	 *	@param string $default_value
	 *	@return void
	 */
	static function getMeta( $name = NULL, $default_value = NULL)
	{
		if( NULL === $name )
		{
			return self::$_meta;
		}
		elseif( isset(self::$_meta[$name]) )
		{
			if( $name === self::META_TITLE )
			{
				if( !empty(self::$_meta_title_added) )
				{
					return self::$_meta_title_added.self::TITLE_SEPARATOR.self::$_meta[self::META_TITLE];
				}
				else
				{
					return self::$_meta[self::META_TITLE];
				}
			}
			else
			{
				return self::$_meta[$name];
			}
		}
		//Search in paired META
		elseif( $paired_meta = array_search($name, self::$_meta_pair) )
		{
			//Return paired META value
			return self::getMeta($paired_meta, $default_value);
		}
		//If default value is set
		elseif( NULL !== $default_value )
		{
			return $default_value;
		}
		else
		{
			return '';
		}
	}
	
	
	/**
	 *	Set META tags
	 * 
	 *	@static
	 *	@param string|array $name
	 *	@param string $value
	 *	@return void
	 */
	static function setMeta($name, $value = NULL)
	{
		switch( gettype($name) )
		{
			case 'array' :
			{
				foreach( $name AS $key => $value )
				{
					self::setMeta($key, $value);
				}
				break;
			}
			case 'string' :
			{
				if( NULL !== $value )
				{
					switch( $name )
					{
						case self::META_TITLE:
						{
							self::$_meta_title_added = $value;
							break;
						}
						default :
						{
							self::$_meta[$name] = $value;
						}
					}
				}
				else
				{
					trigger_error('Both parameters or array needs to be supplied to for Art_Template::setMeta()',E_USER_ERROR);
				}
				break;
			}
			default :
			{
				trigger_error('Invalid argument supplied for Art_Template::setMeta()',E_USER_ERROR);
			}
		}
	}
			
			
	/**
	 *	Set title for this site
	 * 
	 *	@static
	 *	@param string $title
	 *	@return void
	 */
	static function setTitle($title)
	{
		self::setMeta(self::META_TITLE, $title);
	}
	
	
	/**
	 *	@static
	 *	@param Art_Abstract_Module $module
	 *	@return void
	 */
	static function setContentModule(Art_Abstract_Module $module)
	{
		self::$_contentModule = $module;
	}
	
    
	/**
	 *	@static
	 *	@param string $templateName
	 *	@param string $templateFolder optional
	 *	@return void
	 *	@example Template::setTemplate('blueBoxShopMainpage','blueBox')
	 */
	static function setTemplate($templateName, $templateFolder = NULL)
	{
		//If not set, get from class
		if( NULL == $templateFolder )
		{
			$templateFolder = Art_Template::getTemplateFolder();
		}
		
		//Input validation
		if(is_string($templateName) && strlen($templateName) > 0 && is_string($templateFolder) && strlen($templateFolder) > 0)
		{
			$templateFolder = Art_Filter::templateName($templateFolder);			
			
			//File access test
			if( is_readable('templates/'.$templateFolder.'/'.$templateName.'.phtml') )
			{
				self::$_templateName = $templateName;
				self::$_templateFolder = $templateFolder;
			}
			else
			{
				trigger_error('Template '.$templateName.' in '.$templateFolder.' wasn`t found',E_USER_ERROR);
			}
		}
		else
		{
			trigger_error('Invalid value supplied for Main::setTemplate()',E_USER_ERROR);
		}
	}	
	
    /**
     *  Initialize the component
	 * 
     *  @static
     *  @return void
     */
    static function init() {
        if (parent::init()) {
			//If is AJAX - render raw template only
			if (Art_Server::isAjax()) {
				self::setTemplate(TEMPLATE_NAME_AJAX, TEMPLATE_DIR_AJAX);
			} else {				
				//Load template from config
				$layer = strtoupper(Art_Router::getLayer());
				if (constant('TEMPLATE_NAME_' . $layer) && constant('TEMPLATE_DIR_' . $layer)) {
					self::setTemplate(constant('TEMPLATE_NAME_' . $layer), constant('TEMPLATE_DIR_' . $layer));
				} else {
					trigger_error(sprintf("Can't load template: missing %s_template_name and/or %s_template_folder in config file", $layer), E_USER_ERROR);
				}
				
				//Get meta from DB
				self::loadMeta();
			}
		}
    }
	
	
	/**
	 *	Load META from DB table and save as associative array
	 * 
	 *	@return void
	 */
	static function loadMeta() {
		//Load defaults
		// self::$_meta = Art_Model_Meta::getDefaults();

		$metas = Art_Model_Meta::fetchAllCurrLayer();
		foreach($metas as $meta) {
			self::$_meta[$meta->key] = $meta->getContent();
		}
	}
	
	
	/**
	 *	Set HTML alert to echo into content of template
	 *	@static
	 *	@param string $alertMessage HTML alert message
	 *	@return void
	 *	@example Template::alert('<b>Something is wrong</b>');
	 */
	static function alert($alertMessage)
	{
		self::$_alert = $alertMessage;
	}
	
	
	/**
	 *	Render META tags for this site
	 * 
	 *	@return string
	 */
	static function renderMeta() {
		$output = '';
		
		//Pair META
		foreach (self::$_meta_pair AS $key => $value) {
			if (!isset(self::$_meta[$value]) && isset(self::$_meta[$key])) {
				self::$_meta[$value] = self::$_meta[$key];
			}
		}
		
		//For each meta
		foreach (self::$_meta AS $key => $value) {
			switch ($key) {
				case self::META_TITLE: {
					//If title was changed (added to base title)
					if (strlen(self::$_meta_title_added)) {
						$output .= '<title>'.self::$_meta_title_added.self::TITLE_SEPARATOR.$value.'</title>';
					} else {
						$output .= '<title>'.$value.'</title>';
					}
					break;
				}
				case self::META_KEYWORDS:
				case self::META_DESCRIPTION:
				case self::META_COPYRIGHT:
				case self::META_AUTHOR:
				case self::META_APPLICATION_NAME:
				case self::META_ROBOTS:
				case self::META_GOOGLE_SITE_VERIFICATION: {
					$output .= '<meta name="'.$key.'" content="'.$value.'" />';
					break;
				}
				default: {
					$output .= '<meta property="'.$key.'" content="'.$value.'" />';
				}
			}
			
			$output .= "\n\t";
		}
		
		return substr($output, 0, -1);
	}
	
	
	/**
	 *	Renders all module associated with specified position
	 *	@param string $positionName Name of the module div container
	 *	@return string
	 *	@example <body>Template::renderModuleContainer('body')</body> Echoes module to body - creates outer div <div class="module_container_body"></div>
	 */
	static function renderModuleContainer($positionName)
	{
		$output = '';
		
		//Render each module associated with this position
		if($modules = Art_Module::getModulesByPosition($positionName))
		{
			foreach($modules as $module)
			{
				$output .= $module->render();
			}
		}
		return $output;
	}
	
	
	/**
	 *	Render template
	 *	@static
	 *	@return bool True if template was found
	 */
    static function render() {
		// p('template->render: ');
		// print_r(self::getFolder().'/'.self::$_templateName.'.phtml');
        return require(ftest(self::getFolder().'/'.self::$_templateName.'.phtml'));
    }
	
	
	/**
	 *	Render main site content
	 *	@return void
	 */
	static function renderContent() {
		$output = '';
		//Main errors
		if (Art_Main::isError()) {
			$output .= '<div>'.Art_Main::getErrorMessage().'</div>';
		}

		//Template not found or no access error
		if (self::$_alert) {
			$output .= '<div>'.self::$_alert.'</div>';
		}
		else if (self::$_contentModule) {
			// p('template->renderContent');
			// print_r(self::$_contentModule);
			$output .= self::$_contentModule->render();	
		}
		
		// p('template->renderContent');
		// print_r($output);
		return $output;
	}
	
	
	/**
	 *	Render all CSS styles (files and scripts)
	 * 
	 *	@static
	 *	@param string|array $files Files to be included when rendering - must be saved in /templates/{template_name}/css/
	 *	@return string
	 */
	static function renderCSS( $files = NULL )
	{	
		//Include files if set
		if( NULL !== $files )
		{
			switch( gettype($files) )
			{
				case 'array' :
				{
					foreach( $files AS $file )
					{
						Art_Main::includeCSS( Art_Template::getFolder() .'/css/'.$file, true);
					}
					break;
				}
				case 'string' :
				{
					Art_Main::includeCSS( Art_Template::getFolder() .'/css/'.$files, true);
					break;
				}
				default:
					trigger_error('Invalid argument supplied to Art_Template::renderCss()');
			}
		}
		
		//Load all files and scripts
		$cached_files = Art_Main::getIncludedFiles('css', true);
		$cached_scripts = Art_Main::getIncludedScripts('css', true);
		$noncached_files = Art_Main::getIncludedFiles('css', false);
		$noncached_scripts = Art_Main::getIncludedScripts('css', false);
		
		//Load positions
		$positions = Art_Main::POSITIONS;
		
		//Get plain files list
		$cached_files_plain = array();
		foreach( $positions AS $position )
		{
			if( isset($cached_files[$position]) )
			{
				foreach( $cached_files[$position] AS $file )
				{
					$cached_files_plain[] = $file;
				}
			}
		}
		
		//Test if cache is old (need to recompile)
		if( !Art_Compiler::isCaching('css') || Art_Compiler::isCacheOld( 'css', $cached_files_plain, $cached_scripts ) )
		{
			//Get parser
			$parser = Art_Less::getParser();
			
			//Parse files and scripts
			foreach( $positions AS $position )
			{
				//Include files
				if( isset($cached_files[$position]) )
				{
					foreach( $cached_files[$position] AS $file )
					{
						if( isset($cached_files[Art_Main::INCLUDER_OPTIONS][$file]['uri_root']) )
						{
							$parser->parseFile( $file, $cached_files[Art_Main::INCLUDER_OPTIONS][$file]['uri_root']);
						}
						else
						{
							$parser->parseFile( $file);
						}
					}
				}

				//Include scripts
				if( isset($cached_scripts[$position]) )
				{
					$parser->parse($cached_scripts[$position]);
				}
			}
			
			//Get CSS code
			$parsed = $parser->getCss();
			
			//Minify
			$parsed = Art_Minify::minifyCSS($parsed);
			
			//Clean cache if not caching
			if( !Art_Compiler::isCaching('css') )
			{
				Art_Compiler::purgeCache('css');
			}
			
			//Save compiled
			$cached_file_path = Art_Compiler::saveCompiled('css', $parsed);
			
			//Save cache info
			if( Art_Compiler::isCaching('css') )
			{
				Art_Compiler::saveCacheInfo('css', $cached_file_path, $cached_files_plain, $cached_scripts);
			}
		}
		else
		{
			//Load from cache
			$cached_file_path = Art_Compiler::getCachedFileName('css');
		}
		
		$output = "\n";
		
		//Parse files and scripts to output
		foreach( $positions AS $position )
		{
			//Parse cached file
			if( $position == Art_Main::POSITION_INITIAL )
			{
				$output .= "\t".'<link rel="stylesheet" href="/'.$cached_file_path.'">'."\n";
			}
			
			//Include files
			if( isset($noncached_files[$position]) )
			{
				foreach( $noncached_files[$position] AS $file )
				{
					$output .= "\t".'<link rel="stylesheet" href="/'.$file.'">'."\n";
				}
			}

			//Include scripts
			if( !empty($noncached_scripts[$position]) )
			{
				$output .= "\t".'<style>'."\n".$noncached_scripts[$position]."\t".'</style>'."\n";
			}
		}
			
		return $output;
	}
	
	
	/**
	 *	Render All JS (files and scripts)
	 * 
	 *	@static
	 *	@param string|array $files Files to be included when rendering - must be saved in /scripts/
	 *	@return string
	 */
	static function renderJS( $files = NULL )
	{
		$output = "\n";		
		
		//Include files if set
		if( NULL !== $files )
		{
			switch( gettype($files) )
			{
				case 'array' :
				{
					foreach( $files AS $file )
					{
						Art_Main::includeJS('/scripts/'.$file, true);
					}
					break;
				}
				case 'string' :
				{
					Art_Main::includeJS('/scripts/'.$files, true);
					break;
				}
				default:
					trigger_error('Invalid argument supplied to Art_Template::renderJS()');
			}
		}
		
		//Load all files and scripts
		$cached_files = Art_Main::getIncludedFiles('js', true);
		$cached_scripts = Art_Main::getIncludedScripts('js', true);		
		$noncached_files = Art_Main::getIncludedFiles('js', false);
		$noncached_scripts = Art_Main::getIncludedScripts('js', false);		
		
		//Load positions
		$positions = Art_Main::POSITIONS;
			
		//Get plain files list
		$files_plain = array();
		foreach( $cached_files AS $files_by_pos )
		{
			foreach( $files_by_pos AS $file )
			{
				$files_plain[] = $file;
			}
		}
		
		//Test if cache is old (need to recompile)
		if( !Art_Compiler::isCaching('js') || Art_Compiler::isCacheOld('js', $files_plain, $cached_scripts ) )
		{
			$script = '';
						
			//Parse files and scripts
			foreach( $positions AS $position )
			{
				//Include files
				foreach( $cached_files[$position] AS $file )
				{
					if( is_readable($file) )
					{
						$script .= "\n".file_get_contents($file);
					}
				}

				//Include scripts
				if( isset($cached_scripts[$position]) )
				{
					$script .= "\n".$cached_scripts[$position];
				}
			}
						
			//Minify
			$script = Art_Minify::minifyJS($script);
			
			//Clean cache if not caching
			if( !Art_Compiler::isCaching('js') )
			{
				Art_Compiler::purgeCache('js');
			}
			
			//Save compiled
			$cached_file_path = Art_Compiler::saveCompiled('js', $script);
			
			//Save cache info
			if( Art_Compiler::isCaching('js') )
			{
				Art_Compiler::saveCacheInfo('js', $cached_file_path, $files_plain, $cached_scripts);
			}
		}
		else
		{
			//Load from cache
			$cached_file_path = Art_Compiler::getCachedFileName('js');
		}
		
		
		//Parse files and scripts to output
		foreach( $positions AS $position )
		{
			//Parse cached file
			if( $position == Art_Main::POSITION_INITIAL )
			{
				$output .= "\t".'<script type="text/javascript" src="/'.$cached_file_path.'"></script>'."\n";
			}
			
			//Include files
			if( isset($noncached_files[$position]) )
			{
				foreach( $noncached_files[$position] AS $file )
				{
					$output .= "\t".'<script type="text/javascript" src="/'.$file.'"></script>'."\n";
				}
			}

			//Include scripts
			if( !empty($noncached_scripts[$position]) )
			{
				$output .= "\t".'<script type="text/javascript">'."\n".$noncached_scripts[$position]."\t".'</script>'."\n";
			}
		}
			
		return $output;
	}
	
	
	/**
	 *	Include extensions
	 * 
	 *	@static
	 *	@param array $types Extension types
	 *	@return void
	 *	@example Template::loadExtensions(array('jquery','bootstrap')) Loads jquery and bootstrap
	 */
	static function loadExtensions($types)
	{
		foreach($types AS $type)
		{
			switch( strtolower($type) )
			{
				case 'bootstrap':
					if( !in_array('bootstrap', static::$_loaded_extensions) )
					{
						static::$_loaded_extensions[] = 'bootstrap';
						Art_Main::prependCSS('extensions/bootstrap/external/css/bootstrap.css', true, array('uri_root' => Art_Server::getRelativePath().'/extensions/bootstrap/external/css') );
						Art_Main::prependCSS('extensions/bootstrap/external/css/bootstrap-theme.css', true, array('uri_root' => Art_Server::getRelativePath().'/extensions/bootstrap/external/css') );
						Art_Main::prependCSS('extensions/bootstrap/system.css', true, array('uri_root' => Art_Server::getRelativePath().'/extensions/bootstrap'));
						Art_Main::prependJS('extensions/bootstrap/external/js/bootstrap.min.js', true);
					}
					break;
				case 'font-awesome':
				case 'font_awesome':
				case 'fontawesome':
					if( !in_array('font-awesome', static::$_loaded_extensions) )
					{
						static::$_loaded_extensions[] = 'font-awesome';
						Art_Main::prependCSS('extensions/font-awesome/external/css/font-awesome.css', true, array('uri_root' => Art_Server::getRelativePath().'/extensions/font-awesome/external/css') );
					}
					break;
				case 'jquery':
					if( !in_array('jquery', static::$_loaded_extensions) )
					{
						static::$_loaded_extensions[] = 'jquery';
						Art_Main::prependJS('extensions/jquery/external/jquery.js', true);
					}
					break;
				case 'ckeditor':
					if( !in_array('ckeditor', static::$_loaded_extensions) )
					{
						static::$_loaded_extensions[] = 'ckeditor';
						Art_Main::prependJS('extensions/ckeditor/external/ckeditor.js');
					}
					break;
				case 'date-picker':
					if (!in_array('date-picker', static::$_loaded_extensions)) {
						static::$_loaded_extensions[] = 'date-picker';

						// http://amsul.ca/pickadate.js/date/
						Art_Main::appendJS('extensions/date-picker/external/picker.js');
						Art_Main::appendJS('extensions/date-picker/external/picker.date.js');
						Art_Main::appendJS('extensions/date-picker/external/cs_CZ.js');
						Art_Main::appendCSS('extensions/date-picker/external/classic.css', true, array('uri_root' => Art_Server::getRelativePath().'/extensions/date-picker/external'));
						Art_Main::appendCSS('extensions/date-picker/external/classic.date.css', true, array('uri_root' => Art_Server::getRelativePath().'/extensions/date-picker/external'));
					}
					break;

				case 'chosen':
					if (!in_array('chosen', static::$_loaded_extensions)) {
						static::$_loaded_extensions[] = 'chosen';

						// https://harvesthq.github.io/chosen/
						Art_Main::appendJS('extensions/chosen/external/chosen.min.js');
						Art_Main::appendCSS('extensions/chosen/external/chosen.min.css', true, array('uri_root' => Art_Server::getRelativePath().'/extensions/chosen/external'));
					}
					break;
				case 'sortable':
					if (!in_array('sortable', static::$_loaded_extensions)) {
						static::$_loaded_extensions[] = 'sortable';

						Art_Main::appendJS('extensions/sortable/external/sortable.min.js');
						// Art_Main::appendCSS('extensions/sortable/external/sortable.min.css', true, array('uri_root' => Art_Server::getRelativePath().'/extensions/chosen/external'));
					}
					break;
				case 'fancybox':
				case 'fancy_box':
				case 'fancy-box':
					if( !in_array('fancybox', static::$_loaded_extensions) )
					{
						static::$_loaded_extensions[] = 'fancybox';
						Art_Main::prependCSS('extensions/fancybox/external/source/jquery.fancybox.css', true, array('uri_root' => Art_Server::getRelativePath().'/extensions/fancybox/external') );
						Art_Main::prependJS('extensions/fancybox/external/source/jquery.fancybox.js', true);
					}
					break;
				case 'chart':
				case 'chartjs':
				case 'chart.js':
				case 'chart-js':
				case 'chart_js':
					if( !in_array('chartjs', static::$_loaded_extensions) )
					{
						static::$_loaded_extensions[] = 'chartjs';
						Art_Main::prependJS('extensions/chart/external/Chart.js', true);
					}
					break;
			}
		}
	}
}