<?php
// Front Controller con FastRoute + fallback legacy para /src

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\logging\GestorErrores;
use DI\ContainerBuilder;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

/**
 * Bootstrap mínimo para rutas públicas de recuperación (sin sesión autenticada).
 */
function bootstrapAnonymousSrcRequest(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        if (!empty($_COOKIE['PHPSESSID'])) {
            session_id($_COOKIE['PHPSESSID']);
        }
        session_start();
    }

    if (!isset($_SESSION['oGestorErrores'])) {
        $_SESSION['oGestorErrores'] = new GestorErrores();
    }

    if (!isset($GLOBALS['container'])) {
        $builder = new ContainerBuilder();
        $dependenciesFiles = glob(__DIR__ . '/../src/*/config/dependencies.php');
        if (is_array($dependenciesFiles)) {
            foreach ($dependenciesFiles as $dependenciesFile) {
                $builder->addDefinitions($dependenciesFile);
            }
        }

        if (class_exists(ConfigGlobal::class) && !ConfigGlobal::is_debug_mode()) {
            $cacheDir = __DIR__ . '/../var/cache/php-di';
            if (!is_dir($cacheDir)) {
                if (!mkdir($cacheDir, 0775, true) && !is_dir($cacheDir)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $cacheDir));
                }
            }
            $builder->enableCompilation($cacheDir);
            $builder->writeProxiesToFile(true, $cacheDir . '/proxies');
        }

        $GLOBALS['container'] = $builder->build();
    }
}

// 1) Detectar si la petición es una ruta pública de recuperación.
$anonymousSrcRoutes = [
    '/src/usuarios/usuario_ayuda_info',
    '/src/usuarios/recuperar_password_mail',
    '/src/usuarios/recuperar_2fa_mail',
    '/src/usuarios/app_login',
    '/src/usuarios/app_session',
    '/src/usuarios/infrastructure/ui/http/controllers/usuario_ayuda_info.php',
    '/src/usuarios/infrastructure/ui/http/controllers/recuperar_password_mail.php',
    '/src/usuarios/infrastructure/ui/http/controllers/recuperar_2fa_mail.php',
    '/src/usuarios/infrastructure/ui/http/controllers/app_login.php',
    '/src/usuarios/infrastructure/ui/http/controllers/app_session.php',
];
$requestRoute = '';
if (isset($_GET['r']) && is_string($_GET['r']) && str_starts_with($_GET['r'], '/src/')) {
    $requestRoute = $_GET['r'];
} else {
    $requestUriForBootstrap = $_SERVER['REQUEST_URI'] ?? '/';
    $requestPathForBootstrap = is_string($requestUriForBootstrap)
        ? parse_url($requestUriForBootstrap, PHP_URL_PATH)
        : '';
    if (is_string($requestPathForBootstrap)) {
        $requestRoute = preg_replace('/^\/(pruebas|orbix)/', '', $requestPathForBootstrap);
        $esquema_bootstrap = getenv('ESQUEMA');
        if (!empty($esquema_bootstrap)) {
            // Insensible a mayúsculas: la URL lleva H-xxx y ESQUEMA en FPM a veces llega distinto.
            $requestRoute = preg_replace(
                '#^/' . preg_quote($esquema_bootstrap, '#') . '(?=/|$)#i',
                '',
                (string)$requestRoute
            );
        }
    }
}
$requestRoute = rtrim((string)$requestRoute, '/');
$requestRoute = $requestRoute === '' ? '/' : $requestRoute;
$isAnonymousSrcRoute = in_array($requestRoute, $anonymousSrcRoutes, true);

// 2) Autoload y bootstrap global.
require_once __DIR__ . '/../src/shared/global_header.inc';
if ($isAnonymousSrcRoute) {
    bootstrapAnonymousSrcRequest();
} else {
    require_once __DIR__ . '/../src/shared/global_object.inc';
}
$container = $GLOBALS['container'] ?? null;

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


// 3) Fallback legacy para /src.
// Compatibilidad con:
// - /index.php?r=/src/...
// - peticiones directas a /src/... (con o sin base path delante).
// Lo ponemos ANTES del router para que sea inmediato.
$projectRoot = dirname(__DIR__); // /home/.../orbix
$legacyR = null;

if (isset($_GET['r']) && is_string($_GET['r']) && str_starts_with($_GET['r'], '/src/')) {
    $legacyR = $_GET['r'];
}

if ($legacyR === null) {
    $pathsForSrc = [];
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $requestPath = is_string($requestUri) ? parse_url($requestUri, PHP_URL_PATH) : '';
    if (is_string($requestPath) && $requestPath !== '') {
        $pathsForSrc[] = $requestPath;
    }
    $pathInfo = $_SERVER['PATH_INFO'] ?? '';
    if (is_string($pathInfo) && $pathInfo !== '') {
        $pathsForSrc[] = $pathInfo;
    }
    foreach (['REDIRECT_URL', 'HTTP_X_ORIGINAL_URI'] as $serverRoutingKey) {
        if (empty($_SERVER[$serverRoutingKey]) || !is_string($_SERVER[$serverRoutingKey])) {
            continue;
        }
        $altPath = parse_url($_SERVER[$serverRoutingKey], PHP_URL_PATH);
        if (is_string($altPath) && $altPath !== '') {
            $pathsForSrc[] = $altPath;
        }
    }
    foreach ($pathsForSrc as $pathCandidate) {
        $srcPos = strpos($pathCandidate, '/src/');
        if ($srcPos !== false) {
            $legacyR = substr($pathCandidate, $srcPos);
            break;
        }
    }
}

if (is_string($legacyR) && str_starts_with($legacyR, '/src/')) {
    // Construir ruta absoluta y validar que está dentro del proyecto
    $candidate = realpath($projectRoot . '/' . ltrim($legacyR, '/'));
    if ($candidate && is_file($candidate) && str_starts_with($candidate, $projectRoot . DIRECTORY_SEPARATOR)) {
        require $candidate;
        return; // Evita seguir al router
    }
    // Si no existe el archivo, devolvemos 404 para no enmascarar errores
    //http_response_code(404);
    //echo '404 Not Found (legacy src)';
    //return;
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

// Si $legacyR está definido (viene de $_GET['r'] o de encontrar /src/ en REQUEST_URI),
// ya es la ruta limpia y sin esquema → úsala directamente para FastRoute.
// Esto evita depender de REQUEST_URI (que puede tener el esquema) o de getenv('ESQUEMA')
// (que puede no estar disponible en PHP-FPM via proxy).
if ($legacyR !== null) {
    $uri = $legacyR;
} else {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';

    // Eliminar el prefix del directori (pruebas o orbix) per al matching
    $uri = preg_replace('/^\/(pruebas|orbix)/', '', $uri);

    // Eliminar el esquema del path si está configurado (p.ej. /H-dlmEv/src/... → /src/...)
    $esquema_web = getenv('ESQUEMA') ?: ($_SERVER['ESQUEMA'] ?? '');
    if (!empty($esquema_web)) {
        $uri = preg_replace('#^/' . preg_quote($esquema_web, '#') . '(?=/|$)#i', '', $uri);
    }
}

// Eliminar query string para el matching
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
// FastRoute compara la ruta al carácter; un slash final dispara NOT_FOUND.
$uri = rtrim((string)$uri, '/');
if ($uri === '') {
    $uri = '/';
}


// 6) Despachar
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 Not Found';
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        $allowedMethods = $routeInfo[1] ?? [];
        echo '405 Method Not Allowed. Allowed: ' . implode(', ', (array)$allowedMethods);
        break;

    case Dispatcher::FOUND:
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
