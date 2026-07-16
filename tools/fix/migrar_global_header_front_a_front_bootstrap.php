#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Migración histórica: sustituía `require … global_header_front.inc` por `FrontBootstrap::boot()`.
 *
 * Completada en el frontend (2026). El fichero `.inc` fue eliminado; el script se conserva
 * por referencia o re-ejecución puntual sobre backups.
 *
 * Uso:
 *   php tools/fix/migrar_global_header_front_a_front_bootstrap.php              # dry-run
 *   php tools/fix/migrar_global_header_front_a_front_bootstrap.php --apply
 *   php tools/fix/migrar_global_header_front_a_front_bootstrap.php --apply --backup
 *   php tools/fix/migrar_global_header_front_a_front_bootstrap.php --path frontend/notas/controller
 *
 * No modifica:
 *   - Comentarios que mencionen global_header_front.inc (login.php, PostRequest.php, …)
 *   - Ficheros que ya usan FrontBootstrap.php
 */

require_once dirname(__DIR__, 2) . '/src/shared/global_header.inc';

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

const USE_STATEMENT = 'use frontend\shared\FrontBootstrap;';

/** @return array{apply: bool, backup: bool, verbose: bool, path: string} */
function migrar_parse_argv(array $argv): array
{
    $opts = [
        'apply' => false,
        'backup' => false,
        'verbose' => false,
        'path' => dirname(__DIR__, 2) . '/frontend',
    ];

    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--apply') {
            $opts['apply'] = true;
            continue;
        }
        if ($arg === '--backup') {
            $opts['backup'] = true;
            continue;
        }
        if ($arg === '--verbose' || $arg === '-v') {
            $opts['verbose'] = true;
            continue;
        }
        if ($arg === '--help' || $arg === '-h') {
            fwrite(STDOUT, <<<'HELP'
Uso: php tools/fix/migrar_global_header_front_a_front_bootstrap.php [opciones]

Opciones:
  --apply     Escribe los cambios (por defecto: dry-run)
  --backup    Copia .bak.<timestamp> antes de modificar (requiere --apply)
  --path DIR  Directorio o fichero PHP (default: frontend/)
  --verbose   Detalle por fichero
  --help      Esta ayuda

HELP);
            exit(0);
        }
        if (str_starts_with($arg, '--path=')) {
            $opts['path'] = substr($arg, strlen('--path='));
            continue;
        }
        fwrite(STDERR, "Opción desconocida: {$arg}\n");
        exit(1);
    }

    return $opts;
}

/** @return list<string> */
function migrar_collect_php_files(string $path): array
{
    $root = dirname(__DIR__, 2);
    $abs = str_starts_with($path, '/') ? $path : $root . '/' . ltrim($path, '/');

    if (is_file($abs)) {
        return [$abs];
    }

    if (!is_dir($abs)) {
        fwrite(STDERR, "Ruta no encontrada: {$abs}\n");
        exit(1);
    }

    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($abs, FilesystemIterator::SKIP_DOTS),
    );

    foreach ($iterator as $fileInfo) {
        if (!$fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
            continue;
        }
        $files[] = $fileInfo->getPathname();
    }

    sort($files);

    return $files;
}

function migrar_should_skip_file(string $relativePath): bool
{
    if ($relativePath === 'frontend/shared/FrontBootstrap.php') {
        return true;
    }

    return false;
}

function migrar_file_uses_o_posicion(string $content): bool
{
    if (!preg_match_all('/\$oPosicion\b/', $content, $matches, PREG_OFFSET_CAPTURE)) {
        return false;
    }

    foreach ($matches[0] as [$_, $offset]) {
        $lineStart = strrpos(substr($content, 0, $offset), "\n");
        $lineStart = $lineStart === false ? 0 : $lineStart + 1;
        $line = substr($content, $lineStart, $offset - $lineStart + strlen('$oPosicion'));
        if (!str_contains($line, 'FrontBootstrap::boot')) {
            return true;
        }
    }

    return false;
}

function migrar_map_require_path_to_bootstrap(string $pathExpr): ?string
{
    $pathExpr = trim($pathExpr);

    if (preg_match('/^__DIR__\s*\.\s*[\'"]\/\.\.\/global_header_front\.inc[\'"]$/', $pathExpr)) {
        return "__DIR__ . '/../FrontBootstrap.php'";
    }

    if (preg_match('/^[\'"]frontend\/shared\/global_header_front\.inc[\'"]$/', $pathExpr)) {
        return "'frontend/shared/FrontBootstrap.php'";
    }

    if (preg_match('/^\$orbixRoot\s*\.\s*[\'"]\/frontend\/shared\/global_header_front\.inc[\'"]$/', $pathExpr)) {
        return "\$orbixRoot . '/frontend/shared/FrontBootstrap.php'";
    }

    return null;
}

function migrar_ensure_use_statement(string $content): string
{
    if (str_contains($content, USE_STATEMENT)) {
        return $content;
    }

    if (preg_match_all('/^use [^;]+;/m', $content, $matches, PREG_OFFSET_CAPTURE)) {
        $last = end($matches[0]);
        $insertAt = $last[1] + strlen($last[0]);

        return substr($content, 0, $insertAt) . "\n" . USE_STATEMENT . substr($content, $insertAt);
    }

    if (preg_match('/\A(\<\?php\r?\n(?:declare\(strict_types=1\);\r?\n)?)/', $content, $m)) {
        $insertAt = strlen($m[1]);

        return substr($content, 0, $insertAt) . "\n" . USE_STATEMENT . "\n" . substr($content, $insertAt);
    }

    return "<?php\n" . USE_STATEMENT . "\n\n" . ltrim(substr($content, 5));
}

/**
 * @return array{changed: bool, content: string, reason: string}
 */
function migrar_transform_file(string $content): array
{
    if (str_contains($content, 'FrontBootstrap.php') && !str_contains($content, 'global_header_front.inc')) {
        return ['changed' => false, 'content' => $content, 'reason' => 'ya migrado'];
    }

    if (!str_contains($content, 'global_header_front.inc')) {
        return ['changed' => false, 'content' => $content, 'reason' => 'sin referencia'];
    }

    $requirePatterns = [
        '/^(\s*)require(_once)?\s*\(\s*([^)]+global_header_front\.inc[^)]*)\)\s*;?\s*$/m',
        '/^(\s*)require(_once)?\s+([^;\n]+global_header_front\.inc[^;\n]*)\s*;?\s*$/m',
    ];

    $matched = false;
    foreach ($requirePatterns as $requirePattern) {
        if (preg_match($requirePattern, $content)) {
            $matched = true;
            break;
        }
    }

    if (!$matched) {
        return ['changed' => false, 'content' => $content, 'reason' => 'require no reconocido (¿solo comentario?)'];
    }

    $usesOPosicion = migrar_file_uses_o_posicion($content);
    $replacements = 0;

    $replaceCallback = static function (array $m) use ($usesOPosicion, &$replacements): string {
        $indent = $m[1];
        $pathExpr = trim($m[3] ?? $m[4] ?? '');
        $bootstrapPath = migrar_map_require_path_to_bootstrap($pathExpr);

        if ($bootstrapPath === null) {
            throw new RuntimeException("Expresión require no soportada: {$pathExpr}");
        }

        ++$replacements;
        $bootLine = $usesOPosicion
            ? "{$indent}\$oPosicion = FrontBootstrap::boot();"
            : "{$indent}FrontBootstrap::boot();";

        return "{$indent}require_once {$bootstrapPath};\n{$bootLine}";
    };

    $newContent = $content;
    foreach ($requirePatterns as $requirePattern) {
        $newContent = preg_replace_callback($requirePattern, $replaceCallback, $newContent) ?? $newContent;
    }

    if ($replacements === 0) {
        return ['changed' => false, 'content' => $content, 'reason' => 'sin reemplazos'];
    }

    $newContent = migrar_ensure_use_statement($newContent);

    return ['changed' => true, 'content' => $newContent, 'reason' => "{$replacements} require(s)"];
}

/** @return string ruta relativa al repo */
function migrar_relative_path(string $absolutePath, string $repoRoot): string
{
    $relative = substr($absolutePath, strlen($repoRoot) + 1);

    return str_replace('\\', '/', $relative);
}

// -------------------------------------------------------------------------

$opts = migrar_parse_argv($argv);
$repoRoot = dirname(__DIR__, 2);
$files = migrar_collect_php_files($opts['path']);

$stats = [
    'scanned' => 0,
    'changed' => 0,
    'skipped' => 0,
    'errors' => 0,
];

foreach ($files as $absolutePath) {
    $relativePath = migrar_relative_path($absolutePath, $repoRoot);

    if (migrar_should_skip_file($relativePath)) {
        continue;
    }

    ++$stats['scanned'];

    $original = file_get_contents($absolutePath);
    if ($original === false) {
        fwrite(STDERR, "No se pudo leer: {$relativePath}\n");
        ++$stats['errors'];
        continue;
    }

    try {
        $result = migrar_transform_file($original);
    } catch (RuntimeException $e) {
        fwrite(STDERR, "ERROR {$relativePath}: {$e->getMessage()}\n");
        ++$stats['errors'];
        continue;
    }

    if (!$result['changed']) {
        ++$stats['skipped'];
        if ($opts['verbose']) {
            fwrite(STDOUT, "  skip  {$relativePath} ({$result['reason']})\n");
        }
        continue;
    }

    ++$stats['changed'];
    fwrite(STDOUT, ($opts['apply'] ? 'apply' : 'would') . "  {$relativePath} ({$result['reason']})\n");

    if (!$opts['apply']) {
        continue;
    }

    if ($opts['backup']) {
        $backupPath = $absolutePath . '.bak.' . date('YmdHis');
        if (file_put_contents($backupPath, $original) === false) {
            fwrite(STDERR, "No se pudo crear backup: {$backupPath}\n");
            ++$stats['errors'];
            continue;
        }
    }

    if (file_put_contents($absolutePath, $result['content']) === false) {
        fwrite(STDERR, "No se pudo escribir: {$relativePath}\n");
        ++$stats['errors'];
    }
}

$mode = $opts['apply'] ? 'Aplicado' : 'Dry-run';
fwrite(STDOUT, "\n{$mode}: {$stats['changed']} fichero(s) migrados, {$stats['skipped']} omitidos, {$stats['errors']} error(es) ({$stats['scanned']} analizados).\n");

if (!$opts['apply'] && $stats['changed'] > 0) {
    fwrite(STDOUT, "Ejecuta con --apply para escribir los cambios.\n");
}

exit($stats['errors'] > 0 ? 1 : 0);
