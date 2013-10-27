<?php

/**
 * Fertu url router & MVC app manager
 * 
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 */
namespace Fertu;
use Exception,
	BasicObject as Object,
	PrestoEngine\Template,
	ReflectionClass;

/**
 * Vynimka, ktora ma generovat error stranku a hodit HTTP status
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 */
class HttpException extends Exception {

	/**
	 * Upraveny konstruktor, cislo vynimky je na prvom mieste
	 * @param int $status Cislo hodenej vynimky
	 * @param string $message Pomenovanie vynimky
	 */
	public function __construct($status = 500, $message = '') {

		parent::__construct($message ?: $this->getStatusMessageByCode($status), $status);
	}

	/**
	 * Na zaklade kodu hodenej vynimky vrati jej http nazov
	 * @param int $code
	 * @return string
	 */
	public static function getStatusMessageByCode($code) {

		switch($code) {

			case 400:	return 'Bad Request';
			case 401:	return 'Unauthorized';
			case 402:	return 'Payment Required';
			case 403:	return 'Forbidden';
			case 404:	return 'Not Found';
			case 405:	return 'Method Not Allowed';
			case 406:	return 'Not Acceptable';
			case 407:	return 'Proxy Authentication Required';
			case 408:	return 'Request Timeout';
			case 409:	return 'Conflict';
			case 410:	return 'Gone';
			case 411:	return 'Length Required';
			case 412:	return 'Precondition Failed';
			case 413:	return 'Request Entity Too Large';
			case 414:	return 'Request-URI Too Long';
			case 415:	return 'Unsupported Media Type';
			case 416:	return 'Requested Range Not Satisfiable';
			case 417:	return 'Expectation Failed';
			case 500:	return 'Internal Server Error';
			case 501:	return 'Not Implemented';
			case 502:	return 'Bad Gateway';
			case 503:	return 'Service Unavailable';
			case 504:	return 'Gateway Timeout';
			case 505:	return 'HTTP Version Not Supported';
			default:	return '';
		}
	}
}

/**
 * Trieda samotneho routra
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 * @property-read Fertu\HttpRequest $request Aktivny request na server
 */
class Router extends Object {

	/**
	 * Akcie, ktore su volane podla routovania
	 * @var Fertu\Action[]
	 */
	protected $actions;

	/**
	 * Aktivny http request
	 * @var Fertu\HttpRequest
	 */
	protected $request;

	/**
	 * Konstruktor routra
	 * @param array $actions Zoznam akcii, ktore sa defaultne pridaju
	 */
	public function __construct(array $actions = array()) {

		$this->request = new HttpRequest;

		foreach($actions as $action) {

			$this->addAction($action);
		}
	}

	/**
	 * Getter pre request
	 * @return Fertu\HttpRequest
	 */
	public function getRequest() {

		return $this->request;
	}

	/**
	 * Pridanie novej akcie do routra
	 * 
	 * Tato metoda je uniformna, nakolko
	 * jej prvy argument moze byt priamo instancia
	 * akcie {@link Fertu\Action}, pole s nastaveniami
	 * danej akcie (obsahuje kluc class alebo sa vytvori
	 * automaticky {@link Fertu\ControllerAction})
	 * alebo iba len string a automaticky
	 * sa iba inicializuje {@link Fertu\ControllerAction}.
	 * 
	 * @param \Fertu\Action|string|array $action Akcia
	 * @return \Fertu\Router Instancia sameho seba
	 * @support Method-Chaining
	 * @throws Exception Ak akcia nemoze byt vytvorena
	 */
	public function addAction($action) {

		if($action instanceof Action) {

			return $this->addActionInstance($action);
		}

		if(is_string($action) && !is_numeric($action)) {

			return $this->makeControllerAction($action);
		}

		if(is_array($action) && !isset($action['class'])) {

			$pattern = isset($action['pattern']) ? $action['pattern'] : current($action);
			return $this->makeControllerAction($pattern, $action);
		}

		if(is_array($action)) {

			$class = $action['class'];
			$instance = new $class($this);

			if($instance instanceof Action && $instance instanceof Object) {

				unset($action['class']);
				$instance->attributes = $action;
				return $this->addAction($instance);
			}
		}

		throw new Exception('Not valid action');
	}

	/**
	 * Prida novu akciu
	 * @param \Fertu\Action $action
	 * @return \Fertu\Router Instancia sameho seba
	 * @support Method-Chaining
	 */
	public function addActionInstance(Action $action) {

		$this->actions[] = $action;
		return $this;
	}

	/**
	 * Prida novu akciu
	 * @param \Fertu\Action $action
	 * @return \Fertu\Router Instancia sameho seba
	 * @support Method-Chaining
	 */
	public function makeControllerAction($pattern, array $params = array()) {

		$action = new ControllerAction($this, $pattern);
		$action->attributes = $params;
		return $this->addAction($action);
	}

	/**
	 * Spustenie samotneho routovania (zbehnutie akcie)
	 * @return mixed Vysledok akcie
	 * @throws HttpException Ak ziadna akcia nie je vyhodnotena ako spravna
	 */
	public function run() {

		foreach($this->actions as $action) {

			if($action->compare($this->request)) {

				return $action->execute();
			}
		}

		throw new HttpException(404);
	}

	/**
	 * Vytvori link na akciu na zaklade parametrov
	 * @param array $params
	 * @return string
	 */
	public function createUrl(array $params) {

		foreach($this->actions as $action) {

			$url = $action->createUrl($params);

			if($url !== null) {

				return $url;
			}
		}

		return '';
	}
}

/**
 * Trieda reprezentujuca aktualny request na stranku
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 * @property-read string $serverName Adresa volaneho servera
 * @property-read string $requestUri Cela zavolana adresa v ramci servera
 * @property-read string $scriptName Cesta k skriptu, ktora spustila router
 * @property-read string $baseUrl Cesta v ramci servera ku korenu webu
 * @property-read string $routeUrl Relativna cesta, vhodna na samotne routovanie
 * @property-read string $requestMethod Metoda, akou sa vola http request
 * @property-read string $serverProtocol Protokol pouzivany na serveri
 * @property-read string $scriptFilename Absolutna adresa spusteneho suboru
 * @property-read string $appUrl Absolutna adresa samotnej aplikacie
 * @property-read string $visitorIp IP Adresa navstevnika
 * @property-read string $visitorBrowser Prehliadac navstevnika
 * @property-read boolean $isGet ide o GET request?
 * @property-read boolean $isPost ide o POST request?
 * @property-read boolean $isPut ide o PUT request?
 * @property-read boolean $isPatch ide o PATCH request?
 * @property-read boolean $isDelete ide o DELETE request?
 * @property-read int $serverPort Port volaneho servera
 */
class HttpRequest extends Object {

	/**
	 * Vrati zavolanu adresu v ramci servera
	 * @return int
	 */
	public function getRequestUri() {

		return filter_input(INPUT_SERVER, 'REQUEST_URI');
	}

	/**
	 * Vrati zavolanu adresu v ramci servera
	 * @return int
	 */
	public function getScriptName() {

		return filter_input(INPUT_SERVER, 'SCRIPT_NAME');
	}

	/**
	 * Vrati zavolanu adresu v ramci servera
	 * @return int
	 */
	public function getBaseUrl() {

		return dirname($this->scriptName);
	}

	/**
	 * Vrati zavolanu adresu v ramci servera
	 * @return int
	 */
	public function getRouteUrl() {

		$requestUri		= dirname($this->scriptName);
		$redirectedUrl	= filter_input(INPUT_SERVER, 'REDIRECT_URL');
		return $redirectedUrl ? substr($redirectedUrl, strlen($requestUri) + 1) : ltrim($this->requestUri, '/');
	}

	/**
	 * Vrati port volaneho servera
	 * @return int
	 */
	public function getServerPort() {

		return filter_input(INPUT_SERVER, 'SERVER_PORT', FILTER_SANITIZE_NUMBER_INT);
	}

	/**
	 * Vrati adresu volaneho servera
	 * @return string
	 */
	public function getServerName() {

		return filter_input(INPUT_SERVER, 'SERVER_NAME');
	}

	/**
	 * Vrati http "metodu"
	 * @return string
	 */
	public function getRequestMethod() {

		return filter_input(INPUT_SERVER, 'REQUEST_METHOD');
	}

	/**
	 * Vrati pouzivany protokol servera
	 * @return string
	 */
	public function getServerProtocol() {

		return filter_input(INPUT_SERVER, 'SERVER_PROTOCOL');
	}

	/**
	 * Vrati absolutnu adresu spusteneho suboru
	 * @return string
	 */
	public function getScriptFilename() {

		return filter_input(INPUT_SERVER, 'SCRIPT_FILENAME');
	}

	/**
	 * Vrati absolutnu adresu spustenej aplikacie
	 * @return string
	 */
	public function getAppUrl() {

		return dirname($this->getScriptFilename()) . DIRECTORY_SEPARATOR;
	}

	/**
	 * Ziska IP adresu navstevnika
	 * @return string
	 */
	public function getVisitorIp() {

		return filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
	}

	/**
	 * Ziska meno, verziou prehliadaca navstevnika
	 * @return string
	 */
	public function getVisitorBrowser() {

		return filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
	}

	/**
	 * Ziska niektory z url argumentov
	 * @param string $name
	 * @param string $default
	 * @param int $filter
	 * @return string
	 */
	public function getParam($name, $default = null, $filter = FILTER_DEFAULT) {

		return (isset($_GET[$name]) ? filter_var($_GET[$name], $filter) : $default) ?: $default;
	}

	/**
	 * Setovanie argumentov do URL
	 * @param type $name
	 * @param type $value
	 */
	public function setParam($name, $value) {

		$_GET[$name] = $value;
	}

	/**
	 * Prelinkovanie na {@link Fertu\HttpRequest::getParam()}
	 * @param string $name Kluc argumentu
	 * @return string
	 */
	public function __get($name) {

		try {

			return parent::__get($name);
		}
		catch(Exception $ex) {

			return $this->getParam($name);
		}
	}

	/**
	 * Prelinkovanie na {@link Fertu\HttpRequest::setParam()}
	 * @param string $name Kluc argumentu
	 * @param mixed $value Nova hodnota
	 */
	public function __set($name, $value) {

		try {

			return parent::__set($name);
		}
		catch(Exception $ex) {

			return $this->setParam($name);
		}
	}

	/**
	 * Zisti, ci ide o GET request
	 * @return boolean
	 */
	public function getIsGet() {

		return $this->requestMethod == 'GET';
	}

	/**
	 * Zisti, ci ide o POST request
	 * @return boolean
	 */
	public function getIsPost() {

		return $this->requestMethod == 'POST';
	}

	/**
	 * Zisti, ci ide o PUT request
	 * @return boolean
	 */
	public function getIsPut() {

		return $this->requestMethod == 'PUT';
	}

	/**
	 * Zisti, ci ide o PATCH request
	 * @return boolean
	 */
	public function getIsPatch() {

		return $this->requestMethod == 'PATCH';
	}

	/**
	 * Zisti, ci ide o DELETE request
	 * @return boolean
	 */
	public function getIsDelete() {

		return $this->requestMethod == 'DELETE';
	}

	/**
	 * Na zaklade regexp pattern nasupuje premenne do GET
	 * @param string $pattern
	 * @return boolean|array
	 */
	public function fetchVariables($pattern) {

		if(!preg_match($pattern, $this->getRouteUrl(), $matches)) {

			return false;
		}

		$variables = array();

		foreach($matches as $key => $value) {

			if(!is_numeric($key)) {

				$variables[$key] = $value;
				$this->setParam($key, $value);
			}
		}

		return $variables;
	}
}

/**
 * Interface, ktory musia vsetky akcie dodrzat
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 */
interface Action {

	/**
	 * Pri konstruktore akcie sa predava link na router, ktoremu akcia patri
	 * @param Fertu\Router $router Instancia routra, ktory vola akciu
	 */
	public function __construct(Router $router);

	/**
	 * Sedi aktualny request na stranku s touto akciou?
	 * @param Fertu\HttpRequest $request Zavolany request
	 * @return boolean
	 */
	public function compare(HttpRequest $request);

	/**
	 * Vytvor url pre danu akciu na zaklade paramtrov
	 * @param array $param
	 * @param Fertu\Router $router Instancia routra, ktory vola akciu
	 * @return string|null Vrati adresu pre danu akciu alebo NULL
	 */
	public function createUrl(array $params);

	/**
	 * Vytvor url pre danu akciu na zaklade paramtrov
	 * @param \Fertu\Router $router Instancia routra, ktory vola akciu
	 */
	public function execute();
}

/**
 * Pomocne metody akcie
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 */
abstract class FertuAction extends Object implements Action {

	/**
	 * Pripravi regexp pattern na zaklade nasho patternu :)
	 * @param string $subject
	 * @return string;
	 */
	protected function preparePattern($subject) {

		$pattern = '';

		foreach(preg_split('#(\{[^}]+\})#', $subject, -1, PREG_SPLIT_DELIM_CAPTURE) as $value) {

			if(substr($value, 0, 1) == '{' && substr($value, -1) == '}') {

				$pattern .= $this->analyzeSubPattern($value);
			}
			else {

				$pattern .= preg_quote($value);
			}
		}

		return sprintf('#^%s$#', $pattern);
	}

	/**
	 * Analyzuje subpattern
	 * @param string $pattern
	 * @return string
	 */
	protected function analyzeSubPattern($pattern) {

		if(preg_match('#^\{\$(\w+)(\:([^}]+))?\}$#', $pattern, $matches)) {

			$pattern = isset($matches[3]) && $matches[3] ? $matches[3] : '\w+';
			return sprintf('(?P<%s>%s)', $matches[1], $pattern);
		}

		switch($pattern) {

			case '{seo}': return '[a-z0-9-]+';
			default: return preg_quote($pattern);
		}
	}
}

class ControllerAction extends FertuAction {

	/**
	 * Relativna adresa ku controllerom - obsahuje masku %s, za ktoru bude doplneny nazov konkretneho controllera
	 */
	const CONTROLLER_RELATIVE_PATH = 'admin/frontend/%s.php';

	/**
	 * Cesta ku controllerom aplikacie
	 * @var string
	 */
	protected $controllersPath;

	/**
	 * Router, ktoru je tento analyzator akcii pripradeny
	 * @var Fertu\Router
	 */
	protected $router;

	/**
	 * Pattern, na zaklade ktoreho sa budu vyhodnocovat adresy
	 * @var string
	 */
	public $pattern;

	/**
	 * Controller, na ktory smeruje akcie
	 * @var string
	 */
	public $controller = 'site';

	/**
	 * Nazov samotnej akcie
	 * @var string
	 */
	public $action = 'index';

	/**
	 * Cesta ku controllerom aplikacie
	 * @var Fertu\Controller
	 */
	protected $controllerInstance;

	/**
	 * Cesta ku controllerom aplikacie
	 * @var Fertu\Controller
	 */
	protected $reflectionMethod;

	/**
	 * Konstruktor
	 * @param \Fertu\Router $router Router, ktoru patri action manager
	 * @param string $pattern
	 */
	public function __construct(Router $router, $pattern = '') {

		$this->router			= $router;
		$this->pattern			= $this->preparePattern($pattern);
		$this->controllersPath	= $router->request->appUrl . self::CONTROLLER_RELATIVE_PATH;
	}

	/**
	 * Vyhodnoti, ci aktualny request na stranku moze byt vykonany
	 * @param \Fertu\HttpRequest $request
	 */
	public function compare(HttpRequest $request) {

		if(false === ($values = $request->fetchVariables($this->pattern))) {

			return false;
		}

		$this->controller	= $request->getParam('controller',	$this->controller);
		$this->action		= $request->getParam('action',		$this->action);
		$controller			= $this->prepareController();

		if($this->canRun($controller, $request, $this->action)) {

			$this->controllerInstance = $controller;
			return true;
		}

		return false;
	}

	public function createUrl(array $params) {
		
	}

	public function execute() {

		$args = array();

		foreach($this->reflectionMethod->getParameters() as $parameter) {

			$args[$parameter->getName()] = $this->router->request->getParam(
				$parameter->getName(),
				$parameter->isOptional()
					? $parameter->getDefaultValue()
					: null
			);
		}

		return $this->reflectionMethod->invokeArgs($this->controllerInstance, $args);
	}

	public function canRun($controller, HttpRequest $request, $action = null) {

		if(!is_object($controller)) {
			
			return false;
		}

		$action		= ucfirst($action ?: $this->action);
		$reflection	= new ReflectionClass($controller);

		if(false !== ($method = $this->analyzeMethod($reflection, strtolower($request->getRequestMethod()) . 'Action' . $action, $request))) {

			$this->reflectionMethod = $method;
			return true;
		}

		if(false !== ($method = $this->analyzeMethod($reflection, 'action' . $action, $request))) {

			$this->reflectionMethod = $method;
			return true;
		}

		return false;
	}

	/**
	 * Pripravi pozadovany controller
	 * @return \Fertu\Controller
	 */
	protected function prepareController() {

		$classname	= ucfirst($this->controller);
		$filename	= sprintf($this->controllersPath, $classname);

		if(!file_exists($filename)) {

			return null;
		}

		require_once($filename);

		if(!class_exists($classname, false)) {

			return null;
		}

		return new $classname($this->router);
	}

	/**
	 * Zanalyzuje, ci dana metoda/akcia moze byt zavolana
	 * @param \ReflectionClass $reflection Meta info o classe
	 * @param string $method Metoda, ktoru chceme preverit
	 * @return \ReflectionMethod|boolean
	 */
	protected function analyzeMethod(ReflectionClass $reflection, $method, HttpRequest $request) {

		if(!$reflection->hasMethod($method)) {

			return false;
		}

		$method = $reflection->getMethod($method);

		if(!$method->isPublic()) {

			return false;
		}

		foreach($method->getParameters() as $parameter) {

			$param = $request->getParam($parameter->getName(), null);

			if($param === null && !$parameter->isOptional()) {

				return false;
			}
		}

		return $method;
	}
}

/**
 * Nas bozsky Controller
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 * @property-read boolean $isInitialized Bol controller uz initializovany?
 */
abstract class Controller extends Object {

	/**
	 * Bol controller uz initializovany?
	 * @var boolean 
	 */
	protected $isInitialized = false;

	/**
	 * Ktoremu routru patri tento controller?
	 * @var Fertu\Router
	 */
	protected $router;

	/**
	 * Templatovaci Engine pre vykreslovanie stranok
	 * @var \PrestoEngine\Template
	 */
	protected $template;

	/**
	 * Buildovanie noveho controllera, predanie referencie na router
	 * @param \Fertu\Router $router Router, ktory si dany controller vyziadal
	 */
	public function __construct(Router $router) {

		$this->router = $router;
	}

	/**
	 * Bol controller uz initializovany
	 * @return boolean
	 */
	public function getIsInitialized() {

		return $this->isInitialized;
	}

	/**
	 * Pripravi controller na spustenie akcie
	 * 
	 * Okrem toho by sa mal vzdy prepnut flag
	 * o inicializovani controllera
	 */
	public function initiate() {

		$this->isInitialized = true;
	}

	/**
	 * Vyrenderuje pozadovany view s celym layoutom na zaklade predanych premennych
	 * @param string $view Nazov viewu, ktory sa ma vykreslit
	 * @param array $params Premenne, podla ktorych sa ma view vykreslit
	 * @param boolean $return Vrati vysledok
	 */
	public function render($view, array $params = array(), $return = false) {

		$this->template->render($view, $params, $return);
	}

	/**
	 * Vyrenderuje pozadovany view s predanymi premennymi
	 * @param string $view Nazov viewu, ktory sa ma vykreslit
	 * @param array $params Premenne, podla ktorych sa ma view vykreslit
	 * @param boolean $return Vrati vysledok
	 */
	public function renderPartial($view, array $params = array(), $return = false) {

		$this->template->renderPartial($view, $params, $return);
	}

	/**
	 * Vygeneruje link na pozadovanu akciu
	 * @param string $shortcut Priamy link na akciu controllera "controller/action"
	 * @param array $params Parametre, na zaklade ktorych vygenerovat linku
	 * @return string
	 */
	public function createUrl($shortcut, array $params = array()) {

		if(!preg_match('#^(?P<controller>\w+\/)?(?P<action>\w+)$#', trim($shortcut, ' /'), $matches)) {

			throw new Exception('Not valid link to controller');
		}

		$params['action']		= $matches['action'];
		$params['controller']	= rtrim($matches['controller'] ?: lcfirst(get_class($this)), '/');

		return $this->router->createUrl($params);
	}

	/**
	 * Vygeneruje absolutny link na akciu
	 * @param string $shortcut Priamy link na akciu controllera "controller/action"
	 * @param array $params Parametre, na zaklade ktorych vygenerovat linku
	 * @return string
	 */
	public function createAbsoluteUrl($shortcut, array $params = array()) {

		return $this->router->request->getBaseUrl() . $this->createUrl($shortcut, $params);
	}
}