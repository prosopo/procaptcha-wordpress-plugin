<?php

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

require_once join( DIRECTORY_SEPARATOR, array( __DIR__, 'src', 'Autoloader.php' ) );

new Autoloader( __NAMESPACE__, __DIR__ . DIRECTORY_SEPARATOR . 'src' );

require_once __DIR__ . '/src/helpers.php';
