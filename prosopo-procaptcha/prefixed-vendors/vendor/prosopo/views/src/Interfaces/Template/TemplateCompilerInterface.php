<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Template;

interface TemplateCompilerInterface
{
    public function compileTemplate(string $template): string;
}
