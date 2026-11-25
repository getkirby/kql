<?php

namespace Kirby\Kql\Interceptors\Cms;

class StructureObject extends Model
{
	public const CLASS_ALIAS = 'structureItem';

	public function allowedMethods(): array
	{
		return array_merge(
			$this->allowedMethodsForSiblings(),
			[
				'content',
				'id',
				'parent',
			]
		);
	}

	/**
	 * Renders the item flat, like its structure does,
	 * minus blocked methods
	 */
	public function toArray(): array|null
	{
		$data = [];

		if ($this->isAllowedMethod('content') === true) {
			$data = $this->object->content()->toArray();
		}

		if ($this->isAllowedMethod('id') === true) {
			$data['id'] = $this->object->id();
		}

		return $data;
	}
}
