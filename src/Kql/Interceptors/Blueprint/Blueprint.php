<?php

namespace Kirby\Kql\Interceptors\Blueprint;

use Kirby\Kql\Interceptor;

class Blueprint extends Interceptor
{
	public const CLASS_ALIAS = 'blueprint';

	protected $toArray = [
		'description',
		'fields',
		'isDefault',
		'name',
		'options',
		'tabs',
		'title',
	];

	public function allowedMethods(): array
	{
		return [
			'description',
			'field',
			'fields',
			'isDefault',
			'name',
			'options',
			'tab',
			'tabs',
			'title',
		];
	}

	public function fields(): array
	{
		return $this->object->fields();
	}

	public function tab(string|null $name = null): array|null
	{
		$tab = $this->object->tab($name);

		if ($tab === null) {
			return null;
		}

		$props = $tab->toViewProps();

		// only list the field names for each column,
		// the full field definitions are available via `fields`
		foreach ($props['columns'] as $columnIndex => $column) {
			$props['columns'][$columnIndex]['fields'] = array_keys($column['fields'] ?? []);
		}

		return $props;
	}

	public function tabs(): array
	{
		$tabs = [];

		foreach ($this->object->tabs() as $tab) {
			$tabs[$tab->name()] = $this->tab($tab->name());
		}

		return $tabs;
	}
}
