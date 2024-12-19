<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\View;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\View\ViewNamespaceModulesContainerInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewNamespaceModules;
class ViewNamespaceModulesContainer implements ViewNamespaceModulesContainerInterface
{
    /**
     * @var array<string, ViewNamespaceModules>
     */
    private array $viewNamespaceModules;
    public function __construct()
    {
        $this->viewNamespaceModules = [];
    }
    public function registerNamespaceModules(string $namespace, ViewNamespaceModules $viewNamespaceModules): void
    {
        $this->viewNamespaceModules[$namespace] = $viewNamespaceModules;
        // Sort to ensure the rule is followed: more specific namespaces take precedence.
        // For example, /My/Package/Blade will be processed before the more generic /My/Package.
        uksort($this->viewNamespaceModules, function (string $key1, string $key2): int {
            return strlen($key2) <=> strlen($key1);
        });
    }
    public function resolveNamespaceModules(string $modelNamespace): ?ViewNamespaceModules
    {
        $matchedNamespaces = array_filter($this->viewNamespaceModules, function (ViewNamespaceModules $viewNamespaceModules, string $namespace) use ($modelNamespace) {
            return 0 === strpos($modelNamespace, $namespace);
        }, \ARRAY_FILTER_USE_BOTH);
        return count($matchedNamespaces) > 0 ? reset($matchedNamespaces) : null;
    }
}
