<?php
// Front Controller con FastRoute + fallback legacy para /src

// 1) Autoload y bootstrap global (solo se ejecutan una vez por petición)
require_once __DIR__ . '/../apps/core/global_header.inc';
require_once __DIR__ . '/../apps/core/global_object.inc';

use DI\ContainerBuilder;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

/* Lo pongo en global_object.inc para que esté accesible desde apps. 
Cuando vuelva aquí OJO con el '*' de glob:
// 2) Contenedor DI (si no existe ya) y exponerlo globalmente
if (!isset($GLOBALS['container'])) {
    $builder = new ContainerBuilder();

    // Cargar rutas por módulo: cada módulo define sus rutas en src/<modulo>/config/routes.php
    $dependenciesFiles = glob(__DIR__ . '/../src/ * /config/dependencies.php');
    if (is_array($dependenciesFiles)) {
        foreach ($dependenciesFiles as $dependenciesFile) {
            $builder->addDefinitions($dependenciesFile);
        }
    }

    // Cache de compilación en producción
    if (class_exists(ConfigGlobal::class) && !ConfigGlobal::is_debug_mode()) {
        $cacheDir = __DIR__ . '/../var/cache/php-di';
        if (!is_dir($cacheDir)) {
            if (!mkdir($cacheDir, 0775, true) && !is_dir($cacheDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $cacheDir));
            }
        }
        $builder->enableCompilation($cacheDir);
        $builder->writeProxiesToFile(true, $cacheDir . '/proxies');
    }

    $GLOBALS['container'] = $builder->build();
}
$container = $GLOBALS['container'];
*/


// 3) Fallback legacy para /src (si Nginx reescribe /<base>/src/... a ?r=/src/...)
//    Lo ponemos ANTES del router para que sea inmediato.
$projectRoot = dirname(__DIR__) . ''; // /home/.../orbix
$legacyR = $_GET['r'] ?? null;            // p.ej. /src/usuarios/.../controller.php
if (is_string($legacyR) && str_starts_with($legacyR, '/src/')) {
    // Construir ruta absoluta y validar que está dentro del proyecto
    $candidate = realpath($projectRoot . '/' . ltrim($legacyR, '/'));
    if ($candidate && is_file($candidate) && str_starts_with($candidate, $projectRoot . DIRECTORY_SEPARATOR)) {
        // Algunos controladores legacy esperan $container en ámbito global/locale
        //$container = $GLOBALS['container'];
        require $candidate;
        return; // Evita seguir al router
    }
    // Si no existe el archivo, devolvemos 404 para no enmascarar errores
    http_response_code(404);
    echo '404 Not Found (legacy src)';
    return;
}

// 4) Dispatcher de rutas (FastRoute)
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // Cargar rutas por módulo: cada módulo define sus rutas en src/<modulo>/config/routes.php
    $routesFiles = glob(__DIR__ . '/../src/*/config/routes.php');
    if (is_array($routesFiles)) {
        foreach ($routesFiles as $routesFile) {
            $maybeCallable = require $routesFile;
            if (is_callable($maybeCallable)) {
                $maybeCallable($r);
            }
        }
    }

    // Ruta por defecto (home). Ajusta el handler según tu inicio deseado
    $r->addRoute(['GET', 'POST'], '/', function () {
        require __DIR__ . '/../index.php';
    });
});

// 5) Resolver método y URI de la petición
$httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

// Eliminar query string para el matching
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);


// 6) Despachar
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 Not Found';
        break;

    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        $allowedMethods = $routeInfo[1] ?? [];
        echo '405 Method Not Allowed. Allowed: ' . implode(', ', (array)$allowedMethods);
        break;

    case \FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2] ?? [];

        // Handler callable: lo invocamos
        if (is_callable($handler)) {
            // Compatibilidad: exponer contenedor en scope local si el handler incluye legacy
            //$container = $GLOBALS['container'];
            $handler($vars);
            break;
        }

        // Handler string: interpretar como ruta a archivo
        if (is_string($handler)) {
            //$container = $GLOBALS['container'];
            require $handler;
            break;
        }

        // Handler array [Clase::class, 'método'] (si refactorizas a clases)
        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
            $obj = new $class($container);
            $obj->$method(...array_values($vars));
            break;
        }

        throw new RuntimeException('Handler de ruta inválido');
}
