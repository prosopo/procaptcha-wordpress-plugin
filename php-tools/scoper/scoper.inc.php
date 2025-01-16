<?php

declare(strict_types=1);

// scoper.inc.php

use Isolated\Symfony\Component\Finder\Finder;

return [
    'prefix' => 'Io\\Prosopo\\Procaptcha\\Vendors',  // string|null
    'php-version' => '7.4',      // string|null
    'output-dir' => null,       // string|null
    'finders' => [  // list<Finder>
        Finder::create()->files()->in('./../origin-vendors')
    ],
    'patchers' => [],           // list<callable(string $filePath, string $prefix, string $contents): string>

    'exclude-files' => [],      // list<string>
    'exclude-namespaces' => [
        'Io\\Prosopo\\Procaptcha'
    ], // list<string|regex>
    'exclude-constants' => [],  // list<string|regex>
    'exclude-classes' => [],    // list<string|regex>
    'exclude-functions' => [],  // list<string|regex>
];