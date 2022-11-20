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
