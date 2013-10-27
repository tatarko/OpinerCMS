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