#!/usr/bin/env php
<?php

/**
 * Busca propiedades nullable DateTimeLocal sin valor por defecto (= null).
 *
 * Evita "Typed property must not be accessed before initialization" cuando
 * Hydratable::toArrayForDatabase() lee getters sin haber pasado por fromArray().
 *
 * Uso:
 *   php tools/audit/audit_datetime_local_null_defaults.php
 *   php tools/audit/audit_datetime_local_null_defaults.php --fix
 */

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$fix = in_array('--fix', $argv, true);

$propertyPattern = '/^\s*(private|protected|public)\s+(?:\?DateTimeLocal|DateTimeLocal\|null)\s+\$(\w+)\s*(?:=\s*null)?\s*;/';
$missingDefaultPattern = '/^\s*(private|protected|public)\s+(?:\?DateTimeLocal|DateTimeLocal\|null)\s+\$(\w+)\s*;/';
$classPattern = '/^\s*(?:abstract\s+)?class\s+(\w+)/';

/** @var list<array{file: string, line: int, class: string, visibility: string, property: string, declaration: string}> */
$missing = [];

/** @var list<array{file: string, line: int, class: string, property: string}> */
$ok = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root . '/src', FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    $relative = str_replace($root . '/', '', $path);
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        continue;
    }

    $currentClass = '';

    foreach ($lines as $index => $line) {
        if (preg_match($classPattern, $line, $classMatch)) {
            $currentClass = $classMatch[1];
        }

        if (!preg_match($propertyPattern, $line, $match)) {
            continue;
        }

        $visibility = $match[1];
        $property = $match[2];
        $lineNumber = $index + 1;

        if (preg_match($missingDefaultPattern, $line)) {
            $missing[] = [
                'file' => $relative,
                'line' => $lineNumber,
                'class' => $currentClass,
                'visibility' => $visibility,
                'property' => $property,
                'declaration' => trim($line),
            ];
            continue;
        }

        $ok[] = [
            'file' => $relative,
            'line' => $lineNumber,
            'class' => $currentClass,
            'property' => $property,
        ];
    }
}

echo "=== Propiedades DateTimeLocal|null SIN = null (" . count($missing) . ") ===\n";

if ($missing === []) {
    echo "  (ninguna)\n";
} else {
    foreach ($missing as $item) {
        $class = $item['class'] !== '' ? $item['class'] : '?';
        echo sprintf(
            "  - %s:%d  %s::$%s  (%s)\n",
            $item['file'],
            $item['line'],
            $class,
            $item['property'],
            $item['declaration']
        );
    }
}

echo "\n=== Propiedades DateTimeLocal|null CON = null (" . count($ok) . ") ===\n";
foreach ($ok as $item) {
    $class = $item['class'] !== '' ? $item['class'] : '?';
    echo sprintf("  - %s:%d  %s::$%s\n", $item['file'], $item['line'], $class, $item['property']);
}

if ($fix && $missing !== []) {
    echo "\n=== Aplicando --fix ===\n";
    $byFile = [];
    foreach ($missing as $item) {
        $byFile[$item['file']][] = $item;
    }

    foreach ($byFile as $relativeFile => $items) {
        $fullPath = $root . '/' . $relativeFile;
        $content = file_get_contents($fullPath);
        if ($content === false) {
            echo "  ERROR: no se pudo leer $relativeFile\n";
            continue;
        }

        $fileLines = explode("\n", $content);
        foreach ($items as $item) {
            $lineIndex = $item['line'] - 1;
            if (!isset($fileLines[$lineIndex])) {
                continue;
            }
            $fileLines[$lineIndex] = preg_replace(
                '/(\$\w+)\s*;/',
                '$1 = null;',
                $fileLines[$lineIndex],
                1
            ) ?? $fileLines[$lineIndex];
            echo "  fixed {$relativeFile}:{$item['line']} \${$item['property']}\n";
        }

        file_put_contents($fullPath, implode("\n", $fileLines) . (str_ends_with($content, "\n") ? "\n" : ''));
    }
    echo "\nListo. Vuelve a ejecutar sin --fix para verificar.\n";
} elseif ($fix) {
    echo "\nNada que corregir.\n";
} elseif ($missing !== []) {
    echo "\nEjecuta con --fix para añadir = null automáticamente.\n";
}

exit($missing === [] ? 0 : 1);
