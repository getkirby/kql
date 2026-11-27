<?php

class_alias(\Kirby\Kql\Kql::class, 'Kql');
class_alias(\Kirby\Kql\Interceptor::class, 'Kirby\Kql\Interceptors\Interceptor');

// interceptors that moved with Kirby 4's `Kirby\Content` classes
class_alias(\Kirby\Kql\Interceptors\Content\Content::class, 'Kirby\Kql\Interceptors\Cms\Content');
class_alias(\Kirby\Kql\Interceptors\Content\Field::class, 'Kirby\Kql\Interceptors\Cms\Field');

// interceptors that moved with Kirby 6's `Kirby\Blueprint` classes
class_alias(\Kirby\Kql\Interceptors\Blueprint\Blueprint::class, 'Kirby\Kql\Interceptors\Cms\Blueprint');
