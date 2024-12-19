<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model;

interface ModelNamespaceResolverInterface
{
    /**
     * @template T of TemplateModelInterface
     *
     * @param T|class-string<T> $modelOrClass
     */
    public function resolveModelNamespace($modelOrClass): string;
}
