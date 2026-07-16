<?php

declare(strict_types=1);

/**
 * Auditoría post-migración PSR-4: detecta restos procedurales, callbacks inválidos
 * y clases cuyo fichero no coincide con PSR-4 (autoload roto).
 *
 * Uso: php tools/audit/audit_migration_psr4.php
 */

$root = dirname(__DIR__, 2);
$errors = 0;

function scanPhpFiles(string $root): Generator
{
    $dirs = ['src', 'frontend', 'tests'];
    foreach ($dirs as $dir) {
        $base = $root . '/' . $dir;
        if (!is_dir($base)) {
            continue;
        }
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));
        foreach ($rii as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $path = $file->getPathname();
            if (str_contains($path, '/vendor/')) {
                continue;
            }
            yield str_replace($root . '/', '', $path);
        }
    }
}

function fail(string $category, string $file, string $detail): void
{
    global $errors;
    $errors++;
    echo "[$category] $file\n  $detail\n";
}

echo "=== Auditoría migración PSR-4 ===\n\n";

// 1. Llamadas procedurales legacy
$procPatterns = [
    'list_nav_' => '/\b(list_nav_[a-z_]+)\(/',
    'filter_post/get' => '/\b(filter_post|filter_get)\(/',
    'func_tablas helpers' => '/\b(input_string|input_int|poner_null|poner_empty_on_null|payload_string)\(/',
];
foreach (scanPhpFiles($root) as $rel) {
    $c = file_get_contents($root . '/' . $rel);
    foreach ($procPatterns as $label => $pat) {
        if (preg_match_all($pat, $c, $m)) {
            foreach (array_unique($m[1]) as $fn) {
                fail('PROC', $rel, "$label: $fn()");
            }
        }
    }
    if (preg_match('/(list_nav_support|func_tablas|func_input|lista_dossiers)\.php/', $c)) {
        fail('INCLUDE', $rel, 'referencia a hub procedural eliminado');
    }
    if (preg_match('/use function src\\\\shared\\\\domain\\\\helpers\\\\/', $c)) {
        fail('USE_FUNCTION', $rel, 'use function src\\shared\\domain\\helpers\\* (usar FuncTablasSupport)');
    }
}

// 2. Callbacks string a funciones eliminadas
$callbackFns = ['strsinacentocmp', 'poner_null', 'poner_empty_on_null', 'ponerEmptyOnNull', 'ponerNull'];
foreach (scanPhpFiles($root) as $rel) {
    $c = file_get_contents($root . '/' . $rel);
    foreach ($callbackFns as $fn) {
        if (preg_match("/['\"]src\\\\shared\\\\domain\\\\helpers\\\\{$fn}['\"]/", $c)) {
            fail('CALLBACK', $rel, "callback string a src\\shared\\domain\\helpers\\$fn");
        }
    }
}

// 3. Clases sin FQCN (resolución por namespace incorrecta)
$unqualified = [
    'FuncTablasSupport' => '/(?<!\\\\)FuncTablasSupport::/',
    'FilterPostGet' => '/(?<!\\\\)FilterPostGet::/',
    'ListNavSupport' => '/(?<!\\\\)ListNavSupport::/',
    'PayloadCoercion' => '/(?<!\\\\)PayloadCoercion::/',
    'DossiersListaRender' => '/(?<!\\\\)DossiersListaRender::/',
];
foreach (scanPhpFiles($root) as $rel) {
    if (str_contains($rel, 'FuncTablasSupport.php') || str_contains($rel, 'FilterPostGet.php')
        || str_contains($rel, 'ListNavSupport.php') || str_contains($rel, 'PayloadCoercion.php')
        || str_contains($rel, 'DossiersListaRender.php')) {
        continue;
    }
    if (str_starts_with($rel, 'scripts/') || str_starts_with($rel, 'tools/')) {
        continue;
    }
    $c = file_get_contents($root . '/' . $rel);
    $imported = [];
    if (preg_match_all('/^use\s+([^;]+);/m', $c, $uses)) {
        foreach ($uses[1] as $useLine) {
            $useLine = trim($useLine);
            if (str_starts_with($useLine, 'function ') || str_starts_with($useLine, 'const ')) {
                continue;
            }
            if (str_contains($useLine, '{')) {
                continue;
            }
            if (preg_match('/\sas\s+(\w+)$/', $useLine, $am)) {
                $imported[$am[1]] = true;
            } else {
                $parts = explode('\\', $useLine);
                $imported[end($parts)] = true;
            }
        }
    }
    foreach ($unqualified as $cls => $pat) {
        if (isset($imported[$cls])) {
            continue;
        }
        if (preg_match($pat, $c)) {
            fail('UNQUALIFIED', $rel, "$cls:: sin FQCN ni use");
        }
    }
}

// 4. payloadString/cursoEst en src desde frontend
foreach (scanPhpFiles($root) as $rel) {
    if (!str_starts_with($rel, 'frontend/')) {
        continue;
    }
    if (str_contains($rel, 'frontend/shared/helpers/FuncTablasSupport.php')) {
        continue;
    }
    $c = file_get_contents($root . '/' . $rel);
    if (preg_match('/\\\\src\\\\shared\\\\domain\\\\helpers\\\\FuncTablasSupport::(payloadString|cursoEst)/', $c)) {
        fail('FRONTEND_SRC', $rel, 'payloadString/cursoEst deben usar frontend\\shared\\helpers\\FuncTablasSupport');
    }
}

// 5. Violaciones PSR-4 (clase en fichero distinto al esperado)
$defined = [];
foreach (scanPhpFiles($root) as $rel) {
    $c = file_get_contents($root . '/' . $rel);
    if (!preg_match('/^namespace\s+([^;]+);/m', $c, $ns)) {
        continue;
    }
    $namespace = trim($ns[1]);
    preg_match_all('/^(?:abstract\s+|final\s+)?class\s+(\w+)/m', $c, $m);
    foreach ($m[1] as $cls) {
        $defined[$namespace . '\\' . $cls] = $rel;
    }
}
foreach ($defined as $fqcn => $file) {
    $parts = explode('\\', $fqcn);
    $cls = array_pop($parts);
    $expected = implode('/', $parts) . '/' . $cls . '.php';
    if ($expected !== $file && (str_starts_with($file, 'src/') || str_starts_with($file, 'frontend/'))) {
        // Ignorar legacy preexistente fuera de helpers
        if (!str_contains($file, '/helpers/') && !str_contains($file, 'dossiers/')) {
            continue;
        }
        fail('PSR4', $file, "$fqcn debería estar en $expected");
    }
}

// 6. Autoload smoke test (clases extraídas recientemente)
require $root . '/libs/vendor/autoload.php';
$smoke = [
    'frontend\\dossiers\\helpers\\DossiersListaSupport',
    'frontend\\dossiers\\helpers\\DossiersSegmentSupport',
    'frontend\\pasarela\\helpers\\PasarelaExcepcionRender',
    'frontend\\actividadcargos\\helpers\\ActividadcargosRenderSupport',
    'frontend\\menus\\helpers\\MenusPostInput',
    'frontend\\actividadplazas\\helpers\\ActividadplazasPostInput',
    'frontend\\actividadessacd\\helpers\\ActividadessacdSession',
    'src\\shared\\domain\\helpers\\FuncTablasSupport',
    'src\\shared\\domain\\helpers\\FilterPostGet',
    'frontend\\shared\\helpers\\ListNavSupport',
    'frontend\\shared\\helpers\\PayloadCoercion',
    'frontend\\dossiers\\helpers\\DossiersListaRender',
];
foreach ($smoke as $fqcn) {
    if (!class_exists($fqcn)) {
        fail('AUTOLOAD', '(smoke)', "class_exists falla para $fqcn");
    }
}

echo "\n=== Resultado: $errors problema(s) ===\n";
exit($errors > 0 ? 1 : 0);
