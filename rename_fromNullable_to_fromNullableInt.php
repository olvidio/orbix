<?php
/**
 * Script para renombrar fromNullable a fromNullableInt en StatusId
 * Similar al refactoring Shift+F6 de IntelliJ
 */

$files = [
    'src/actividades/domain/value_objects/StatusId.php',
    'src/procesos/domain/entity/TareaProceso.php',
    'src/cambios/domain/entity/Cambio.php',
];

$replacements = 0;

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "⚠️  Archivo no encontrado: $file\n";
        continue;
    }

    $content = file_get_contents($file);
    $originalContent = $content;

    // Reemplazar StatusId::fromNullable por StatusId::fromNullableInt
    $content = preg_replace(
        '/\bStatusId::fromNullable\b/',
        'StatusId::fromNullableInt',
        $content,
        -1,
        $count
    );

    $replacements += $count;

    // Reemplazar la definición del método: public static function fromNullable(
    $content = preg_replace(
        '/\bpublic\s+static\s+function\s+fromNullable\s*\(\s*\?int/',
        'public static function fromNullableInt(?int',
        $content,
        -1,
        $defCount
    );

    $replacements += $defCount;

    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "✓ $file - " . ($count + $defCount) . " reemplazo(s)\n";
    } else {
        echo "  $file - sin cambios\n";
    }
}

echo "\n✓ Total: $replacements reemplazo(s) realizados\n";
