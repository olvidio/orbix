<?php

declare(strict_types=1);

use src\shared\domain\helpers\FilterPostGet;


/**
 * Migra `filter_input(INPUT_POST, ...)` → `\src\shared\domain\helpers\FilterPostGet::post(...)` y
 * `filter_input(INPUT_GET, ...)` → `\src\shared\domain\helpers\FilterPostGet::get(...)` en los controladores `/src`.
 *
 * Motivo: `filter_input(INPUT_POST/GET, ...)` lee de la copia inmutable del SAPI
 * (request HTTP exterior) y NO ve los `$_POST` / `$_GET` que reescribe
 * `frontend\shared\PostRequest::dispatchInProcess` al ejecutar un controlador
 * `/src` en proceso. Los reemplazos {@see \src\shared\domain\helpers\FilterPostGet::post()} / {@see \src\shared\domain\helpers\FilterPostGet::get()}
 * (definidos en src/shared/domain/helpers/func_input.php) leen de las
 * superglobales y se comportan igual en HTTP directo, in-process y CLI.
 *
 * Sólo elimina el primer argumento (INPUT_POST / INPUT_GET) y renombra la
 * función; el resto de argumentos (nombre, filtro, opciones) se conservan tal
 * cual. Es idempotente: re-ejecutarlo no hace cambios adicionales.
 *
 * Uso:
 *   php tools/fix/migrar_filter_input_a_filter_post_get.php           # aplica
 *   php tools/fix/migrar_filter_input_a_filter_post_get.php --dry-run # sólo informa
 */

$root = dirname(__DIR__, 2);
$targetDir = $root . '/src';

$dryRun = in_array('--dry-run', $argv, true);

$patterns = [
    '/\bfilter_input\s*\(\s*INPUT_POST\s*,\s*/' => '\src\shared\domain\helpers\FilterPostGet::post(',
    '/\bfilter_input\s*\(\s*INPUT_GET\s*,\s*/'  => '\src\shared\domain\helpers\FilterPostGet::get(',
];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($targetDir, FilesystemIterator::SKIP_DOTS)
);

$totalFiles = 0;
$totalReplacements = 0;
$changedFiles = [];

/** @var SplFileInfo $fileInfo */
foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
        continue;
    }

    $path = $fileInfo->getPathname();
    $original = file_get_contents($path);
    if ($original === false) {
        fwrite(STDERR, "No se pudo leer: $path\n");
        continue;
    }

    $contents = $original;
    $fileReplacements = 0;
    foreach ($patterns as $regex => $replacement) {
        $count = 0;
        $contents = preg_replace($regex, $replacement, $contents, -1, $count);
        if ($contents === null) {
            fwrite(STDERR, "Error de regex en: $path\n");
            continue 2;
        }
        $fileReplacements += $count;
    }

    if ($fileReplacements === 0 || $contents === $original) {
        continue;
    }

    $totalFiles++;
    $totalReplacements += $fileReplacements;
    $changedFiles[] = sprintf(
        '%s (%d)',
        substr($path, strlen($root) + 1),
        $fileReplacements
    );

    if (!$dryRun) {
        if (file_put_contents($path, $contents) === false) {
            fwrite(STDERR, "No se pudo escribir: $path\n");
        }
    }
}

sort($changedFiles);
foreach ($changedFiles as $line) {
    echo ($dryRun ? '[dry-run] ' : '') . $line . "\n";
}

echo "\n";
echo ($dryRun ? '[dry-run] ' : '')
    . sprintf("Ficheros modificados: %d — reemplazos: %d\n", $totalFiles, $totalReplacements);
