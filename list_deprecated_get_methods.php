<?php

/**
 * Script para listar métodos GET deprecated y sus reemplazos con 'Vo'
 *
 * SOLO lista métodos GET
 * Busca en archivos del directorio src/
 * NO realiza cambios, solo muestra información
 *
 * Ejemplo de salida:
 *   src/dossiers/domain/entity/Dossier.php
 *     getTabla() -> getTablaVo()
 *     getId_tipo() -> getIdTipoVo()
 */

function findDeprecatedGetMethods(string $filePath, array &$results): void
{
    $content = file_get_contents($filePath);
    if ($content === false) {
        return;
    }

    // Patrón para encontrar métodos GET deprecated y su sucesor
    // Busca comentarios @deprecated seguidos de "use getXxxVo()" o "Usar getXxxVo()"
    $pattern = '/\/\*\*[\s\S]*?@deprecated[^\n]*(?:use|Usar)\s+(get\w+Vo)\(\)[\s\S]*?\*\/\s*public\s+function\s+(get\w+)\s*\(/i';

    if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
        $fileMethods = [];

        foreach ($matches as $match) {
            $newMethod = $match[1];  // método nuevo (ej: getTablaVo)
            $oldMethod = $match[2];  // método deprecated (ej: getTabla o get_tabla)

            // Solo procesar si es un método get
            if (stripos($oldMethod, 'get') === 0) {
                $fileMethods[] = [
                    'old' => $oldMethod,
                    'new' => $newMethod
                ];
            }
        }

        if (!empty($fileMethods)) {
            $results[$filePath] = $fileMethods;
        }
    }
}

function countUsages(string $method): int
{
    $pattern = '/(->\s*|::\s*)' . preg_quote($method, '/') . '\s*\(/';
    $count = 0;

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
            if ($content !== false) {
                $count += preg_match_all($pattern, $content);
            }
        }
    }

    return $count;
}

// Buscar todos los métodos GET deprecated
echo "=== BUSCANDO MÉTODOS GET DEPRECATED ===\n\n";

$deprecatedMethods = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('src/', RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        findDeprecatedGetMethods($file->getPathname(), $deprecatedMethods);
    }
}

if (empty($deprecatedMethods)) {
    echo "No se encontraron métodos GET deprecated.\n";
    exit(0);
}

// Mostrar resultados
$totalMethods = 0;
$totalUsages = 0;

foreach ($deprecatedMethods as $filePath => $methods) {
    echo "📄 " . str_replace('src/', '', $filePath) . "\n";

    foreach ($methods as $method) {
        $oldMethod = $method['old'];
        $newMethod = $method['new'];
        $usages = countUsages($oldMethod);

        $totalMethods++;
        $totalUsages += $usages;

        echo "   {$oldMethod}() -> {$newMethod}()";
        if ($usages > 0) {
            echo " [{$usages} usos encontrados]";
        }
        echo "\n";
    }
    echo "\n";
}

echo "=== RESUMEN ===\n";
echo "Total métodos GET deprecated: $totalMethods\n";
echo "Total archivos: " . count($deprecatedMethods) . "\n";
echo "Total usos encontrados: $totalUsages\n";
echo "\nNOTA: Los usos se cuentan en archivos de src/ excluyendo las entidades.\n";
