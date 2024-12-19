<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Template;

interface TemplateRendererInterface
{
    /**
     * @param array<string,mixed> $variables
     */
    public function renderTemplate(string $template, array $variables = []): string;
}
