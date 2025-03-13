<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\View;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Object\ObjectClassReader;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Object\PropertyValueProviderForModels;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Object\ObjectReader;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Object\ObjectPropertyWriter;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Object\PropertyValueProvider;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Object\PropertyValueProviderByTypes;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Object\PropertyValueProviderForNullable;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Model\ModelFactory;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Model\ModelFactoryWithDefaultsManagement;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Model\ModelFactoryWithSetupCallback;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Model\ModelNameResolver;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Model\ModelNamespaceResolver;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Model\ModelRenderer;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Model\ModelRendererWithEventDetails;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\EventDispatcher;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Template\FileModelTemplateResolver;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Template\TemplateRendererWithModelsRender;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewNamespaceConfig;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewNamespaceModules;
/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ViewNamespace
{
    private ViewNamespaceModules $modules;
    /**
     * Using the external ViewFactory and ViewRenderer enables us to seamlessly mix Models from different namespaces,
     * even if they use different template engines, such as Blade and Twig.
     * (see the Views class)
     */
    public function __construct(string $namespace, ViewNamespaceConfig $config, ModelFactoryInterface $modelFactoryWithNamespaces, ModelRendererInterface $modelRendererWithNamespace)
    {
        $modules = clone $config->getModules();
        //// 1. Modules creation:
        $templateErrorEventName = $config->getTemplateErrorEventName();
        $eventDispatcher = $modules->getEventDispatcher();
        $eventDispatcher = null === $eventDispatcher ? new EventDispatcher() : $eventDispatcher;
        $templateErrorHandler = $config->getTemplateErrorHandler();
        if (null !== $templateErrorHandler) {
            $eventDispatcher->addEventListener($templateErrorEventName, $templateErrorHandler);
        }
        $objectReader = $modules->getObjectReader();
        $objectReader = null === $objectReader ? new ObjectReader() : $objectReader;
        $objectPropertyWriter = $modules->getObjectPropertyWriter();
        $objectPropertyWriter = null === $objectPropertyWriter ? new ObjectPropertyWriter() : $objectPropertyWriter;
        $modelNamespaceProvider = $modules->getModelNamespaceResolver();
        $modelNamespaceProvider = null === $modelNamespaceProvider ? new ModelNamespaceResolver(new ObjectClassReader()) : $modelNamespaceProvider;
        $modelNameProvider = $modules->getModelNameResolver();
        $modelNameProvider = null === $modelNameProvider ? new ModelNameResolver(new ObjectClassReader()) : $modelNameProvider;
        $modelTemplateResolver = $modules->getModelTemplateResolver();
        $modelTemplateResolver = null === $modelTemplateResolver ? new FileModelTemplateResolver($namespace, $config->getTemplatesRootPath(), $config->getTemplateFileExtension(), $config->fileBasedTemplates(), $modelNamespaceProvider, $modelNameProvider) : $modelTemplateResolver;
        $propertyValueProvider = $modules->getPropertyValueProvider();
        $propertyValueProvider = null === $propertyValueProvider ? new PropertyValueProvider() : $propertyValueProvider;
        $propertyValueProvider = new PropertyValueProviderByTypes($propertyValueProvider, $config->getDefaultPropertyValues());
        $propertyValueProvider = new PropertyValueProviderForModels($propertyValueProvider, $modelFactoryWithNamespaces);
        $propertyValueProvider = new PropertyValueProviderForNullable($propertyValueProvider);
        // Without null check - templateRenderer is a mandatory module.
        $templateRenderer = $modules->getTemplateRenderer();
        $templateRendererWithModelsRender = new TemplateRendererWithModelsRender($templateRenderer, $modelRendererWithNamespace);
        if ($config->modelsAsStringsInTemplates()) {
            $templateRenderer = $templateRendererWithModelsRender;
        }
        //// 2. Real Factory and Renderer creation (used in the ViewsManager class):
        $realModelFactory = $modules->getModelFactory();
        $realModelFactory = null === $realModelFactory ? new ModelFactory($objectReader, $propertyValueProvider, $modelTemplateResolver, $templateRendererWithModelsRender) : $realModelFactory;
        $realModelFactory = new ModelFactoryWithDefaultsManagement(
            $realModelFactory,
            // Plain reader, without rendering.
            $objectReader,
            $objectPropertyWriter
        );
        $realModelFactory = new ModelFactoryWithSetupCallback($realModelFactory);
        $realModelRenderer = $modules->getModelRenderer();
        $realModelRenderer = null === $realModelRenderer ? new ModelRenderer($templateRenderer, $modelFactoryWithNamespaces, $modelTemplateResolver) : $realModelRenderer;
        $realModelRenderer = new ModelRendererWithEventDetails($realModelRenderer, $eventDispatcher, $templateErrorEventName);
        //// 3. Now we can save the objects to the storage.
        $modules->setEventDispatcher($eventDispatcher)->setObjectReader($objectReader)->setObjectPropertyWriter($objectPropertyWriter)->setModelTemplateResolver($modelTemplateResolver)->setPropertyValueProvider($propertyValueProvider)->setModelFactory($realModelFactory)->setModelRenderer($realModelRenderer)->setModelNamespaceResolver($modelNamespaceProvider)->setModelNameResolver($modelNameProvider);
        $this->modules = $modules;
    }
    public function getModules(): ViewNamespaceModules
    {
        return $this->modules;
    }
}
