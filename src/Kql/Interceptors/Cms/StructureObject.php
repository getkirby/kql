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
	 * but without the names the block list removes
	 */
	public function toArray(): array|null
	{
		return [
			...$this->isAllowedMethod('content') ? $this->object->content()->toArray() : [],
			...$this->isAllowedMethod('id') ? ['id' => $this->object->id()] : []
		];
	}
}
