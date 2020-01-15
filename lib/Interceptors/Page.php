<?php

namespace Kirby\Kql\Interceptors;

use Exception;

class Page extends Model
{

    const CLASS_ALIAS = 'page';

    protected $toArray = [
        'children',
        'drafts',
        'files',
        'id',
        'intendedTemplate',
        'isHomePage',
        'isErrorPage',
        'num',
        'template',
        'title',
        'slug',
        'status',
        'uid',
        'url'
    ];

    public function allowedMethods(): array
    {
        return array_merge(
            $this->allowedMethodsForChildren(),
            $this->allowedMethodsForFiles(),
            $this->allowedMethodsForModels(),
            $this->allowedMethodsForParents(),
            [
                'blueprints',
                'depth',
                'hasTemplate',
                'intendedTemplate',
                'isDraft',
                'isErrorPage',
                'isHomePage',
                'isHomeOrErrorPage',
                'isListed',
                'isReadable',
                'isSortable',
                'isUnlisted',
                'num',
                'slug',
                'status',
                'template',
                'title',
                'uid',
                'uri',
            ]
        );
    }

    public function intendedTemplate(): string
    {
        return $this->object->intendedTemplate()->name();
    }

    public function template(): string
    {
        return $this->object->template()->name();
    }

}
