<?php

declare(strict_types=1);

/**
 * Genera `{base}.roles.inc` y `.conn.inc` desde monolitos (`comun.inc`, `pruebas-comun.inc`, …).
 *
 * No carga global_object.inc (evita conectar a BD).
 *
 * Uso:
 *   php scripts/migrar_inc_formato_partido.php comun
 *   php scripts/migrar_inc_formato_partido.php comun sv
 *   php scripts/migrar_inc_formato_partido.php --dir=/home/dani/docker_images/orbix/web/html/conf comun
 *   ORBIX_DIR_PWD=/ruta/al/conf php scripts/migrar_inc_formato_partido.php comun
 *
 * Resolución de directorio (primer match válido):
 *   1. `--dir=...` o variable `ORBIX_DIR_PWD` / `DIR_PWD`
 *   2. `ServerConf::DIR_PWD` si el directorio existe en este host
 *   3. `$HOME/docker_images/orbix/web/html/conf` (conf Docker local habitual)
 */

require_once dirname(__DIR__) . '/src/shared/global_header.inc';

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConfigDB;

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

/** @return array{0: string, 1: list<string>} */
function migrar_parse_argv(array $argv): array
{
    $dir = '';
    $bases = [];
    foreach (array_slice($argv, 1) as $arg) {
        if (str_starts_with($arg, '--dir=')) {
            $dir = substr($arg, strlen('--dir='));

            continue;
        }
        if ($arg === '--help' || $arg === '-h') {
            fwrite(STDOUT, "Uso: php scripts/migrar_inc_formato_partido.php [--dir=/ruta/conf] comun [sv] …\n");
            exit(0);
        }
        $bases[] = $arg;
    }

    return [$dir, $bases];
}

function migrar_resolver_dir_conf(string $dirCli): string
{
    $candidatos = [];
    if ($dirCli !== '') {
        $candidatos[] = $dirCli;
    }
    foreach (['ORBIX_DIR_PWD', 'DIR_PWD'] as $envKey) {
        $v = getenv($envKey);
        if (is_string($v) && $v !== '') {
            $candidatos[] = $v;
        }
    }
    $candidatos[] = ConfigGlobal::getDIR_PWD();

    $home = getenv('HOME');
    if (is_string($home) && $home !== '') {
        $candidatos[] = $home . '/docker_images/orbix/web/html/conf';
    }

    $vistos = [];
    foreach ($candidatos as $path) {
        $path = rtrim($path, '/');
        if ($path === '' || isset($vistos[$path])) {
            continue;
        }
        $vistos[$path] = true;
        if (is_dir($path)) {
            return $path;
        }
    }

    fwrite(STDERR, "No se encontró un directorio de .inc válido.\n");
    fwrite(STDERR, "ServerConf DIR_PWD: " . ConfigGlobal::getDIR_PWD() . "\n");
    fwrite(STDERR, "Prueba:\n");
    fwrite(STDERR, "  php scripts/migrar_inc_formato_partido.php --dir=/home/dani/docker_images/orbix/web/html/conf comun\n");
    fwrite(STDERR, "  ORBIX_DIR_PWD=/ruta/conf php scripts/migrar_inc_formato_partido.php comun\n");
    exit(1);
}

[$dirCli, $bases] = migrar_parse_argv($argv);
if ($bases === []) {
    fwrite(STDERR, "Indica al menos una base: comun, sv, sf, …\n");
    exit(1);
}

$dir = migrar_resolver_dir_conf($dirCli);
ConfigDB::$dirPwdOverride = $dir;

fwrite(STDOUT, "DIR_PWD: {$dir}\n");
fwrite(STDOUT, 'WEBDIR (ServerConf): ' . ConfigGlobal::WEBDIR . "\n");

foreach ($bases as $base) {
    fwrite(STDOUT, "\n=== {$base} ===\n");
    foreach (ConfigDB::crearFicherosPartidosDesdeMonoliticos($base) as $msg) {
        fwrite(STDOUT, $msg . "\n");
    }
}

ConfigDB::$dirPwdOverride = null;
