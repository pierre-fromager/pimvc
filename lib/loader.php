<?php

/**
 * Description of loader
 *
 * @author pierrefromager
 */

namespace lib;

$libPath = __DIR__ . DIRECTORY_SEPARATOR;
$libHttp = $libPath . 'http' . DIRECTORY_SEPARATOR;

// lib requirements
require_once $libHttp . 'routes.php';
require_once $libHttp . 'router.php';
require_once $libHttp . 'request.php';
require_once $libHttp . 'response.php';
require_once $libPath . 'controller.php';
require_once $libPath . 'controller/basic.php';
require_once $libPath . 'view.php';

// app interfaces
require_once $libPath . '/interfaces/app.php';
