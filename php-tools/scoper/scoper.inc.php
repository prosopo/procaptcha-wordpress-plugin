<?php

declare(strict_types=1);

// scoper.inc.php

use Isolated\Symfony\Component\Finder\Finder;

return [
    'prefix' => 'Io\\Prosopo\\Procaptcha\\Vendors',  // string|null
    'php-version' => null,      // string|null
    'output-dir' => '../../prosopo-procaptcha/prefixed-vendors',       // string|null
    'finders' => [  // list<Finder>
        Finder::create()->files()->in('../origin-vendors/vendor')
    ],
    'patchers' => [],           // list<callable(string $filePath, string $prefix, string $contents): string>

    'exclude-files' => [],      // list<string>
    'exclude-namespaces' => [], // list<string|regex>
    'exclude-constants' => [],  // list<string|regex>
    'exclude-classes' => [],    // list<string|regex>
    'exclude-functions' => [],  // list<string|regex>
];