<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model;

interface ModelNameResolverInterface
{
    public function resolveModelName(TemplateModelInterface $model): string;
}
