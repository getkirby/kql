<?php

namespace Kirby\Kql\Interceptors;

class File extends Model
{

    const CLASS_ALIAS = 'file';

    protected $toArray = [
        'extension',
        'filename',
        'height',
        'id',
        'mime',
        'niceSize',
        'template',
        'type',
        'url',
        'width'
    ];

    public function allowedMethods(): array
    {
        return array_merge(
            $this->allowedMethodsForModels(),
            $this->allowedMethodsForParents(),
            $this->allowedMethodsForSiblings(),
            [
                'dataUri',
                'dimensions',
                'exif',
                'extension',
                'filename',
                'files',
                'height',
                'isPortrait',
                'isLandscape',
                'isSquare',
                'mime',
                'name',
                'niceSize',
                'orientation',
                'ratio',
                'size',
                'template',
                'templateSiblings',
                'type',
                'width'
            ]
        );
    }

    public function dimensions(): array
    {
        return $this->object->dimensions()->toArray();
    }

    public function exif(): array
    {
        return $this->object->exif()->toArray();
    }

}
