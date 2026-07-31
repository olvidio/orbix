#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Completa `## Ruta de menú` en flujos del catálogo que aún no la tienen,
 * cruzando pantallas del flujo con `docs/guias/_referencia_menus.md`.
 *
 * Uso:
 *   php tools/fix/completar_ruta_menu_flujos.php [--apply] [modulo ...]
 *
 * Sin --apply: dry-run. Sin módulos: inventario y notas (los que bloquean el manual).
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
$apply = in_array('--apply', $argv, true);
$mods = array_values(array_filter(
    array_slice($argv, 1),
    static fn(string $a): bool => $a !== '--apply' && preg_match('/^[a-z0-9_]+$/', $a) === 1
));
if ($mods === []) {
    $mods = ['inventario', 'notas'];
}

$refPath = $root . '/docs/guias/_referencia_menus.md';
if (!is_file($refPath)) {
    fwrite(STDERR, "No existe {$refPath}\n");
    exit(1);
}

/** @return list<array{url: string, basename: string, legacy: string, pills: string}> */
function parseMenuIndex(string $refPath): array
{
    $rows = [];
    foreach (file($refPath) ?: [] as $line) {
        if (!str_contains($line, '`frontend/')) {
            continue;
        }
        $parts = array_map('trim', explode('|', trim($line, "|\n\r ")));
        if (count($parts) < 6) {
            continue;
        }
        $url = trim($parts[1], '` ');
        if (!str_starts_with($url, 'frontend/')) {
            continue;
        }
        $legacy = str_replace(['<br>', '<br/>', '<br />'], ' · ', $parts[4]);
        $pills = str_replace(['<br>', '<br/>', '<br />'], ' · ', $parts[5]);
        $legacy = html_entity_decode(strip_tags($legacy), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $pills = html_entity_decode(strip_tags($pills), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $rows[] = [
            'url' => $url,
            'basename' => basename($url, '.php'),
            'legacy' => trim(preg_replace('/\s+/', ' ', $legacy) ?? $legacy),
            'pills' => trim(preg_replace('/\s+/', ' ', $pills) ?? $pills),
        ];
    }
    return $rows;
}

/**
 * @param list<array{url: string, basename: string, legacy: string, pills: string}> $menuRows
 * @return array{legacy: string, pills: string}|null
 */
function lookupMenu(array $menuRows, string $controllerBasename): ?array
{
    $legacy = [];
    $pills = [];
    foreach ($menuRows as $row) {
        if ($row['basename'] !== $controllerBasename) {
            continue;
        }
        if ($row['legacy'] !== '' && $row['legacy'] !== '—') {
            $legacy[] = $row['legacy'];
        }
        if ($row['pills'] !== '' && $row['pills'] !== '—') {
            $pills[] = $row['pills'];
        }
    }
    if ($legacy === [] && $pills === []) {
        return null;
    }
    return [
        'legacy' => implode(' · ', array_values(array_unique($legacy))) ?: 'sin entrada de menú en el índice',
        'pills' => implode(' · ', array_values(array_unique($pills))) ?: 'sin entrada de menú en el índice',
    ];
}

function screenController(string $root, string $module, string $screenId): ?string
{
    // inventario.pantalla.equipajes_docs_libres → equipajes_docs_libres.md
    $name = $screenId;
    if (str_contains($screenId, '.pantalla.')) {
        $name = substr($screenId, strrpos($screenId, '.') + 1);
    }
    $path = "{$root}/docs/catalogo/{$module}/pantallas/{$name}.md";
    if (!is_file($path)) {
        return null;
    }
    $text = (string)file_get_contents($path);
    if (preg_match('/^controller:\s*"([^"]+)"/m', $text, $m) !== 1) {
        return null;
    }
    return basename($m[1], '.php');
}

/**
 * @return list<string>
 */
function flowScreenIds(string $text): array
{
    $ids = [];
    if (preg_match('/^pantallas_principales:\s*\[(.*?)\]/m', $text, $m) === 1) {
        if (preg_match_all('/"([^"]+)"/', $m[1], $mm)) {
            $ids = array_merge($ids, $mm[1]);
        }
    }
    if (preg_match('/^fragmentos:\s*\[(.*?)\]/m', $text, $m) === 1) {
        if (preg_match_all('/"([^"]+)"/', $m[1], $mm)) {
            $ids = array_merge($ids, $mm[1]);
        }
    }
    // also from body bullets inventario.pantalla.X
    if (preg_match_all('/`([a-z0-9_]+\.pantalla\.[a-z0-9_]+)`/', $text, $mm)) {
        $ids = array_merge($ids, $mm[1]);
    }
    return array_values(array_unique($ids));
}

$menuRows = parseMenuIndex($refPath);
$changed = 0;
$skipped = 0;

foreach ($mods as $module) {
    $dir = "{$root}/docs/catalogo/{$module}/flujos";
    if (!is_dir($dir)) {
        fwrite(STDERR, "Sin flujos: {$module}\n");
        continue;
    }
    foreach (glob("{$dir}/*.md") ?: [] as $file) {
        $text = (string)file_get_contents($file);
        if (str_contains($text, '## Ruta de menú') || str_contains($text, '## Ruta de menu')) {
            $skipped++;
            continue;
        }

        $legacy = 'sin entrada de menú en el índice (fragmento/AJAX/dossier)';
        $pills = $legacy;
        $found = false;
        foreach (flowScreenIds($text) as $screenId) {
            $ctrl = screenController($root, $module, $screenId);
            if ($ctrl === null) {
                continue;
            }
            $hit = lookupMenu($menuRows, $ctrl);
            if ($hit !== null) {
                $legacy = $hit['legacy'];
                $pills = $hit['pills'];
                $found = true;
                break;
            }
        }

        // Heurística: nombre del flujo ≈ controller
        if (!$found) {
            $base = basename($file, '.md');
            $hit = lookupMenu($menuRows, $base);
            if ($hit === null && str_ends_with($base, '_data')) {
                $hit = lookupMenu($menuRows, substr($base, 0, -5));
            }
            if ($hit !== null) {
                $legacy = $hit['legacy'];
                $pills = $hit['pills'];
                $found = true;
            }
        }

        $section = "\n## Ruta de menú\n\n"
            . "- **Legacy:** {$legacy}\n"
            . "- **Pills2:** {$pills}\n";

        $new = rtrim($text) . "\n" . $section;
        $rel = substr($file, strlen($root) + 1);
        echo ($found ? 'MENU ' : 'FRAG ') . $rel . "\n";
        if ($apply) {
            file_put_contents($file, $new);
        }
        $changed++;
    }
}

echo ($apply ? "Aplicado" : "Dry-run") . ": {$changed} flujos actualizados, {$skipped} ya tenían menú.\n";
exit(0);
