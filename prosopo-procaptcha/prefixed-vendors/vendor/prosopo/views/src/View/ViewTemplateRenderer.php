<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Blade\BladeCompiler;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\CodeRunner\CodeRunnerWithErrorHandler;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\CodeRunner\CodeRunnerWithGlobalArguments;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\CodeRunner\CodeRunnerWithTemplateCompilation;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\CodeRunner\PhpCodeRunner;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\EventDispatcher;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Template\TemplateRenderer;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Template\TemplateRendererWithCustomEscape;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Template\TemplateRendererWithEventDetails;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Template\TemplateRendererWithFileTemplate;
/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties, and introduce new public ones.
 */
final class ViewTemplateRenderer implements TemplateRendererInterface
{
    private TemplateRendererInterface $templateRenderer;
    private ViewTemplateRendererModules $modules;
    public function __construct(?ViewTemplateRendererConfig $config = null)
    {
        $config = null === $config ? new ViewTemplateRendererConfig() : $config;
        // Clone, as we're going to modify it.
        $modules = clone $config->getModules();
        $errorEventName = $config->getTemplateErrorEventName();
        $eventDispatcher = $modules->getEventDispatcher();
        $eventDispatcher = null === $eventDispatcher ? new EventDispatcher() : $eventDispatcher;
        $templateErrorHandler = $config->getTemplateErrorHandler();
        if (null !== $templateErrorHandler) {
            $eventDispatcher->addEventListener($errorEventName, $templateErrorHandler);
        }
        $templateCompiler = $modules->getTemplateCompiler();
        $templateCompiler = null === $templateCompiler ? new BladeCompiler($config->getEscapeVariableName(), $config->getCompilerExtensionCallback()) : $templateCompiler;
        $codeExecutor = $modules->getCodeExecutor();
        $codeExecutor = null === $codeExecutor ? new PhpCodeRunner() : $codeExecutor;
        $codeExecutor = new CodeRunnerWithErrorHandler($codeExecutor, $eventDispatcher, $errorEventName);
        $codeExecutor = new CodeRunnerWithGlobalArguments($codeExecutor, $config->getGlobalVariables());
        $codeExecutor = new CodeRunnerWithTemplateCompilation($codeExecutor, $templateCompiler);
        $templateRenderer = $modules->getTemplateRenderer();
        $templateRenderer = null === $templateRenderer ? new TemplateRenderer($codeExecutor) : $templateRenderer;
        if (\true === $config->fileBasedTemplates()) {
            $templateRenderer = new TemplateRendererWithFileTemplate($templateRenderer);
        }
        $templateRenderer = new TemplateRendererWithCustomEscape($templateRenderer, $config->getCustomOutputEscapeCallback(), $config->getEscapeVariableName());
        $templateRenderer = new TemplateRendererWithEventDetails($templateRenderer, $eventDispatcher, $errorEventName);
        $modules->setEventDispatcher($eventDispatcher)->setTemplateCompiler($templateCompiler)->setTemplateRenderer($templateRenderer)->setCodeExecutor($codeExecutor);
        $this->modules = $modules;
        $this->templateRenderer = $templateRenderer;
    }
    public function renderTemplate(string $template, array $variables = []): string
    {
        return $this->templateRenderer->renderTemplate($template, $variables);
    }
    public function getModules(): ViewTemplateRendererModules
    {
        return $this->modules;
    }
}
