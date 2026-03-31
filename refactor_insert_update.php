<?php
/**
 * Script para refactorizar bloques INSERT/UPDATE
 *
 * Transforma de:
 *   INSERT: $aDatos['id_xxx'] = $valor;
 *   UPDATE: sin cambios
 *
 * A:
 *   INSERT: sin cambios
 *   UPDATE: unset($aDatos['id_xxx']); (al inicio del bloque UPDATE)
 */

function refactorInsertUpdate($filePath) {
    if (!file_exists($filePath)) {
        echo "Error: El archivo no existe: $filePath\n";
        return false;
    }

    $content = file_get_contents($filePath);
    $originalContent = $content;

    // Pattern para encontrar bloques if ($bInsert === false) {...} else {...}
    $pattern = '/if\s*\(\s*\$bInsert\s*===\s*false\s*\)\s*\{(.*?)\}\s*else\s*\{(.*?)\n\s*\}/s';

    preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);

    if (count($matches[0]) === 0) {
        echo "No se encontraron bloques if (\$bInsert === false) en: $filePath\n";
        return false;
    }

    $modificaciones = 0;
    $offset = 0;

    foreach ($matches[0] as $index => $match) {
        $fullMatch = $match[0];
        $updateBlock = $matches[1][$index][0];
        $insertBlock = $matches[2][$index][0];

        // Buscar asignaciones tipo $aDatos['xxx'] = ... en el bloque INSERT
        preg_match_all('/\$aDatos\[\'([^\']+)\'\]\s*=\s*[^;]+;/m', $insertBlock, $insertAssignments);

        if (count($insertAssignments[0]) === 0) {
            continue; // No hay asignaciones a mover
        }

        // Verificar que estas asignaciones NO estén ya como unset en el UPDATE
        $unsetStatements = '';
        $assignments = [];

        foreach ($insertAssignments[1] as $key) {
            // Verificar si ya existe un unset para esta clave en UPDATE
            if (!preg_match('/unset\s*\(\s*\$aDatos\[\'' . preg_quote($key, '/') . '\'\]\s*\)/', $updateBlock)) {
                $unsetStatements .= "            unset(\$aDatos['$key']);\n";
                $assignments[] = $key;
            }
        }

        if (empty($unsetStatements)) {
            continue; // Ya están todos los unset
        }

        // Insertar los unset al inicio del bloque UPDATE (después del comentario //UPDATE si existe)
        if (preg_match('/(\/\/\s*UPDATE\s*\n)/', $updateBlock, $commentMatch)) {
            $newUpdateBlock = preg_replace(
                '/(\/\/\s*UPDATE\s*\n)/',
                '$1' . $unsetStatements,
                $updateBlock,
                1
            );
        } else {
            // Si no hay comentario, insertar al principio del bloque
            $newUpdateBlock = $unsetStatements . $updateBlock;
        }

        // Eliminar las asignaciones del bloque INSERT
        $newInsertBlock = $insertBlock;
        foreach ($insertAssignments[0] as $assignmentIndex => $assignment) {
            $key = $insertAssignments[1][$assignmentIndex];
            if (in_array($key, $assignments)) {
                // Eliminar la línea completa con su indentación y salto de línea
                $newInsertBlock = preg_replace(
                    '/[ \t]*' . preg_quote($assignment, '/') . '[ \t]*\r?\n/',
                    '',
                    $newInsertBlock,
                    1
                );
            }
        }

        // Reconstruir el bloque completo preservando formato
        $newFullMatch = "if (\$bInsert === false) {" . $newUpdateBlock . "} else {" . $newInsertBlock . "    }";

        $content = substr_replace(
            $content,
            $newFullMatch,
            $match[1] + $offset,
            strlen($fullMatch)
        );

        $offset += strlen($newFullMatch) - strlen($fullMatch);
        $modificaciones++;

        echo "Modificado bloque " . ($index + 1) . " en $filePath\n";
        echo "  - Movidas " . count($assignments) . " asignaciones: " . implode(', ', $assignments) . "\n";
    }

    if ($modificaciones > 0) {
        file_put_contents($filePath, $content);
        echo "✓ Archivo actualizado: $filePath ($modificaciones bloques modificados)\n\n";
        return true;
    }

    echo "Sin cambios en: $filePath\n\n";
    return false;
}

// Uso del script
if ($argc < 2) {
    echo "Uso: php refactor_insert_update.php <archivo.php> [<archivo2.php> ...]\n";
    echo "   o: php refactor_insert_update.php <directorio>\n";
    exit(1);
}

$archivosModificados = 0;

for ($i = 1; $i < $argc; $i++) {
    $path = $argv[$i];

    if (is_dir($path)) {
        // Buscar archivos PHP recursivamente
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                if (refactorInsertUpdate($file->getPathname())) {
                    $archivosModificados++;
                }
            }
        }
    } elseif (is_file($path)) {
        if (refactorInsertUpdate($path)) {
            $archivosModificados++;
        }
    } else {
        echo "Error: No existe el archivo o directorio: $path\n";
    }
}

echo "\n===========================================\n";
echo "Refactorización completada\n";
echo "Archivos modificados: $archivosModificados\n";
echo "===========================================\n";
