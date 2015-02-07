<?php

/**
 * Ziska public properties danej triedy
 * @param object $me
 * @return string[]
 */
function getPublicProperties($me) {

	return array_keys(get_object_vars($me));
}

/**
 * Zakladny objekt pre cely template engine
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 * @property array $attributes Magicka property reprezentujuca vsetky attributy daneho objektu
 */
class BasicObject {

	/**
	 * Polia, v ktorych sa kontroluje existencia volanych properties
	 * @var string[] 
	 */
	protected $_s = array();

	/**
	 * Basic getter
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {

		$getter = 'get' . ucfirst($name);

		if(method_exists($this, $getter)) {

			return call_user_func_array(array($this, $getter), array());
		}

		foreach($this->_s as $stack) {

			if(isset($this->{$stack}[$name])) {

				return $this->{$stack}[$name];
			}
		}

		$this->triggerPropertyError($name);
	}

	/**
	 * Basic setter
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {

		$setter = 'set' . ucfirst($name);

		if(method_exists($this, $setter)) {

			return call_user_func_array(array($this, $setter), array($value));
		}

		foreach($this->_s as $stack) {

			if(isset($this->{$stack}[$name])) {

				return $this->{$stack}[$name] = $value;
			}
		}

		$this->triggerPropertyError($name);
	}

	/**
	 * Masivne setnutie attributov
	 * @param array $attributes
	 */
	public function setAttributes(array $attributes) {

		$publics = getPublicProperties($this);

		foreach($attributes as $key => $value) {
			
			if(is_numeric($key)) {

				continue;
			}

			if(in_array($key, $publics)) {

				$this->$key = $value;
			}
			else {

				$this->__set($key, $value);
			}
		}
	}

	/**
	 * Masivne ziskavanie atributov
	 * @return array
	 */
	public function getAttributes() {

		$attributes	= array();

		foreach(getPublicProperties($this) as $name) {

			$attributes[$name] = $this->$name;
		}

		foreach($this->_s as $stack) {

			if(is_array($this->$stack)) {

				$attributes += $this->$stack;
			}
		}

		return $attributes;
	}

	/**
	 * Hodi chybu o neexistencii volanej property
	 * @param type $name
	 */
	protected function triggerPropertyError($name) {

		throw new Exception(sprintf('Class %s does not have %s property', get_class($this), $name), E_USER_WARNING);
	}
}

/**
 * Classa reprezentujuca samotny system/framework
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 * @property-read string[] $labsProjects Zoznam Labs projektov
 * @property-read PrestoEngine\Template $template Instancia motivu
 * @property-read Fertu\Router $router Instancia routra
 */
class System extends BasicObject {

	/**
	 * Nazov redakcneho systemu
	 */
	const NAME = 'Opiner CMS';

	/**
	 * Verzia redakcneho systemu
	 */
	const VERSION = '1.7 Lorraine';

	/**
	 * URL adresa stranok redakcneho systemu
	 */
	const URL = 'http://opiner.tatarko.sk';

	/**
	 * Meno autora redakcneho systemu
	 */
	const AUTHOR = 'Tomáš Tatarko';

	/**
	 * TSPK oznacenie verzie redakcneho systemu
	 */
	const TSPK = '0012013111101';

	/**
	 * Staticke mapovanie pre autoloader
	 * @var string
	 */
	protected static $map = array(
		'TemplateClass' => 'admin/includes/TemplateClass.php',
		'texyla' => 'codes/texyla/texyla.php',
		'plugin' => 'admin/includes/pluginClass.php',
		'image' => 'admin/includes/image.class.inc.php',
		'wdriver' => 'admin/includes/wysiwyg-class.php',
		'OpinerText' => 'admin/opiner-text/OpinerText.php',
		'loadPlugin' => 'admin/includes/pluginClass.php',
		'PrestoEngine\\Author' => 'admin/includes/PrestoEngine.php',
		'PrestoEngine\\Template' => 'admin/includes/PrestoEngine.php',
		'PrestoEngine\\Engine' => 'admin/includes/PrestoEngine.php',
		'PrestoEngine\\Template' => 'admin/includes/PrestoEngine.php',
		'PrestoEngine\\Layout' => 'admin/includes/PrestoEngine.php',
		'PrestoEngine\\LayoutView' => 'admin/includes/PrestoEngine.php',
		'PrestoEngine\\MenuView' => 'admin/includes/PrestoEngine.php',
		'PrestoEngine\\PatternBuilder' => 'admin/includes/PrestoEngine.php',
		'PrestoEngine\\Template' => 'admin/includes/PrestoEngine.php',
		'PrestoEngine\\View' => 'admin/includes/PrestoEngine.php',
		'Fertu\\Action' => 'admin/includes/Fertu.php',
		'Fertu\\Controller' => 'admin/includes/Fertu.php',
		'Fertu\\ControllerAction' => 'admin/includes/Fertu.php',
		'Fertu\\FertuAction' => 'admin/includes/Fertu.php',
		'Fertu\\HttpException' => 'admin/includes/Fertu.php',
		'Fertu\\Router' => 'admin/includes/Fertu.php',
	);

	/**
	 * Pouzity template na frontende
	 * @var \PrestoEngine\Template 
	 */
	protected $template;

	/**
	 * Aktivny router
	 * @var Fertu\Router
	 */
	protected $router;

	/**
	 * Aktualne instancia appky
	 * @var System
	 */
	protected static $instance;

	/**
	 * Vrati/vytvori instanciu appky
	 * @return System
	 */
	public static function app() {

		return self::$instance = self::$instance ?: new self;
	}

	/**
	 * Vrati adresu k pozadovanemu suboru
	 * @param string $file Nazov suboru
	 * @return string
	 */
	public static function getRoot($file) {

		return dirname(__FILE__) . '/../../' . $file;
	}

	/**
	 * Prida novy alias pre nejaku classu/funkciu
	 * @param string $class Nazov triedy/funkcie
	 * @param string $file Nazov suboru, v ktorom sa ukryva
	 */
	public static function addToMap($class, $file) {

		if(!file_exists(self::getRoot($file))) {

			trigger_error(sprintf('File %s for autoloading class %s does not exists!', $file, $class));
		}

		self::$map[$class] = $file;
	}

	/**
	 * Autoloader tried
	 * @param string $class Nazov volanej triedy
	 */
	public static function loadClass($class) {

		if(!class_exists($class, false) && isset(self::$map[$class])) {

			require_once self::getRoot(self::$map[$class]);
		}
	}

	/**
	 * Zachytavac hodenych vynimiek
	 * @param Exception $ex
	 */
	public static function errorParser(Exception $ex) {

		var_dump($ex->getMessage(), $ex->getFile(), $ex->getLine());
	}

	/**
	 * Autoloader funkcii
	 * @param string $function Nazov volanej funkcie
	 * @param type $args Argumenty volania danej funkcie
	 * @return mixed Vysledok danej funkcie
	 */
	public static function __callStatic($function, $args) {

		if(!function_exists($function) && isset(self::$map[$function])) {

			require_once self::getRoot(self::$map[$function]);
		}

		var_dump($function);
		return call_user_func_array($function, $args);
	}

	/**
	 * Vrati zoznam Labs projektov
	 * @return string[]
	 */
	public function getLabsProjects() {

		return array (
			'sotu' => 'statsontheuse',
			'ocaa' => 'ajaxadmin',
		);
	}

	/**
	 * Setne to appky router a nasadi routy pod mena (configu)
	 * @param string $name Pomenovanie skupiny rout, ktore sa maju pouzit
	 * @return \Fertu\Router
	 */
	public function createRouter($name) {
		switch($name) {
			case 'old':
				$actions = array(
					array('clanok-{$id:\d+}-{seo}.html', 'controller' => 'article', 'action' => 'view'),
					array('sekcia-{$id}-{seo}.html', 'controller' => 'section', 'action' => 'view'),
				);
				break;
		}

		$this->router = new Fertu\Router($actions);

		if($this->template instanceof PrestoEngine\Template) {
			$this->router->template = $this->template;
		}

		return $this->router;
	}

	/**
	 * Setne to appky template
	 * @param string $name Pomenovanie skupiny rout, ktore sa maju pouzit
	 * @return \PrestoEngine\Template
	 */
	public function createTemplate($name) {

		$this->template = new PrestoEngine\Template($name);

		if($this->router instanceof Fertu\Router) {

			$this->router->template = $this->template;
		}

		return $this->router;
	}

	/**
	 * Spusti samotne routovanie a execuovanie akcie
	 */
	public function run() {

		$this->router->run();
	}

	/**
	 * Getter pre template
	 * @return \PrestoEngine\Template
	 */
	public function getTemplate() {

		return $this->template;
	}

	/**
	 * Getter pre template
	 * @return \Fertu\Router
	 */
	public function getRouter() {

		return $this->router;
	}

	/**
	 * Zparsuje obsah a menu a zapise vystup do predanej premennej
	 * @param string $content
	 * @param array $store_location
	 */
	public static function parseMenuBox($content, &$store_location) {

		if($content == '<%mainmenu%>') {

			global $_CONFIG, $prefix, $translate;

			$query = mysql_query('SELECT * FROM ('
					. 'SELECT "category" type, id, nadpis title, skr seo_title, position, NULL href, NULL target FROM `' . $prefix . '_cats` where inmenu = 1'
					. ' UNION SELECT "link" type, id, title, NULL seo_title, position, href, target FROM `' . $prefix . '_links` where location = 1'
					. ' UNION SELECT "section" type, id, nadpis title, seo seo_title, position, NULL href, NULL taget FROM `' . $prefix . '_sec` WHERE msec = 0'
					. ') mainmenu ORDER BY position ASC, id ASC');
			
			while($item = mysql_fetch_assoc($query)) {

				switch($item['type']) {

					case 'category':
						$permalink = System::app()->router->createUrl(array(
							'controller' => 'article',
							'view' => 'category',
							'id' => $item['item'],
							'seo' => $item['seo_title'],
						));
						break;

					case 'link':
						$permalink = $item['href'];
						break;

					case 'section':
						$permalink = System::app()->router->createUrl(array(
							'controller' => 'section',
							'view' => 'view',
							'id' => $item['id'],
							'seo' => $item['seo_title'],
						));
						break;
				}
				
				$store_location['links'][] = array(
					'id'	=> $item['id'],
					'type'	=> $item['type'],
					'title'	=> $item['title'],
					'link'	=> $permalink,
				);
			}

		list ($id, $seo) = @mysql_fetch_row (@mysql_query ("SELECT `id`, `seo` FROM `{$prefix}_sec` WHERE `id` = {$_CONFIG['homepage']} LIMIT 1"));
		$homepage = "$id-$seo";
		$arr = array (
			'[archive]' => array ('archiv', 1),
			'[catlist]' => array ('stranka', 'archiv'),
			'[gallery]' => array ('gallery', 1),
			'[gbook]' => array ('kniha', 1),
			'[sitemap]' => array ('stranka', 'sitemap'),
			'[rss]' => array ('stranka', 'rss'),
			'[home]' => array ('sekcia', $homepage),
		);
		$sql = @mysql_query ("SELECT `name`, `title`, `target`, `href`, `id`, `position` FROM `{$prefix}_links` WHERE `location` = 1 ORDER BY `position` ASC");
		while ($tab = @mysql_fetch_row ($sql)) {
			if (isset ($arr[$tab[3]])) {
				unset ($_GET[array_search($homepage,$_GET)]);
				if ($tab[3] == '[home]' and empty ($_GET))
				$array[$this->bzf($tab[5]).$this->bzf($tab[4]).'c'] = (isset($_TEMP['link-active'])?$_TEMP['link-active']:$_TEMP['link-start']). '<a href="./" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'].n;
				else if ($tab[3] == '[home]') $array[$this->bzf($tab[5]).$this->bzf($tab[4]).'l'] = $_TEMP['link-start']. '<a href="./" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'].n;
				else if (isset ($_REQUEST[$arr[$tab[3]][0]], $_TEMP['link-active']) and (is_int ($arr[$tab[3]][1]) or $_REQUEST[$arr[$tab[3]][0]] == $arr[$tab[3]][1]))
				$array[$this->bzf($tab[5]).$this->bzf($tab[4]).'l'] = $_TEMP['link-active']. '<a href="' . rwl ($arr[$tab[3]][0], $arr[$tab[3]][1]) . '" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'].n;
				else $array[$this->bzf($tab[5]).$this->bzf($tab[4]).'l'] = $_TEMP['link-start']. '<a href="' . rwl ($arr[$tab[3]][0], $arr[$tab[3]][1]) . '" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'].n;
			} else if (preg_match ('#\[app:([a-zA-Z0-9_]*)\]#', $tab[3], $match, PREG_OFFSET_CAPTURE)) {
				if (isset ($_REQUEST['plugin']) and $_REQUEST['plugin'] == $match[1][0])
				$array[$this->bzf($tab[5]).$this->bzf($tab[4]).'l'] = ((isset($_TEMP['link-active'])) ? $_TEMP['link-active'] : $_TEMP['link-start']) . '<a href="' . rwl ('plugin', $match[1][0]) . '" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'].n;
				else $array[$this->bzf($tab[5]).$this->bzf($tab[4]).'l'] = $_TEMP['link-start']. '<a href="' . rwl ('plugin', $match[1][0]) . '" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'].n;
			} else $array[$this->bzf($tab[5]).$this->bzf($tab[4]).'l'] = $_TEMP['link-start'] . '<a href="' . $tab[3] . '" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'] . n;
		};
		ksort ($array);
		$mainmenu .= implode ('', $array);
		$mainmenu .= $_TEMP['list-end'].n;
		return $mainmenu;			
			
		}
		else {
			
		}
	}
}

// backward compatibility
define('n', PHP_EOL);
define('in', System::TSPK);
$dot = strrpos ($_SERVER['SCRIPT_NAME'], '/');
$filename = ($dot === false) ? $_SERVER['SCRIPT_NAME'] : substr ($_SERVER['SCRIPT_NAME'], $dot + 1);
$linker = str_replace ($filename, '', $_SERVER['PHP_SELF']);
$linker = 'http://' . $_SERVER['SERVER_NAME'] . $linker;
if (!defined ('_SiteLink')) define ('_SiteLink', str_replace ('www.', '', $linker));
if (!defined ('_SiteFileName')) define ('_SiteFileName', $filename);
if (!defined ('_SiteForm')) define ('_SiteForm', _SiteLink . _SiteFileName . (($_SERVER['QUERY_STRING'] != '')?'?':'') . $_SERVER['QUERY_STRING']);
$sn = strtoupper (md5(_SiteLink));
$sn = array (substr ($sn, 0, 4), substr ($sn, 4, 4), substr ($sn, 8, 4), substr ($sn, 12, 4));
if (!defined ('_SerialNumber')) define ('_SerialNumber', implode ('-', $sn));
$tohead = array ();
function langrep ($name) {
	global $translate;
	$s = $translate[$name];
	foreach (func_get_args() as $i => $v) $s = str_replace ('$' . $i, $v, $s);
	return $s;
};

spl_autoload_register(array('System', 'loadClass'));
set_exception_handler(array('System', 'errorParser'));