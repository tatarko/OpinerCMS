<?php

namespace PrestoEngine;
use Exception;

/**
 * Ziska public properties danej triedy
 * @param object $me
 * @return string[]
 */
function getPublicProperties(object $me) {

	return array_keys(get_object_vars($me));
}

/**
 * Zakladny objekt pre cely template engine
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 * @property array $attributes Magicka property reprezentujuca vsetky attributy daneho objektu
 */
class Object {

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
 * Presto Template engine
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 * @property-read string $title Nazov motivu
 * @property-read float $version Verzia motivu
 * @property-read float $system S akou verziou motivu je template kompatibilny
 * @property-read PrestoEngine\Author[] $authors Autori motivu
 * @property-read PrestoEngine\Layout[] $layouts Mozne layouty motivu
 * @property-read string $path Adresa priecinku s motivom
 * @property-read string $name Nazov priecinka motivu
 * @property-read string $cacheFolder Adresa cache priecinku
 * @property PrestoEngine\Layout $layout Aktivny layout
 */
class Template extends Object {

	/**
	 * Maska pre sprintf, na zaklade ktorej sa ziskavaju adresy priecinkov motivom (__FILE__, $templateFolderName)
	 */
	const TEMPLATE_FOLDER_MASK = '%s/../../templates/%s/';

	/**
	 * Staticke premenne na renderovanie
	 * @var array 
	 */
	public $values = array();

	/**
	 * Adresa priecinku s motivom
	 * @var string
	 */
	protected $path;

	/**
	 * Nazov priecinka motivu
	 * @var string
	 */
	protected $name;

	/**
	 * Aktivny layout
	 * @var PrestoEngine\Layout
	 */
	protected $layout;

	/**
	 * Udaje nacitane z manifest.json suboru
	 * @var PrestoEngine\Author[] 
	 */
	protected $manifestData = array();

	/**
	 * Mozne layouty motivu
	 * @var PrestoEngine\Layout[] 
	 */
	protected $layouts = array();

	/**
	 * Konstruktor templatu
	 * @param string $name Nazov priecinku daneho templatu
	 * @throws Exception
	 */
	public function __construct($name) {

		$templatePath = $this->resolveTemplatePath($name);

		if(!is_dir($templatePath)) {

			throw new Exception(sprintf('Template "%s" was not found in the filesystem', $name), 404);
		}

		$this->path = $templatePath;
		$this->name = $name;
		$this->readManifest();
	}

	/**
	 * Precita manifest daneho templatu
	 * @throws Exception Ak manifest subor neexistuje
	 * @suports Method-Chaining
	 */
	protected function readManifest() {

		$manifestFile = $this->path . 'manifest.json';

		if(!file_exists($manifestFile)) {

			throw new Exception(sprintf('Manifest file does not exists in template %s', $this->name), 404);
		}

		$this->manifestData	= json_decode(file_get_contents($manifestFile), true);
		$this->_s[]	= 'manifestData';

		return $this->parseAuthors()->parseLayouts();
	}

	/**
	 * Parsuj autorov motivu
	 * @return \PrestoEngine\Template
	 * @suports Method-Chaining
	 */
	protected function parseAuthors() {

		if(!isset($this->manifestData['authors']) || !is_array($this->manifestData['authors'])) {

			$this->manifestData['authors'] = array();
		}

		foreach($this->manifestData['authors'] as $key => $values) {

			$this->manifestData['authors'][$key] = new Author;
			$this->manifestData['authors'][$key]->attributes = $values;
		}

		return $this;
	}

	/**
	 * Parsuj layouty motivu
	 * @return \PrestoEngine\Template
	 * @suports Method-Chaining
	 */
	protected function parseLayouts() {

		if(!empty($this->layouts)) {

			return $this;
		}

		if(!isset($this->manifestData['layouts']) || !is_array($this->manifestData['layouts'])) {

			$this->manifestData['layouts'] = array();
		}

		foreach($this->manifestData['layouts'] as $values) {

			$layout							= new Layout($this);
			$layout->attributes				= $values;
			$this->layouts[$layout->name]	= $layout;
		}

		return $this;
	}

	/**
	 * Skontroluje kompatibilitu motivu
	 * @return \PrestoEngine\Template
	 * @suports Method-Chaining
	 */
	protected function checkCompatibility() {

		list(, $systemVersion)	= explode('~$~', \SystemInfo);
		$blankSystemVersion		= current(explode(' ', $systemVersion));

		if(version_compare($blankSystemVersion, $this->system, '<')) {

			throw new Exception('Template is not compatible with system version', 500);
		}

		return $this;
	}

	/**
	 * Ziska zoznam layoutov
	 * @return PrestoEngine\Layout[]
	 */
	public function getLayouts() {

		return $this->layouts;
	}

	/**
	 * Vrati aktivny layout
	 * @return PrestoEngine\Layout
	 */
	public function getLayout() {

		return $this->layout ?: current($this->layouts);
	}

	/**
	 * Vrati cestu k priecinku daneho motivu
	 * @return string
	 */
	public function getPath() {

		return $this->path;
	}

	/**
	 * Setnutie aktivneho layoutu
	 * @param string|\PrestoEngine\Layout $layout Nazov alebo instancie layoutu
	 * @throws Exception Ak sa layout nenajde
	 */
	public function setLayout($layout) {

		if($layout instanceof Layout) {

			return $this->layout = $layout;
		}

		foreach($this->layouts as $name => $model) {

			if($name == $layout) {

				return $this->layout = $model;
			}
		}

		throw new Exception('Requested layout does not exists', 404);
	}

	/**
	 * Vrati adresu ku cache priecinku
	 * @return string
	 */
	public function getCacheFolder() {

		return dirname(__FILE__) . '/../../store/cache/';
	}

	/**
	 * Ziska adresu priecinka motivu
	 * @param string $name
	 * @return string
	 */
	protected function resolveTemplatePath($name) {

		return sprintf(self::TEMPLATE_FOLDER_MASK, dirname(__FILE__), $name);
	}

	/**
	 * Renderovanie iba samotneho viewu
	 * @param string $view Nazov viewu
	 * @param array $values Premenne pre renderovanie
	 * @param boolean $return Vratit vyrenderovany view?
	 * @return string
	 */
	public function renderPartial($view, array $values, $return = false) {

		$engine	= new Engine(new View($this, $view, $values));
		return $engine->render($return);
	}

	/**
	 * Renderovanie iba samotneho viewu
	 * @param string $view Nazov viewu
	 * @param array $values Premenne pre renderovanie
	 * @param boolean $return Vratit vyrenderovany view?
	 * @return string
	 */
	public function render($view, array $values, $return = false) {

		$engine	= new Engine(
			new LayoutView(
				$this,
				$this->getLayout()->name,
				array('content' => $this->renderPartial($view, $values, true)) + $this->getLayout()->menuRenderValues
			)
		);
		$engine->escapeHtml = false;
		return $engine->render($return);
	}
}

/**
 * Author motivu
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 * @property-read string $htmlLink Html link na autora motivu
 */
class Author extends Object {

	/**
	 * Meno autora motivu
	 * @var string
	 */
	public $name;

	/**
	 * Url adresa webu autora
	 * @var string
	 */
	public $url;

	/**
	 * Vrati html link na autora motivu
	 * @return string
	 */
	public function getHtmlLink() {

		return sprintf('<a href="%s">%s</a>', $this->url, $this->name);
	}
}

/**
 * Layout motivu
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 * @property PrestoEngine\Menu[] $menus Menu daneho layoutu
 * @property string[] $menuRenderValues Vyrenderovane jednotlive menu polozky
 */
class Layout extends Object {

	/**
	 * Nazov suboru daneho suboru
	 * @var string
	 */
	public $name;

	/**
	 * Nazov daneho layoutu
	 * @var string
	 */
	public $title;

	/**
	 * Menu, ktore dany layout obsahuje
	 * @var PrestoEngine\Menu[]
	 */
	protected $menus;

	/**
	 * Link na motiv, ktoremu layout patri
	 * @var PrestoEngine\Template
	 */
	protected $template;

	/**
	 * Konstruktor (linkovanie templatu)
	 * @param \PrestoEngine\Template $template
	 */
	public function __construct(Template $template) {

		$this->template = $template;
	}

	/**
	 * Pridavanie novych menu do layoutu
	 * @param array $menus
	 */
	public function setMenus(array $menus) {

		foreach($menus as $values) {

			$menu = new Menu($this);
			$menu->attributes = $values;
			$this->menus[] = $menu;
		}
	}

	/**
	 * Getter pre menu daneho layoutu
	 * @return PrestoEngine\Menu[]
	 */
	public function getMenus() {

		return $this->menus;
	}

	/**
	 * Vrati vyrenderovane jednotlive menu
	 * @return string[]
	 */
	public function getMenuRenderValues() {

		$menus = array();

		foreach($this->menus as $key => $menu) {

			$key = sprintf('menu%d', $key + 1);

			if(!isset($this->template->values[$key])) {

				continue;
			}

			$engine			= new Engine(new MenuView($this->template, $menu->view, $this->template->values[$key]));
			$menus[$key]	= $engine->render(true);
		}

		return $menus;
	}
}

/**
 * View motivu
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 * @property-read string $path Cesta k suboru daneho view
 * @property-read PrestoEngine\Template $template Template, ktoremu view patri
 */
class View extends Object {

	/**
	 * Nazov suboru daneho view
	 * @var string
	 */
	public $name;

	/**
	 * Cesta k suboru daneho view
	 * @var string
	 */
	protected $path;

	/**
	 * Hodnoty daneho view na renderovanie
	 * @var array 
	 */
	public $values = array();

	/**
	 * Template, ktoremu view patri
	 * @var PrestoEngine\Template
	 */
	protected $template;

	/**
	 * Konstruktor daneho view
	 * @param \PrestoEngine\Template $template
	 * @param string $name Nazov daneho view-u
	 * @param array $values Premenne daneho viewu
	 */
	public function __construct(Template $template, $name, array $values = array()) {

		$this->name		= $name;
		$this->template	= $template;
		$this->path		= $this->getFilePath($name);
		$this->values	+= $values;
	}

	/**
	 * Ziska cestu k suboru viewu podla nazvu
	 * @param string $name
	 * @return string
	 */
	public function getFilePath($name) {

		return sprintf('%sviews/%s.tpl', $this->template->path, $name);
	}

	/**
	 * Ziska adresu daneho view suboru
	 * @return string
	 */
	public function getPath() {

		return $this->path;
	}

	/**
	 * Vrati template, ktoremu view patri
	 * @return PrestoEngine\Template
	 */
	public function getTemplate() {

		return $this->template;
	}
}

/**
 * View motivu
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 */
class MenuView extends View {

	/**
	 * Ziska cestu k suboru viewu podla nazvu
	 * @param string $name
	 * @return string
	 */
	public function getFilePath($name) {

		return sprintf('%slayouts/menu/%s.tpl', $this->template->path, $name);
	}
}

/**
 * View motivu
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 */
class LayoutView extends View {

	/**
	 * Ziska cestu k suboru viewu podla nazvu
	 * @param string $name
	 * @return string
	 */
	public function getFilePath($name) {

		return sprintf('%slayouts/%s.tpl', $this->template->path, $name);
	}
}


/**
 * Menu
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 */
class Menu extends Object {

	/**
	 * View pouzity pre dane menu
	 * @var string
	 */
	public $view;

	/**
	 * Typ daneho menu
	 * @var string
	 */
	public $type;

	/**
	 * Layout, ktoremu patri dane menu
	 * @var string
	 */
	protected $layout;

	/**
	 * Konstruktor (linkovanie layoutu
	 * @param \PrestoEngine\Layout $layout
	 */
	public function __construct(Layout $layout) {

		$this->layout = $layout;
	}
}

/**
 * View motivu
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 */
class Engine extends Object {

	/**
	 * Pattern na odchytavanie nazvu premennych
	 */
	const PATTERN_VARIABLES = '([a-zA-Z0-9.]+)';

	/**
	 * Pattern na odchytavanie nazvu premennych
	 */
	const PATTERN_WHITESPACE = '[ \t\r\n\v\f]*';

	/**
	 * Escapovat premenne?
	 * @var boolean
	 */
	public $escapeHtml = true;

	/**
	 * View na renderovanie
	 * @var PrestoEngine\View
	 */
	protected $view;

	/**
	 * Konstruktor, setovanie viewu
	 * @param \PrestoEngine\View $view
	 */
	public function __construct(View $view) {

		$this->view = $view;
	}

	/**
	 * Sposobi renderovanie
	 * @param boolean $return Vrati string alebo vypisat?
	 */
	public function render($return = false) {

		$filename = $this->view->path;

		if(!file_exists($filename)) {

			throw new Exception(sprintf('Template file %s does not exists', $filename), 404);
		}

		$lastEdit	= filectime($filename);
		$cacheFile	= $this->view->template->cacheFolder . sprintf('template_%s.php', substr(md5($filename . $lastEdit), 0, 10));

		if(!file_exists($cacheFile)) {

			$this->parseTemplate($filename, $cacheFile);
		}

		$output = $this->renderFileContent($cacheFile, $this->view->values);

		if($return) {

			return $output;
		}

		echo $output;
	}

	/**
	 * Prekladac markupu
	 * @param string $sourceFile Zdrojovy subor s markupom
	 * @param string $targetFile Vystupny subor v plaintexte
	 */
	protected function parseTemplate($sourceFile, $targetFile) {

		$content = file_get_contents($sourceFile);

		$this	->parseIncludes($content)
				->parseConditions($content)
				->parseCycles($content)
				->parseValues($content);

		file_put_contents($targetFile, $content);
	}

	/**
	 * Vyparsuj includovanie partial suborov
	 * @param string $content Aktualne vyparsovany subor
	 * @return PrestoEngine\Engine
	 * @suports Method-Chaining
	 */
	protected function parseIncludes(&$content) {

		while(preg_match_all(sprintf('#\{\%s@import%s([a-zA-Z0-9/]+)%s\}#', self::PATTERN_WHITESPACE, self::PATTERN_WHITESPACE, self::PATTERN_WHITESPACE), $content, $matches, PREG_SET_ORDER)) {

			foreach($matches as $match) {

				$partialFile = $this->view->getFilePath($match[1]);

				if(!file_exists($partialFile)) {

					throw new Exception(sprintf('Template file %s does not exists', $partialFile), 404);
				}

				$content = str_replace($match[0], file_get_contents($partialFile), $content);
			}
		}

		return $this;
	}

	/**
	 * Vyparsuj volanie premennych
	 * @param string $content Aktualne vyparsovany subor
	 * @return PrestoEngine\Engine
	 * @suports Method-Chaining
	 */
	protected function parseValues(&$content) {

		if(!preg_match_all(sprintf('#\{\{%s' . self::PATTERN_VARIABLES . '(\|.+)?%s\}\}#', self::PATTERN_WHITESPACE, self::PATTERN_WHITESPACE), $content, $matches, PREG_SET_ORDER)) {

			return $this;
		}

		foreach($matches as $match) {

			$modificators = $this->listModificators(isset($match[2]) ? $match[2] : '');
			$this->addBasicModificators($modificators);

			$content = str_replace($match[0], '<?='
					. ' isset(' . $this->unmaskVariable($match[1]) . ')'
					. ' ? ' . $this->unmaskVariable($match[1], $modificators) . ''
					. ' : "" ?>', $content);
		}

		return $this;
	}

	/**
	 * Precita, ake modifikatory pouzit pre vypis premennej
	 * @param string $where
	 * @return string[] Zoznam modikatorov $modificatorName => $modificatorParams
	 */
	protected function listModificators($where) {

		$modifiers = array();

		if(preg_match_all('#\|(?P<name>[a-zA-Z]+)(\((?P<args>.+)\))?#', $where, $modifiersMatches, PREG_SET_ORDER)) {

			foreach($modifiersMatches as $modifierMatch) {

				$modifiers[$modifierMatch['name']] = isset($modifierMatch['args']) ? $modifierMatch['args'] : null;
			}
		}

		return $modifiers;
	}

	/**
	 * Prida zakladne (defaultne) modifikatory
	 * @param array $modificators
	 */
	protected function addBasicModificators(array &$modificators) {

		if($this->escapeHtml && !in_array('raw', $modificators)) {

			$modificators['escape'] = null;
		}
	}

	/**
	 * Vyparsuj volanie premennych
	 * @param string $content Aktualne vyparsovany subor
	 * @return PrestoEngine\Engine
	 * @suports Method-Chaining
	 */
	protected function parseConditions(&$content) {

		
		return $this;
	}

	/**
	 * Vyparsuj volanie premennych
	 * @param string $content Aktualne vyparsovany subor
	 * @return PrestoEngine\Engine
	 * @suports Method-Chaining
	 */
	protected function parseCycles(&$content) {

		if(!preg_match_all('#\{\% for ' . self::PATTERN_VARIABLES . ' in ' . self::PATTERN_VARIABLES . ' \%\}#', $content, $matches, PREG_SET_ORDER)) {

			return $this;
		}

		foreach($matches as $match) {
			
			$content = str_replace($match[0], '<? if(isset(' . $this->unmaskVariable($match[2]) . ')'
					. ' && is_array(' . $this->unmaskVariable($match[2]) . ')) :'
					. PHP_EOL . '$thisCount = count(' . $this->unmaskVariable($match[2]) . ');'
					. PHP_EOL . '$thisPosition = 0;'
					. PHP_EOL . 'foreach(' . $this->unmaskVariable($match[2])
					. ' as $thisKey => ' . $this->unmaskVariable($match[1]) . ') :'
					. PHP_EOL . '	++$thisPosition;'
					. PHP_EOL . '	$thisIsEven = $thisPosition % 2 == 1;'
					. PHP_EOL . '	$thisIsOdd = $thisPosition % 2 == 0;'
					. PHP_EOL . '	$thisIsFirst = $thisPosition == 1;'
					. PHP_EOL . '	$thisIsLast = $thisPosition == $thisCount; ?>', $content);
		}

		$content = str_replace('{% endfor %}', '<? endforeach; unset($thisKey, $thisPosition, $thisCount, $thisIsEven, $thisIsOdd, $thisIsFirst, $thisIsLast); endif; ?>', $content);

		return $this;
	}

	/**
	 * Odmaskuje volanie premennej
	 * @param string $variable Ktoru premennu chce odmaskovat?
	 * @return string
	 */
	protected function unmaskVariable($variable, array $modificators = array()) {

		$variable = explode('.', $variable);
		$pattern = '$' . current($variable);

		foreach(array_slice($variable, 1) as $part) {

			$pattern .= sprintf('["%s"]', $part);
		}

		if(empty($modificators)) {

			return $pattern;
		}

		$indent = 0;
		unset($modificators['raw']);
		foreach($modificators as $name => $args) {

			$pattern = sprintf(
				'%s%sPrestoEngine\\Helper::%s(%s%s%s)%s',
				PHP_EOL,
				str_repeat("\t", count($modificators) - ++$indent),
				$name,
				$pattern,
				$args === null ? '' : ', ',
				$args,
				PHP_EOL
			);
		}

		return $pattern;
	}

	/**
	 * Sposobi samotne renderovanie viewu
	 * @param string $__fileToInclude
	 * @param array $__valuesToUse
	 * @return string
	 */
	protected function renderFileContent($__fileToInclude, array $__valuesToUse) {

		ob_start();
		extract($__valuesToUse);
		require $__fileToInclude;
		return ob_get_clean();
	}
}

/**
 * Helper (obsahujuci metody pre modificatory
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 */
class Helper {

	/**
	 * Naformatuje datum podla pozadovaneho tvaru
	 * @param string|int $value Cas/datum
	 * @param string $format Format, v akom sa ma cas zobrazit
	 * @return string
	 */
	public static function date($value, $format = 'd.m.Y H:i:s') {

		return date($format, is_numeric($value) ? $value : strtotime($value));
	}

	/**
	 * Escapuje hodnotu pomocou html entit
	 * @param string $value Vstupna hodnota
	 * @return string
	 */
	public static function escape($value) {

		return htmlspecialchars($value);
	}

	/**
	 * Vrati absolutnu hodnotu cisla
	 * @param int $value Vstupna hodnota
	 * @return int
	 */
	public static function abs($value) {

		return abs($value);
	}

	/**
	 * Hodi prve pismeno velke
	 * @param string $value Vstupna hodnota
	 * @return string
	 */
	public static function capitalize($value) {

		return ucfirst($value);
	}

	/**
	 * Upravi datum na zaklade poziadavky, napr "+1day" prida den
	 * @param string $value Vstupna hodnota
	 * @param string $how Cokolvek, co dokaze {@link strtotime()} pochopit
	 * @return string
	 */
	public static function modifyDate($value, $how) {

		return strtotime($how, is_numeric($value) ? $value : strtotime($value));
	}

	/**
	 * Vyplni hodnotu niecim v pripade, ze neexistuje/je prazdna
	 * @param string $value Vstupna hodnota
	 * @param string $default Co sa pouzije v tom pripade
	 * @return string
	 */
	public static function byDefault($value, $default = '') {

		return empty($value) ? $default : $value;
	}

	/**
	 * Pospaja prvky podla
	 * @param array $value Vstupna hodnota
	 * @param string $separator String, ktorym budy jednotlive prvky zlepene
	 * @return string
	 */
	public static function join($value, $separator = ', ') {

		return is_array($value) ? implode($separator, $value) : $value;
	}

	/**
	 * Encoduje data ako JSON
	 * @param array $value Vstupna hodnota
	 * @return string
	 */
	public static function json($value) {

		return json_encode($value);
	}

	/**
	 * Vrati pocet prvkov daneho pola alebo dlzku retazca
	 * @param array|string $value Vstupna hodnota
	 * @return int
	 */
	public static function length($value) {

		return is_array($value) ? count($value) : mb_strlen($value);
	}

	/**
	 * Zmeni vsetky pismena na male
	 * @param string $value Vstupna hodnota
	 * @return string
	 */
	public static function lower($value) {

		return mb_strtolower($value);
	}

	/**
	 * Vrati pocet prvkov daneho pola alebo dlzku retazca
	 * @param float $value Vstupna hodnota
	 * @param int $decimals Pocet desatinnych miest
	 * @param string $decimalSeparator Oddelovac desatinnej ciarky
	 * @param string $thousandsSeparator Oddelovac tisicok
	 * @return int
	 */
	public static function numberFormat($value, $decimals = 2, $decimalSeparator = ',', $thousandsSeparator = ' ') {

		return number_format($value, $decimals, $decimalSeparator, $thousandsSeparator);
	}

	/**
	 * Zmeni vsetky pismena na velke
	 * @param string $value Vstupna hodnota
	 * @return string
	 */
	public static function upper($value) {

		return mb_strtoupper($value);
	}

	/**
	 * Odstrany html tagy
	 * @param string $value Vstupna hodnota
	 * @return string
	 */
	public static function stripTags($value) {

		return strip_tags($value);
	}

	/**
	 * Zmeni zaciatocne pismena slov na velke
	 * @param string $value Vstupna hodnota
	 * @return string
	 */
	public static function title($value) {

		return ucwords($value);
	}

	/**
	 * Odstrani pozadovane znaky zo zaciatku/konca stringu
	 * @param string $value Vstupna hodnota
	 * @param string $what Ktore znaky 
	 * @return string
	 */
	public static function trim($value, $what = ' ') {

		return trim($value, $what);
	}

	/**
	 * Escapuje string ako url
	 * @param string $value Vstupna hodnota
	 * @return string
	 */
	public static function urlEncode($value) {

		return urlencode($value);
	}
}

set_exception_handler(function(\Exception $ex) {

//	var_dump($ex->getTrace(), $ex->getCode(), $ex->getMessage(), $ex->getLine());
	var_dump($ex->getCode(), $ex->getMessage(), $ex->getLine());
});

$template = new Template('presto');

$template->render('index', array(
	'message' => 'Hello world!',
	'authors' => array(
		array(
			'name' => 'Tomas Tatarko',
			'born' => time()
		)
	)
));