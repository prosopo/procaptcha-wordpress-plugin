<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Object;

interface ObjectReaderInterface
{
    /**
     * @return array<string,mixed> name => value (or callback)
     */
    public function extractObjectVariables(object $instance): array;
}
