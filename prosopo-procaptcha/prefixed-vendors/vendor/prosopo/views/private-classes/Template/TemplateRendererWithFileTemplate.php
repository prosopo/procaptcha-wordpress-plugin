<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Template;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class TemplateRendererWithFileTemplate implements TemplateRendererInterface
{
    private TemplateRendererInterface $templateRenderer;
    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }
    public function renderTemplate(string $template, array $variables = []): string
    {
        $template = $this->getFileContent($template);
        return $this->templateRenderer->renderTemplate($template, $variables);
    }
    protected function getFileContent(string $file): string
    {
        if (\false === file_exists($file)) {
            return '';
        }
        return (string) file_get_contents($file);
    }
}
