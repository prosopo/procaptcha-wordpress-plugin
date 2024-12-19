<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Model;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Template\ModelTemplateResolverInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ModelFactory implements ModelFactoryInterface
{
    private ObjectReaderInterface $objectReader;
    private PropertyValueProviderInterface $propertyValueProvider;
    private ModelTemplateResolverInterface $modelTemplateResolver;
    private TemplateRendererInterface $templateRenderer;
    public function __construct(ObjectReaderInterface $objectReader, PropertyValueProviderInterface $propertyValueProvider, ModelTemplateResolverInterface $modelTemplateResolver, TemplateRendererInterface $templateRenderer)
    {
        $this->objectReader = $objectReader;
        $this->propertyValueProvider = $propertyValueProvider;
        $this->modelTemplateResolver = $modelTemplateResolver;
        $this->templateRenderer = $templateRenderer;
    }
    public function createModel(string $modelClass)
    {
        return new $modelClass($this->objectReader, $this->propertyValueProvider, $this->modelTemplateResolver, $this->templateRenderer);
    }
}
