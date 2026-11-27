<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Cms\File as FileObject;

class FileVersion extends File
{
	public const CLASS_ALIAS = 'file';

	public function __call($method, array $args = [])
	{
		if ($this->isAllowedMethod($method) === true) {
			return $this->callMethod($method, $args);
		}

		// the content field fallback of `Kirby\Cms\FileVersion::__call()`,
		// without its protected property access
		if (
			method_exists($this->object, $method) === false &&
			method_exists($this->object->asset(), $method) === false &&
			$this->object->original() instanceof FileObject
		) {
			return $this->object->original()->content()->get($method);
		}

		$this->forbiddenMethod($method);
	}

	public function allowedMethods(): array
	{
		return [
			'dataUri',
			'dimensions',
			'exif',
			'exists',
			'extension',
			'filename',
			'height',
			'html',
			'id',
			'isLandscape',
			'isPortrait',
			'isSquare',
			'mime',
			'modifications',
			'modified',
			'name',
			'niceSize',
			'orientation',
			'original',
			'ratio',
			'size',
			'template',
			'type',
			'url',
			'width'
		];
	}
}
