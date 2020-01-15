<?php

namespace Kirby\Kql\Interceptors;

use Exception;

class Model extends Interceptor
{

    public function __call($method, array $args = [])
    {
        if ($this->isAllowedMethod($method) === true) {
            return $this->object->$method(...$args);
        }

        if (method_exists($this->object, $method) === false) {
            return $this->object->content()->get($method);
        }

        $this->forbiddenMethod($method);
    }

    protected function allowedMethodsForChildren()
    {
        return [
            'children',
            'childrenAndDrafts',
            'draft',
            'drafts',
            'find',
            'findPageOrDraft',
            'grandChildren',
            'hasChildren',
            'hasDrafts',
            'hasListedChildren',
            'hasUnlistedChildren',
            'index',
            'search',
        ];
    }

    protected function allowedMethodsForFiles()
    {
        return [
            'audio',
            'code',
            'documents',
            'file',
            'files',
            'hasAudio',
            'hasCode',
            'hasDocuments',
            'hasFiles',
            'hasImages',
            'hasVideos',
            'image',
            'images',
            'videos'
        ];
    }

    protected function allowedMethodsForModels()
    {
        return [
            'apiUrl',
            'blueprint',
            'content',
            'dragText',
            'exists',
            'id',
            'mediaUrl',
            'modified',
            'permissions',
            'panelIcon',
            'panelId',
            'panelPath',
            'panelUrl',
            'previewUrl',
            'url'
        ];
    }

    protected function allowedMethodsForParents()
    {
        return [
            'parent',
            'parentId',
            'parentModel',
            'site',
        ];
    }

}
