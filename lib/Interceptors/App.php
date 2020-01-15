<?php

namespace Kirby\Kql\Interceptors;

class App extends Interceptor
{

    const CLASS_ALIAS = 'kirby';

    protected $toArray = [
        'site',
        'url'
    ];

    public function allowedMethods(): array
    {
        return [
            'collection',
            'defaultLanguage',
            'detectedLanguage',
            'draft',
            'file',
            'language',
            'languageCode',
            'languages',
            'multilang',
            'page',
            'roles',
            'site',
            'translation',
            'translations',
            'url',
            'user',
            'users',
            'version'
        ];
    }

}
