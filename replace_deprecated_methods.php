<?php

/**
 * Script para reemplazar llamadas a métodos SET deprecated por sus equivalentes en camelCase con 'Vo'
 *
 * SOLO reemplaza métodos SET
 * SOLO en archivos del directorio src/
 *
 * Ejemplo:
 *   $entity->setTabla()      ->  $entity->setTablaVo()
 *   $entity->set_campo()     ->  $entity->setCampoVo()
 *   $entity->setId_tarifa()  ->  $entity->setIdTarifaVo()
 */

function snakeToCamel(string $snake): string
{
    // Convierte snake_case a camelCase
    $camel = str_replace('_', '', ucwords($snake, '_'));
    return $camel;
}

function processFile(string $filePath, array &$replacements): void
{
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "Error al leer: $filePath\n";
        return;
    }

    $originalContent = $content;
    $fileReplacements = [];

    // Patrón para encontrar métodos SET deprecated y su sucesor
    // Busca comentarios @deprecated seguidos de "use setXxxVo()" o "Usar setXxxVo()"
    $pattern = '/\/\*\*[\s\S]*?@deprecated[^\n]*(?:use|Usar)\s+(set\w+Vo)\(\)[\s\S]*?\*\/\s*public\s+function\s+(set\w+)\s*\(/i';

    if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $newMethod = $match[1];  // método nuevo (ej: setTablaVo)
            $oldMethod = $match[2];  // método deprecated (ej: setTabla o set_tabla)

            // Solo procesar si es un método set
            if (stripos($oldMethod, 'set') === 0) {
                $fileReplacements[$oldMethod] = $newMethod;
            }
        }
    }

    if (empty($fileReplacements)) {
        return;
    }

    echo "Procesando: $filePath\n";
    foreach ($fileReplacements as $old => $new) {
        echo "  Encontrado deprecated: $old() -> $new()\n";
    }

    $replacements[$filePath] = $fileReplacements;
}

function replaceInFile(string $filePath, string $oldMethod, string $newMethod): int
{
    $content = file_get_contents($filePath);
    if ($content === false) {
        return 0;
    }

    // Patrón para encontrar llamadas al método: ->oldMethod() o ::oldMethod()
    $pattern = '/(->\s*|::\s*)(' . preg_quote($oldMethod, '/') . ')\s*\(/';
    $replacement = '${1}' . $newMethod . '(';

    $newContent = preg_replace($pattern, $replacement, $content, -1, $count);

    if ($count > 0) {
        file_put_contents($filePath, $newContent);
    }

    return $count;
}

// Paso 1: Encontrar todos los métodos deprecated en las entidades
echo "=== PASO 1: Buscando métodos deprecated ===\n\n";

$deprecatedMethods = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('src/', RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        processFile($file->getPathname(), $deprecatedMethods);
    }
}

if (empty($deprecatedMethods)) {
    echo "No se encontraron métodos deprecated con sus reemplazos.\n";
    exit(0);
}

echo "\n=== RESUMEN DE MÉTODOS DEPRECATED ENCONTRADOS ===\n";
$totalMethods = 0;
foreach ($deprecatedMethods as $file => $methods) {
    $totalMethods += count($methods);
}
echo "Total: $totalMethods métodos en " . count($deprecatedMethods) . " archivos\n\n";

// Paso 2: Preguntar al usuario si desea continuar
echo "¿Desea proceder con el reemplazo en el directorio src/? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) !== 'y') {
    echo "Operación cancelada.\n";
    exit(0);
}

// Paso 3: Reemplazar en todos los archivos PHP del directorio src/ (excepto las propias entidades)
echo "\n=== PASO 2: Reemplazando llamadas a métodos deprecated ===\n\n";

$totalReplacements = 0;
$filesModified = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('src/', RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getPathname();

        // IMPORTANTE: Saltar las entidades que definen los métodos deprecated
        // No modificar archivos en src/*/domain/entity/
        if (preg_match('#/domain/entity/#', $filePath)) {
            continue;
        }

        $fileReplacementCount = 0;

        foreach ($deprecatedMethods as $entityFile => $methods) {
            foreach ($methods as $oldMethod => $newMethod) {
                $count = replaceInFile($filePath, $oldMethod, $newMethod);
                $fileReplacementCount += $count;
            }
        }

        if ($fileReplacementCount > 0) {
            $filesModified++;
            $totalReplacements += $fileReplacementCount;
            echo "  ✓ $filePath: $fileReplacementCount reemplazos\n";
        }
    }
}

echo "\n=== RESULTADO ===\n";
echo "Archivos modificados: $filesModified\n";
echo "Total de reemplazos: $totalReplacements\n";
echo "\nFinalizado.\n";
