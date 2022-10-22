<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__) . '/vendor/autoload.php';
$env = getenv("SYMFONY_ENV");
$env = "dev";

$isDebug = $env != "prod";
$kernel = new Kernel($env, $isDebug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);


header('Access-Control-Allow-Origin: *');
header(
    "Access-Control-Allow-Headers: " .
    "X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, " .
    "x-client-type, x-client-version, x-client-build"
);
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}

$response->send();
$kernel->terminate($request, $response);
