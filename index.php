<?php

load([
    'kirby\kql\help'                         => __DIR__ . '/lib/Help.php',
    'kirby\kql\interceptor'                  => __DIR__ . '/lib/Interceptor.php',
    'kirby\kql\kql'                          => __DIR__ . '/lib/Kql.php',
    'kirby\kql\query'                        => __DIR__ . '/lib/Query.php',
    'kirby\kql\interceptors\app'             => __DIR__ . '/lib/Interceptors/App.php',
    'kirby\kql\interceptors\blueprint'       => __DIR__ . '/lib/Interceptors/Blueprint.php',
    'kirby\kql\interceptors\collection'      => __DIR__ . '/lib/Interceptors/Collection.php',
    'kirby\kql\interceptors\content'         => __DIR__ . '/lib/Interceptors/Content.php',
    'kirby\kql\interceptors\field'           => __DIR__ . '/lib/Interceptors/Field.php',
    'kirby\kql\interceptors\file'            => __DIR__ . '/lib/Interceptors/File.php',
    'kirby\kql\interceptors\files'           => __DIR__ . '/lib/Interceptors/Files.php',
    'kirby\kql\interceptors\interceptor'     => __DIR__ . '/lib/Interceptors/Interceptor.php',
    'kirby\kql\interceptors\model'           => __DIR__ . '/lib/Interceptors/Model.php',
    'kirby\kql\interceptors\page'            => __DIR__ . '/lib/Interceptors/Page.php',
    'kirby\kql\interceptors\pages'           => __DIR__ . '/lib/Interceptors/Pages.php',
    'kirby\kql\interceptors\role'            => __DIR__ . '/lib/Interceptors/Role.php',
    'kirby\kql\interceptors\site'            => __DIR__ . '/lib/Interceptors/Site.php',
    'kirby\kql\interceptors\structureobject' => __DIR__ . '/lib/Interceptors/StructureObject.php',
    'kirby\kql\interceptors\user'            => __DIR__ . '/lib/Interceptors/User.php',
    'kirby\kql\interceptors\users'           => __DIR__ . '/lib/Interceptors/Users.php',
]);

class_alias('Kirby\Kql\Kql', 'Kql');

function kql($input, $model = null) {
    return Kql::run($input, $model);
}

Kirby::plugin('getkirby/kql', [
    'api' => [
        'routes' => [
            [
                'pattern' => 'query',
                'method' => 'POST|GET',
                'action' => function () {

                    $result = Kql::run([
                        'query'      => get('query'),
                        'select'     => get('select'),
                        'pagination' => [
                            'page'  => get('page', 1),
                            'limit' => get('limit', 100)
                        ]
                    ]);

                    return [
                        'code'   => 200,
                        'result' => $result,
                        'status' => 'ok',
                    ];

                }
            ]
        ]
    ]
]);
