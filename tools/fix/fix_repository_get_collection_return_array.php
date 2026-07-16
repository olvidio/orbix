<?php

declare(strict_types=1);

/**
 * Cambia métodos get* de repositorios que devuelven array|bool para que devuelvan array.
 *
 * Patrón objetivo: colecciones tipo getMatriculas(), getAsistentes(), getPersonas()…
 * en interfaces *RepositoryInterface y implementaciones en infrastructure/persistence.
 *
 * No toca datosById/datosByPk ni otros métodos que no empiezan por get.
 *
 * Uso:
 *   php tools/fix/fix_repository_get_collection_return_array.php --dry-run
 *   php tools/fix/fix_repository_get_collection_return_array.php --apply
 *   php tools/fix/fix_repository_get_collection_return_array.php --apply --path src/asistentes
 */

const SIGNATURE_PATTERN = '/^(\s*(?:public|protected|private)\s+function (get\w+)\([^)]*\)):\s*array\|bool(\s*;?\s*)$/m';
const DOC_COLLECTION_PATTERN = '/@return array\|bool Una colección/';

function repoRoot(): string
{
    return dirname(__DIR__, 2);
}

/** @return list<string> */
function discoverRepositoryFiles(?string $pathFilter = null): array
{
    $root = repoRoot();
    $files = [];

    $interfaceGlob = $root . '/src/*/domain/contracts/*RepositoryInterface.php';
    foreach (glob($interfaceGlob) ?: [] as $file) {
        $files[] = $file;
    }

    $persistenceRoot = $root . '/src';
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($persistenceRoot, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $fileInfo) {
        if (!$fileInfo->isFile()) {
            continue;
        }
        $path = $fileInfo->getPathname();
        if (!str_contains($path, '/infrastructure/persistence/')) {
            continue;
        }
        if (!str_ends_with($path, 'Repository.php')) {
            continue;
        }
        $files[] = $path;
    }

    $files = array_values(array_unique($files));
    sort($files);

    if ($pathFilter === null || $pathFilter === '') {
        return $files;
    }

    $filter = str_replace('\\', '/', $pathFilter);
    $filter = trim($filter, '/');

    return array_values(array_filter(
        $files,
        static fn (string $file): bool => str_contains(str_replace('\\', '/', $file), $filter)
    ));
}

/**
 * @return array{changed: bool, content: string, signatures: list<string>, docblocks: int}
 */
function transformFile(string $path): array
{
    $original = file_get_contents($path);
    if ($original === false) {
        throw new RuntimeException("No se puede leer: {$path}");
    }

    $signatures = [];
    if (preg_match_all(SIGNATURE_PATTERN, $original, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $signatures[] = $match[2];
        }
    }

    $docblocks = preg_match_all(DOC_COLLECTION_PATTERN, $original) ?: 0;

    $updated = preg_replace(SIGNATURE_PATTERN, '$1: array$3', $original);
    if ($updated === null) {
        throw new RuntimeException("Error procesando firmas en: {$path}");
    }

    $updated = preg_replace(DOC_COLLECTION_PATTERN, '@return array Una colección', $updated);
    if ($updated === null) {
        throw new RuntimeException("Error procesando docblocks en: {$path}");
    }

    return [
        'changed' => $updated !== $original,
        'content' => $updated,
        'signatures' => $signatures,
        'docblocks' => $docblocks,
    ];
}

function relPath(string $absolute): string
{
    $root = repoRoot() . '/';
    return str_starts_with($absolute, $root) ? substr($absolute, strlen($root)) : $absolute;
}

$args = array_slice($argv, 1);
$apply = in_array('--apply', $args, true);
$dryRun = in_array('--dry-run', $args, true) || !$apply;
$pathFilter = null;
foreach ($args as $arg) {
    if (str_starts_with($arg, '--path=')) {
        $pathFilter = substr($arg, strlen('--path='));
    }
}
if (in_array('--help', $args, true) || in_array('-h', $args, true)) {
    echo <<<'TXT'
Cambia get*() de repositorios de array|bool a array.

Opciones:
  --dry-run        Solo muestra cambios (por defecto si no hay --apply)
  --apply          Escribe los ficheros modificados
  --path=SUBRUTA   Limita a ficheros bajo esa ruta (ej. src/asistentes)

TXT;
    exit(0);
}

$files = discoverRepositoryFiles($pathFilter);
if ($files === []) {
    fwrite(STDERR, "No se encontraron ficheros de repositorio.\n");
    exit(1);
}

$totalFiles = 0;
$totalMethods = 0;
$totalDocblocks = 0;

foreach ($files as $file) {
    $result = transformFile($file);
    if (!$result['changed']) {
        continue;
    }

    $totalFiles++;
    $totalMethods += count($result['signatures']);
    $totalDocblocks += $result['docblocks'];

    echo relPath($file) . "\n";
    foreach ($result['signatures'] as $method) {
        echo "  - {$method}(): array|bool -> array\n";
    }
    if ($result['docblocks'] > 0) {
        echo "  - @return docblocks: {$result['docblocks']}\n";
    }

    if ($apply) {
        if (file_put_contents($file, $result['content']) === false) {
            throw new RuntimeException("No se puede escribir: {$file}");
        }
    }
}

echo "\n";
echo $apply ? "Aplicado" : "Dry-run";
echo ": {$totalFiles} ficheros, {$totalMethods} métodos get*, {$totalDocblocks} docblocks @return\n";

if ($dryRun && !$apply) {
    echo "Ejecuta con --apply para escribir los cambios.\n";
}
