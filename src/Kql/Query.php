<?php

namespace Kirby\Kql;

use Kirby\Query\Query as BaseQuery;

/**
 * Extends the core Query class with the KQL-specific
 * functionalities to intercept the segments chain calls
 *
 * @package   Kirby KQL
 * @author    Nico Hoffmann <nico@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://getkirby.com/license
 */
class Query extends BaseQuery
{
	public function intercept(mixed $result): mixed
	{
		if (is_object($result) === false) {
			return $result;
		}

		return Interceptor::replace($result);
	}
}
