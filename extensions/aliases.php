<?php

class_alias(\Kirby\Kql\Kql::class, 'Kql');
class_alias(\Kirby\Kql\Interceptor::class, 'Kirby\Kql\Interceptors\Interceptor');

// Provide backwards compatibility for Kirby 3 core classes
class_alias(\Kirby\Kql\Interceptors\Content\Content::class, 'Kirby\Kql\Interceptors\Cms\Content');
class_alias(\Kirby\Kql\Interceptors\Content\Field::class, 'Kirby\Kql\Interceptors\Cms\Field');
