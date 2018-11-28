<?php
/**
 *  @author Pastuszek Jakub <pastuszek@itart.cz>
 *  @package helpers
 * 
 *	Helper used for TBDev project
 */
class Helper_TBDev extends Art_Abstract_Helper {
	
	const MEMBERSHIP		= 'Membership';
	const MEMBERSHIP_TYPE	= 'membership';
	const INVESTMENT_TYPE	= 'investment';
	
	const MANAGER_GROUP				= 'Manager';
	const AUTHORIZED_GROUP			= 'Authorized';	
	const NONMEMBERS_GROUP			= 'Nonmembers';
	const MEMBERSHIP_MEMBERS_GROUP	= 'Membership members';
		
	const DIRECTORY_PDF			= 'files/pdf';
	const CONDITIONS_DIRECTORY	= 'files/pdf/conditions';
		
	const PROTECTED_GROUPS = array('Default group','Authorized','Manager','Registered');
	
	const FA_ICONS = array('adjust','adn','align-center','align-justify','align-left','align-right','amazon',
		'american-sign-language-interpreting','anchor','android','angellist','angle-double-down',
		'angle-double-left','angle-double-right','angle-double-up','angle-down','angle-left',
		'angle-right','angle-up','apple','archive','area-chart','arrow-circle-down','arrow-circle-left',
		'arrow-circle-o-down','arrow-circle-o-left','arrow-circle-o-right','arrow-circle-o-up',
		'arrow-circle-right','arrow-circle-up','arrow-down','arrow-left','arrow-right','arrow-up',
		'arrows','arrows-alt','arrows-h','assistive-listening-systems','asterisk','audio-description',
		'automobile','backward','balance-scale','ban','bank','bar-chart','bar-chart-o','barcode','bars',
		'battery-0','battery-1','battery-2','battery-3','battery-4','battery-empty','battery-full',
		'battery-half','battery-quarter','battery-three-quarters','bed','beer','behance','behance-square',
		'bell','bell-o','bell-slash','bell-slash-o','bicycle','binoculars','birthday-cake','bitbucket',
		'bitbucket-square','bitcoin','blind','bluetooth','bluetooth-b','bold','bolt','bomb','book','bookmark',
		'braille','briefcase','btc','bug','building','building-o','bullhorn','bullseye','bus','buysellads',
		'cab','calculator','calendar','calendar-check-o','calendar-minus-o','calendar-o','calendar-plus-o',
		'calendar-times-o','camera','camera-retro','car','caret-down','caret-left','caret-right',
		'caret-square-o-down','caret-square-o-left','caret-square-o-right','caret-square-o-up','caret-up',
		'cart-arrow-down','cart-plus','cc','cc-amex','cc-diners-club','cc-discover','cc-jcb','cc-mastercard',
		'cc-paypal','cc-stripe','cc-visa','certificate','chain','chain-broken','check','check-circle',
		'check-circle-o','check-square','check-square-o','chevron-circle-down','chevron-circle-left',
		'chevron-circle-right','chevron-circle-up','chevron-down','chevron-left','chevron-right','chevron-up',
		'child','chrome','circle','circle-o','circle-o-notch','circle-thin','clipboard','clock-o','clone',
		'close','cloud','cloud-download','cloud-upload','cny','code','code-fork','codepen','codiepie','coffee',
		'cog','cogs','columns','comment','comment-o','commenting','commenting-o','comments','comments-o',
		'compass','compress','connectdevelop','contao','copy','copyright','creative-commons','credit-card',
		'credit-card-alt','crop','crosshairs','css3','cube','cubes','cut','cutlery','dashboard','dashcube',
		'deafness','dedent','delicious','desktop','deviantart','diamond','digg','dollar','dot-circle-o',
		'download','dribbble','dropbox','drupal','edge','edit','eject','ellipsis-h','ellipsis-v','empire',
		'envelope','envelope-o','envira','eraser','eur','euro','exchange','exclamation','exclamation-circle',
		'exclamation-triangle','expand','expeditedssl','external-link','external-link-square','eye','eye-slash',
		'fa','facebook','facebook-f','facebook-official','facebook-square','fast-backward','fast-forward','fax',
		'feed','female','fighter-jet','file','file-archive-o','file-audio-o','file-code-o','file-excel-o',
		'file-image-o','file-movie-o','file-o','file-pdf-o','file-photo-o','file-picture-o','file-powerpoint-o',
		'file-sound-o','file-text','file-text-o','file-video-o','file-word-o','file-zip-o','files-o','film',
		'filter','fire','fire-extinguisher','first-order','flag','flag-checkered','flag-o','flash','flask',
		'flickr','floppy-o','folder','folder-o','folder-open','folder-open-o','font-awesome','fonticons',
		'fort-awesome','forumbee','forward','foursquare','frown-o','futbol-o','gamepad','gavel','gbp','ge',
		'gear','gears','genderless','get-pocket','gg','gg-circle','gift','git','git-square','github',
		'github-alt','gitlab','gittip','glide-g','globe','google','google-plus-official','google-plus-square',
		'google-wallet','graduation-cap','gratipay','group','h-square','hacker-news','hand-grab-o',
		'hand-lizard-o','hand-o-down','hand-o-left','hand-o-right','hand-o-up','hand-paper-o','hand-peace-o',
		'hand-pointer-o','hand-rock-o','hand-scissors-o','hand-spock-o','hard-of-hearing','hashtag','hdd-o',
		'header','headphones','heart','heart-o','heartbeat','history','home','hospital-o','hotel','hourglass',
		'hourglass-1','hourglass-2','hourglass-3','hourglass-end','hourglass-half','hourglass-o','hourglass-start',
		'houzz','html5','i-cursor','ils','image','inbox','indent','industry','info','info-circle','instagram',
		'institution','internet-explorer','intersex','ioxhost','italic','joomla','jpy','jsfiddle','key',
		'keyboard-o','krw','language','laptop','lastfm','lastfm-square','leaf','leanpub','legal','lemon-o',
		'level-down','level-up','life-bouy','life-buoy','life-ring','life-saver','lightbulb-o','line-chart',
		'link','linkedin','linkedin-square','linux','list','list-alt','list-ol','list-ul','location-arrow',
		'lock','long-arrow-down','long-arrow-left','long-arrow-right','low-vision','magic','magnet',
		'mail-forward','mail-reply','mail-reply-all','male','map','map-marker','map-o','map-pin','map-signs',
		'mars','mars-double','mars-stroke','mars-stroke-h','mars-stroke-v','maxcdn','meanpath','medium',
		'medkit','meh-o','mercury','microphone','microphone-slash','minus','minus-circle','minus-square',
		'minus-square-o','mixcloud','mobile','mobile-phone','modx','money','moon-o','mortar-board',
		'motorcycle','mouse-pointer','music','navicon','neuter','newspaper-o','object-group','object-ungroup',
		'odnoklassniki','odnoklassniki-square','opencart','openid','opera','optin-monster','outdent',
		'pagelines','paint-brush','paper-plane','paper-plane-o','paperclip','paragraph','paste','pause',
		'pause-circle','pause-circle-o','paw','paypal','pencil','pencil-square','pencil-square-o','percent',
		'phone','phone-square','photo','picture-o','pied-piper','pied-piper-alt','pied-piper-pp','pinterest',
		'pinterest-p','pinterest-square','plane','play','play-circle','play-circle-o','plug','plus',
		'plus-circle','plus-square','plus-square-o','power-off','print','product-hunt','puzzle-piece','qq',
		'qrcode','question','question-circle-o','quote-left','quote-right','ra','random','rebel','recycle',
		'reddit','reddit-alien','reddit-square','refresh','registered','remove','renren','reorder','repeat',
		'reply','reply-all','resistance','retweet','rmb','road','rocket','rotate-left','rotate-right','rouble',
		'rss','rss-square','rub','ruble','rupee','safari','save','scissors','scribd','search','search-minus',
		'search-plus','sellsy','send','send-o','server','share','share-alt','share-alt-square','share-square',
		'share-square-o','shekel','sheqel','shield','ship','shirtsinbulk','shopping-bag','shopping-basket',
		'shopping-cart','sign-language','sign-out','signing','simplybuilt','sitemap','skyatlas','skype',
		'slack','sliders','slideshare','snapchat-square','soccer-ball-o','sort','sort-alpha-asc',
		'sort-alpha-desc','sort-amount-asc','sort-amount-desc','sort-asc','sort-desc','sort-down',
		'sort-numeric-asc','sort-numeric-desc','sort-up','soundcloud','space-shuttle','spinner','spoon',
		'spotify','square','square-o','stack-exchange','stack-overflow','star','star-half','star-half-empty',
		'star-half-full','star-half-o','star-o','steam','steam-square','step-backward','step-forward',
		'stethoscope','sticky-note','sticky-note-o','stop','stop-circle','stop-circle-o','street-view',
		'strikethrough','stumbleupon','stumbleupon-circle','subscript','subway','suitcase','sun-o',
		'superscript','support','table','tablet','tachometer','tag','tags','tasks','taxi','television',
		'tencent-weibo','terminal','text-height','text-width','th','th-large','themeisle','thumb-tack',
		'thumbs-down','thumbs-o-down','thumbs-o-up','thumbs-up','ticket','times','times-circle',
		'times-circle-o','tint','toggle-down','toggle-left','toggle-off','toggle-on','toggle-right',
		'toggle-up','trademark','train','transgender','transgender-alt','trash','trash-o','tree','trello',
		'tripadvisor','trophy','truck','try','tty','tumblr','tumblr-square','turkish-lira','tv','twitch',
		'twitter','twitter-square','umbrella','underline','universal-access','university','unlink','unlock',
		'unlock-alt','unsorted','upload','usb','usd','user','user-md','user-plus','user-secret','user-times',
		'users','venus','venus-double','venus-mars','viadeo-square','video-camera','vimeo','vimeo-square',
		'vine','volume-control-phone','volume-down','volume-off','volume-up','warning','wechat','weibo',
		'weixin','whatsapp','wheelchair-alt','wifi','wikipedia-w','windows','won','wpforms','wrench','xing',
		'xing-square','y-combinator','y-combinator-square','yahoo','yc','yc-square','yelp','yoast','youtube',
		'youtube-play','youtube-square');
			
	const MAX_DASHBOARD_RESULTS_EMBEDD	= 5;
	const MAX_DASHBOARD_RESULTS			= 15;
	const DASHBOARD_TIME_FROM			= '3';	//months (without text 'month' for corresponding with DB value
			
	const FORGOTTEN_VALID_TIME			= "12 hours";
	
	const CURRENCY_FROM_ADDRESS_STATE	= Art_Model_Address::DELIVERY_PREFIX;
	
	const EMAIL_TYPE_GOT_APP		= 'Got_application';
	const EMAIL_TYPE_NOT_GOT_APP	= 'Not_got_application';
	
	const GROUP_SERVICE_MEMBERS	= ' service members';
	const GROUP_COMPANY			= 'Company ';
	
	const MAX_INV_CODES_PER_USER				= 'inv-code-max-user';		
	const MAX_INV_CODES_PER_USER_GEN_BY_ADMIN	= 'inv-code-max-admin';
	
	const DEFAULT_STANOVY		= 'stanovy';
	const DEFAULT_INVESTMENT_INTEREST	= 'investment-interest';
	const DEFAULT_MAIL_FOOTER	= 'mail-footer';
	
	
	/**
	 *	Get day range for date
	 * 
	 *	@return array
	 */
	static function getDayRange ()
	{
		return range(1,31);
	}


	/**
	 *	Get month range for date
	 * 
	 *	@return array
	 */
	static function getMonthRange ()
	{
		return range(1,12);
	}


	/**
	 *	Get year range for date for born
	 * 
	 *	@return array
	 */
	static function getBornYearRange ()
	{
		$year = date("Y");

		return range($year-10,$year-80);
	}
	
	/**
	 *	Get year range for date for service
	 * 
	 *	@return array
	 */
	static function getServiceYearRange ()
	{
		$year = date("Y");

		return range($year-1,$year+10);
	}

	/**
	 *	Get current day
	 * 
	 *	@return int
	 */
	static function getCurrentDay ()
	{
		return date("d");
	}
	
	/**
	 *	Get current month
	 * 
	 *	@return int
	 */
	static function getCurrentMonth ()
	{
		return date("m");
	}
	
	/**
	 *	Get current year
	 * 
	 *	@return int
	 */
	static function getCurrentYear ()
	{
		return date("Y");
	}

	/**
	 *	Get next year
	 * 
	 *	@return int
	 */
	static function getNextYear ()
	{
		return date("Y",strtotime("+1 year"));
	}
	
	
	/**
	 *	Get interval type range
	 * 
	 *	@return array
	 */
	static function getIntervalTypeRange ()
	{
		return array('d'=>__('days'),'m'=>__('months'),'r'=>__('years'));
	}
	
	
	/**
	 *	Get date from year, month and day
	 * 	 
	 *  @param int	$year
	 *  @param int	$month
	 *	@param int	$day
	 *	@return array
	 */
	static function getDate ( $year, $month = 1, $day = 1, $separator = '-' )
	{
		return $day . $separator . $month . $separator . $year;
	}
	
	
	/**
	 *	Get numeric random string desired length
	 * 
	 *  @param int	$str_length		Length of string
	 *	@return string
	 */	
	static function getNumRandString ( $str_length = 12 )
	{
		$characters = implode(range(0,9));

		$string = '';
		
		for ($i = 0; $i < $str_length; $i++) {
			 $string .= $characters[rand(0, strlen($characters) - 1)];
		}
		
		return $string;
	}
	
	/**
	 *	Get alfanumeric random string desired length
	 * 
	 *  @param int	$str_length		Length of string
	 *	@return string
	 */	
	static function getAlfanumRandString ( $str_length = 12 )
	{
		$characters = implode(array_merge(range(0,9),range('a','z'),range('A','Z')));

		$string = '';
		
		for ($i = 0; $i < $str_length; $i++) {
			 $string .= $characters[rand(0, strlen($characters) - 1)];
		}
		
		return $string;
	}
		
	
	/**
	 *	Get gender name
	 *	@param int $gender_number
	 * 
	 *	@return array
	 */
	static function getGender ($gender_number) {
		return $gender_number ? __('male') : __('female');
	}
	

	/**
	 *	Render Date according to condition
	 * 
	 *  @param int	$condition
	 *	@param string	$content
	 *	@return string
	 */	
	static function renderTrueFalseDateTo($condition , $content) {
		if ($condition) {
			return Helper_Default::elementPaired('span', $content, NULL, 'overdue', null);
		} else {
			return Helper_Default::elementPaired('span', $content);	
		}	
	}
	
	/**
	 *	Get membership from date for User
	 * 
	 *  @param Art_Model_User	$user
	 *	@return date
	 */	
	static function getMembershipFromForUser($user) {
		if (!$user->isLoaded()) {
			return 0;
		}
		
		$service = new Service(array(
			'type' => self::MEMBERSHIP_TYPE
		));

		return static::getServiceFromForUser($user, $service);	
	}
	
	
	/**
	 *	Get service from date for User
	 * 
	 *  @param Art_Model_User	$user
	 *	@param Service		$service
	 *	@return date
	 */	
	static function getServiceFromForUser($user, $service) {
		if (!$user->isLoaded() || NULL === $service) {
			return 0;
		}
		
		$userService = new User_X_Service(array(
			'id_user' => $user->id,
			'id_service' => $service->id,
			'activated' => '1'
		));

		if (!$userService->isLoaded()) {
			$from = null;
		} else {
			$from = strpos($userService->activated_date, '0000-00-00') === false ? $userService->activated_date : NULL;
		}
		
		return !is_null($from) ? date('Y-m-d', strtotime($from)) : $from;
	}
	
	
	/**
	 *	Get membership to date for User
	 * 
	 *  @param Art_Model_User	$user
	 *	@return date
	 */	
	static function getMembershipToForUser($user) {
		if (!$user->isLoaded()) {
			return 0;
		}
		
		$service = new Service(array(
			'type' => self::MEMBERSHIP_TYPE
		));
		
		return static::getServiceToForUser($user, $service);	
	}

		/**
	 *	Get service to date for User
	 * 
	 *  @param Art_Model_User	$user
	 *	@param Service		$service
	 *	@return date
	 */	
	static function getServiceToForUser($user, $service) {
		if (!$user->isLoaded() || NULL === $service ) {
			return 0;
		}
		
		$userService = new User_X_Service(array(
			'id_user' => $user->id,
			'id_service' => $service->id,
			'activated' => '1'
		));

		if (empty($userService) || 0 == $userService->activated) {
			return 0;
		}

		$from = strpos($userService->activated_date, '0000-00-00') === false ? $userService->activated_date : NULL;
		
		return static::_getServiceToForUser($user, $service, $from);
	}

	
	/**
	 *	Get service to date by name for User
	 * 
	 *  @param Art_Model_User	$user
	 *  @param string			$serviceName
	 *	@return date
	 */	
	static function getServiceToByNameForUser( $user, $serviceName )
	{
		if ( !$user->isLoaded() || NULL === $serviceName )
		{
			return 0;
		}
		
		$service = Service::fetchAllPrivileged(array('type'=>$serviceName));
		
		if ( empty($service) )
		{
			return 0;
		}
		else
		{
			$service = $service[0];
		}
		
		$userService = new User_X_Service(array('id_user'=>$user->id,'id_service'=>$service->id,'activated'=>'1'));
		 
		if ( empty($userService) )
		{
			return 0;
		}
		 
		$from = $userService->activated_date;
		
		return static::_getServiceToForUser( $user, $service, $from );	
	}
	
	
	/**
	 *	Get service to date for User
	 * 
	 *  @param Art_Model_User	$user
	 *	@param Service		$service	
	 *	@param datetime		$from
	 *	@return date
	 */	
	static private function _getServiceToForUser($user, $service, $from) {
		if (!$user->isLoaded() || NULL === $service || NULL === $from) {
			return 0;
		}
		
		$payments = static::getServicePaymentsForUser($user, $service);
		$minServicePrice = static::getMinimalServicePriceForServiceForUser($user, $service);

		if ( NULL !== $minServicePrice && $minServicePrice->price == 0) {
			return static::getMembershipToForUser($user);
		}
		
		$y = $m = $d = 0;
		
		foreach ($payments as $payment) {
			$type = substr($payment->servicePrice->time_interval,-1);
			$value = substr($payment->servicePrice->time_interval, 0, strlen($payment->servicePrice->time_interval)-1);
			
			if( $payment->servicePrice->price == 0 )
			{
				return static::getMembershipToForUser($user);
			}
			
			$duration = ($payment->value / $payment->servicePrice->price) * $value;
			
			switch ( $type ) 
			{
				case "r":
					$y += $duration;
					break;
				case "m":
					$m += $duration;
					break;
				default:
					$d += $duration;
					break;
			}
		}
		
		$yf = $y - floor($y);
		
		if ( 0 !== $yf )
		{
			$m += 12*$yf;
		}
		
		$mf = $m - floor($m);
		
		if ( 0 !== $mf )
		{
			$d += 30*$mf;
		}
		
		// p($y.'y '.$m.'m '.$d.'d');
		//p(date('Y-m-d',  strtotime("+".floor($y)." years ".floor($m)." months ".floor($d)." days", strtotime($from))));
		//p($from);
		
		return date('Y-m-d', strtotime("+".floor($y)." years ".floor($m)." months ".floor($d)." days", strtotime($from)));
	}
	
	
	/**
	 *	Get all Services of which the User belongs
	 * 
	 *  @param Art_Model_User	$user
	 *	@return Service[]
	 */	
	static function getAllServicesForUser( $user )
	{		
		$services = array();
		
		if ( $user->isLoaded() )
		{		
			$sql = Art_Main::db()->query('SELECT id FROM service WHERE id IN(SELECT DISTINCT id_service FROM service_price WHERE id IN(SELECT id_service_price FROM user_group_x_service_price WHERE id_user_group IN(SELECT id_user_group FROM user_x_user_group WHERE id_user = '.$user->id.'))) ORDER BY sort;');		

			foreach ($sql->fetchAll() as $value) /* @var $value  */ 
			{
				$services[] = new Service($value['id']);
			}

			$bypass = true;
			
			if ( !$bypass )
			{
				//Fetch all User Groups of which the User belongs
				foreach ( Art_Model_User_X_User_Group::fetchAllPrivileged(array('id_user'=>$user->id)) as $userUserGroup )
					/* @var $userUserGroup Art_Model_User_X_User_Group */
				{
					//Fetch all Service Prices of which the User Group belongs
					foreach ( User_Group_X_Service_Price::fetchAllPrivileged(array('id_user_group'=>$userUserGroup->id_user_group)) as $userGroupServicePrice ) 
						/* @var $userGroupServicePrice User_Group_X_Service_Price */
					{
						$service = $userGroupServicePrice->getServicePrice()->getService();

						if ( !in_array($service, $services) )
						{
							//Get Service from Service Price
							$services[] = $service;
						}
					}
				}
			}
		}
		
		return $services;
	}

	
	/**
	 *	Get all activated Services of which the User belongs
	 * 
	 *  @param Art_Model_User	$user
	 *	@return Service[]
	 */	
	static function getAllActivatedServicesForUser($user) {
		$services = array();
		
		if ($user->isLoaded()) {		
			foreach (User_X_Service::fetchAllPrivilegedActive(array('id_user'=>$user->id)) as $userService)
				/* @var $userService User_X_Service */
			{
				if ($userService->activated) {
					$services[] = $userService->getService();
				}
			}
		}
		
		return $services;	
	}
	
	
	/**
	 *	Get all nonactivated Services of which the User belongs
	 * 
	 *  @param Art_Model_User	$user
	 *	@return Service[]
	 */	
	static function getAllNonactivatedServicesForUser( $user )
	{		
		return array_diff(getAllServicesForUser($user),getAllActivatedServicesForUser($user));	
	}
	
	
	/**
	 *	Get Service by article name
	 * 
	 *	@param string			$name
	 *	@return Service
	 */	
	static function getServiceByArticleName( $name )
	{
		$service = null;
		
		if ( NULL !== $name )
		{
			foreach ( Service::fetchAllPrivileged() as $value ) /* @var $value Service */ 
			{
				$settings = json_decode($value->settings, true);
				if ( $settings['promo'] === $name )
				{
					$service = $value;
					break;
				}
			}
		}
		
		return $service;
	}
	
	
	/**
	 *	Get Service by type
	 * 
	 *	@param string	$type
	 *	@return Service
	 */	
	static function getServiceByType( $type )
	{
		$service = null;
		
		if ( NULL !== $type )
		{
			foreach ( Service::fetchAllPrivileged() as $value ) /* @var $value Service */ 
			{
				if ( $value->type === $type )
				{
					$service = $value;
					break;
				}
			}
		}
		
		return $service;
	}
	

	/**
	 *	Is $service activated for $user
	 * 
	 *	@param Service			$service
	 *  @param Art_Model_User	$user
	 *	@return boolean
	 */	
	static function isServiceActivatedForUser( $service, $user )
	{
		$isActivated = false;

		foreach ( static::getAllActivatedServicesForUser($user) as $value ) /* @var $value Service */ 
		{
			if ( $service->id === $value->id )
			{
				$isActivated = true;
				break;
			}
		}

		return $isActivated;	
	}
	
	
	/**
	 *	Is $service by article name activated for $user
	 * 
	 *	@param string			$articleName
	 *  @param Art_Model_User	$user
	 *	@return boolean
	 */	
	static function isServiceByArticleNameActivatedForUser( $articleName, $user )
	{
		$isActivated = false;

		foreach ( static::getAllActivatedServicesForUser($user) as $value ) /* @var $value Service */ 
		{
			$settings = json_decode($value->settings, true);
			if ( $settings['promo'] === $articleName )
			{
				$isActivated = true;
				break;
			}
		}

		return $isActivated;	
	}
	
	
	/**
	 *	Get all Users using Service
	 * 
	 *  @param Service	$service
	 *	@return Art_Model_User[]
	 */	
	static function getAllUsersForService ( $service )
	{
		$userGroups = array();
		
		if ( $service->isLoaded() )
		{		
			$sql = Art_Main::db()->query('SELECT * FROM user_group_x_service_price WHERE id_service_price IN(SELECT id FROM service_price WHERE id_service = '.$service->id.');');		

			foreach ($sql->fetchAll() as $value) /* @var $value  */ 
			{
				$userGroup = new Art_Model_User_Group($value['id_user_group']);

				if ( !in_array($userGroup, $userGroups) )
				{
					$userGroups[] = $userGroup;
				}
			}
			
			$bypass = true;
			
			if ( !$bypass )
			{
				//Fetch all Service Prices of Service
				foreach (Service_Price::fetchAllPrivileged(array('id_service'=>$service->id)) as $servicePrice )
					/* @var $servicePrice Service_Price */
				{
					//Fetch all User Groups contained Service Price
					foreach ( User_Group_X_Service_Price::fetchAllPrivileged(array('id_service_price'=>$servicePrice->id)) as $userGroupServicePrice ) 
						/* @var $userGroupServicePrice User_Group_X_Service_Price */
					{
						$userGroup = $userGroupServicePrice->getUserGroup();

						if ( !in_array($userGroup, $userGroups) )
						{
							$userGroups[] = $userGroup;
						}
					}
				}
			}
		}
		
		$users = array();
		
		foreach ($userGroups as $userGroup) /* @var $userGroup Art_Model_User_Group */
		{
			foreach ( Art_Model_User_X_User_Group::fetchAllPrivileged(array('id_user_group'=>$userGroup->id)) as $userUserGroup) 
				/* @var $userUserGroup Art_Model_User_X_User_Group */
			{
				$user = $userUserGroup->getUser();

				if ( !in_array($user, $users) )
				{
					$users[] = $user;
				}
			}
		}

		return $users;
	}
	
	
	/**
	 *	Get all Users with activated Service
	 * 
	 *  @param Service	$service
	 *	@return Art_Model_User[]
	 */	
	static function getAllUsersForActivatedService ( $service )
	{
		$users = array();
		
		foreach (Helper_TBDev::getAllUsersForService($service) as $value) /* @var $value Art_Model_User */ 
			{
				if ( Helper_TBDev::isServiceActivatedForUser($service, $value) )
				{
					$users[] = $value;
				}
			}

		return $users;
	}

	
	/**
	 *	Get all Service Prices of which the User belongs
	 * 
	 *  @param Art_Model_User	$user
	 *	@return Service_Price[]
	 */		
	static function getAllServicePricesForUser ( $user )
	{		
		$servicePrices = array();

		if ( $user->isLoaded() )
		{	
			//Fetch all User Groups of which the User belongs
			foreach ( Art_Model_User_X_User_Group::fetchAllPrivileged(array('id_user'=>$user->id)) as $userUserGroup )
				/* @var $userUserGroup Art_Model_User_X_User_Group */
			{
				//Fetch all Service Prices of which the User Group belongs
				foreach ( User_Group_X_Service_Price::fetchAllPrivileged(array('id_user_group'=>$userUserGroup->id_user_group)) as $userGroupServicePrice ) 
					/* @var $userGroupServicePrice User_Group_X_Service_Price */
				{
					$servicePrice = $userGroupServicePrice->getServicePrice();
					$servicePrice->id_user_group_x_service_price = $userGroupServicePrice->id;
					
					if ( !in_array($servicePrice, $servicePrices) )
					{
						$servicePrices[] = $servicePrice;
					}
				}
			}
		}
		
		return $servicePrices;
	}
	
		
	/**
	 *	Get Service Prices fro Service of which the User belongs
	 * 
	 *  @param Art_Model_User	$user
	 *	@param Service			$service
	 *	@return Service_Price[]
	 */		
	static function getServicePricesForServiceForUser ($user, $service) {		

		// echo 'user:';
		// print_r($user);
		// echo 'service:';
		// print_r($service);

		$servicePrices = array();

		if ($user->isLoaded() && NULL !== $service) {	
			// print_r($service);
			//Fetch all User Groups of which the User belongs
			foreach (Art_Model_User_X_User_Group::fetchAllPrivileged(array('id_user' => $user->id)) as $userUserGroup ) {				
				foreach (User_Group_X_Service_Price::fetchAllPrivileged(array('id_user_group' => $userUserGroup->id_user_group)) as $userGroupServicePrice) {
					$servicePrice = $userGroupServicePrice->getServicePrice();
					// echo 'idservice:';
					// print_r($servicePrice->id_service);
					// echo 'servisid:';
					// print_r($service->id);
					if ($servicePrice->id_service == $service->id) {
						$servicePrice->id_user_group_x_service_price = $userGroupServicePrice->id;
						if (!in_array($servicePrice, $servicePrices)) {
							$servicePrices[] = $servicePrice;
						}
					}
				}
			}
		}
		
		return $servicePrices;
	}
	
	
	/**
	 *	Get all minimal Service Prices of which the User belongs
	 * 
	 *  @param Art_Model_User	$user
	 *	@return Service_Price[]
	 */		
	static function getAllMinimalServicePricesForUser ( $user )
	{	
		$servicePrices = array();

		foreach (static::getAllServicePricesForUser($user) as $servicePrice ) 
			/* @var $servicePrice Service_Price */
		{
			if ( !empty($servicePrices[$servicePrice->id_service]) )
			{
				if ( $servicePrices[$servicePrice->id_service]->price <= $servicePrice->price )
				{
					continue;
				}
			}
			
			//Get Service from Service Price
			$servicePrices[$servicePrice->id_service] = $servicePrice;
		}
		
		return $servicePrices;
	}
	
		
	/**
	 *	Get minimal Service Price for Service of which the User belongs
	 * 
	 *  @param Art_Model_User	$user
	 *	@param Service			$service
	 *	@return Service_Price
	 */		
	static function getMinimalServicePriceForServiceForUser ($user, $service) {	
		$servicePrice = null;

		foreach (static::getServicePricesForServiceForUser($user, $service) as $value ) 
			/* @var $servicePrice Service_Price */
		{
			if (NULL !== $servicePrice) {
				if ($servicePrice->price <= $value->price) {
					continue;
				}
			}
			
			//Get Service from Service Price
			$servicePrice = $value;
		}
		
		return $servicePrice;
	}
	
	
	/**
	 *	Get all Payments of User
	 * 
	 *  @param Art_Model_User	$user
	 *	@return Payments[]
	 */	
	static function getAllPaymentsForUser ( $user )
	{
		$payments = array();

		foreach ( Service_Payment::fetchAllPrivileged(array('id_user'=>$user->id)) as $payment )
			/* @var $payment Service_Payment */
		{
			$payment->paid_by_fullname = (new Art_Model_User($payment->id_user_paid_by))->fullname;

			$servicePrice = $payment->getUserGroupXServicePrice()->getServicePrice();
			$payment->servicePrice = $servicePrice; 

			$payment->service = $servicePrice->getService();

			$payments[] = $payment;
		}

		return $payments;
	}
	
	
	/**
	 *	Get service Payments of User
	 * 
	 *  @param Art_Model_User	$user
	 *	@param Service			$service
	 *	@return Payments[]
	 */	
	static function getServicePaymentsForUser ( $user, $service )
	{
		$payments = array();

		foreach ( Service_Payment::fetchAllPrivileged(array('id_user'=>$user->id)) as $payment )
			/* @var $payment Service_Payment */
		{
			$payment->paid_by_fullname = (new Art_Model_User($payment->id_user_paid_by))->fullname;

			$servicePrice = $payment->getUserGroupXServicePrice()->getServicePrice();
			$payment->servicePrice = $servicePrice; 

			$payment->service = $servicePrice->getService();
			
			if ( $service->id == $payment->service->id )
			{
				$payments[] = $payment;	
			}
		}

		return $payments;
	}	

	
	/**
	 *	Get know if the User is authenticated
	 * 
	 *  @param Art_Model_User	$user
	 *	@return boolean
	 */	
	static function isUserAuthenticated($user) {
		if ($user->isLoaded()) {
			if (!empty(Art_Model_User_X_User_Group::fetchAll(array(
					'id_user' => $user->id,
					'id_user_group' => Art_Model_User_Group::getAuthorizedId())))
				) {
				return true;
			}
		}
		
		return false;
	}
	
	
	/**
	 *	Is User Manager or more
	 * 
	 *  @param Art_Model_User	$user
	 *	@return boolean
	 */	
	static function isManager( $user )
	{
		$managerGroup = Art_Model_User_Group::getManager();

		if ( $user->isLoaded() )
		{
			//Fetch all User Groups of which the User belongs
			foreach ( Art_Model_User_X_User_Group::fetchAll(array('id_user'=>$user->id)) as $userUserGroup )
			{	
				if ( $managerGroup->getRights() <= $userUserGroup->getGroup()->getRights() )
				{
					return true;
				}
			}
		}
		
		return false;
	}
	
	
	/**
	 *	Get telephone number for $user
	 *  
	 *  @param Art_Model_User	$user
	 *	@return int
	 */	
	static function getTelephoneForUser ($user)
	{
		if ( !$user->isLoaded() )
		{
			return;
		}
		
		$addresses = $user->getAddresses();
		
		$telephone = null;
		
		foreach ($addresses as $value) /* @var $value Art_Model_Address */ 
		{
			if ( NULL != $value->phone )
			{
				if ( NULL != $value->area_code )
				{
					$telephone .= $value->area_code;
				}
				
				$telephone .= $value->phone;
				break;
			}

		}
		
		return $telephone;
	}
	
	
	/**
	 *	Get all permitted user groups with rights equal or less than user
	 *  
	 *  @param Art_Model_User	$user
	 *	@return array
	 */
	static function getAllPermittedUserGroupsForUser ( $user ) 
	{
		$userGroups = array();
		
		if ( $user->isLoaded() )
		{	
			foreach ( Art_Model_User_Group::fetchAllPrivileged() as $value ) /* @var $value Art_Model_User_Group */
			{
				if ( $value->getRights() <= $user->getRights() )
				{
					$userGroups[] = $value;
				}
			}
		}
		
		return $userGroups;
	}
	
	
	/**
	 *	Get User who invited $user 
	 *  
	 *  @param Art_Model_User	$user
	 *	@return Art_Model_User
	 */
	static function getUserInvitedBy ( $user )
	{
		if ( NULL === $user || !$user->isLoaded() )
		{
			return null;
		}
		
		$userInvCode = new User_X_Invite_Code(array('id_user'=>$user->id));
		
		if ( !$userInvCode->isLoaded() )
		{
			return null;
		}
		
		return $userInvCode->getInviteCode()->getUser();
	}

	
	/**
	 *	Get fullname or company name
	 *  
	 *  @param Art_Model_User	$user
	 *	@return string
	 */
	static function getFullnameOrCompanyName ( $user )
	{
		if ( NULL === $user || !$user->isLoaded() )
		{
			return null;
		}
		
		if ( static::isUserRepresentsCompany($user) )
		{
			return static::getCompanyAddress($user)->company_name;
		}
		else
		{
			return $user->fullname;
		}
	}
	
		
	/**
	 *	Get manager for $user
	 * 
	 *  @param string	$user		
	 *	@return Art_Model_User
	 */
	static function getManagerForUser ( $user )
	{
		if ( NULL === $user || !$user->isLoaded() )
		{
			return null;
		}

		if ( Helper_TBDev::isManager($user) )
		{
			$manager = $user;
		}
		else 				
		{
			$userManager = new User_X_Manager(array('id_user'=>$user->id));
			
			if ( $userManager->isLoaded() )
			{
				$manager = new Art_Model_User($userManager->id_manager);
			}
			else
			{
				$manager = static::getManagerForUser((new User_X_Invite_Code(array('id_user'=>$user->id)))->getInviteCode()->getUser());
			}
			
		}

		return $manager;
	}
	
	
	/**
	 *	Get Users who was invited by $user 
	 *  
	 *  @param Art_Model_User	$user
	 *	@return Art_Model_User[]
	 */
	static function getAllInvitedUsers ( $user )
	{	
		$invitedCodesId = array();
		
		foreach ( Invite_Code::fetchAllPrivileged(array('id_user'=>$user->id)) as $inviteCode )
			/* @var $inviteCode Invite_Code */
		{
			$invitedCodesId[] = $inviteCode->id;
		}
		
		if ( empty($invitedCodesId) )
		{
			return array();
		}
			
		$where = new Art_Model_Db_Where(array('name'=>'id_invite_code', 'value'=>$invitedCodesId, 'relation'=>Art_Model_Db_Where::REL_IN));	

		$users = array();
		
		foreach ( User_X_Invite_Code::fetchAllPrivilegedActive($where) as $userInvCode ) 
			/* @var $userInvCode User_X_Invite_Code */
		{
			$users[] = $userInvCode->getUser();
		}
		
		return $users;
	}
	
	
	/**
	 *	Get Users plus InvCode who was invited by $user 
	 *  
	 *  @param Art_Model_User	$user
	 *	@return Art_Model_User[]
	 */
	static function getAllInvitedUsersWithInvCode ( $user )
	{	
		$invitedCodesId = array();
		
		foreach ( Invite_Code::fetchAllPrivileged(array('id_user'=>$user->id)) as $inviteCode )
			/* @var $inviteCode Invite_Code */
		{
			$invitedCodesId[] = $inviteCode->id;
		}
		
		if ( empty($invitedCodesId) )
		{
			return array();
		}
			
		$where = new Art_Model_Db_Where(array('name'=>'id_invite_code', 'value'=>$invitedCodesId, 'relation'=>Art_Model_Db_Where::REL_IN));	

		$users = array();
		
		foreach ( User_X_Invite_Code::fetchAllPrivilegedActive($where) as $userInvCode ) 
			/* @var $userInvCode User_X_Invite_Code */
		{
			$user = $userInvCode->getUser();
			$user->invCode = $userInvCode->getInviteCode()->code;
			$users[] = $user;
		}
		
		return $users;
	}
	
	
	/**
	 *	Get all authenticated users data
	 *	@param boolean $onlyCompany
	 * 
	 *	@return Art_Model_User_Data[]
	 */	
	static function getAllAuthenticatedUsers( $onlyCompany = false )
	{
		//Get all user data
		$usersData = Art_Model_User_Data::fetchAll();
		
		$authenticatedUsers = array();

		foreach ( $usersData as $value ) /* @var $value Art_Model_User_Data */
		{
			$user = $value->getUser();
			
			if ( !Helper_TBDev::isUserAuthenticated($user) )
			{
				continue;
			}

			if ( (Helper_TBDev::isUserRepresentsCompany($user) && !$onlyCompany) ||
					(!Helper_TBDev::isUserRepresentsCompany($user) && $onlyCompany) )
			{
				continue;
			}
			
			$authenticatedUsers[] = $user;
		}
		
		return $authenticatedUsers;
	}
	
	
	/**
	 *	Is invite code valid 
	 *  
	 *  @param string	$code
	 *	@return boolean
	 */
	static function isInviteCodeValid ( $code )
	{
		$where = new Art_Model_Db_Where(array(array('name'=>'code', 'value'=>$code),array('name'=>'active', 'value'=>1)));

		$invCode = Invite_Code::fetchAll($where);
		
		if ( empty($invCode) )
		{
			return false;
		}
		else
		{
			$invCode = $invCode[0];
		}

		return ($code === $invCode->code);
	}
	
	
	/**
	 *	Get invited code id
	 *  
	 *  @param string	$code
	 *	@return int
	 */
	static function getInvitedCodeId ( $code )
	{
		$where = new Art_Model_Db_Where(array(array('name'=>'code', 'value'=>$code),array('name'=>'active', 'value'=>1)));

		$invCode = Invite_Code::fetchAll($where);
		
		if ( empty($invCode) )
		{
			return null;
		}
		
		return $invCode[0]->id;
	}
	
	
	/**
	 *	Get invited code URL
	 *  
	 *  @param Invite_Code	$code
	 *	@return string
	 */
	static function getInvitedCodeURL($code) {
		$url = Art_Server::getHost() . '/invitation?code=' . $code->code;
		
		return $url;
	}
	
	
	/**
	 *	Get company representant
	 *  
	 *  @param Art_Model_User	$user
	 *	@return Art_Model_User
	 */
	static function getCompanyRepresentant ( $user )
	{
		if ( NULL == $user || !$user->isLoaded() )
		{
			return null;
		}
		
		$representant = null;
		
		$userCompany = User_X_Company::fetchAll(array('id_user'=>$user->id));

		if ( !empty($userCompany) )
		{
			$representant = new Art_Model_User($userCompany[0]->id_company_user);
		}
		
		return $representant;
	}
	
	
	/**
	 *	Get user function in company
	 *  
	 *  @param Art_Model_User	$user
	 *	@param Art_Model_User	$representant
	 *	@return Art_Model_User
	 */
	static function getUserFunctionInCompany( $user, $representant )
	{
		if ( NULL == $user || !$user->isLoaded() || NULL == $representant || !$representant->isLoaded() )
		{
			return null;
		}
		
		$function = null;
		
		$userCompany = User_X_Company::fetchAll(array('id_user'=>$user->id,'id_company_user'=>$representant->id));

		if ( !empty($userCompany) )
		{
			$function = $userCompany[0]->function;
		}
		
		return $function;
	}
	
	
	/**
	 *	Get company address for representant user
	 *  
	 *  @param Art_Model_User	$user
	 *	@return Art_Model_Address
	 */
	static function getCompanyAddress ($user) {
		if (NULL == $user || !$user->isLoaded()) {
			return null;
		}
		
		$user = static::getCompanyRepresentant($user);
		
		if (NULL == $user || !$user->isLoaded()) {
			return null;
		}
		
		$address = Art_Model_Address::fetchAll(array(
			'id_user' => $user->id,
			'id_address_type' => Art_Model_Address_Type::getCompanyId()
		));

		if (!empty($address)) {
			$address = $address[0];
		} else {
			$address = null;	
		}
		
		return $address;
	}
	
	
	/**
	 *	Get delivery address
	 *  
	 *  @param Art_Model_User	$user
	 *	@return Art_Model_Address
	 */
	static function getDeliveryAddress ( $user )
	{
		if ( NULL == $user || !$user->isLoaded() )
		{
			return null;
		}
		
		foreach ($user->getAddresses() as $value) /* @var $value Art_Model_Address */ 
		{
			if ($value->id_address_type == Art_Model_Address_Type::getDeliveryId())
			{
				return $value;
			}
		} 
		
		return null;
	}
	
	
	/**
	 *	Get parsed time interval
	 *  
	 *  @param string $time_interval
	 *	@return array
	 */
	static function getParsedTimeInterval ( $time_interval )
	{
		$timeInterval = array();
		
		$timeInterval['value'] = substr($time_interval, 0, strlen($time_interval)-1);
		$timeInterval['type'] = substr($time_interval, -1);
		
		return $timeInterval;
	}
	
	
	/**
	 *	Set time interval from value and type
	 *  
	 *  @param int		$value
	 *  @param string	$type
	 *	@return string
	 */
	static function setTimeInterval ( $value, $type )
	{
		$timeInterval = $value . $type;
		
		return $timeInterval;
	}
	
	
	/**
	 *	Get all fa icons for Services
	 * 
	 *	@static
	 *	@return array
	 */
	static function getAllServicesFaIcons()
	{
		
		$icons = array();
		
		foreach (self::FA_ICONS as $value)
		{
			$icons[] = array('name'=>$value);
		}
		
		return $icons;
	}
		
	
	/**
	 *	Get know if $user represents company
	 * 
	 *	@param Art_Model_User $user
	 *	@static
	 *	@return boolean
	 */
	static function isUserRepresentsCompany($user)
	{
		$isCompany = false;
		
		if ( NULL !== $user && $user->isLoaded() )
		{
			$userCompany = User_X_Company::fetchAll(array('id_user'=>$user->id));
			
			if ( !empty($userCompany) )
			{
				$isCompany = true;
			}
		}

		return $isCompany;
	}
	

	/**
	 *	Get bank account numbers for $user
	 * 
	 *	@param Art_Model_User $user
	 *	@static
	 *	@return User_X_Bank_Account
	 */
	static function getBankAccountNumbers($user)
	{
		if ( NULL == $user || !$user->isLoaded() )
		{
			return null;
		}
		
		$bankAccounts = User_X_Bank_Account::fetchAll(array('id_user'=>$user->id));
			
		if ( empty($bankAccounts) )
		{
			return null;
		}
		
		return $bankAccounts;			
	}
	
	
	/**
	 *	Get bank account number for bank account
	 * 
	 *	@param User_X_Bank_Account $bankAccount
	 *	@static
	 *	@return string
	 */
	static function getBankAccountNumber($bankAccount)
	{
		if ( !$bankAccount->isLoaded() )
		{
			return null;
		}
		
		return (($bankAccount->prefix) ? $bankAccount->prefix.'-' : null).$bankAccount->basic.'/'.$bankAccount->bank_code;
	}
	
	/**
	 *	Get company name for user
	 *	@param Art_Model_User $user
	 * 
	 *	@static
	 *	@return boolean
	 */
	static function getCompanyNameForUser($user)
	{
		$companyName = null;
		
		if ( NULL !== $user && $user->isLoaded() )
		{
			$companyAddress = static::getCompanyAddress($user);

			$companyName = $companyAddress->company_name;
		}

		return $companyName;
	}
	
	
	/**
	 *	Get property from objects in array
	 *
	 *	@param array of objects $arrayOfObjects
	 *	@return array of property
	 */
	static function getPropertyFromObjectsInArray ( $arrayOfObjects, $property )
	{	
		$result = array();
		
		if ( is_array($arrayOfObjects) )
		{
			foreach ($arrayOfObjects as $value) 
			{
				if ( is_object($value) )
				{
					if ( property_exists($value, $property) )
					{
						$result[] = $value->$property;
					}
				}
			}
		}
		else if ( is_object($arrayOfObjects) )
		{
			$result[] = $arrayOfObjects->$property;
		}
		
		return $result;
	}	
	
	
	/**
	 *	Sort services
	 *
	 *	@param array of Service $services
	 *	@return boolean
	 */
	static function sortServices(&$services) {
		usort($services, 'static::_sort');
	}
	
	static function _sort($a, $b) { return $b->sort < $a->sort; }
	
	/**
	 *	Is service investment service
	 *
	 *	@param Service $service
	 *	@return boolean
	 */
	static function isServiceInvestment ( $service )
	{	
		if ( NULL !== $service && $service->isLoaded() )
		{
			return ($service->type === self::INVESTMENT_TYPE);
		}
		return false;
	}
	
	
	/**
	 *	Get parsed Service settings property
	 *
	 *	@param Service $service
	 *	@return Service
	 */
	static function parseServiceSettings ( &$service )
	{	
		if ( NULL !== $service && $service->isLoaded() && 
					property_exists($service, 'settings') &&
						!empty($service->settings) )
		{
			$settings = json_decode($service->settings, true);

			$service->icon = isset($settings['icon']) ? $settings['icon'] : null;
			$service->article = isset($settings['article']) ? $settings['article'] : null;			
			$service->promo = isset($settings['promo']) ? $settings['promo'] : null;
			$service->conditions = isset($settings['conditions']) ? $settings['conditions'] : null;
		}
	}
	
	
	/**
	 *	Is user part of group
	 *
	 *	@param Art_Model_User $user
	 *	@param Art_Model_User_Group $group
	 *	@return boolean
	 */
	static function isUserPartOfGroup ( $user, $group )
	{
		$return = false;
		
		if ( NULL !== $user && $user->isLoaded() && NULL !== $group )
		{
			if ( $group->isLoaded() )
			{
				$userGroup = Art_Model_User_X_User_Group::fetchAll(array('id_user'=>$user->id,'id_user_group'=>$group->id));
				
				if ( !empty($userGroup) )
				{
					$return = true;
				}
			}
			else
			{
				$groupNamed = new Art_Model_User_Group(array('name'=>$group));
				
				if ( $groupNamed->isLoaded() )
				{
					$userGroup = Art_Model_User_X_User_Group::fetchAll(array('id_user'=>$user->id,'id_user_group'=>$groupNamed->id));
					
					if ( !empty($userGroup) )
					{
						$return = true;
					}
				}
			}
		}
		
		return $return;
	}
	
	
	/**
	 *	Get sort by
	 *
	 *	@param nullable int(s)
	 *	@return int	Most right win - 1, null, 0 => 4
	 *							   - 1, null, null => 1
	 */
	static function getSortBy () {
		$i = 0;
		$sort = -1;
		
		foreach (func_get_args() as $k => $value) {
			if (NULL !== $value) {
				if (0 == $value) {
					$sort = $i;
				} else {
					$sort = $i + 1;
				}
			}
			
			$i += 2;

			// p($i);
		}

		return $sort;
	}
		
	
	/**
	 *	Get sorted array according to objects property
	 *
	 *	@param array $array
	 *	@param string $customSorter
	 *	@return array
	 */
	static function getSortedArray ($array, $customSorter) {
		usort($array, 'static::_' . $customSorter);
		return $array;
	}

	static function convertForSort($a, $b) {
		static $czechCharsS = array('Á', 'Č', 'Ď', 'É', 'Ě' , 'Ch' , 'Í', 'Ň', 'Ó', 'Ř', 'Š', 'Ť', 'Ú', 'Ů' , 'Ý', 'Ž', 'á', 'č', 'ď', 'é', 'ě' , 'ch' , 'í', 'ň', 'ó', 'ř', 'š', 'ť', 'ú', 'ů' , 'ý', 'ž');
		static $czechCharsR = array('AZ','CZ','DZ','EZ','EZZ','HZZZ','IZ','NZ','OZ','RZ','SZ','TZ','UZ','UZZ','YZ','ZZ','az','cz','dz','ez','ezz','hzzz','iz','nz','oz','rz','sz','tz','uz','uzz','yz','zz');

		$aa = str_replace($czechCharsS, $czechCharsR, $a);
		$bb = str_replace($czechCharsS, $czechCharsR, $b);
		
		return strnatcasecmp($aa, $bb);
	}

	static function _id($a, $b) { return $a->user_number > $b->user_number; }
	static function _idR($a, $b) { return $b->user_number > $a->user_number; }
	static function _firstname($a, $b) { return strcasecmp($a->name, $b->name); }
	static function _firstnameR($a, $b) { return strcasecmp($b->name, $a->name); }
	// static function _surname($a, $b) { return strcasecmp($a->surname, $b->surname); }
	static function _surname($a, $b) { return static::convertForSort($a->surname, $b->surname); }
	// static function _surnameR($a, $b) { return strcasecmp($b->surname, $a->surname); }
	static function _surnameR($a, $b) { return static::convertForSort($b->surname, $a->surname); }
	static function _company_name($a, $b) { return strcasecmp($a->company_name, $b->company_name); }
	static function _company_nameR($a, $b) { return strcasecmp($b->company_name, $a->company_name); }
	static function _membership_from($a, $b) { return strtotime($a->membership_from) > strtotime($b->membership_from); }
	static function _membership_fromR($a, $b) { return strtotime($b->membership_from) > strtotime($a->membership_from); }
	static function _membership_to($a, $b) { return strtotime($a->membership_to) > strtotime($b->membership_to); }
	static function _membership_toR($a, $b) { return strtotime($b->membership_to) > strtotime($a->membership_to); }
	
	static function _value($a, $b) { return $a->value > $b->value; }
	static function _valueR($a, $b) { return $a->value < $b->value; }	
	static function _date($a, $b) { return strtotime($a->date) > strtotime($b->date); }
	static function _dateR($a, $b) { return strtotime($b->date) > strtotime($a->date); }	
	static function _expiry_date($a, $b) { return strtotime($a->expiry_date) > strtotime($b->expiry_date); }
	static function _expiry_dateR($a, $b) { return strtotime($b->expiry_date) > strtotime($a->expiry_date); }
	
	/**
	 *	Generate invite code
	 *
	 *	@return string
	 */
	static function generateInviteCode()
	{
		do {
			$code = Helper_TBDev::getNumRandString(10);
		} while ( (new Invite_Code(array('code'=>$code)))->isLoaded() );
		
		return $code;
	}
		
	
	/**
	 *	Generate user number for user
	 *
	 *	@return int
	 */
	static function generateUserNumber()
	{
		return static::_generateUserNumber('SELECT max(user_number) AS value FROM user WHERE user_number < 50000', 99);
	}
	
	
	/**
	 *	Generate user number for compeny
	 *
	 *	@return int
	 */
	static function generateCompanyNumber()
	{
		return static::_generateUserNumber('SELECT max(user_number) AS value FROM user WHERE user_number > 900000', 900000, 100);
	}
	
	
	/**
	 *	Generate user number for representant of company
	 *
	 *	@return int
	 */
	static function generateCompanyRepresentantNumber()
	{
		return static::_generateUserNumber('SELECT max(user_number) AS value FROM user WHERE user_number < 100000 AND user_number > 50000', 50000);
	}
	
	
	/**
	 *	Generate user number for nonmember
	 *
	 *	@param int $companyNumber
	 *	@return int
	 */
	static function generateNonmemberNumber( $companyNumber )
	{
		return static::_generateUserNumber('SELECT max(user_number) AS value FROM user WHERE user_number > '.$companyNumber.' AND user_number < '.($companyNumber+100));
	}
	
	
	/**
	 *	Generate user number for table user
	 *
	 *	@return int
	 */
	private static function _generateUserNumber ( $query, $default = 0, $increment = 1 )
	{		
		if ( $increment <= 0 )
		{
			$increment = 1;
		}		
		
		$query = Art_Main::db()->query($query);
		$query->execute();

		$data = $query->fetchAll(PDO::FETCH_ASSOC);

		if ( NULL == $data[0]['value'] )
		{
			$value = $default;
		}
		else
		{
			$value = $data[0]['value'];
		}

		do
		{
			$value += $increment;
		} while ( !empty(Art_Model_User::fetchAll(array('user_number'=>$value))) );
		
		return $value;
	}
	
	
	/**
	 *	Initialize DB
	 *
	 *	@param boolean $test_users
	 *	@return void
	 */
	static function initTBDDB ( $test_users = false )
	{	
		Art_Main::db()->query('TRUNCATE TABLE address');
		Art_Main::db()->query('TRUNCATE TABLE dashboard');
		Art_Main::db()->query('TRUNCATE TABLE invite_code');
		Art_Main::db()->query('TRUNCATE TABLE note');
		Art_Main::db()->query('TRUNCATE TABLE review');
		Art_Main::db()->query('TRUNCATE TABLE service');
		Art_Main::db()->query('TRUNCATE TABLE service_payment');
		Art_Main::db()->query('TRUNCATE TABLE service_price');
		Art_Main::db()->query('TRUNCATE TABLE user_group_x_service_price');
		Art_Main::db()->query('TRUNCATE TABLE user_x_invite_code');
		Art_Main::db()->query('TRUNCATE TABLE user_x_manager');
		Art_Main::db()->query('TRUNCATE TABLE user_x_service');
		
		Art_User::createDefaultUser('IUGF5678KJHY');
		
		/* SERVICES */
		
		$serviceM = new Service;
		$serviceM->type = 'membership';
		$serviceM->name = 'Membership';
		$serviceM->settings = '{"icon":"user","promo":"service-membership","conditions":""}';
		$serviceM->save();
		
		$serviceE = new Service;
		$serviceE->type = 'energy';
		$serviceE->name = 'Energie';
		$serviceE->settings = '{"icon":"plug","promo":"service-energy","conditions":"energy-conditions.pdf"}';
		$serviceE->save();
		
		$serviceI = new Service;
		$serviceI->type = 'investment';
		$serviceI->name = 'Investice';
		$serviceI->settings = '{"icon":"pie-chart","promo":"service-investment","conditions":"investment-conditions.pdf"}';
		$serviceI->save();
		
		$serviceT = new Service;
		$serviceT->type = 'tariffs';
		$serviceT->name = 'Tarify';
		$serviceT->settings = '{"icon":"phone","promo":"service-tariffs","conditions":"tariffs-conditions.pdf"}';
		$serviceT->save();
				
		$serviceP = new Service;
		$serviceP->type = 'petrol';
		$serviceP->name = 'Benzín';
		$serviceP->settings = '{"icon":"car","promo":"service-petrol","conditions":"petrol-conditions.pdf"}';
		$serviceP->save();
		
		/* SERVICE PRICES */
		
		$servicePriceM = new Service_Price;
		$servicePriceM->is_default = 1;
		$servicePriceM->price = 100;
		$servicePriceM->time_interval = '1m';
		$servicePriceM->setService($serviceM);
		$servicePriceM->save();		
				
		$servicePriceE = new Service_Price;
		$servicePriceE->is_default = 1;
		$servicePriceE->price = 100;
		$servicePriceE->time_interval = '1m';
		$servicePriceE->setService($serviceE);
		$servicePriceE->save();
		
		$servicePriceI = new Service_Price;
		$servicePriceI->is_default = 1;
		$servicePriceI->price = 100;
		$servicePriceI->time_interval = '1m';
		$servicePriceI->setService($serviceI);
		$servicePriceI->save();
		
		$servicePriceT = new Service_Price;
		$servicePriceT->is_default = 1;
		$servicePriceT->price = 100;
		$servicePriceT->time_interval = '1m';
		$servicePriceT->setService($serviceT);
		$servicePriceT->save();
		
		$servicePriceP = new Service_Price;
		$servicePriceP->is_default = 1;
		$servicePriceP->price = 100;
		$servicePriceP->time_interval = '1m';
		$servicePriceP->setService($serviceP);
		$servicePriceP->save();
				
		/* USER GROUPS */
		
		$userGroup = new Art_Model_User_Group;
		$userGroup->id_rights = 2;
		$userGroup->name = 'Registered';
		$userGroup->save();
		
		$userGroupA = new Art_Model_User_Group;
		$userGroupA->id_rights = 8;
		$userGroupA->name = 'Authorized';
		$userGroupA->save();
		
		$managerUserGroup = new Art_Model_User_Group;
		$managerUserGroup->id_rights = 9;
		$managerUserGroup->name = 'Manager';
		$managerUserGroup->save();
		
		$userGroupE = new Art_Model_User_Group;
		$userGroupE->id_rights = 2;
		$userGroupE->name = 'Energy service members';
		$userGroupE->save();
		
		$userGroupI = new Art_Model_User_Group;
		$userGroupI->id_rights = 2;
		$userGroupI->name = 'Investment service members';
		$userGroupI->save();
		
		$userGroupT = new Art_Model_User_Group;
		$userGroupT->id_rights = 2;
		$userGroupT->name = 'Tariffs service members';
		$userGroupT->save();
		
		$userGroupP = new Art_Model_User_Group;
		$userGroupP->id_rights = 2;
		$userGroupP->name = 'Petrol service members';
		$userGroupP->save();
		
		/* USER GROUP - SERVICE PRICE */
		
		$userGroupServicePrice = new User_Group_X_Service_Price;
		$userGroupServicePrice->time_from = dateSQL();
		$userGroupServicePrice->time_to = dateSQL('+5 years');
		$userGroupServicePrice->setServicePrice($servicePriceM);
		$userGroupServicePrice->setUserGroup($userGroupA);
		$userGroupServicePrice->save();
		
		$userGroupServicePrice = new User_Group_X_Service_Price;
		$userGroupServicePrice->time_from = dateSQL();
		$userGroupServicePrice->time_to = dateSQL('+5 years');
		$userGroupServicePrice->setServicePrice($servicePriceE);
		$userGroupServicePrice->setUserGroup($userGroupE);
		$userGroupServicePrice->save();
		
		$userGroupServicePrice = new User_Group_X_Service_Price;
		$userGroupServicePrice->time_from = dateSQL();
		$userGroupServicePrice->time_to = dateSQL('+5 years');
		$userGroupServicePrice->setServicePrice($servicePriceI);
		$userGroupServicePrice->setUserGroup($userGroupI);
		$userGroupServicePrice->save();
		
		$userGroupServicePrice = new User_Group_X_Service_Price;
		$userGroupServicePrice->time_from = dateSQL();
		$userGroupServicePrice->time_to = dateSQL('+5 years');
		$userGroupServicePrice->setServicePrice($servicePriceT);
		$userGroupServicePrice->setUserGroup($userGroupT);
		$userGroupServicePrice->save();
		
		$userGroupServicePrice = new User_Group_X_Service_Price;
		$userGroupServicePrice->time_from = dateSQL();
		$userGroupServicePrice->time_to = dateSQL('+5 years');
		$userGroupServicePrice->setServicePrice($servicePriceP);
		$userGroupServicePrice->setUserGroup($userGroupP);
		$userGroupServicePrice->save();
		
		/* MANAGERS */
		
		$managers = array();
		
		//tbdm1@leeching.net:tbdmanager
		foreach (array(1,2,3) as $i) 
		{
			$user = new Art_Model_User;
			$user->user_number = 100 + $i;
			$user->active = 1;
			$user->id_currency = 1;	
			$user->save();

			$password = 'tbdmanager';

			$user_data = new Art_Model_User_Data;
			$user_data->name = 'Man';
			$user_data->surname = 'Ager'.$i;
			$user_data->email = 'tbdm'.$i.'@leeching.net';
			$user_data->salt = Art_User::generateSalt();
			$user_data->password = Art_User::hashPassword($password, $user_data->salt);
			$user_data->verif = 1;
			$user_data->setUser($user);
			$user_data->born_day = rand(1, 28);
			$user_data->born_month = rand(1, 12);
			$user_data->born_year = rand(1970, 1995);
			$user_data->pass_changed_date = dateSQL();
			$user_data->save();

			$user_x_user_group = new Art_Model_User_X_User_Group;
			$user_x_user_group->setUser($user);
			$user_x_user_group->setGroup($managerUserGroup);
			$user_x_user_group->save();
			
			/* INV CODES */
			$invCode = new Invite_Code;
			$invCode->active = 1;

			do {
				$code = Helper_TBDev::getAlfanumRandString();
			} while ( (new Invite_Code(array('code'=>$code)))->isLoaded() );
			
			$pom = 'code'.$i;
			$$pom = $code;
			
			$managers[$$pom] = $user;
			
			$invCode->code = $code;
			$invCode->note = 'Initial';
			$invCode->setUser($user);
			$invCode->created_by = $user->id;
			$invCode->save();
		}
		
		/* TEST USERS */
		
		//tbdu1@leeching.net:tbduser
		if ( $test_users )
		{
			foreach (array(array('name'=>'Pavel','surname'=>'První','i'=>'1'),
							array('name'=>'Jiří','surname'=>'Druhý','i'=>'2'),
							array('name'=>'Petr','surname'=>'Třetí','i'=>'3'),
							array('name'=>'Dan','surname'=>'Čtvrtý','i'=>'4'),
							array('name'=>'Robin','surname'=>'Pátý','i'=>'5')) 
					as $value) /* @var $value  */ 
			{
				$user = new Art_Model_User;
				$user->save();

				$userId = $user->id;

				$user->user_number = Art_User::generateUserNumber($userId);
				$user->active = 1;
				$user->id_currency = 1;	
				$user->save();

				$password = 'tbduser';

				$user_data = new Art_Model_User_Data;
				$user_data->name = $value['name'];
				$user_data->surname = $value['surname'];
				$user_data->email = 'tbdu'.$value['i'].'@leeching.net';
				$user_data->salt = Art_User::generateSalt();
				$user_data->password = Art_User::hashPassword($password, $user_data->salt);
				$user_data->verif = 0;
				$user_data->born_day = rand(1, 28);
				$user_data->born_month = rand(1, 12);
				$user_data->born_year = rand(1970, 1995);
				$user_data->setUser($user);
				$user_data->save();
				
				$i = ($value['i'] % 3) + 1;
				$pom = 'code'.$i;
				$code = $$pom;
				
				$inviteCode = new User_X_Invite_Code();
				$inviteCode->id_user = $userId;
				$inviteCode->id_invite_code = Helper_TBDev::getInvitedCodeId($code);
				$inviteCode->save();
				
				$insertUserToGroup = new Art_Model_User_X_User_Group();
				$insertUserToGroup->id_user = $userId;
				$insertUserToGroup->id_user_group = Art_Model_User_Group::getRegisteredId();
				$insertUserToGroup->save();
				
				$insertDeliveryUserAddress = new Art_Model_Address();
				$insertDeliveryUserAddress->city = array('Ostrava','Brno','Praha','Olomouc')[rand(0, 3)];
				$insertDeliveryUserAddress->housenum = rand(50, 350);
				$insertDeliveryUserAddress->zip = rand(11111,99999);
				$insertDeliveryUserAddress->street = array('Ostravská','Brněnská','Pražská','Olomoucká')[rand(0, 3)];
				$insertDeliveryUserAddress->phone = rand(600000000,999999999);
				$insertDeliveryUserAddress->setUser($user);
				$insertDeliveryUserAddress->setType(Art_Model_Address_Type::getDelivery());
				$insertDeliveryUserAddress->id_country = rand(1, 2);
				$insertDeliveryUserAddress->save();
				
				if ( 1 == rand(0, 1) )
				{
					$insertContactUserAddress = new Art_Model_Address();
					$insertContactUserAddress->city = array('Ostrava','Brno','Praha','Olomouc')[rand(0, 3)];
					$insertContactUserAddress->housenum = rand(50, 350);
					$insertContactUserAddress->zip = rand(11111,99999);
					$insertContactUserAddress->street = array('Ostravská','Brněnská','Pražská','Olomoucká')[rand(0, 3)];
					$insertContactUserAddress->setUser($user);
					$insertContactUserAddress->setType(Art_Model_Address_Type::getContact());
					$insertContactUserAddress->id_country = rand(1, 2);
					$insertContactUserAddress->save();
				}
				
				if ( 1 == rand(0, 3) )
				{
					$review = new Review;
					$review->setUser($user);
					$review->note = array('Skvělá stránka', 'Perfektní stránka', 'Úžasná stránka', 'Ohromná stránka')[rand(0, 3)];
					$review->reply = array('', 'Díky', 'Vážíme si toho', '')[rand(0, 3)];
					$review->seen = 0;
					$review->visible = 0;
					$review->save();
				}
				
				if ( 1 == rand(0, 1) )
				{
					$user_data->verif = 1;
					$user_data->verif_date = dateSQL();
					$user_data->verif_id = 1;
					$user_data->pass_changed_date = dateSQL();
					$user_data->save();
					
					$userUserGroup = new Art_Model_User_X_User_Group;
					$userUserGroup->setUser($user);
					$userUserGroup->setGroup(Art_Model_User_Group::getAuthorized());
					$userUserGroup->save();
						
					$userManager = new User_X_Manager();
					$userManager->id_user = $userId;
					$userManager->id_manager = static::getUserInvitedBy($user)->id;
					$userManager->save();
					
					$x = array('E', 'I', 'P', 'T')[rand(0, 3)];

					$userUserGroup = new Art_Model_User_X_User_Group;
					$userUserGroup->setUser($user);
					$userUserGroup->setGroup(${'userGroup'.$x});
					$userUserGroup->save();
					
					if ( 1 == rand(0, 1) )
					{
						$userService = new User_X_Service;
						$userService->activated = 1;
						$userService->activated_date = dateSQL();
						$userService->setUser($user);
						$userService->setService(${'service'.$x});
						$userService->save();
					}
				}
			}
		}
		
		/* TEST FIRM USERS */
		
		//tbdu1@leeching.net:tbduser
		if ( $test_users )
		{
			foreach (array(array('name'=>'Jakub','surname'=>'Šestý','i'=>'6'),
							array('name'=>'Jan','surname'=>'Sedmý','i'=>'7')) 
					as $value) /* @var $value  */ 
			{
				$user = new Art_Model_User;
				$user->save();

				$userId = $user->id;

				$user->user_number = Art_User::generateUserNumber($userId);
				$user->active = 1;
				$user->id_currency = 1;	
				$user->save();

				$password = 'tbduser';

				$user_data = new Art_Model_User_Data;
				$user_data->name = $value['name'];
				$user_data->surname = $value['surname'];
				$user_data->email = 'tbdu'.$value['i'].'@leeching.net';
				$user_data->salt = Art_User::generateSalt();
				$user_data->password = Art_User::hashPassword($password, $user_data->salt);
				$user_data->verif = 0;
				$user_data->born_day = rand(1, 28);
				$user_data->born_month = rand(1, 12);
				$user_data->born_year = rand(1970, 1995);
				$user_data->setUser($user);
				$user_data->save();
				
				$i = ($value['i'] % 3) + 1;
				$pom = 'code'.$i;
				$code = $$pom;
				
				$inviteCode = new User_X_Invite_Code();
				$inviteCode->id_user = $userId;
				$inviteCode->id_invite_code = Helper_TBDev::getInvitedCodeId($code);
				$inviteCode->save();
				
				$insertUserToGroup = new Art_Model_User_X_User_Group();
				$insertUserToGroup->id_user = $userId;
				$insertUserToGroup->id_user_group = Art_Model_User_Group::getRegisteredId();
				$insertUserToGroup->save();
								
				$userManager = new User_X_Manager();
				$userManager->id_user = $userId;
				$userManager->id_manager = static::getUserInvitedBy($user)->id;
				$userManager->save();
				
				$insertDeliveryUserAddress = new Art_Model_Address();
				$insertDeliveryUserAddress->city = array('Ostrava','Brno','Praha','Olomouc')[rand(0, 3)];
				$insertDeliveryUserAddress->housenum = rand(50, 350);
				$insertDeliveryUserAddress->zip = rand(11111,99999);
				$insertDeliveryUserAddress->street = array('Ostravská','Brněnská','Pražská','Olomoucká')[rand(0, 3)];
				$insertDeliveryUserAddress->phone = rand(600000000,999999999);
				$insertDeliveryUserAddress->setUser($user);
				$insertDeliveryUserAddress->setType(Art_Model_Address_Type::getDelivery());
				$insertDeliveryUserAddress->id_country = rand(1, 2);
				$insertDeliveryUserAddress->company_name = array('Company One', 'Company Two')[$value['i']-6];
				$insertDeliveryUserAddress->ico = rand(100000000,999999999);
				$insertDeliveryUserAddress->dic = 'CZ'.rand(100000000,999999999);;
				$insertDeliveryUserAddress->save();
				
				if ( 1 == rand(0, 1) )
				{
					$user_data->verif = 1;
					$user_data->verif_date = dateSQL();
					$user_data->verif_id = 1;
					$user_data->pass_changed_date = dateSQL();
					$user_data->save();

					$userUserGroup = new Art_Model_User_X_User_Group;
					$userUserGroup->setUser($user);
					$userUserGroup->setGroup(Art_Model_User_Group::getAuthorized());
					$userUserGroup->save();

					$x = array('E', 'I', 'P', 'T')[rand(0, 3)];

					$userUserGroup = new Art_Model_User_X_User_Group;
					$userUserGroup->setUser($user);
					$userUserGroup->setGroup(${'userGroup'.$x});
					$userUserGroup->save();

					if ( 1 == rand(0, 1) )
					{
						$userService = new User_X_Service;
						$userService->activated = 1;
						$userService->activated_date = dateSQL();
						$userService->setUser($user);
						$userService->setService(${'service'.$x});
						$userService->save();
					}
				}
			}
		}
		
		/* DASHBOARD */
		
		$dashboard = new Dashboard;
		$dashboard->body = 'Obligátní zpráva';
		$dashboard->header = '';
		$dashboard->important = 0;
		$dashboard->save();
		
		$dashboard = new Dashboard;
		$dashboard->body = 'Super clanek na nastence.';
		$dashboard->header = 'Koukni';
		$dashboard->important = 1;
		$dashboard->save();
		
		$dashboard = new Dashboard;
		$dashboard->body = 'Hrajte!';
		$dashboard->header = 'Chcete vyhrát?';
		$dashboard->important = 0;
		$dashboard->save();
	}
}
