<?php

declare(strict_types=1);

/**
 * Barrido PS₀: PHPStan nobaseline por módulo y poda entradas obsoletas del baseline.
 *
 * Uso:
 *   php tools/phpstan/phpstan_prune_clean_modules.php [--dry-run] [--scope=src|frontend|all] [modulo ...]
 */

$root = dirname(__DIR__, 2);
$dryRun = in_array('--dry-run', $argv, true);
$scope = 'src';
$onlyModules = [];
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--dry-run') {
        continue;
    }
    if (str_starts_with($arg, '--scope=')) {
        $scope = substr($arg, 8);
        continue;
    }
    if (!str_starts_with($arg, '-')) {
        $onlyModules[] = $arg;
    }
}
if (!in_array($scope, ['src', 'frontend', 'all'], true)) {
    fwrite(STDERR, "Scope inválido: $scope (use src, frontend o all)\n");
    exit(1);
}

$roots = match ($scope) {
    'src' => ['src' => $root . '/src'],
    'frontend' => ['frontend' => $root . '/frontend'],
    'all' => [
        'src' => $root . '/src',
        'frontend' => $root . '/frontend',
    ],
};

$phpstanBin = $root . '/libs/vendor/bin/phpstan';
$phpstan = 'php ' . $phpstanBin;
$config = $root . '/phpstan-nobaseline.neon';
$baselinePath = $root . '/phpstan-baseline.neon';

if (!is_file($phpstanBin)) {
    fwrite(STDERR, "PHPStan no encontrado: $phpstanBin\n");
    exit(1);
}

/** @var list<array{root: string, module: string, path: string, prefix: string}> $targets */
$targets = [];
foreach ($roots as $rootName => $rootPath) {
    foreach (scandir($rootPath) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        if (!is_dir($rootPath . '/' . $entry)) {
            continue;
        }
        if ($onlyModules !== [] && !in_array($entry, $onlyModules, true)) {
            continue;
        }
        $targets[] = [
            'root' => $rootName,
            'module' => $entry,
            'path' => "$rootName/$entry/",
            'prefix' => "\t\t\tpath: $rootName/$entry/",
        ];
    }
}
usort($targets, static fn(array $a, array $b): int => strcmp($a['path'], $b['path']));

$clean = [];
$dirty = [];

foreach ($targets as $target) {
    $cmd = $phpstan
        . ' analyse --memory-limit=2G -c ' . escapeshellarg($config)
        . ' ' . escapeshellarg($target['path'])
        . ' 2>&1';
    echo "Analysing {$target['path']} ... ";
    exec($cmd, $output, $exitCode);
    if ($exitCode === 0) {
        echo "OK\n";
        $clean[] = $target;
    } else {
        echo "FAIL ($exitCode)\n";
        $dirty[$target['path']] = implode("\n", array_slice($output, -8));
    }
}

echo "\n=== Resumen ===\n";
echo 'Módulos limpios: ' . count($clean) . ' / ' . count($targets) . "\n";
if ($dirty !== []) {
    echo "Módulos con errores nobaseline:\n";
    foreach ($dirty as $path => $tail) {
        echo "  - $path\n";
    }
}

if ($clean === []) {
    echo "Nada que podar.\n";
    exit($dirty === [] ? 0 : 1);
}

$lines = file($baselinePath, FILE_IGNORE_NEW_LINES);
if ($lines === false) {
    fwrite(STDERR, "No se puede leer $baselinePath\n");
    exit(1);
}

$out = [];
$removed = 0;
/** @var array<string, int> $removedByPath */
$removedByPath = [];
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
    $matched = null;
    foreach ($clean as $target) {
        if (str_starts_with($pathLine, $target['prefix'])) {
            $matched = $target['path'];
            break;
        }
    }

    if ($matched !== null) {
        $removed++;
        $removedByPath[$matched] = ($removedByPath[$matched] ?? 0) + 1;
        $i = $j + 1;
        if ($i < $n && $lines[$i] === '') {
            $i++;
        }
        continue;
    }

    $out[] = $lines[$i];
    $i++;
}

echo "\nEntradas baseline a podar: $removed\n";
ksort($removedByPath);
foreach ($removedByPath as $path => $count) {
    echo sprintf("  %5d  %s\n", $count, $path);
}

if ($dryRun) {
    echo "\n(--dry-run: baseline no modificado)\n";
    exit($dirty === [] ? 0 : 1);
}

file_put_contents($baselinePath, implode("\n", $out) . "\n");
echo "\nBaseline actualizado: $baselinePath\n";
exit($dirty === [] ? 0 : 1);
