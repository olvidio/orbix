#!/usr/bin/env php
<?php

/**
 * Script para reemplazar llamadas a getters deprecated por versiones con Value Objects
 *
 * Este script analiza las entidades de un módulo y busca los getters deprecated
 * (formato get_snake_case), luego reemplaza todas sus llamadas en los repositorios
 * o en todo el módulo por las versiones modernas con Value Objects (getXxxVo()->value()).
 *
 * Uso: php replace_deprecated_getters.php <modulo> [--all]
 *
 * Parámetros:
 *   <modulo>  : Nombre del módulo a procesar (requerido)
 *   --all     : Buscar en todos los archivos del módulo (opcional, por defecto solo repositorios)
 *
 * Ejemplos:
 *   php replace_deprecated_getters.php profesores
 *   php replace_deprecated_getters.php profesores --all
 *   php replace_deprecated_getters.php actividades
 *   php replace_deprecated_getters.php ubis --all
 *
 * El script:
 * 1. Busca todas las entidades en src/<modulo>/domain/entity/
 * 2. Identifica getters deprecated marcados con @deprecated
 * 3. Encuentra el getter moderno correspondiente (getXxxVo)
 * 4. Detecta si el getter es nullable mirando el tipo de retorno
 * 5. Modifica los archivos reemplazando las llamadas deprecated
 *
 * Conversión:
 *   Antes: $var = $Entity->getEscrito_nombramiento();
 *   Después: $var = $Entity->getEscritoNombramientoVo()?->value();
 *
 * El operador nullsafe ?-> se añade automáticamente si el tipo de retorno
 * del getter deprecated permite null.
 */

if ($argc < 2) {
    echo "Uso: php replace_deprecated_getters.php <modulo> [--all]\n";
    echo "Ejemplos:\n";
    echo "  php replace_deprecated_getters.php profesores\n";
    echo "  php replace_deprecated_getters.php profesores --all\n";
    exit(1);
}

$modulo = $argv[1];
$baseDir = __DIR__;
$entityDir = "$baseDir/src/$modulo/domain/entity";
$repositoryDir = "$baseDir/src/$modulo/infrastructure/persistence/postgresql";

// Parámetro opcional: buscar en todo el módulo o solo repositorios
$searchAll = isset($argv[2]) && $argv[2] === '--all';

if (!is_dir($entityDir)) {
    echo "Error: No existe el directorio $entityDir\n";
    exit(1);
}

if (!is_dir($repositoryDir)) {
    echo "Error: No existe el directorio $repositoryDir\n";
    exit(1);
}

echo "Analizando módulo: $modulo\n";
echo "Directorio de entidades: $entityDir\n";
echo "Directorio de repositorios: $repositoryDir\n";
if ($searchAll) {
    echo "Modo: Buscar en TODOS los archivos PHP del módulo\n";
} else {
    echo "Modo: Buscar solo en repositorios (usa --all para buscar en todo el módulo)\n";
}
echo "\n";

/**
 * Extrae los getters deprecated y sus equivalentes modernos de una entidad
 */
function getDeprecatedGetters($filePath): array
{
    $content = file_get_contents($filePath);
    $deprecatedGetters = [];

    // Buscar métodos deprecated - patrón específico con límites claros
    // Patrón: /**\n     * @deprecated use getXxxVo()\n     */\n    public function get_xxx(): type
    preg_match_all('/\/\*\*\s*\*\s*@deprecated\s+use\s+(\w+)\(\)\s*\*\/\s*public\s+function\s+(\w+)\(\):\s*(\??[\w\\\\]+)/m', $content, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $modernGetter = $match[1]; // getXxxVo
        $deprecatedGetter = $match[2]; // get_xxx
        $returnType = trim($match[3]); // ?string, int, etc.

        // Solo procesar getters, no setters
        if (strpos($deprecatedGetter, 'get') !== 0) {
            continue;
        }

        // Determinar si es nullable por el tipo de retorno
        $isNullable = strpos($returnType, '?') === 0;

        // Excluir fechas (DateTimeInterface, DateTimeImmutable, etc.)
        if (stripos($returnType, 'date') !== false || stripos($returnType, 'time') !== false) {
            continue;
        }

        $deprecatedGetters[$deprecatedGetter] = [
            'modern' => $modernGetter,
            'nullable' => $isNullable,
            'returnType' => $returnType
        ];
    }

    return $deprecatedGetters;
}

/**
 * Procesa un archivo de repositorio
 */
function processRepository($repositoryPath, $entityName, $deprecatedGetters): array
{
    if (!file_exists($repositoryPath)) {
        return ['changed' => false, 'changes' => 0, 'message' => 'Repositorio no encontrado'];
    }

    $content = file_get_contents($repositoryPath);
    $originalContent = $content;
    $changes = 0;
    $replacements = [];

    foreach ($deprecatedGetters as $deprecatedGetter => $info) {
        $modernGetter = $info['modern'];
        $isNullable = $info['nullable'];

        // Patrón para encontrar llamadas al getter deprecated
        // Busca: ->deprecatedGetter()
        $pattern = '/\$\w+->' . preg_quote($deprecatedGetter) . '\(\)/';

        $newContent = preg_replace_callback($pattern, function($matches) use (&$changes, $modernGetter, $isNullable, $deprecatedGetter, &$replacements) {
            $changes++;
            $nullsafe = $isNullable ? '?' : '';
            $replacement = str_replace($deprecatedGetter . '()', $modernGetter . '()' . $nullsafe . '->value()', $matches[0]);

            if (!isset($replacements[$deprecatedGetter])) {
                $replacements[$deprecatedGetter] = 0;
            }
            $replacements[$deprecatedGetter]++;

            return $replacement;
        }, $content);

        if ($newContent !== $content) {
            $content = $newContent;
        }
    }

    if ($changes > 0) {
        file_put_contents($repositoryPath, $content);
        return [
            'changed' => true,
            'changes' => $changes,
            'replacements' => $replacements,
            'message' => "Modificado: $changes cambios"
        ];
    }

    return ['changed' => false, 'changes' => 0, 'message' => 'Sin cambios'];
}

// Obtener todas las entidades del módulo
$entityFiles = glob("$entityDir/*.php");
$totalModified = 0;
$totalChanges = 0;
$modifiedFiles = [];

foreach ($entityFiles as $entityFile) {
    $entityName = basename($entityFile, '.php');
    echo "\n=== Procesando entidad: $entityName ===\n";

    // Obtener getters deprecated
    $deprecatedGetters = getDeprecatedGetters($entityFile);

    if (empty($deprecatedGetters)) {
        echo "  - No hay getters deprecated (no fechas) en esta entidad\n";
        continue;
    }

    echo "  Getters deprecated encontrados:\n";
    foreach ($deprecatedGetters as $deprecated => $info) {
        $nullable = $info['nullable'] ? ' (nullable)' : '';
        echo "    - $deprecated -> {$info['modern']}$nullable\n";
    }

    // Obtener archivos a procesar
    if ($searchAll) {
        // Buscar en todo el módulo
        $filesToProcess = [];
        $moduleDir = "$baseDir/src/$modulo";
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($moduleDir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filesToProcess[] = $file->getPathname();
            }
        }
    } else {
        // Solo buscar en repositorios
        $repositoryName = "Pg{$entityName}Repository.php";
        $repositoryPath = "$repositoryDir/$repositoryName";
        $filesToProcess = [$repositoryPath];
    }

    // Procesar archivos
    foreach ($filesToProcess as $filePath) {
        $result = processRepository($filePath, $entityName, $deprecatedGetters);

        if ($result['changed']) {
            $fileName = basename($filePath);
            $relativePath = str_replace($baseDir . '/', '', $filePath);
            echo "  ✓ $fileName - {$result['message']}\n";
            foreach ($result['replacements'] as $method => $count) {
                echo "    - $method: $count reemplazos\n";
            }
            $totalModified++;
            $totalChanges += $result['changes'];
            $modifiedFiles[] = $relativePath;
        }
    }
}

echo "\n=== Resumen ===\n";
echo "Total de archivos modificados: $totalModified\n";
echo "Total de líneas cambiadas: $totalChanges\n";

if (!empty($modifiedFiles)) {
    echo "\nArchivos modificados:\n";
    foreach ($modifiedFiles as $file) {
        echo "  - $file\n";
    }
}

echo "\nProceso completado.\n";
