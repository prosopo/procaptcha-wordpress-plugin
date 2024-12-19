<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties and introduce new public ones.
 */
final class ViewNamespaceConfig
{
    private string $templatesRootPath;
    private string $templateFileExtension;
    private bool $fileBasedTemplates;
    private bool $modelsAsStringsInTemplates;
    /**
     * @var callable(array<string,mixed> $eventDetails): void|null
     */
    private $templateErrorHandler;
    private string $templateErrorEventName;
    /**
     * @var array<string,mixed>
     */
    private array $defaultPropertyValues;
    private ViewNamespaceModules $modules;
    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        $this->templatesRootPath = '';
        $this->templateFileExtension = '';
        $this->fileBasedTemplates = \true;
        $this->modelsAsStringsInTemplates = \false;
        $this->templateErrorHandler = null;
        $this->defaultPropertyValues = array('array' => array(), 'bool' => \false, 'float' => 0.0, 'int' => 0, 'string' => '');
        $this->templateErrorEventName = 'template_error';
        $this->modules = new ViewNamespaceModules($templateRenderer);
    }
    //// Getters.
    public function getTemplatesRootPath(): string
    {
        return $this->templatesRootPath;
    }
    public function getTemplateFileExtension(): string
    {
        return $this->templateFileExtension;
    }
    public function fileBasedTemplates(): bool
    {
        return $this->fileBasedTemplates;
    }
    public function modelsAsStringsInTemplates(): bool
    {
        return $this->modelsAsStringsInTemplates;
    }
    /**
     * @return  callable(array<string,mixed> $eventDetails): void|null
     */
    public function getTemplateErrorHandler(): ?callable
    {
        return $this->templateErrorHandler;
    }
    public function getTemplateErrorEventName(): string
    {
        return $this->templateErrorEventName;
    }
    /**
     * @return array<string,mixed>
     */
    public function getDefaultPropertyValues(): array
    {
        return $this->defaultPropertyValues;
    }
    public function getModules(): ViewNamespaceModules
    {
        return $this->modules;
    }
    //// Setters:
    public function setTemplatesRootPath(string $templatesRootPath): self
    {
        $this->templatesRootPath = $templatesRootPath;
        return $this;
    }
    public function setTemplateFileExtension(string $templateFileExtension): self
    {
        $this->templateFileExtension = $templateFileExtension;
        return $this;
    }
    public function setFileBasedTemplates(bool $fileBasedTemplates): self
    {
        $this->fileBasedTemplates = $fileBasedTemplates;
        return $this;
    }
    public function setModelsAsStringsInTemplates(bool $modelsAsStringsInTemplates): self
    {
        $this->modelsAsStringsInTemplates = $modelsAsStringsInTemplates;
        return $this;
    }
    /**
     * @param callable(array<string,mixed> $eventDetails): void|null $templateErrorHandler
     */
    public function setTemplateErrorHandler(?callable $templateErrorHandler): self
    {
        $this->templateErrorHandler = $templateErrorHandler;
        return $this;
    }
    /**
     * @param array<string,mixed> $defaultPropertyValues
     */
    public function setDefaultPropertyValues(array $defaultPropertyValues): self
    {
        $this->defaultPropertyValues = $defaultPropertyValues;
        return $this;
    }
    public function setTemplateErrorEventName(string $templateErrorEventName): self
    {
        $this->templateErrorEventName = $templateErrorEventName;
        return $this;
    }
    public function setModules(ViewNamespaceModules $modules): self
    {
        $this->modules = $modules;
        return $this;
    }
}
