<?php

declare(strict_types=1);

use src\shared\application\copias\AvisarErrorCopias;
use src\shared\application\copias\CopiasResincronizar;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\cli\CronSesion;

/**
 * Reconcilia las copias entre bases en un solo proceso (una sesión, un cron).
 *
 * En sv: cp_sacd, cd_cargos_activ_dl, cu_centros_dl.
 * En sf: sólo cu_centros_dlf.
 *
 * La sesión no va en la línea de cron: se lee de cron_sesion.inc (fuera del repo).
 *
 * Uso:
 *   php src/shared/infrastructure/cli/copias_resincronizar.php [--aplicar] [--esquema=H-dlb] [--sesion=/ruta/cron_sesion.inc]
 *
 * Crontab (interior sv):
 *   17 3 * * * /usr/bin/php /var/www/orbix/src/shared/infrastructure/cli/copias_resincronizar.php --aplicar \
 *       >> /var/www/orbix/log/copias.out 2>> /var/www/orbix/log/copias.err
 *
 * Códigos: 0 correcto, 1 error o abortado, 2 uso incorrecto.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 4);
$aplicar = false;
$soloEsquema = '';
$sesionRuta = '';
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--aplicar') {
        $aplicar = true;
        continue;
    }
    if (str_starts_with($arg, '--esquema=')) {
        $soloEsquema = substr($arg, strlen('--esquema='));
        continue;
    }
    if (str_starts_with($arg, '--sesion=')) {
        $sesionRuta = substr($arg, strlen('--sesion='));
        continue;
    }
    fwrite(STDERR, "uso: copias_resincronizar.php [--aplicar] [--esquema=H-dlb] [--sesion=/ruta/cron_sesion.inc]\n");
    exit(2);
}

$envSesion = getenv('ORBIX_CRON_SESION');
$candidatos = array_values(array_filter([
    $sesionRuta,
    is_string($envSesion) ? $envSesion : '',
    dirname($root) . '/conf/cron_sesion.inc',
    '/var/www/conf/cron_sesion.inc',
    $root . '/cron_sesion.inc',
], static fn (string $r): bool => $r !== ''));

try {
    $sesion = CronSesion::desdeFichero(CronSesion::buscar($candidatos));
    $sesion->aplicar();
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(2);
}

register_shutdown_function(static function (): void {
    if (defined('COPIAS_RESYNC_ATENDIDO')) {
        return;
    }
    fwrite(
        STDERR,
        sprintf(
            "[%s] copias resync: abortado antes de ejecutarse; revise cron_sesion.inc (usuario, contraseña, esquema)\n",
            date('c'),
        ),
    );
    exit(1);
});

$document_root = isset($_SERVER['DOCUMENT_ROOT']) && is_string($_SERVER['DOCUMENT_ROOT'])
    ? $_SERVER['DOCUMENT_ROOT']
    : '';
$dir_web = isset($_SERVER['DIRWEB']) && is_string($_SERVER['DIRWEB']) ? $_SERVER['DIRWEB'] : '';
$path = "$document_root/$dir_web";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'src/shared/global_header.inc';
require_once 'src/shared/global_object.inc';

if (ConfigGlobal::is_dmz()) {
    define('COPIAS_RESYNC_ATENDIDO', true);
    fwrite(STDERR, _('Sólo se puede resincronizar desde el interior (esta instalación es DMZ)') . "\n");
    exit(1);
}

$pid = ConfigGlobal::$directorio . '/log/copias_resync.pid';
if ($aplicar) {
    if (file_exists($pid)) {
        $edad = time() - (int) filemtime($pid);
        if ($edad < 45 * 60) {
            define('COPIAS_RESYNC_ATENDIDO', true);
            fwrite(STDERR, _('Ya hay una resincronización de copias en marcha') . "\n");
            exit(1);
        }
    }
    file_put_contents($pid, sprintf("%s -- pid %d\n", date('c'), getmypid()));
}

/**
 * @param array{tareas: list<array{nombre: string, omitida: ?string, resultado: ?array, error: ?string}>, errores: int}|null $resultado
 */
$avisarFallo = static function (CronSesion $sesion, ?array $resultado, ?string $excepcion = null): void {
    $mail = $sesion->mailAviso();
    if ($mail === '') {
        return;
    }
    try {
        $ok = DependencyResolver::get(AvisarErrorCopias::class)
            ->execute($mail, $sesion->ubicacion(), $resultado, $excepcion);
        if (!$ok) {
            fwrite(STDERR, sprintf("[%s] copias resync: no se pudo encolar el aviso a %s\n", date('c'), $mail));
        }
    } catch (Throwable $e) {
        fwrite(STDERR, sprintf("[%s] copias resync: fallo al encolar el aviso: %s\n", date('c'), $e->getMessage()));
    }
};

try {
    $useCase = DependencyResolver::get(CopiasResincronizar::class);
    $resultado = $useCase->execute($aplicar, $soloEsquema, $sesion->ubicacion());
    define('COPIAS_RESYNC_ATENDIDO', true);
} catch (Throwable $e) {
    define('COPIAS_RESYNC_ATENDIDO', true);
    if ($aplicar && is_file($pid)) {
        unlink($pid);
    }
    fwrite(STDERR, sprintf("[%s] copias resync: %s\n", date('c'), $e->getMessage()));
    $avisarFallo($sesion, null, $e->getMessage());
    exit(1);
}

if ($aplicar && is_file($pid)) {
    unlink($pid);
}

fwrite(STDOUT, sprintf(
    "[%s] copias resync %s errores=%d\n",
    date('c'),
    $aplicar ? 'aplicar' : 'informe',
    $resultado['errores'],
));
foreach ($resultado['tareas'] as $tarea) {
    if ($tarea['omitida'] !== null) {
        fwrite(STDOUT, sprintf("  %s  omitida: %s\n", $tarea['nombre'], $tarea['omitida']));
        continue;
    }
    if ($tarea['error'] !== null) {
        fwrite(STDERR, sprintf("  %s  ERROR: %s\n", $tarea['nombre'], $tarea['error']));
        continue;
    }
    $totales = $tarea['resultado']['totales'] ?? [];
    fwrite(STDOUT, sprintf(
        "  %s  esquemas=%d altas=%d cambios=%d bajas=%d errores=%d\n",
        $tarea['nombre'],
        $totales['esquemas'] ?? 0,
        $totales['altas'] ?? 0,
        $totales['cambios'] ?? 0,
        $totales['bajas'] ?? 0,
        $totales['errores'] ?? 0,
    ));
    foreach ($tarea['resultado']['lineas'] ?? [] as $linea) {
        fwrite(STDOUT, $linea . "\n");
    }
}

if ($resultado['errores'] > 0) {
    $avisarFallo($sesion, $resultado);
}

exit($resultado['errores'] > 0 ? 1 : 0);
