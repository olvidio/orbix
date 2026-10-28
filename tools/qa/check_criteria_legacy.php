<?php

declare(strict_types=1);

/**
 * Aviso de usos nuevos de la API legacy de búsquedas ($aWhere / $aOperators).
 *
 * Cuenta por fichero las apariciones de $aWhere, $aOperators, $aOperador y
 * $aOperadores en src/ y frontend/ y las compara con la línea base
 * tools/qa/criteria_legacy_baseline.json. Avisa si un fichero sube su contador
 * o aparece uno nuevo. El código nuevo debe usar matching(Criteria)
 * (ver AGENTS.md, «Búsquedas con Criteria»).
 *
 * Uso (desde la raíz del repo):
 *   php tools/qa/check_criteria_legacy.php             # avisa, sale con 0
 *   php tools/qa/check_criteria_legacy.php --strict    # sale con 1 si hay usos nuevos (CI)
 *   php tools/qa/check_criteria_legacy.php --update    # reescribe la línea base (tras migrar)
 */

$root = dirname(__DIR__, 2);
$baselineFile = __DIR__ . '/criteria_legacy_baseline.json';
$strict = in_array('--strict', $argv, true);
$update = in_array('--update', $argv, true);

$counts = [];
foreach (['src', 'frontend'] as $dir) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }
        $content = file_get_contents($file->getPathname());
        $n = preg_match_all('/\$(?:aWhere|aOperators?|aOperadores?)\b/', (string) $content);
        if ($n > 0) {
            $counts[substr($file->getPathname(), strlen($root) + 1)] = $n;
        }
    }
}
ksort($counts);

if ($update) {
    file_put_contents($baselineFile, json_encode($counts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    echo 'Línea base actualizada: ' . count($counts) . " ficheros.\n";
    exit(0);
}

$baseline = is_file($baselineFile) ? (array) json_decode((string) file_get_contents($baselineFile), true) : [];
$nuevos = [];
foreach ($counts as $path => $n) {
    $antes = (int) ($baseline[$path] ?? 0);
    if ($n > $antes) {
        $nuevos[$path] = [$antes, $n];
    }
}

if ($nuevos === []) {
    echo "OK: sin usos nuevos de \$aWhere/\$aOperators.\n";
    exit(0);
}

echo "AVISO: usos nuevos de la API legacy \$aWhere/\$aOperators (usa matching(Criteria), ver AGENTS.md):\n";
foreach ($nuevos as $path => [$antes, $n]) {
    echo "  $path: $antes -> $n\n";
}
exit($strict ? 1 : 0);
