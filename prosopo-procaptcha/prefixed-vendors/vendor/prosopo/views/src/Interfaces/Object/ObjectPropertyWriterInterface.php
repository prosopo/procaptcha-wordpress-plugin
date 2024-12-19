<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Object;

interface ObjectPropertyWriterInterface
{
    public function assignPropertyValues(object $instance, PropertyValueProviderInterface $propertyValueProvider): void;
}
