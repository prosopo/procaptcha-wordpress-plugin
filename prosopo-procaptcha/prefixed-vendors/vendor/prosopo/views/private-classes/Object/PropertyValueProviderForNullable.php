<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Object;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use ReflectionProperty;
/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class PropertyValueProviderForNullable implements PropertyValueProviderInterface
{
    private PropertyValueProviderInterface $propertyValueProvider;
    public function __construct(PropertyValueProviderInterface $propertyValueProvider)
    {
        $this->propertyValueProvider = $propertyValueProvider;
    }
    public function supportsProperty(ReflectionProperty $property): bool
    {
        if ($this->propertyValueProvider->supportsProperty($property)) {
            return \true;
        }
        $type = $property->getType();
        return null !== $type && $type->allowsNull();
    }
    public function getPropertyValue(ReflectionProperty $property)
    {
        if ($this->propertyValueProvider->supportsProperty($property)) {
            return $this->propertyValueProvider->getPropertyValue($property);
        }
        return null;
    }
}
