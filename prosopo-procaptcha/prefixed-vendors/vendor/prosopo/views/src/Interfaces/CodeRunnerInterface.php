<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces;

interface CodeRunnerInterface
{
    /**
     * @param array<string,mixed> $arguments
     */
    public function runCode(string $code, array $arguments = []): void;
}
