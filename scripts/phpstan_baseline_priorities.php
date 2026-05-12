<?php

declare(strict_types=1);

/**
 * Clasifica entradas de phpstan-baseline.neon por severidad aproximada (A/B/C/D) y genera CSV.
 *
 * Prioridades:
 *   A — Símbolo inexistente o construcción inválida (error grave).
 *   B — Incompatibilidad de tipos concreta o acceso arriesgado.
 *   C — Higiene de tipos, código muerto, mejora incremental.
 *   D — Ruido nivel 9: cluster de errores "mixed" en un archivo por array sin shape (>=5).
 *       Estos se rebajan automáticamente desde B/C cuando un archivo acumula muchos.
 *
 * Uso:
 *   php scripts/phpstan_baseline_priorities.php [ruta/baseline.neon]
 *   php scripts/phpstan_baseline_priorities.php --summary
 *   php scripts/phpstan_baseline_priorities.php --stdout   (CSV a stdout en lugar de build/)
 *
 * Salida por defecto: build/phpstan-baseline-by-priority.csv
 */

/** @return list<array{message: string, identifier: string, count: int, path: string}> */
function parsePhpstanBaseline(string $path): array
{
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        fwrite(STDERR, "No se puede leer: {$path}\n");
        exit(1);
    }

    $entries = [];
    $current = null;

    foreach ($lines as $line) {
        if (preg_match('/^\t\t-\s*$/', $line)) {
            if ($current !== null && isset($current['identifier'], $current['path'])) {
                $entries[] = $current;
            }
            $current = [];
            continue;
        }
        if ($current === null) {
            continue;
        }
        if (!preg_match('/^\t\t\t(\w+):\s*(.*)$/', $line, $m)) {
            continue;
        }
        $key = $m[1];
        $val = $m[2];
        if ($key === 'message' && str_starts_with($val, "'") && str_ends_with($val, "'")) {
            $val = substr($val, 1, -1);
            $val = str_replace("''", "'", $val);
        }
        if ($key === 'count') {
            $current[$key] = (int) $val;
        } else {
            $current[$key] = $val;
        }
    }
    if ($current !== null && isset($current['identifier'], $current['path'])) {
        $entries[] = $current;
    }

    return $entries;
}

/**
 * @return array{prioridad: string, detalle: string}
 */
function clasificar(string $identifier, string $message): array
{
    $tierA = [
        'method.notFound',
        'class.notFound',
        'property.notFound',
        'parameter.notFound',
        'function.notFound',
        'foreach.nonIterable',
        'constant.notFound',
        'requireOnce.fileNotFound',
        'new.noConstructor',
    ];
    if (in_array($identifier, $tierA, true)) {
        return [
            'prioridad' => 'A',
            'detalle' => 'Simbolo inexistente o construccion/iteracion invalida: suele ser error grave si se ejecuta.',
        ];
    }

    if ($identifier === 'argument.type') {
        if (str_contains($message, 'mixed given')) {
            return [
                'prioridad' => 'C',
                'detalle' => 'Tipo debil (mixed): riesgo real de TypeError u offset invalido segun datos; el analizador no acota el valor.',
            ];
        }

        return [
            'prioridad' => 'B',
            'detalle' => 'Incompatibilidad de tipos concreta (no solo mixed): revisar uniones false/null, PDOStatement|false, etc.',
        ];
    }

    $tierB = [
        'method.nonObject',
        'property.nonObject',
        'variable.undefined',
        'return.type',
        'return.missing',
        'assign.propertyType',
        'assignOp.invalid',
        'binaryOp.invalid',
        'echo.nonString',
        'isset.offset',
        'isset.property',
        'isset.variable',
        'offsetAccess.invalidOffset',
        'offsetAccess.notFound',
        'offsetAccess.nonOffsetAccessible',
        'offsetAccess.nonArray',
        'encapsedStringPart.nonString',
        'argument.unresolvableType',
        'nullCoalesce.offset',
        'nullCoalesce.property',
        'nullCoalesce.variable',
        'function.impossibleType',
        'array.duplicateKey',
        'offsetAssign.dimType',
        'deadCode.unreachable',
        'property.onlyRead',
    ];
    if (in_array($identifier, $tierB, true)) {
        return [
            'prioridad' => 'B',
            'detalle' => 'Tipado/logica fuerte o acceso arriesgado: prioridad alta de revision.',
        ];
    }

    $tierCImplicit = [
        'missingType.iterableValue',
        'missingType.parameter',
        'missingType.return',
        'missingType.property',
        'cast.string',
        'cast.int',
        'cast.double',
        'phpDoc.parseError',
        'function.alreadyNarrowedType',
        'instanceof.alwaysFalse',
        'instanceof.alwaysTrue',
        'identical.alwaysFalse',
        'identical.alwaysTrue',
        'notIdentical.alwaysTrue',
        'notIdentical.alwaysFalse',
        'booleanAnd.alwaysFalse',
        'booleanAnd.alwaysTrue',
        'booleanOr.alwaysFalse',
        'booleanOr.alwaysTrue',
        'equal.alwaysFalse',
        'ternary.alwaysFalse',
        'ternary.alwaysTrue',
        'if.alwaysFalse',
        'new.static',
        'method.unused',
        'property.unused',
        'property.onlyWritten',
        'property.unusedType',
        'void.pure',
    ];
    if (in_array($identifier, $tierCImplicit, true) || str_starts_with($identifier, 'missingType.')) {
        return [
            'prioridad' => 'C',
            'detalle' => 'Higiene de tipos, codigo muerto aparente o reglas con muchos falsos positivos contextuales.',
        ];
    }

    return [
        'prioridad' => 'C',
        'detalle' => 'Resto de reglas: valorar caso a caso (suele ser mejora incremental o estilo).',
    ];
}

/**
 * Detecta archivos con cluster de errores "mixed" (>=umbral) y rebaja esas entradas a D.
 *
 * @param list<array{message: string, identifier: string, count: int, path: string}> $entries
 * @return list<array{prioridad: string, detalle: string, identifier: string, count: int, path: string, message: string}>
 */
function clasificarConClusters(array $entries, int $umbralCluster = 5): array
{
    // Primera pasada: clasificar todo con las reglas normales.
    $classified = [];
    foreach ($entries as $e) {
        $msg = $e['message'] ?? '';
        $id = $e['identifier'] ?? '';
        $c = clasificar($id, $msg);
        $classified[] = [
            'prioridad' => $c['prioridad'],
            'detalle' => $c['detalle'],
            'identifier' => $id,
            'count' => (int) ($e['count'] ?? 1),
            'path' => $e['path'] ?? '',
            'message' => $msg,
        ];
    }

    // Segunda pasada: contar errores "mixed" por archivo.
    $mixedCountByFile = [];
    foreach ($classified as $i => $row) {
        if (esMixedNoise($row['message'])) {
            $file = $row['path'];
            $mixedCountByFile[$file] = ($mixedCountByFile[$file] ?? 0) + (int) $row['count'];
        }
    }

    // Tercera pasada: rebajar a D los archivos que superan el umbral.
    foreach ($classified as $i => $row) {
        if (
            esMixedNoise($row['message'])
            && ($mixedCountByFile[$row['path']] ?? 0) >= $umbralCluster
            && in_array($row['prioridad'], ['B', 'C'], true)
        ) {
            $classified[$i]['prioridad'] = 'D';
            $classified[$i]['detalle'] = 'Ruido nivel 9: array sin shape (cluster ' . $mixedCountByFile[$row['path']] . ' mixed en archivo). Tipar retorno o ignorar.';
        }
    }

    return $classified;
}

/** Detecta si un mensaje de error es "ruido mixed" típico de arrays sin shape. */
function esMixedNoise(string $message): bool
{
    return str_contains($message, 'on mixed')
        || str_contains($message, 'mixed given')
        || str_contains($message, 'mixed supplied')
        || str_contains($message, 'cast mixed');
}

/**
 * Ordena entradas clasificadas por prioridad A→B→C→D, luego por path.
 *
 * @param list<array{prioridad: string, detalle: string, identifier: string, count: int, path: string, message: string}> $rows
 * @return list<array{prioridad: string, detalle: string, identifier: string, count: int, path: string, message: string}>
 */
function ordenarPorPrioridad(array $rows): array
{
    $orden = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
    usort($rows, static function (array $a, array $b) use ($orden): int {
        $cmp = ($orden[$a['prioridad']] ?? 9) <=> ($orden[$b['prioridad']] ?? 9);
        if ($cmp !== 0) {
            return $cmp;
        }
        return strcmp($a['path'], $b['path']);
    });

    return $rows;
}

/** @param list<array{message: string, identifier: string, count: int, path: string}> $entries */
function writeCsvToStream(array $entries, $stream): void
{
    $rows = clasificarConClusters($entries);
    $rows = ordenarPorPrioridad($rows);

    fputcsv($stream, ['prioridad', 'prioridad_detalle', 'identifier', 'count', 'path', 'message'], ';', '"', '\\');
    foreach ($rows as $row) {
        fputcsv(
            $stream,
            [$row['prioridad'], $row['detalle'], $row['identifier'], (string) $row['count'], $row['path'], $row['message']],
            ';',
            '"',
            '\\'
        );
    }
}

/**
 * @param list<array{message: string, identifier: string, count: int, path: string}> $entries
 * @return array{byP: array<string, int>, byIdAB: array<string, int>}
 */
function agregarResumen(array $entries): array
{
    $byP = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
    $byIdAB = [];
    $rows = clasificarConClusters($entries);
    foreach ($rows as $row) {
        $p = $row['prioridad'];
        $n = (int) $row['count'];
        $byP[$p] = ($byP[$p] ?? 0) + $n;
        if ($p === 'A' || $p === 'B') {
            $id = $row['identifier'];
            $byIdAB[$id] = ($byIdAB[$id] ?? 0) + $n;
        }
    }
    arsort($byIdAB);

    return ['byP' => $byP, 'byIdAB' => $byIdAB];
}

/** @param array{byP: array<string, int>, byIdAB: array<string, int>} $agg */
function resumenMarkdown(array $agg): string
{
    $byP = $agg['byP'];
    $byIdAB = $agg['byIdAB'];
    $out = "# PHPStan baseline: resumen por prioridad\n\n";
    $out .= "| Prioridad | Ocurrencias (suma de count) |\n|---|---:|\n";
    foreach (['A', 'B', 'C', 'D'] as $p) {
        $out .= "| {$p} | {$byP[$p]} |\n";
    }
    $out .= "\n## Identificadores en A y B (por volumen)\n\n";
    $out .= "| identifier | count |\n|---|--:|\n";
    $i = 0;
    foreach ($byIdAB as $id => $cnt) {
        $out .= '| `' . str_replace('|', '\|', $id) . "` | {$cnt} |\n";
        if (++$i >= 60) {
            $out .= "\n_(truncado a 60 filas; ver CSV completo.)_\n";
            break;
        }
    }

    return $out;
}

/** @param array{byP: array<string, int>, byIdAB: array<string, int>} $agg */
function resumenTexto(array $agg): string
{
    $byP = $agg['byP'];
    $byIdAB = $agg['byIdAB'];
    $lines = [];
    $lines[] = 'PHPStan baseline — ocurrencias por prioridad (suma de count):';
    foreach (['A', 'B', 'C', 'D'] as $p) {
        $lines[] = "  {$p}: {$byP[$p]}";
    }
    $lines[] = '';
    $lines[] = 'Top identificadores (solo A+B):';
    $i = 0;
    foreach ($byIdAB as $id => $cnt) {
        $lines[] = "  {$id}: {$cnt}";
        if (++$i >= 25) {
            $lines[] = '  ... (mas en el CSV)';
            break;
        }
    }

    return implode("\n", $lines) . "\n";
}

$args = array_slice($argv, 1);
$stdout = in_array('--stdout', $args, true);
$summary = in_array('--summary', $args, true);
$markdown = in_array('--markdown', $args, true);
$pathArgs = array_values(array_filter($args, static fn (string $a): bool => !str_starts_with($a, '--')));
$baselinePath = $pathArgs[0] ?? dirname(__DIR__) . '/phpstan-baseline.neon';

if (in_array('--help', $args, true) || in_array('-h', $args, true)) {
    echo <<<'TXT'
Clasifica phpstan-baseline.neon en prioridades A/B/C y escribe CSV.

Argumentos:
  [ruta]           Baseline (por defecto: phpstan-baseline.neon en la raiz del repo)
  --stdout         Escribir CSV en stdout
  --summary        Resumen texto (prioridades y top A+B) en stdout
  --markdown       Escribe build/phpstan-baseline-priority-summary.md (puede usarse solo o con --summary)

TXT;
    exit(0);
}

$entries = parsePhpstanBaseline($baselinePath);
$needsAgg = $summary || $markdown;
$agg = $needsAgg ? agregarResumen($entries) : null;

$outDir = dirname(__DIR__) . '/build';

if ($stdout) {
    if ($summary && $agg !== null && !$markdown) {
        echo resumenTexto($agg);
    }
    writeCsvToStream($entries, STDOUT);
    exit(0);
}

if (!is_dir($outDir) && !mkdir($outDir, 0775, true) && !is_dir($outDir)) {
    fwrite(STDERR, "No se puede crear: {$outDir}\n");
    exit(1);
}
$csvPath = $outDir . '/phpstan-baseline-by-priority.csv';
$fh = fopen($csvPath, 'wb');
if ($fh === false) {
    fwrite(STDERR, "No se puede escribir: {$csvPath}\n");
    exit(1);
}
writeCsvToStream($entries, $fh);
fclose($fh);

if ($markdown && $agg !== null) {
    file_put_contents($outDir . '/phpstan-baseline-priority-summary.md', resumenMarkdown($agg));
    echo "Markdown: {$outDir}/phpstan-baseline-priority-summary.md\n";
}
if ($summary && $agg !== null) {
    echo resumenTexto($agg);
}

echo "CSV: {$csvPath}\n";
