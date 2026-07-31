#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Auditoría de cobertura documentación vs código.
 *
 * Uso:
 *   php tools/audit/doc_cobertura_modulos.php
 *   php tools/audit/doc_cobertura_modulos.php notas
 *
 * Solo lectura: lista endpoints en routes.php sin ficha api/,
 * controllers FE sin pantallas/, y fichas api/ con estado_revision != revisado.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
$only = $argv[1] ?? '';

function listPhpBasenames(string $dir): array
{
    if (!is_dir($dir)) {
        return [];
    }
    $out = [];
    foreach (scandir($dir) ?: [] as $f) {
        if (str_ends_with($f, '.php')) {
            $out[] = substr($f, 0, -4);
        }
    }
    sort($out);
    return $out;
}

function listMdBasenames(string $dir): array
{
    if (!is_dir($dir)) {
        return [];
    }
    $out = [];
    foreach (scandir($dir) ?: [] as $f) {
        if (str_ends_with($f, '.md')) {
            $out[] = substr($f, 0, -3);
        }
    }
    sort($out);
    return $out;
}

function routesEndpoints(string $routesFile): array
{
    if (!is_file($routesFile)) {
        return [];
    }
    $text = file_get_contents($routesFile) ?: '';
    // Quitar comentarios de bloque y línea para no contar rutas comentadas.
    $text = preg_replace('#/\*.*?\*/#s', '', $text) ?? $text;
    $text = preg_replace('#//.*$#m', '', $text) ?? $text;
    preg_match_all("/addRoute\\(\\s*\\[[^\\]]+\\]\\s*,\\s*['\"]([^'\"]+)['\"]/", $text, $m);
    $names = [];
    foreach ($m[1] as $path) {
        $seg = basename(rtrim($path, '/'));
        if ($seg !== '' && !str_starts_with($seg, '{')) {
            $names[$seg] = true;
        }
    }
    $out = array_keys($names);
    sort($out);
    return $out;
}

function revisionState(string $file): string
{
    $head = file_get_contents($file, false, null, 0, 2500) ?: '';
    if (preg_match('/^estado_revision:\\s*"?([^"\\n]+)"?/m', $head, $m)) {
        return trim($m[1]);
    }
    return '(sin)';
}

$modules = [];
foreach (scandir($root . '/src') ?: [] as $mod) {
    if ($mod === '.' || $mod === '..') {
        continue;
    }
    if ($only !== '' && $mod !== $only) {
        continue;
    }
    if (!is_dir($root . '/src/' . $mod)) {
        continue;
    }
    $modules[] = $mod;
}
sort($modules);

$exit = 0;
foreach ($modules as $mod) {
    $routes = routesEndpoints($root . "/src/{$mod}/config/routes.php");
    $api = listMdBasenames($root . "/docs/catalogo/{$mod}/api");
    $fe = listPhpBasenames($root . "/frontend/{$mod}/controller");
    $pant = listMdBasenames($root . "/docs/catalogo/{$mod}/pantallas");

    $missApi = array_values(array_diff($routes, $api));
    // ignore include-style helpers
    $missApi = array_values(array_filter($missApi, static fn ($n) => !str_ends_with($n, '.inc')));
    $missPant = array_values(array_diff($fe, $pant));

    $apiNotRev = [];
    foreach ($api as $name) {
        $st = revisionState($root . "/docs/catalogo/{$mod}/api/{$name}.md");
        if ($st !== 'revisado') {
            $apiNotRev[] = "{$name}={$st}";
        }
    }

    if ($missApi === [] && $missPant === [] && $apiNotRev === []) {
        continue;
    }

    $exit = 1;
    echo "=== {$mod} ===\n";
    if ($missApi !== []) {
        echo "  endpoints sin ficha api (" . count($missApi) . "): " . implode(', ', $missApi) . "\n";
    }
    if ($missPant !== []) {
        echo "  FE ctrl sin pantalla (" . count($missPant) . "): " . implode(', ', $missPant) . "\n";
    }
    if ($apiNotRev !== []) {
        echo "  api no revisado (" . count($apiNotRev) . "): " . implode(', ', $apiNotRev) . "\n";
    }
}

if ($exit === 0) {
    echo "OK: sin huecos detectados" . ($only !== '' ? " en {$only}" : '') . ".\n";
}

exit($exit);
