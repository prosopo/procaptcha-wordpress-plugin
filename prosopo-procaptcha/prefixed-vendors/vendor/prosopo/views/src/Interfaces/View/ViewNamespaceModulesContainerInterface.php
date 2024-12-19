<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\View;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewNamespaceModules;
interface ViewNamespaceModulesContainerInterface
{
    public function registerNamespaceModules(string $namespace, ViewNamespaceModules $viewNamespaceModules): void;
    public function resolveNamespaceModules(string $modelNamespace): ?ViewNamespaceModules;
}
