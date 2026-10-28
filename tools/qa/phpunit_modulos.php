<?php

/**
 * Ejecuta PHPUnit (dentro del contenedor php-fpm) sobre una o varias rutas y resume cada una.
 * Pensado para tandas automáticas: evita bucles y variables de shell en la línea de comandos.
 *
 * Uso (desde la raíz del repo):
 *   php tools/qa/phpunit_modulos.php tests/unit/asistentes tests/integration/asistentes tests/unit/actividades
 *
 * Variables opcionales: ORBIX_PHP_CONTAINER (def. orbix-php-fpm-1), ORBIX_APP_DIR (def. /var/www/orbix).
 * Código de salida: 0 si todas las rutas acaban en OK o solo con avisos; 1 si alguna falla, da error o aborta.
 */

declare(strict_types=1);

$rutas = array_slice($argv, 1);
if ($rutas === []) {
    fwrite(STDERR, "Uso: php tools/qa/phpunit_modulos.php <ruta de tests>...\n");
    exit(2);
}
$contenedor = getenv('ORBIX_PHP_CONTAINER') ?: 'orbix-php-fpm-1';
$dir = getenv('ORBIX_APP_DIR') ?: '/var/www/orbix';
$estado = 0;

foreach ($rutas as $ruta) {
    $cmd = ['docker', 'exec', '-w', $dir, $contenedor, 'php', '-d', 'xdebug.mode=off', 'libs/vendor/bin/phpunit', '-c', 'phpunit.xml', $ruta];
    $p = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    if (!is_resource($p)) {
        printf("%-55s %s\n", $ruta, 'SIN RESUMEN (no se pudo lanzar docker)');
        $estado = 1;
        continue;
    }
    $salida = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($p);

    $lineas = preg_split('/\R/', trim($salida)) ?: [];
    $resumen = '';
    foreach (array_reverse($lineas) as $l) {
        if (preg_match('/^(OK|Tests:|No tests executed)/', $l) === 1) {
            $resumen = $l;
            break;
        }
    }
    $fallo = false;
    if ($resumen === '') {
        $resumen = 'SIN RESUMEN (la ejecución abortó): ' . (end($lineas) ?: '');
        $estado = 1;
    } elseif (preg_match('/Failures|Errors/', $resumen) === 1) {
        $fallo = true;
        $estado = 1;
    }
    printf("%-55s %s\n", $ruta, $resumen);
    if ($fallo) {
        $n = 0;
        foreach ($lineas as $l) {
            if (preg_match('/^\d+\) /', $l) === 1 && $n++ < 5) {
                echo '    ', $l, "\n";
            }
        }
    }
}
exit($estado);
