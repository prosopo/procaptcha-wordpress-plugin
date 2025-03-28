<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\PrivateClasses\Template;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelNameResolverInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelNamespaceResolverInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Template\FileTemplateContentProviderInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Template\ModelTemplateResolverInterface;
/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class FileModelTemplateResolver implements ModelTemplateResolverInterface
{
    private string $templatesRootPath;
    private string $namespace;
    private string $extension;
    private bool $isFileBasedTemplate;
    private FileTemplateContentProviderInterface $fileTemplateContentProvider;
    private ModelNameResolverInterface $modelNameProvider;
    private ModelNamespaceResolverInterface $modelNamespaceProvider;
    public function __construct(string $namespace, string $templatesRootPath, string $extension, bool $isFileBasedTemplate, FileTemplateContentProviderInterface $fileTemplateContentProvider, ModelNamespaceResolverInterface $modelNamespaceProvider, ModelNameResolverInterface $modelNameProvider)
    {
        $this->templatesRootPath = $templatesRootPath;
        $this->namespace = $namespace;
        $this->extension = $extension;
        $this->fileTemplateContentProvider = $fileTemplateContentProvider;
        $this->isFileBasedTemplate = $isFileBasedTemplate;
        $this->modelNameProvider = $modelNameProvider;
        $this->modelNamespaceProvider = $modelNamespaceProvider;
    }
    public function resolveModelTemplate(TemplateModelInterface $model): string
    {
        $modelNamespace = $this->modelNamespaceProvider->resolveModelNamespace($model);
        $relativeModelNamespace = substr($modelNamespace, strlen($this->namespace));
        $relativeModelNamespace = ltrim($relativeModelNamespace, '\\');
        $modelName = $this->modelNameProvider->resolveModelName($model);
        $relativeTemplatePath = $this->getRelativeTemplatePath($relativeModelNamespace, $modelName);
        $absoluteTemplatePath = $this->getAbsoluteTemplatePath($relativeTemplatePath);
        return $this->isFileBasedTemplate ? $absoluteTemplatePath : $this->fileTemplateContentProvider->getFileTemplateContent($absoluteTemplatePath);
    }
    protected function getAbsoluteTemplatePath(string $relativeTemplatePath): string
    {
        return rtrim($this->templatesRootPath, '/') . \DIRECTORY_SEPARATOR . $relativeTemplatePath . $this->extension;
    }
    protected function getRelativeTemplatePath(string $relativeModelNamespace, string $modelName): string
    {
        $relativeModelPath = str_replace('\\', \DIRECTORY_SEPARATOR, $relativeModelNamespace);
        $modelName = (string) preg_replace('/([a-z])([A-Z])/', '$1-$2', $modelName);
        $relativeTemplatePath = $relativeModelPath;
        $relativeTemplatePath .= '' !== $relativeTemplatePath ? \DIRECTORY_SEPARATOR : '';
        $relativeTemplatePath .= strtolower($modelName);
        return $relativeTemplatePath;
    }
}
