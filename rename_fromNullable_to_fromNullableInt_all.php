<?php
/**
 * Script generalizado para renombrar fromNullable(?int a fromNullableInt
 * Similar al refactoring Shift+F6 de IntelliJ
 *
 * 1. Busca todos los value_objects con fromNullable(?int
 * 2. Si existe fromNullableInt, lo elimina primero
 * 3. Renombra fromNullable a fromNullableInt en definiciones y usos
 */

echo "=== Renombrando fromNullable(?int a fromNullableInt ===\n\n";

// Paso 1: Encontrar todos los archivos con fromNullable(?int
echo "Paso 1: Buscando archivos con fromNullable(?int...\n";
$grepCmd = "grep -rl 'public static function fromNullable(?int' src/ 2>/dev/null";
exec($grepCmd, $filesWithFromNullable);

if (empty($filesWithFromNullable)) {
    echo "No se encontraron archivos con fromNullable(?int\n";
    exit(0);
}

echo "Encontrados " . count($filesWithFromNullable) . " archivos\n\n";

// Paso 2: Para cada clase, obtener el nombre de la clase
$classData = [];
foreach ($filesWithFromNullable as $file) {
    if (!file_exists($file)) {
        continue;
    }

    $content = file_get_contents($file);

    // Extraer nombre de la clase
    if (preg_match('/^(?:final\s+|abstract\s+)?class\s+(\w+)/m', $content, $matches)) {
        $className = $matches[1];
        $classData[] = [
            'file' => $file,
            'className' => $className,
            'hasFromNullable' => strpos($content, 'function fromNullable(?int') !== false,
            'hasFromNullableInt' => strpos($content, 'function fromNullableInt(?int') !== false,
        ];
    }
}

// Paso 3: Procesar cada clase
$totalReplacements = 0;
$filesModified = 0;

foreach ($classData as $data) {
    $className = $data['className'];
    echo "Procesando $className...\n";

    // 3.1: Si existe fromNullableInt, eliminarlo primero
    if ($data['hasFromNullableInt']) {
        echo "  - Eliminando fromNullableInt existente en {$data['file']}\n";
        $content = file_get_contents($data['file']);

        // Eliminar el método fromNullableInt completo
        $content = preg_replace(
            '/\s*public\s+static\s+function\s+fromNullableInt\s*\(\s*\?int[^}]+}\s*/s',
            '',
            $content,
            -1,
            $deleteCount
        );

        if ($deleteCount > 0) {
            file_put_contents($data['file'], $content);
            echo "  ✓ fromNullableInt eliminado\n";
        }
    }

    // 3.2: Buscar todos los archivos que usan esta clase
    echo "  - Buscando usos de {$className}::fromNullable...\n";
    $grepPattern = "{$className}::fromNullable";
    $grepCmd = "grep -rl '$grepPattern' src/ 2>/dev/null";
    exec($grepCmd, $filesWithUsages, $returnCode);

    $filesToUpdate = array_unique(array_merge([$data['file']], $filesWithUsages ?? []));
    $filesWithUsages = []; // Limpiar para siguiente iteración

    // 3.3: Renombrar en todos los archivos
    $classReplacements = 0;
    foreach ($filesToUpdate as $file) {
        if (!file_exists($file)) {
            continue;
        }

        $content = file_get_contents($file);
        $originalContent = $content;

        // Reemplazar $className::fromNullable por $className::fromNullableInt
        $pattern = "/{$className}::fromNullable\b/";
        $content = preg_replace($pattern, "{$className}::fromNullableInt", $content, -1, $count);
        $classReplacements += $count;

        // Si es el archivo de la clase, también renombrar la definición
        if ($file === $data['file']) {
            $content = preg_replace(
                '/\bpublic\s+static\s+function\s+fromNullable\s*\(\s*\?int/',
                'public static function fromNullableInt(?int',
                $content,
                -1,
                $defCount
            );
            $classReplacements += $defCount;
        }

        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            $filesModified++;
        }
    }

    $totalReplacements += $classReplacements;
    echo "  ✓ $classReplacements reemplazo(s) realizados\n\n";
}

echo "\n=== Resumen ===\n";
echo "Clases procesadas: " . count($classData) . "\n";
echo "Archivos modificados: $filesModified\n";
echo "Total de reemplazos: $totalReplacements\n";
echo "\n✓ Proceso completado\n";
