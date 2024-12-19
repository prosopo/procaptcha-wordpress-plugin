<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model;

use Exception;
interface ModelFactoryInterface
{
    /**
     * @template T of TemplateModelInterface
     *
     * @param class-string<T> $modelClass
     *
     * @return T
     *
     * @throws Exception
     */
    public function createModel(string $modelClass);
}
