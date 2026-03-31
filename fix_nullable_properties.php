#!/usr/bin/env php
<?php

/**
 * Script para corregir llamadas a getters en repositorios para propiedades nullable
 *
 * Este script analiza las entidades de un módulo específico y busca propiedades
 * que pueden ser null (excepto fechas). Luego modifica los repositorios correspondientes
 * para añadir el operador nullsafe (?->) en las llamadas a los getters de estas propiedades.
 *
 * Uso: php fix_nullable_properties.php <modulo>
 *
 * Ejemplos:
 *   php fix_nullable_properties.php profesores
 *   php fix_nullable_properties.php actividades
 *   php fix_nullable_properties.php ubis
 *
 * El script:
 * 1. Busca todas las entidades en src/<modulo>/domain/entity/
 * 2. Identifica getters que retornan tipos nullable: public function getXxxVo(): ?Type
 * 3. Excluye automáticamente propiedades de tipo fecha
 * 4. Modifica los repositorios en src/<modulo>/infrastructure/persistence/postgresql/
 * 5. Añade el operador nullsafe ?-> donde sea necesario
 *
 * Conversión:
 *   Antes: $aDatos['campo'] = $Entity->getCampoVo()->value();
 *   Después: $aDatos['campo'] = $Entity->getCampoVo()?->value();
 */

if ($argc < 2) {
    echo "Uso: php fix_nullable_properties.php <modulo>\n";
    echo "Ejemplo: php fix_nullable_properties.php profesores\n";
    exit(1);
}

$modulo = $argv[1];
$baseDir = __DIR__;
$entityDir = "$baseDir/src/$modulo/domain/entity";
$repositoryDir = "$baseDir/src/$modulo/infrastructure/persistence/postgresql";

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
echo "Directorio de repositorios: $repositoryDir\n\n";

/**
 * Extrae las propiedades nullable de una entidad (excepto fechas)
 * Busca getters que retornen ?Type y que tengan "Vo" en el nombre
 */
function getNullableProperties($filePath): array
{
    $content = file_get_contents($filePath);
    $nullableProps = [];

    // Buscar getters públicos que retornen tipos nullable y terminen con Vo
    // Patrón: public function getXxxVo(): ?Type
    preg_match_all('/public\s+function\s+(get\w+Vo)\(\):\s+\?([^\s\{]+)/m', $content, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $getterName = $match[1];
        $type = $match[2];

        // Excluir fechas (DateTimeInterface, DateTimeImmutable, DateTimeLocal, etc.)
        if (stripos($type, 'date') !== false || stripos($type, 'time') !== false) {
            continue;
        }

        // Extraer el nombre de la propiedad desde el getter
        // getIdTipoProfesorVo -> id_tipo_profesor
        $propName = $getterName;
        $propName = preg_replace('/^get/', '', $propName); // Quitar 'get'
        $propName = preg_replace('/Vo$/', '', $propName); // Quitar 'Vo'
        // Convertir CamelCase a snake_case
        $propName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $propName));

        $nullableProps[$propName] = [
            'type' => $type,
            'getter' => $getterName
        ];
    }

    return $nullableProps;
}

/**
 * Procesa un archivo de repositorio
 */
function processRepository($repositoryPath, $entityName, $nullableProps): bool
{
    if (!file_exists($repositoryPath)) {
        echo "  ⚠ Repositorio no encontrado: $repositoryPath\n";
        return false;
    }

    $content = file_get_contents($repositoryPath);
    $originalContent = $content;
    $changes = 0;

    foreach ($nullableProps as $propName => $propData) {
        $getterName = $propData['getter'];
        $type = $propData['type'];

        // Buscar patrones como: $Entity->getterVo()->value();
        // Y reemplazar por: $Entity->getterVo()?->value();

        // Patrón general: ->getterVo()->
        // Debe buscar todas las ocurrencias donde se llama al getter seguido de ->
        $pattern = '/\$\w+->' . preg_quote($getterName) . '\(\)->/';

        $newContent = preg_replace_callback($pattern, function($matches) use (&$changes, $getterName) {
            // Verificar que no tenga ya el operador nullsafe
            if (strpos($matches[0], '?->') === false) {
                $changes++;
                return str_replace($getterName . '()->', $getterName . '()?->', $matches[0]);
            }
            return $matches[0];
        }, $content);

        if ($newContent !== $content) {
            $content = $newContent;
        }
    }

    if ($changes > 0) {
        file_put_contents($repositoryPath, $content);
        echo "  ✓ Modificado: $repositoryPath ($changes cambios)\n";
        return true;
    } else {
        echo "  - Sin cambios: $repositoryPath\n";
        return false;
    }
}

// Obtener todas las entidades del módulo
$entityFiles = glob("$entityDir/*.php");
$totalChanges = 0;
$totalFixedLines = 0;
$modifiedRepositories = [];

foreach ($entityFiles as $entityFile) {
    $entityName = basename($entityFile, '.php');
    echo "\n=== Procesando entidad: $entityName ===\n";

    // Obtener propiedades nullable
    $nullableProps = getNullableProperties($entityFile);

    if (empty($nullableProps)) {
        echo "  - No hay propiedades nullable (no fechas) en esta entidad\n";
        continue;
    }

    echo "  Propiedades nullable encontradas:\n";
    foreach ($nullableProps as $prop => $propData) {
        echo "    - $prop ({$propData['type']}) -> {$propData['getter']}\n";
    }

    // Buscar el repositorio correspondiente
    $repositoryName = "Pg{$entityName}Repository.php";
    $repositoryPath = "$repositoryDir/$repositoryName";

    if (processRepository($repositoryPath, $entityName, $nullableProps)) {
        $totalChanges++;
        $modifiedRepositories[] = $repositoryName;
    }
}

echo "\n=== Resumen ===\n";
echo "Total de repositorios modificados: $totalChanges\n";
if (!empty($modifiedRepositories)) {
    echo "Repositorios modificados:\n";
    foreach ($modifiedRepositories as $repo) {
        echo "  - $repo\n";
    }
}
echo "\nProceso completado.\n";
