<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Model;

use Closure;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Template\ModelTemplateResolverInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ModelRenderer implements ModelRendererInterface
{
    private TemplateRendererInterface $templateRenderer;
    private ModelFactoryInterface $viewFactory;
    private ModelTemplateResolverInterface $templateProvider;
    public function __construct(TemplateRendererInterface $templateRenderer, ModelFactoryInterface $modelFactory, ModelTemplateResolverInterface $templateProvider)
    {
        $this->templateRenderer = $templateRenderer;
        $this->viewFactory = $modelFactory;
        $this->templateProvider = $templateProvider;
    }
    public function renderModel($modelOrClass, ?Closure $setupModelCallback = null): string
    {
        $model = is_string($modelOrClass) ? $this->viewFactory->createModel($modelOrClass) : $modelOrClass;
        if (null !== $setupModelCallback) {
            $setupModelCallback($model);
        }
        $variables = $model->getTemplateArguments();
        $template = $this->templateProvider->resolveModelTemplate($model);
        return $this->templateRenderer->renderTemplate($template, $variables);
    }
}
