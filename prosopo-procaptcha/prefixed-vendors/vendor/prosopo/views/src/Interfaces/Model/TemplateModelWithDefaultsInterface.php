<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
interface TemplateModelWithDefaultsInterface
{
    public function getDefaultPropertyValueProvider(): PropertyValueProviderInterface;
}
