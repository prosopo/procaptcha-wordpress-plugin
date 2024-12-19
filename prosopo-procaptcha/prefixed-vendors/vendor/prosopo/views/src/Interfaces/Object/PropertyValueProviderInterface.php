<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Object;

use ReflectionProperty;
interface PropertyValueProviderInterface
{
    public function supportsProperty(ReflectionProperty $property): bool;
    /**
     * @return mixed
     */
    public function getPropertyValue(ReflectionProperty $property);
}
