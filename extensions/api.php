<?php

use Kirby\Kql\Kql;

return [
	'routes' => function ($kirby) {
		return [
			[
				'pattern' => 'query',
				'method'  => 'POST|GET',
				'auth'    => $kirby->option('kql.auth') === false ? false : true,
				'action'  => function () use ($kirby) {
					// set the Kirby language in multilanguage sites
					$languageCode = $kirby->request()->header('X-Language');
					if (
						$kirby->multilang() === true &&
						is_string($languageCode) === true
					) {
						$kirby->setCurrentLanguage($languageCode);
					}
					
					$input = $kirby->request()->get();
					$result = Kql::run($input);

					return [
						'code'   => 200,
						'result' => $result,
						'status' => 'ok',
					];
				}
			]
		];
	}
];
