<?php

declare(strict_types = 1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\Class_\CoversAnnotationWithValueToAttributeRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\DataProviderAnnotationToAttributeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
	->withPaths([
		__DIR__ . '/tests/Kql',
	])
	->withRules([
		CoversAnnotationWithValueToAttributeRector::class,
		DataProviderAnnotationToAttributeRector::class,
		AddVoidReturnTypeWhereNoReturnRector::class
	])
	->withImportNames();
