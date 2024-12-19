<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Template;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
interface ModelTemplateResolverInterface
{
    public function resolveModelTemplate(TemplateModelInterface $model): string;
}
