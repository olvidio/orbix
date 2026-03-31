<?php

/**
 * Script para reemplazar llamadas a métodos GET deprecated por sus equivalentes con 'Vo' y '->value()'
 *
 * SOLO reemplaza métodos GET
 * SOLO en archivos del directorio src/
 * EXCLUYE las entidades (deben mantener ambos métodos)
 *
 * Ejemplo:
 *   $entity->getTabla()      ->  $entity->getTablaVo()->value()
 *   $entity->get_campo()     ->  $entity->getCampoVo()->value()
 *   $entity->getId_tarifa()  ->  $entity->getIdTarifaVo()->value()
 */

function processFile(string $filePath, array &$replacements): void
{
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "Error al leer: $filePath\n";
        return;
    }

    $fileReplacements = [];

    // Patrón para encontrar métodos GET deprecated y su sucesor
    // Busca comentarios @deprecated seguidos de "use getXxxVo()" o "Usar getXxxVo()"
    $pattern = '/\/\*\*[\s\S]*?@deprecated[^\n]*(?:use|Usar)\s+(get\w+Vo)\(\)[\s\S]*?\*\/\s*public\s+function\s+(get\w+)\s*\(/i';

    if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $newMethod = $match[1];  // método nuevo (ej: getTablaVo)
            $oldMethod = $match[2];  // método deprecated (ej: getTabla o get_tabla)

            // Solo procesar si es un método get
            if (stripos($oldMethod, 'get') === 0) {
                $fileReplacements[$oldMethod] = $newMethod;
            }
        }
    }

    if (empty($fileReplacements)) {
        return;
    }

    echo "Procesando: $filePath\n";
    foreach ($fileReplacements as $old => $new) {
        echo "  Encontrado deprecated: $old() -> $new()->value()\n";
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
    // Y reemplazar por: ->newMethod()->value() o ::newMethod()->value()
    $pattern = '/(->\s*|::\s*)(' . preg_quote($oldMethod, '/') . ')\s*\(\)/';
    $replacement = '${1}' . $newMethod . '()->value()';

    $newContent = preg_replace($pattern, $replacement, $content, -1, $count);

    if ($count > 0) {
        file_put_contents($filePath, $newContent);
    }

    return $count;
}

// Paso 1: Encontrar todos los métodos GET deprecated en las entidades
echo "=== PASO 1: Buscando métodos GET deprecated ===\n\n";

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
    echo "No se encontraron métodos GET deprecated con sus reemplazos.\n";
    exit(0);
}

echo "\n=== RESUMEN DE MÉTODOS GET DEPRECATED ENCONTRADOS ===\n";
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
echo "\n=== PASO 2: Reemplazando llamadas a métodos GET deprecated ===\n\n";

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
