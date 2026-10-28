<?php

/**
 * Inventario para el refactor de búsquedas `$aWhere` / `$aOperators` → `Criteria`.
 *
 * Ver docs/dev/refactor_busquedas_criteria.md (paso 0.1).
 *
 * Uso (desde la raíz del repo):
 *   php tools/audit/audit_criteria_inventario.php
 *   php tools/audit/audit_criteria_inventario.php --out=docs/dev/reports/criteria_inventario.md
 *
 * Solo lee ficheros; no modifica nada. La detección es por expresiones regulares,
 * así que las cifras son aproximadas (sirven para ordenar el trabajo, no como contrato).
 */

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$out = $root . '/docs/dev/reports/criteria_inventario.md';
foreach (array_slice($argv, 1) as $arg) {
    if (str_starts_with($arg, '--out=')) {
        $out = substr($arg, 6);
        if (!str_starts_with($out, '/')) {
            $out = $root . '/' . $out;
        }
    }
}

$scanDirs = ['src', 'frontend', 'tests'];

/** Operadores que entiende Condicion::getCondicion() (más los que caen en default). */
$knownOps = [
    '=', '!=', '<>', '<', '>', '<=', '>=', 'IS NULL', 'IS NOT NULL', 'OR', 'BETWEEN',
    'IN', 'NOT IN', 'ANY', 'IN_ARRAY', '~', '~*', '!~', '!~*', '~INV', 'sin_acentos', '&', 'TXT',
    'LIKE', 'ILIKE', 'NOT LIKE',
];
/** Operadores sin equivalente genérico en Criteria: listar ficheros. */
$watchOps = ['TXT', '~INV', 'OR', 'ANY', 'IN_ARRAY', '&', 'LIKE', 'ILIKE', 'NOT LIKE', '<>'];

// ---------------------------------------------------------------------------
// Recoger ficheros
// ---------------------------------------------------------------------------
/** @return list<string> rutas relativas a $root */
function phpFiles(string $root, string $dir): array
{
    $base = $root . '/' . $dir;
    if (!is_dir($base)) {
        return [];
    }
    $files = [];
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $f) {
        /** @var SplFileInfo $f */
        $name = $f->getFilename();
        if ($f->isFile() && (str_ends_with($name, '.php') || str_ends_with($name, '.phtml'))) {
            $files[] = substr($f->getPathname(), strlen($root) + 1);
        }
    }
    sort($files);
    return $files;
}

/** Módulo = segundo segmento (src/<mod>/..., frontend/<mod>/...); tests/<tipo>/<mod>/... */
function moduleOf(string $rel): string
{
    $p = explode('/', $rel);
    if ($p[0] === 'tests') {
        return 'tests/' . ($p[2] ?? $p[1] ?? '?');
    }
    return $p[0] . '/' . ($p[1] ?? '?');
}

$files = [];
foreach ($scanDirs as $d) {
    $files = array_merge($files, phpFiles($root, $d));
}

// ---------------------------------------------------------------------------
// 1. Métodos de búsqueda con contrato (array $aWhere..., array $aOperators...)
// ---------------------------------------------------------------------------
$searchMethods = [];      // nombre método => list<interfaz>
$reposByModule = [];      // módulo => ['interfaces' => n, 'con_aWhere' => n]
$sigRe = '/function\s+(\w+)\s*\(\s*array\s+\$aWhere\b[^)]*\barray\s+\$aOperador\w*|function\s+(\w+)\s*\(\s*array\s+\$aWhere\b[^)]*\barray\s+\$aOperator\w*/';

foreach ($files as $rel) {
    if (!str_ends_with($rel, 'RepositoryInterface.php') || !str_starts_with($rel, 'src/')) {
        continue;
    }
    $mod = moduleOf($rel);
    $reposByModule[$mod]['interfaces'] = ($reposByModule[$mod]['interfaces'] ?? 0) + 1;
    $code = (string) file_get_contents($root . '/' . $rel);
    if (preg_match_all($sigRe, $code, $m)) {
        $reposByModule[$mod]['con_aWhere'] = ($reposByModule[$mod]['con_aWhere'] ?? 0) + 1;
        foreach (array_keys($m[0]) as $i) {
            $name = $m[1][$i] !== '' ? $m[1][$i] : $m[2][$i];
            $searchMethods[$name][] = basename($rel, '.php');
        }
    }
}
// getConjunt se cuenta aparte
unset($searchMethods['getConjunt']);

// ---------------------------------------------------------------------------
// 2. Recorrido de todos los ficheros
// ---------------------------------------------------------------------------
$callsByModule = [];          // módulo => n llamadas
$callFilesByModule = [];      // módulo => set ficheros
$opCount = [];                // op => n
$opByModule = [];             // módulo => op => n
$watchFiles = [];             // op => set "fichero:línea"
$unknownOps = [];             // literal => n
$conjuntCalls = [];           // list "fichero:línea"
$ordreRaw = [];               // valor _ordre => n
$limitUses = 0;
$callRe = $searchMethods === []
    ? null
    : '/->(' . implode('|', array_map('preg_quote', array_keys($searchMethods))) . ')\s*\(/';

foreach ($files as $rel) {
    $isRepoImpl = (bool) preg_match('#/infrastructure/persistence/.*Repository\.php$#', $rel)
        || str_ends_with($rel, 'RepositoryInterface.php');
    $mod = moduleOf($rel);
    $lines = file($root . '/' . $rel) ?: [];
    foreach ($lines as $n => $line) {
        $loc = $rel . ':' . ($n + 1);

        // Llamadas a métodos de búsqueda (fuera de los propios repositorios)
        if ($callRe !== null && !$isRepoImpl && preg_match_all($callRe, $line, $mm)) {
            $c = count($mm[0]);
            $callsByModule[$mod] = ($callsByModule[$mod] ?? 0) + $c;
            $callFilesByModule[$mod][$rel] = true;
        }

        if (str_contains($line, 'getConjunt(') && !$isRepoImpl && !str_contains($line, 'function getConjunt')) {
            $conjuntCalls[] = $loc;
        }

        // Operadores: líneas de llamantes que hablan de $aOperador / $aOperators
        if (!$isRepoImpl && preg_match('/operador|operator/i', $line) && !str_contains($rel, 'Condicion.php')) {
            if (preg_match_all("/=>?\s*'([^']*)'|=>?\s*\"([^\"]*)\"/", $line, $om)) {
                foreach (array_keys($om[0]) as $i) {
                    $lit = $om[1][$i] !== '' ? $om[1][$i] : $om[2][$i];
                    if ($lit === '') {
                        continue;
                    }
                    if (in_array($lit, $knownOps, true)) {
                        $opCount[$lit] = ($opCount[$lit] ?? 0) + 1;
                        $opByModule[$mod][$lit] = ($opByModule[$mod][$lit] ?? 0) + 1;
                        if (in_array($lit, $watchOps, true)) {
                            $watchFiles[$lit][$loc] = true;
                        }
                    } elseif (strlen($lit) <= 12 && preg_match('/^[A-Z_~!<>=&*]+$/', $lit)) {
                        $unknownOps[$lit] = ($unknownOps[$lit] ?? 0) + 1;
                    }
                }
            }
        }

        // _ordre: valores literales
        if (preg_match_all("/'_ordre'\]?\s*=>?\s*(['\"])(.*?)\\1/", $line, $om2)) {
            foreach ($om2[2] as $v) {
                $ordreRaw[$v] = ($ordreRaw[$v] ?? 0) + 1;
            }
        } elseif (str_contains($line, "'_ordre'") && !$isRepoImpl) {
            $ordreRaw['(no literal: variable o concatenación)'] = ($ordreRaw['(no literal: variable o concatenación)'] ?? 0) + 1;
        }
        if (str_contains($line, "'_limit'") && !$isRepoImpl) {
            $limitUses++;
        }
    }
}

// _ordre "complejos": cualquier cosa que no sea lista de columnas con ASC/DESC opcional
$ordreComplex = [];
$ordreSimple = 0;
foreach ($ordreRaw as $v => $c) {
    if (preg_match('/^\s*[a-z_][a-z0-9_.]*(\s+(ASC|DESC))?(\s*,\s*[a-z_][a-z0-9_.]*(\s+(ASC|DESC))?)*\s*$/i', $v)) {
        $ordreSimple += $c;
    } else {
        $ordreComplex[$v] = $c;
    }
}

// ---------------------------------------------------------------------------
// 3. Informe
// ---------------------------------------------------------------------------
ksort($reposByModule);
arsort($opCount);
arsort($ordreComplex);
$allMods = array_unique(array_merge(array_keys($reposByModule), array_keys($callsByModule)));
sort($allMods);

$md = [];
$md[] = '# Inventario: búsquedas `$aWhere` / `$aOperators`';
$md[] = '';
$md[] = '> Generado por `php tools/audit/audit_criteria_inventario.php` el ' . date('Y-m-d H:i') . '.';
$md[] = '> Detección por regex: cifras aproximadas. Contexto: `docs/dev/refactor_busquedas_criteria.md`.';
$md[] = '';
$md[] = '## Resumen';
$md[] = '';
$md[] = '| Métrica | Valor |';
$md[] = '|---|---|';
$md[] = '| Ficheros analizados (' . implode(', ', $scanDirs) . ') | ' . count($files) . ' |';
$md[] = '| Interfaces `*RepositoryInterface` | ' . array_sum(array_column($reposByModule, 'interfaces')) . ' |';
$md[] = '| …con método `(array $aWhere, array $aOperators)` | ' . array_sum(array_column($reposByModule, 'con_aWhere')) . ' |';
$md[] = '| Métodos de búsqueda distintos | ' . count($searchMethods) . ' |';
$md[] = '| Llamadas a esos métodos (fuera de repositorios) | ' . array_sum($callsByModule) . ' |';
$md[] = '| Llamadas a `getConjunt()` | ' . count($conjuntCalls) . ' |';
$md[] = '| Usos de `_ordre` (literal simple / complejo / no literal) | ' . $ordreSimple . ' / '
    . array_sum(array_diff_key($ordreComplex, ['(no literal: variable o concatenación)' => 0])) . ' / '
    . ($ordreComplex['(no literal: variable o concatenación)'] ?? 0) . ' |';
$md[] = '| Usos de `_limit` | ' . $limitUses . ' |';
$md[] = '';

$md[] = '## Por módulo (orden sugerido: de menos a más llamadas)';
$md[] = '';
$md[] = '| Módulo | Interfaces | Con `$aWhere` | Llamadas | Ficheros llamantes | Operadores usados |';
$md[] = '|---|---:|---:|---:|---:|---|';
$rows = [];
foreach ($allMods as $mod) {
    $ops = $opByModule[$mod] ?? [];
    arsort($ops);
    $opsTxt = implode(', ', array_map(static fn ($o, $c) => '`' . $o . '`×' . $c, array_keys($ops), $ops));
    $rows[] = [
        'calls' => $callsByModule[$mod] ?? 0,
        'line' => sprintf(
            '| %s | %d | %d | %d | %d | %s |',
            $mod,
            $reposByModule[$mod]['interfaces'] ?? 0,
            $reposByModule[$mod]['con_aWhere'] ?? 0,
            $callsByModule[$mod] ?? 0,
            count($callFilesByModule[$mod] ?? []),
            $opsTxt
        ),
    ];
}
usort($rows, static fn ($a, $b) => $a['calls'] <=> $b['calls']);
foreach ($rows as $r) {
    $md[] = $r['line'];
}
$md[] = '';

$md[] = '## Operadores (todas las apariciones)';
$md[] = '';
$md[] = '| Operador | Apariciones |';
$md[] = '|---|---:|';
foreach ($opCount as $op => $c) {
    $md[] = '| `' . $op . '` | ' . $c . ' |';
}
if ($unknownOps !== []) {
    arsort($unknownOps);
    $md[] = '';
    $md[] = 'Literales con pinta de operador no reconocidos (revisar): '
        . implode(', ', array_map(static fn ($o, $c) => '`' . $o . '`×' . $c, array_keys($unknownOps), $unknownOps));
}
$md[] = '';

$md[] = '## Operadores sin equivalente directo o a revisar';
$md[] = '';
foreach ($watchOps as $op) {
    if (empty($watchFiles[$op])) {
        continue;
    }
    $md[] = '### `' . $op . '` (' . count($watchFiles[$op]) . ')';
    $md[] = '';
    foreach (array_keys($watchFiles[$op]) as $loc) {
        $md[] = '- `' . $loc . '`';
    }
    $md[] = '';
}

$md[] = '## `getConjunt()`';
$md[] = '';
foreach ($conjuntCalls as $loc) {
    $md[] = '- `' . $loc . '`';
}
if ($conjuntCalls === []) {
    $md[] = '_Sin llamadas._';
}
$md[] = '';

$md[] = '## `_ordre` complejos (expresiones, funciones, no literales)';
$md[] = '';
$md[] = 'Con nombres de dominio en `Criteria` (decisión B) estos casos necesitan revisión manual.';
$md[] = '';
foreach ($ordreComplex as $v => $c) {
    $md[] = '- `' . str_replace('`', "'", $v) . '` ×' . $c;
}
$md[] = '';

$md[] = '## Métodos de búsqueda detectados';
$md[] = '';
ksort($searchMethods);
foreach ($searchMethods as $name => $ifaces) {
    $md[] = '- `' . $name . '` — ' . count($ifaces) . ' interfaz(es)';
}
$md[] = '';

$dir = dirname($out);
if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
    fwrite(STDERR, "No se puede crear $dir\n");
    exit(1);
}
file_put_contents($out, implode("\n", $md));
echo "Informe escrito en $out\n";
