#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Traslada zonas / zonas_grupos / zonas_sacd de sv-e a comun y llena zonas_ctr.
 *
 * Solo actúa sobre esquemas que ya tienen el módulo instalado (tabla `zonas` en sv-e).
 * No borra las tablas de sv-e.
 *
 * Uso:
 *   php tools/fix/zonas_a_comun.php --dry-run
 *   php tools/fix/zonas_a_comun.php --apply [--esquema=H-dlb]
 *   php tools/fix/zonas_a_comun.php --dry-run --dir-pwd=/ruta/al/conf
 *
 * Sin `--apply` solo informa. Usa las conexiones de mantenimiento (`importar`).
 * Fuera de Docker, `/var/www/conf` no existe: se busca el `importar.inc` del
 * directorio hermano `../conf` o se puede pasar `--dir-pwd`.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
require $root . '/libs/vendor/autoload.php';
require $root . '/src/shared/load_env.php';

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConfigDB;
use src\zonassacd\application\ZonasAComun;

$aplicar = in_array('--apply', $argv, true);
$soloEsquema = '';
$dirPwd = '';
foreach (array_slice($argv, 1) as $arg) {
    if (str_starts_with($arg, '--esquema=')) {
        $soloEsquema = substr($arg, 10);
    }
    if (str_starts_with($arg, '--dir-pwd=')) {
        $dirPwd = substr($arg, 10);
    }
}

$conocido = static fn (string $a): bool => $a === '--apply'
    || $a === '--dry-run'
    || str_starts_with($a, '--esquema=')
    || str_starts_with($a, '--dir-pwd=');
$desconocidos = array_values(array_filter(array_slice($argv, 1), static fn (string $a): bool => !$conocido($a)));
if ($desconocidos !== []) {
    fwrite(STDERR, "uso: php tools/fix/zonas_a_comun.php [--dry-run|--apply] [--esquema=H-dlb] [--dir-pwd=RUTA]\n");
    exit(2);
}

function zonas_a_comun_dir_pwd_usable(string $dir): bool
{
    return is_readable($dir . '/importar.inc') || is_readable($dir . '/importar.roles.inc');
}

if ($dirPwd === '') {
    $envPwd = getenv('ORBIX_DIR_PWD');
    $candidatos = [];
    if (is_string($envPwd) && $envPwd !== '') {
        $candidatos[] = $envPwd;
    }
    $candidatos[] = ConfigGlobal::getDIR_PWD();
    $candidatos[] = dirname($root) . '/conf';
    $candidatos[] = $root . '/tests/config';
    foreach ($candidatos as $candidato) {
        if (zonas_a_comun_dir_pwd_usable($candidato)) {
            $dirPwd = $candidato;
            break;
        }
    }
}

if ($dirPwd === '' || !zonas_a_comun_dir_pwd_usable($dirPwd)) {
    fwrite(STDERR, "No se encontró importar.inc. Pasa --dir-pwd=/ruta/al/conf\n");
    exit(1);
}

ConfigDB::$dirPwdOverride = $dirPwd;

try {
    $resultado = (new ZonasAComun())->execute($aplicar, $soloEsquema);
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

$modo = $resultado['aplicado'] ? 'apply' : 'dry-run';
fwrite(STDOUT, sprintf(
    "[%s] zonas_a_comun %s (%s): %d esquema(s), errores=%d\n",
    date('c'),
    $modo,
    $dirPwd,
    $resultado['esquemas'],
    $resultado['errores'],
));
foreach ($resultado['lineas'] as $linea) {
    fwrite(STDOUT, $linea . "\n");
}

exit($resultado['errores'] > 0 ? 1 : 0);
