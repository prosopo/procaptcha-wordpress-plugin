<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Object;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use ReflectionProperty;
/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class PropertyValueProviderForModels implements PropertyValueProviderInterface
{
    private PropertyValueProviderInterface $propertyValueProvider;
    private ModelFactoryInterface $modelFactory;
    public function __construct(PropertyValueProviderInterface $propertyValueProvider, ModelFactoryInterface $viewFactory)
    {
        $this->propertyValueProvider = $propertyValueProvider;
        $this->modelFactory = $viewFactory;
    }
    public function supportsProperty(ReflectionProperty $property): bool
    {
        if ($this->propertyValueProvider->supportsProperty($property)) {
            return \true;
        }
        $type = $this->getPropertyType($property);
        return null !== $this->getValidModelClass($type);
    }
    public function getPropertyValue(ReflectionProperty $property)
    {
        if ($this->propertyValueProvider->supportsProperty($property)) {
            return $this->propertyValueProvider->getPropertyValue($property);
        }
        $type = $this->getPropertyType($property);
        $modelClassString = $this->getValidModelClass($type);
        return null !== $modelClassString ? $this->modelFactory->createModel($modelClassString) : null;
    }
    /**
     * @param class-string<TemplateModelInterface>|string $propertyType
     *
     * @return class-string<TemplateModelInterface>|null
     */
    protected function getValidModelClass(string $propertyType)
    {
        return class_exists($propertyType) && is_a($propertyType, TemplateModelInterface::class, \true) ? $propertyType : null;
    }
    protected function getPropertyType(ReflectionProperty $property): string
    {
        $reflectionType = $property->getType();
        return null !== $reflectionType ? $reflectionType->getName() : '';
    }
}
