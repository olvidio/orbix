<?php

use src\actividadcargos\application\ResincronizarCdCargosActiv;
use src\shared\config\ConfigGlobal;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Driver CLI para reconciliar la copia `cd_cargos_activ_dl` (BD comun) con
 * `d_cargos_activ_dl` de la BD sv-e, en todos los esquemas.
 *
 * Pensado para crontab en el servidor **interior sv** (una sola línea: los
 * esquemas se descubren solos desde `public.db_idschema`). También se invoca
 * desde el menú web a través del controller homónimo, que hace `require` de
 * este fichero.
 *
 * Por defecto **sólo informa**. Hay que pasar `--aplicar` para que escriba.
 *
 * Parámetros posicionales (mismo orden que `avisos_generar_tabla.php`):
 *   argv[1] $username
 *   argv[2] $password
 *   argv[3] $dirweb
 *   argv[4] $document_root
 *   argv[5] $ubicacion
 *   argv[6] $esquema
 *   argv[7] $private
 *   argv[8] $DB_SERVER
 * Opciones (en cualquier posición posterior):
 *   --aplicar          escribe los cambios (sin ella, sólo informe)
 *   --esquema=H-dlb    limita la reconciliación a un esquema de comun
 *
 * Los ocho parámetros son obligatorios en CLI: no hay sesión, y el login se
 * hace con ellos en `frontend/usuarios/controller/login.php` (incluido desde
 * `global_object.inc`). Hace falta aunque la reconciliación abra sus propias
 * conexiones de mantenimiento, porque el catálogo de esquemas se lee con la
 * conexión de sesión `oDBPC`.
 *
 * Códigos de salida: 0 correcto, 1 error o abortado, 2 uso incorrecto.
 *
 * Ejemplo de crontab (interior sv, cada noche):
 *   27 3 * * * /usr/bin/php /var/www/orbix/src/actividadcargos/infrastructure/cli/cargos_activ_resincronizar.php \
 *       usuario clave orbix /var/www sv H-dlbv sv 1 --aplicar \
 *       >> /var/www/orbix/log/cd_cargos_activ.out 2>> /var/www/orbix/log/cd_cargos_activ.err
 */

if (PHP_SAPI === 'cli' && count(array_slice($argv ?? [], 1, 8)) < 8) {
    fwrite(STDERR, "uso: cargos_activ_resincronizar.php <username> <password> <dirweb> <document_root>"
        . " <ubicacion> <esquema> <private> <db_server> [--aplicar] [--esquema=H-dlb]\n");
    exit(2);
}

if (!empty($argv[1])) {
    $_POST['username'] = $argv[1];
    $_POST['password'] = $argv[2];
    $_SERVER['DIRWEB'] = $argv[3];
    $_SERVER['DOCUMENT_ROOT'] = $argv[4];
    putenv("UBICACION=$argv[5]");
    putenv("ESQUEMA=$argv[6]");
    putenv("PRIVATE=$argv[7]");
    putenv("DB_SERVER=$argv[8]");
}

/**
 * Si las credenciales no valen, `login.php` pinta el formulario de login y hace
 * `die()`: sin esto, el cron acabaría con código 0 y sin nada en STDERR.
 */
register_shutdown_function(static function (): void {
    if (PHP_SAPI !== 'cli' || defined('CARGOS_ACTIV_RESYNC_ATENDIDO')) {
        return;
    }
    fwrite(
        STDERR,
        sprintf(
            "[%s] cd_cargos_activ_dl resync: abortado antes de ejecutarse; revise usuario, contraseña y esquema\n",
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

require_once("src/shared/global_header.inc");
require_once("src/shared/global_object.inc");

$isWeb = PHP_SAPI !== 'cli';

/* -------- opciones -------------------------------------------------------- */

$aplicar = false;
$soloEsquema = '';
if ($isWeb) {
    $aplicar = (string) FilterPostGet::post('aplicar') === '1';
    $esquemaRaw = FilterPostGet::post('esquema');
    $soloEsquema = is_scalar($esquemaRaw) ? trim((string) $esquemaRaw) : '';
} else {
    foreach (array_slice($argv ?? [], 9) as $arg) {
        if ($arg === '--aplicar') {
            $aplicar = true;
        }
        if (str_starts_with($arg, '--esquema=')) {
            $soloEsquema = substr($arg, strlen('--esquema='));
        }
    }
}

/* -------- guardas --------------------------------------------------------- */

/**
 * La copia se alimenta desde `d_cargos_activ_dl` de sv-e, y la escritura recorre
 * todos los esquemas de comun. Desde sf o desde la DMZ el origen o el
 * mantenimiento podrían no estar disponibles; el cron corre en interior sv.
 */
function cargos_activ_resincronizar_motivo_no_ejecutable(): string
{
    $ubicacion = getenv('UBICACION');
    $ubicacion = is_string($ubicacion) ? strtolower(trim($ubicacion)) : '';
    if ($ubicacion !== 'sv') {
        return sprintf(_('Sólo se puede resincronizar desde el interior sv (UBICACION=%s)'), $ubicacion ?: '?');
    }
    if (ConfigGlobal::is_dmz()) {
        return _('Sólo se puede resincronizar desde el interior (esta instalación es DMZ)');
    }

    return '';
}

/** Evita solapes con otra ejecución (mismo mecanismo que los avisos). */
function cargos_activ_resincronizar_tomar_pid(): string
{
    $filename = ConfigGlobal::$directorio . '/log/cd_cargos_activ_resync.pid';
    if (file_exists($filename)) {
        $edad = time() - (int) filemtime($filename);
        if ($edad < 15 * 60) {
            return _('Ya hay una resincronización de cd_cargos_activ_dl en marcha');
        }
    }
    file_put_contents($filename, sprintf("%s -- pid %d\n", date('c'), getmypid()));

    return '';
}

function cargos_activ_resincronizar_soltar_pid(): void
{
    $filename = ConfigGlobal::$directorio . '/log/cd_cargos_activ_resync.pid';
    if (file_exists($filename)) {
        unlink($filename);
    }
}

$motivo = cargos_activ_resincronizar_motivo_no_ejecutable();
if ($motivo === '' && $aplicar) {
    $motivo = cargos_activ_resincronizar_tomar_pid();
}
if ($motivo !== '') {
    define('CARGOS_ACTIV_RESYNC_ATENDIDO', true);
    if ($isWeb) {
        ContestarJson::enviar($motivo, '');

        return;
    }
    fwrite(STDERR, $motivo . "\n");
    exit(1);
}

/* -------- ejecución ------------------------------------------------------- */

try {
    $useCase = DependencyResolver::get(ResincronizarCdCargosActiv::class);
    $resultado = $useCase->execute($aplicar, $soloEsquema);
    define('CARGOS_ACTIV_RESYNC_ATENDIDO', true);
} catch (\Throwable $e) {
    define('CARGOS_ACTIV_RESYNC_ATENDIDO', true);
    if ($aplicar) {
        cargos_activ_resincronizar_soltar_pid();
    }
    if ($isWeb) {
        ContestarJson::enviar($e->getMessage(), '');

        return;
    }
    fwrite(STDERR, sprintf("[%s] cd_cargos_activ_dl resync: %s\n", date('c'), $e->getMessage()));
    exit(1);
}

if ($aplicar) {
    cargos_activ_resincronizar_soltar_pid();
}

$totales = $resultado['totales'];
$resumen = sprintf(
    '%s: %d esquema(s), altas=%d cambios=%d bajas=%d errores=%d',
    $aplicar ? _('aplicado') : _('informe'),
    $totales['esquemas'],
    $totales['altas'],
    $totales['cambios'],
    $totales['bajas'],
    $totales['errores'],
);

if ($isWeb) {
    $html = '<p>' . htmlspecialchars($resumen, ENT_QUOTES, 'UTF-8') . '</p>';
    $html .= '<pre>' . htmlspecialchars(implode("\n", $resultado['lineas']), ENT_QUOTES, 'UTF-8') . '</pre>';
    if (!$aplicar) {
        $html .= '<p class="comentario">'
            . _('Informe sin cambios en la base de datos. Para aplicar, repita con «aplicar».')
            . '</p>';
    }
    ContestarJson::enviar('', ['html' => $html, 'totales' => $totales]);

    return;
}

fwrite(STDOUT, sprintf("[%s] cd_cargos_activ_dl resync %s\n", date('c'), $resumen));
foreach ($resultado['lineas'] as $linea) {
    fwrite(STDOUT, $linea . "\n");
}

if ($totales['errores'] > 0) {
    exit(1);
}
