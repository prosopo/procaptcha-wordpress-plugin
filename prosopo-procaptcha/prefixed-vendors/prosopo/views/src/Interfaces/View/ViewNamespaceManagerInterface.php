<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\View;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewNamespaceConfig;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewNamespaceModules;
interface ViewNamespaceManagerInterface
{
    public function registerNamespace(string $namespace, ViewNamespaceConfig $config): ViewNamespaceModules;
}
