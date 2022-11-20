<?php

namespace Kirby\Kql;

use Closure;
use Exception;
use Kirby\Cms\App;
use Kirby\Exception\PermissionException;
use Kirby\Kql\Help;
use Kirby\Kql\Kql;
use Kirby\Toolkit\Str;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;

/**
 * Base class for proxying core classes to
 * intercept method calls that are not allowed
 * on the related core class
 *
 * @package   Kirby KQL
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://getkirby.com/license
 */
abstract class Interceptor
{
	public const CLASS_ALIAS = null;

	protected $toArray = [];

	public function __construct(protected $object)
	{}

	public function __call($method, array $args = [])
	{
		if ($this->isAllowedMethod($method) === true) {
			return $this->object->$method(...$args);
		}

		$this->forbiddenMethod($method);
	}

	public function __debugInfo(): array
	{
		$help = Help::forMethods($this->object, $this->allowedMethods());

		return [
			'type'    => $this::CLASS_ALIAS,
			'methods' => $help,
			'value'   => $this->toArray()
		];
	}

	public function allowedMethods(): array
	{
		return [];
	}

	protected static function class(string $class): string
	{
		return str_replace('Kirby\\', 'Kirby\\Kql\\Interceptors\\', $class);
	}

	protected function forbiddenMethod(string $method)
	{
		$name = get_class($this->object) . '::' . $method . '()';
		throw new PermissionException('The method "' . $name .  '" is not allowed in the API context');
	}

	/**
	 * Returns a registered method by name, either from
	 * the current class or from a parent class ordered by
	 * inheritance order (top to bottom)
	 */
	protected function getMethod(string $method): Closure|null
	{
		if (isset($this->object::$methods[$method]) === true) {
			return $this->object::$methods[$method];
		}

		foreach (class_parents($this->object) as $parent) {
			if (isset($parent::$methods[$method]) === true) {
				return $parent::$methods[$method];
			}
		}

		return null;
	}

	protected function isAllowedCallable($method): bool
	{
		try {
			$ref = match(true) {
				$method instanceof Closure
					=> new ReflectionFunction($method),
				is_string($method) === true
					=> new ReflectionMethod($this->object, $method),
				default
					=> throw new Exception('Invalid method')

			};

			if ($comment = $ref->getDocComment()) {
				if (Str::contains($comment, '@kql-allowed') === true) {
					return true;
				}
			}
		} catch (Throwable) {}

		return false;
	}

	protected function isAllowedMethod($method)
	{
		$kirby = App::instance();
		$name  = strtolower(get_class($this->object) . '::' . $method);

		// get list of blocked methods from config
		$blocked = $kirby->option('kql.methods.blocked', []);
		$blocked = array_map('strtolower', $blocked);

		// check in the block list from the config
		if (in_array($name, $blocked) === true) {
			return false;
		}

		// check in class allow list
		if (in_array($method, $this->allowedMethods()) === true) {
			return true;
		}

		// get list of explicitly allowed methods from config
		$allowed = $kirby->option('kql.methods.allowed', []);
		$allowed = array_map('strtolower', $allowed);

		// check in the allow list from the config
		if (in_array($name, $allowed) === true) {
			return true;
		}

		// support for model methods with docblock comment
		if ($this->isAllowedCallable($method) === true) {
			return true;
		}

		// support for custom methods with docblock comment
		if ($this->isAllowedCustomMethod($method) === true) {
			return true;
		}

		return false;
	}

	protected function isAllowedCustomMethod(string $method): bool
	{
		// has no custom methods
		if (property_exists($this->object, 'methods') === false) {
			return false;
		}

		// does not have that method
		if (!$call = $this->getMethod($method)) {
			return false;
		}

		// check for a docblock comment
		if ($this->isAllowedCallable($call) === true) {
			return true;
		}

		return false;
	}

	public static function replace($object)
	{
		if (is_object($object) === false) {
			throw new Exception('Unsupported value: ' . gettype($object));
		}

		$kirby = App::instance();
		$class = get_class($object);
		$name  = strtolower($class);

		// 1. Is $object class explicitly blocked?
		// get list of blocked classes from config
		$blocked = $kirby->option('kql.classes.blocked', []);
		$blocked = array_map('strtolower', $blocked);

		// check in the block list from the config
		if (in_array($name, $blocked) === true) {
			throw new PermissionException('Access to the class "' . $class . '" is blocked');
		}

		// 2. Is $object already an interceptor?
		// directly return interceptor objects
		if ($object instanceof Interceptor) {
			return $object;
		}

		// 3. Does an interceptor class for $object exist?
		// check for an interceptor class
		$interceptors = $kirby->option('kql.interceptors', []);
		$interceptors = array_change_key_case($interceptors, CASE_LOWER);
		// load an interceptor from config if it exists and otherwise fall back to a built-in interceptor
		$interceptor = $interceptors[$name] ?? static::class($class);

		// check for a valid interceptor class
		if ($class !== $interceptor && class_exists($interceptor) === true) {
			return new $interceptor($object);
		}

		// 4. Also check for parent classes of $object
		// go through parents of the current object to use their interceptors as fallback
		foreach (class_parents($object) as $parent) {
			$interceptor = static::class($parent);

			if (class_exists($interceptor) === true) {
				return new $interceptor($object);
			}
		}

		// 5. $object has no interceptor but is explicitly allowed?
		// check for a class in the allow list
		$allowed = $kirby->option('kql.classes.allowed', []);
		$allowed = array_map('strtolower', $allowed);

		// return the plain object if it is allowed
		if (in_array($name, $allowed) === true) {
			return $object;
		}

		// 6. None of the above? Block class.
		throw new PermissionException('Access to the class "' . $class . '" is not supported');
	}

	public function toArray(): array|null
	{
		$toArray = [];

		// filter methods which cannot be called
		foreach ($this->toArray as $method) {
			if ($this->isAllowedMethod($method) === true) {
				$toArray[] = $method;
			}
		}

		return Kql::select($this, $toArray);
	}

	public function toResponse()
	{
		return $this->toArray();
	}
}
