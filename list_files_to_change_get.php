<?php

/**
 * Script para listar los archivos donde hay que cambiar métodos GET deprecated
 *
 * SOLO lista métodos GET
 * Busca usos en archivos del directorio src/ (excluyendo entidades)
 * NO realiza cambios, solo muestra qué archivos serían modificados
 *
 * El cambio será: getTabla() -> getTablaVo()->value()
 */

function findDeprecatedGetMethods(array &$deprecatedMethods): void
{
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator('src/', RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $filePath = $file->getPathname();
            $content = file_get_contents($filePath);
            if ($content === false) {
                continue;
            }

            // Patrón para encontrar métodos GET deprecated y su sucesor
            $pattern = '/\/\*\*[\s\S]*?@deprecated[^\n]*(?:use|Usar)\s+(get\w+Vo)\(\)[\s\S]*?\*\/\s*public\s+function\s+(get\w+)\s*\(/i';

            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $newMethod = $match[1];  // método nuevo (ej: getTablaVo)
                    $oldMethod = $match[2];  // método deprecated (ej: getTabla)

                    // Solo procesar si es un método get
                    if (stripos($oldMethod, 'get') === 0) {
                        $deprecatedMethods[$oldMethod] = $newMethod;
                    }
                }
            }
        }
    }
}

function findFilesWithUsages(array $deprecatedMethods): array
{
    $filesWithChanges = [];

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator('src/', RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $filePath = $file->getPathname();

            // Saltar las propias entidades
            if (preg_match('#/domain/entity/#', $filePath)) {
                continue;
            }

            $content = file_get_contents($filePath);
            if ($content === false) {
                continue;
            }

            $fileChanges = [];

            foreach ($deprecatedMethods as $oldMethod => $newMethod) {
                $pattern = '/(->\s*|::\s*)' . preg_quote($oldMethod, '/') . '\s*\(/';

                if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                    if (!isset($fileChanges[$oldMethod])) {
                        $fileChanges[$oldMethod] = [
                            'new' => $newMethod,
                            'count' => 0
                        ];
                    }
                    $fileChanges[$oldMethod]['count'] += count($matches[0]);
                }
            }

            if (!empty($fileChanges)) {
                $filesWithChanges[$filePath] = $fileChanges;
            }
        }
    }

    return $filesWithChanges;
}

// Paso 1: Encontrar todos los métodos GET deprecated
echo "=== BUSCANDO MÉTODOS GET DEPRECATED ===\n\n";

$deprecatedMethods = [];
findDeprecatedGetMethods($deprecatedMethods);

if (empty($deprecatedMethods)) {
    echo "No se encontraron métodos GET deprecated.\n";
    exit(0);
}

echo "Encontrados " . count($deprecatedMethods) . " métodos GET deprecated\n\n";

// Paso 2: Encontrar archivos que usan esos métodos
echo "=== BUSCANDO ARCHIVOS CON USOS ===\n\n";

$filesWithChanges = findFilesWithUsages($deprecatedMethods);

if (empty($filesWithChanges)) {
    echo "No se encontraron archivos con usos de métodos GET deprecated.\n";
    exit(0);
}

// Mostrar resultados
$totalFiles = count($filesWithChanges);
$totalReplacements = 0;

foreach ($filesWithChanges as $filePath => $changes) {
    $fileTotal = 0;
    foreach ($changes as $info) {
        $fileTotal += $info['count'];
    }
    $totalReplacements += $fileTotal;

    echo "📄 " . str_replace('src/', '', $filePath) . " ($fileTotal cambios)\n";

    foreach ($changes as $oldMethod => $info) {
        echo "   {$oldMethod}() -> {$info['new']}()->value() [{$info['count']}x]\n";
    }
    echo "\n";
}

echo "=== RESUMEN ===\n";
echo "Total archivos a modificar: $totalFiles\n";
echo "Total reemplazos a realizar: $totalReplacements\n";
echo "\nLista de archivos:\n";
echo "-------------------\n";
foreach (array_keys($filesWithChanges) as $filePath) {
    echo str_replace('src/', '', $filePath) . "\n";
}
