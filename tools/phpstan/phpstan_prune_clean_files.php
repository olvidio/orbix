<?php

declare(strict_types=1);

/**
 * Poda entradas de baseline para ficheros que ya pasan PHPStan nobaseline.
 *
 * Uso:
 *   php tools/phpstan/phpstan_prune_clean_files.php [--dry-run] [--prefix=frontend/|src/] [--limit=N]
 */

$root = dirname(__DIR__, 2);
$dryRun = in_array('--dry-run', $argv, true);
$prefix = 'frontend/';
$limit = 0;
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--dry-run') {
        continue;
    }
    if (str_starts_with($arg, '--prefix=')) {
        $prefix = substr($arg, 9);
        continue;
    }
    if (str_starts_with($arg, '--limit=')) {
        $limit = (int) substr($arg, 8);
        continue;
    }
}

$phpstan = 'php ' . $root . '/libs/vendor/bin/phpstan';
$config = $root . '/phpstan-nobaseline.neon';
$baselinePath = $root . '/phpstan-baseline.neon';

$lines = file($baselinePath, FILE_IGNORE_NEW_LINES);
if ($lines === false) {
    fwrite(STDERR, "No se puede leer baseline\n");
    exit(1);
}

$files = [];
for ($i = 0, $n = count($lines); $i < $n; $i++) {
    if (!str_starts_with($lines[$i], "\t\t\tpath: $prefix")) {
        continue;
    }
    $path = substr($lines[$i], strlen("\t\t\tpath: "));
    $files[$path] = true;
}
$fileList = array_keys($files);
sort($fileList);
if ($limit > 0) {
    $fileList = array_slice($fileList, 0, $limit);
}

echo 'Ficheros a comprobar: ' . count($fileList) . " (prefix=$prefix)\n";

$cleanFiles = [];
foreach ($fileList as $idx => $relPath) {
    $abs = $root . '/' . $relPath;
    if (!is_file($abs)) {
        echo "[skip missing] $relPath\n";
        $cleanFiles[$relPath] = true;
        continue;
    }
    $cmd = $phpstan
        . ' analyse --memory-limit=2G -c ' . escapeshellarg($config)
        . ' ' . escapeshellarg($relPath)
        . ' 2>&1';
    exec($cmd, $output, $exitCode);
    if ($exitCode === 0) {
        $cleanFiles[$relPath] = true;
    }
    if (($idx + 1) % 25 === 0) {
        echo 'Progreso: ' . ($idx + 1) . '/' . count($fileList) . ' (limpios: ' . count($cleanFiles) . ")\n";
    }
}

echo 'Ficheros limpios: ' . count($cleanFiles) . '/' . count($fileList) . "\n";

$out = [];
$removed = 0;
$i = 0;
$n = count($lines);
while ($i < $n) {
    if ($lines[$i] !== "\t\t-") {
        $out[] = $lines[$i];
        $i++;
        continue;
    }
    $j = $i + 1;
    while ($j < $n && !str_starts_with($lines[$j], "\t\t\tpath: ")) {
        $j++;
    }
    if ($j >= $n) {
        $out[] = $lines[$i];
        $i++;
        continue;
    }
    $pathLine = $lines[$j];
    $relPath = substr($pathLine, strlen("\t\t\tpath: "));
    if (isset($cleanFiles[$relPath])) {
        $removed++;
        $i = $j + 1;
        if ($i < $n && $lines[$i] === '') {
            $i++;
        }
        continue;
    }
    $out[] = $lines[$i];
    $i++;
}

echo "Entradas baseline podadas: $removed\n";

if ($dryRun) {
    echo "(--dry-run: baseline no modificado)\n";
    exit(0);
}

file_put_contents($baselinePath, implode("\n", $out) . "\n");
echo "Baseline actualizado\n";
