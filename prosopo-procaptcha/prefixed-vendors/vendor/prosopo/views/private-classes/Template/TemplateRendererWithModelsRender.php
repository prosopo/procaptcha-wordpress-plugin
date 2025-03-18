<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Template;

use Exception;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class TemplateRendererWithModelsRender implements TemplateRendererInterface
{
    private TemplateRendererInterface $templateRenderer;
    private ModelRendererInterface $modelRenderer;
    public function __construct(TemplateRendererInterface $templateRenderer, ModelRendererInterface $modelRenderer)
    {
        $this->templateRenderer = $templateRenderer;
        $this->modelRenderer = $modelRenderer;
    }
    public function renderTemplate(string $template, array $variables = []): string
    {
        $variables = $this->renderModels($variables);
        return $this->templateRenderer->renderTemplate($template, $variables);
    }
    /**
     * @param array<string,mixed> $variables
     *
     * @return array<string,mixed>
     */
    protected function renderModels(array $variables): array
    {
        return array_map(function ($item) {
            return $this->renderIfModel($item);
        }, $variables);
    }
    /**
     * @param mixed $item
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function renderIfModel($item)
    {
        if ($item instanceof TemplateModelInterface) {
            $item = $this->modelRenderer->renderModel($item);
        } elseif (is_array($item) && !is_callable($item)) {
            // @phpstan-ignore-next-line
            $item = $this->renderModels($item);
        }
        return $item;
    }
}
