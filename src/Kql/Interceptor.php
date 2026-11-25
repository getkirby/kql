<?php

namespace Kirby\Kql;

use Closure;
use Kirby\Cms\App;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Exception\PermissionException;
use Kirby\Toolkit\Str;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;

/**
 * Base class for proxying core classes to
 * intercept method calls that are not allowed
 * on the related core class
 *
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
	{
	}

	/**
	 * Magic caller that prevents access
	 * to restricted methods
	 */
	public function __call(string $method, array $args = [])
	{
		if ($this->isAllowedMethod($method) === true) {
			return $this->object->$method(...$args);
		}

		$this->forbiddenMethod($method);
	}

	/**
	 * Return information about corresponding object
	 * incl. information about allowed methods
	 */
	public function __debugInfo(): array
	{
		$help = Help::forMethods($this->object, $this->allowedMethods());

		return [
			'type'    => $this::CLASS_ALIAS,
			'methods' => $help,
			'value'   => $this->toArray()
		];
	}

	/**
	 * Returns list of allowed classes. Specific list
	 * to be implemented in specific interceptor child classes.
	 * @codeCoverageIgnore
	 */
	public function allowedMethods(): array
	{
		return [];
	}

	/**
	 * Returns class name for Interceptor that responds
	 * to passed name string of a Kirby core class
	 * @internal
	 */
	public static function class(string $class): string
	{
		return str_replace('Kirby\\', 'Kirby\\Kql\\Interceptors\\', $class);
	}

	/**
	 * Returns the object's class and its parent classes,
	 * nearest first
	 */
	protected static function classNames(object $object): array
	{
		return [$object::class, ...array_values(class_parents($object))];
	}

	/**
	 * Throws exception for accessing a restricted method
	 * @throws PermissionException
	 */
	protected function forbiddenMethod(string $method)
	{
		$name = get_class($this->object) . '::' . $method . '()';
		throw new PermissionException('The method "' . $name . '" is not allowed in the API context');
	}

	/**
	 * Checks if closure or object method is allowed
	 */
	protected function isAllowedCallable($method): bool
	{
		try {
			$ref = match (true) {
				$method instanceof Closure
					=> new ReflectionFunction($method),
				is_string($method) === true
					=> new ReflectionMethod($this->object, $method),
				default
				=> throw new InvalidArgumentException('Invalid method')
			};

			if ($comment = $ref->getDocComment()) {
				if (Str::contains($comment, '@kql-allowed') === true) {
					return true;
				}
			}
		} catch (Throwable) {
			return false;
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
		if (!$call = $this->method($method)) {
			return false;
		}

		// check for a docblock comment
		if ($this->isAllowedCallable($call) === true) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if method is allowed to call
	 */
	public function isAllowedMethod($method)
	{
		// check in the block list from the config
		if ($this->isBlockedMethod($method) === true) {
			return false;
		}

		// check in class allow list
		if (in_array($method, $this->allowedMethods()) === true) {
			return true;
		}

		// check in the allow list from the config
		if (static::isListed($this->methodNames($method), 'kql.methods.allowed') === true) {
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

	protected function isBlockedMethod(string $method): bool
	{
		return static::isListed($this->methodNames($method), 'kql.methods.blocked');
	}

	/**
	 * Checks if the class exists and can be used as
	 * interceptor. Abstract interceptors only serve as
	 * base classes and are never used directly.
	 */
	protected static function isInterceptor(string $class): bool
	{
		if (is_subclass_of($class, self::class) === false) {
			return false;
		}

		return (new ReflectionClass($class))->isAbstract() === false;
	}

	protected static function isListed(array $names, string $option): bool
	{
		$list  = array_map('strtolower', App::instance()->option($option, []));
		$names = array_map('strtolower', $names);

		return array_intersect($names, $list) !== [];
	}

	/**
	 * Returns a registered method by name, either from
	 * the current class or from a parent class ordered by
	 * inheritance order (top to bottom)
	 */
	protected function method(string $method)
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

	protected function methodNames(string $method): array
	{
		return array_map(
			fn (string $class) => $class . '::' . $method,
			static::classNames($this->object)
		);
	}

	/**
	 * Tries to replace a Kirby core object with the
	 * corresponding interceptor.
	 * @throws InvalidArgumentException for non-objects
	 * @throws PermissionException when accessing blocked class
	 */
	public static function replace($object)
	{
		if (is_object($object) === false) {
			throw new InvalidArgumentException('Unsupported value: ' . gettype($object));
		}

		$class   = $object::class;
		$classes = static::classNames($object);

		// 1. Is $object class explicitly blocked?
		// check in the block list from the config
		if (static::isListed($classes, 'kql.classes.blocked') === true) {
			throw new PermissionException('Access to the class "' . $class . '" is blocked');
		}

		// 2. Is $object already an interceptor?
		// directly return interceptor objects
		if ($object instanceof Interceptor) {
			return $object;
		}

		// 3. Does an interceptor class for $object or a parent class exist?
		// the nearest class wins, and for each class an interceptor
		// from the config wins over the built-in one
		$interceptors = App::instance()->option('kql.interceptors', []);
		$interceptors = array_change_key_case($interceptors, CASE_LOWER);

		foreach ($classes as $className) {
			$interceptorClasses = [
				$interceptors[strtolower($className)] ?? null,
				static::class($className)
			];

			foreach ($interceptorClasses as $interceptor) {
				if ($interceptor !== null && static::isInterceptor($interceptor) === true) {
					return new $interceptor($object);
				}
			}
		}

		// 4. $object has no interceptor but is explicitly allowed?
		// check in the allow list from the config
		if (static::isListed($classes, 'kql.classes.allowed') === true) {
			return $object;
		}

		// 5. None of the above? Block class.
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

	/**
	 * Mirrors by default ::toArray but can be
	 * implemented differently by specifc interceptor.
	 * KQL will prefer ::toResponse over ::toArray
	 */
	public function toResponse()
	{
		return $this->toArray();
	}
}
