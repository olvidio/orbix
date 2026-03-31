#!/usr/bin/env php
<?php
/**
 * Script para corregir automáticamente los métodos setXxxVo en entidades.
 *
 * Uso:
 *   php fix_entity_setters.php <ruta_entidad>
 *   php fix_entity_setters.php <modulo>
 *
 * Ejemplos:
 *   php fix_entity_setters.php src/ubis/domain/entity/Casa.php
 *   php fix_entity_setters.php ubis
 *
 * El script:
 * 1. Lee la(s) entidad(es)
 * 2. Identifica propiedades que son Value Objects
 * 3. Detecta el tipo del VO (int/string, nullable/non-nullable)
 * 4. Regenera los métodos setXxxVo según el patrón correcto
 */

if ($argc < 2) {
    echo "Uso: php fix_entity_setters.php <ruta_entidad|modulo>\n";
    echo "Ejemplos:\n";
    echo "  php fix_entity_setters.php src/ubis/domain/entity/Casa.php\n";
    echo "  php fix_entity_setters.php ubis\n";
    exit(1);
}

$input = $argv[1];

// Determinar si es un archivo o un módulo
if (file_exists($input) && is_file($input)) {
    // Es un archivo específico
    $entityFiles = [$input];
} else {
    // Asumimos que es un nombre de módulo
    $moduleName = $input;
    $entityDir = "src/$moduleName/domain/entity";

    if (!is_dir($entityDir)) {
        echo "Error: No se encontró el directorio '$entityDir'.\n";
        echo "Asegúrate de que el módulo existe o proporciona una ruta de archivo válida.\n";
        exit(1);
    }

    // Buscar todos los archivos .php en el directorio de entidades
    $entityFiles = glob("$entityDir/*.php");

    if (empty($entityFiles)) {
        echo "No se encontraron archivos de entidades en '$entityDir'.\n";
        exit(0);
    }

    echo "=== Módulo: $moduleName ===\n";
    echo "Directorio: $entityDir\n";
    echo "Entidades encontradas: " . count($entityFiles) . "\n\n";
}

// Procesar cada archivo de entidad
$totalProcessed = 0;
$totalModified = 0;

foreach ($entityFiles as $entityPath) {
    $result = processEntity($entityPath);
    $totalProcessed++;
    if ($result > 0) {
        $totalModified++;
    }
}

if (count($entityFiles) > 1) {
    echo "\n" . str_repeat('=', 70) . "\n";
    echo "=== RESUMEN FINAL ===\n";
    echo "Entidades procesadas: $totalProcessed\n";
    echo "Entidades modificadas: $totalModified\n";
    echo str_repeat('=', 70) . "\n";
}

exit(0);

// ============================================================================
// FUNCIÓN PRINCIPAL DE PROCESAMIENTO
// ============================================================================

function processEntity(string $entityPath): int
{
    if (basename($entityPath) === 'processEntity') {
        // Evitar procesar funciones auxiliares
        return 0;
    }

    echo "\n" . str_repeat('-', 70) . "\n";
    echo "=== Procesando: " . basename($entityPath) . " ===\n";
    echo "Ruta: $entityPath\n\n";

$content = file_get_contents($entityPath);

    // Extraer el namespace y los value objects importados
    preg_match('/namespace\s+([\w\\\\]+);/', $content, $namespaceMatch);
    $namespace = $namespaceMatch[1] ?? '';

    // Extraer todos los VOs importados
    preg_match_all('/use\s+[\w\\\\]+\\\\value_objects\\\\\{([^}]+)\}/', $content, $voImports);
    if (empty($voImports[1])) {
        preg_match_all('/use\s+([\w\\\\]+\\\\value_objects\\\\(\w+));/', $content, $singleVoImports);
        $valueObjects = $singleVoImports[2] ?? [];
    } else {
        $voList = $voImports[1][0];
        $valueObjects = array_map('trim', explode(',', $voList));
    }

    if (empty($valueObjects)) {
        echo "No se encontraron Value Objects importados.\n";
        return 0;
    }

    // Extraer propiedades privadas de la clase
    preg_match_all('/private\s+\??(\w+)\s+\$(\w+)\s*=?\s*null?;/', $content, $properties);
    $propertyTypes = array_combine($properties[2], $properties[1]);

    // Detectar propiedades nullable
    preg_match_all('/private\s+\?(\w+)\s+\$(\w+)/', $content, $nullableProps);
    $nullableProperties = $nullableProps[2] ?? [];

    echo "Value Objects encontrados: " . implode(', ', $valueObjects) . "\n";
    echo "Propiedades encontradas: " . count($propertyTypes) . "\n\n";

    $modifications = [];

    foreach ($propertyTypes as $property => $type) {
        // Verificar si el tipo es un Value Object
        if (!in_array($type, $valueObjects)) {
            continue;
        }

        // Determinar si es nullable
        $isNullable = in_array($property, $nullableProperties);

        // Construir el nombre del método setter
        $methodName = 'set' . toCamelCase($property) . 'Vo';

        // Detectar el tipo primitivo del VO
        $primitiveType = detectVoPrimitiveType($content, $type, $property);

        echo "Propiedad: \$$property\n";
        echo "  - Tipo VO: $type\n";
        echo "  - Nullable: " . ($isNullable ? 'Sí' : 'No') . "\n";
        echo "  - Tipo primitivo: $primitiveType\n";
        echo "  - Método a regenerar: $methodName\n\n";

        // Generar el nuevo método
        $newMethod = generateSetterMethod($methodName, $property, $type, $primitiveType, $isNullable);
        $modifications[$methodName] = [
            'property' => $property,
            'type' => $type,
            'nullable' => $isNullable,
            'primitiveType' => $primitiveType,
            'newMethod' => $newMethod
        ];
    }

    if (empty($modifications)) {
        echo "No se encontraron métodos setXxxVo que necesiten corrección.\n";
        return 0;
    }

    echo "\n=== Métodos a modificar: " . count($modifications) . " ===\n\n";

    // Aplicar las modificaciones
    foreach ($modifications as $methodName => $info) {
        echo "Reemplazando método: $methodName...\n";

        // Buscar el método con llaves balanceadas
        $methodStart = '/public\s+function\s+' . preg_quote($methodName, '/') . '\s*\([^)]*\)\s*:\s*void\s*\{/';

        if (preg_match($methodStart, $content, $match, PREG_OFFSET_CAPTURE)) {
            $startPos = $match[0][1];
            $openBracePos = $startPos + strlen($match[0][0]) - 1; // Posición de la '{' inicial

            // Encontrar la llave de cierre balanceada
            $closeBracePos = findClosingBrace($content, $openBracePos);

            if ($closeBracePos !== false) {
                $methodLength = $closeBracePos - $startPos + 1;
                $oldMethod = substr($content, $startPos, $methodLength);

                $content = substr_replace($content, $info['newMethod'], $startPos, $methodLength);
                echo "  ✓ Método reemplazado\n";
            } else {
                echo "  ✗ No se pudo encontrar el cierre del método\n";
            }
        } else {
            echo "  ✗ No se encontró el método existente\n";
        }
    }

    // Guardar el archivo modificado
    file_put_contents($entityPath, $content);

    echo "✓ Archivo guardado\n";

    return count($modifications);
}

// ============================================================================
// FUNCIONES AUXILIARES
// ============================================================================

/**
 * Encuentra la llave de cierre balanceada para una llave de apertura dada
 */
function findClosingBrace(string $content, int $openBracePos): int|false
{
    $length = strlen($content);
    $braceCount = 1;
    $pos = $openBracePos + 1;

    while ($pos < $length && $braceCount > 0) {
        $char = $content[$pos];

        // Ignorar llaves dentro de strings
        if ($char === '"' || $char === "'") {
            $quote = $char;
            $pos++;
            while ($pos < $length) {
                if ($content[$pos] === '\\') {
                    $pos += 2; // Saltar carácter escapado
                    continue;
                }
                if ($content[$pos] === $quote) {
                    break;
                }
                $pos++;
            }
        } elseif ($char === '{') {
            $braceCount++;
        } elseif ($char === '}') {
            $braceCount--;
            if ($braceCount === 0) {
                return $pos;
            }
        }

        $pos++;
    }

    return false;
}

/**
 * Convierte snake_case a CamelCase
 */
function toCamelCase(string $snakeCase): string
{
    return str_replace('_', '', ucwords($snakeCase, '_'));
}

/**
 * Detecta el tipo primitivo de un VO (int o string) basándose en el contexto
 */
function detectVoPrimitiveType(string $content, string $voType, string $property): string
{
    // Buscar en el método deprecated o en el constructor del VO

    // Buscar setter deprecated
    $deprecatedSetterPattern = '/function\s+set' . toCamelCase($property) .
                               '\s*\(\s*\??(\w+)\s+\$/';
    if (preg_match($deprecatedSetterPattern, $content, $match)) {
        $type = $match[1];
        if ($type === 'int') return 'int';
        if ($type === 'string') return 'string';
    }

    // Buscar getter deprecated
    $deprecatedGetterPattern = '/function\s+get' . toCamelCase($property) .
                               '\s*\(\s*\)\s*:\s*\??(\w+)/';
    if (preg_match($deprecatedGetterPattern, $content, $match)) {
        $type = $match[1];
        if ($type === 'int') return 'int';
        if ($type === 'string') return 'string';
    }

    // Si el nombre del VO contiene "Id", probablemente es int
    if (stripos($voType, 'Id') !== false) {
        return 'int';
    }

    // Si contiene "Text", "Name", "Code", probablemente es string
    if (stripos($voType, 'Text') !== false ||
            stripos($voType, 'Name') !== false ||
            stripos($voType, 'Code') !== false) {
        return 'string';
    }

    // Si contiene "Num", "Id", "Code", probablemente es int
    if (stripos($voType, 'Num') !== false ||
        stripos($voType, 'Id') !== false) {
        return 'int';
    }

    // Por defecto, asumir string
    return 'string';
}

/**
 * Genera el método setter según el patrón correcto
 */
function generateSetterMethod(
    string $methodName,
    string $property,
    string $voType,
    string $primitiveType,
    bool $isNullable
): string
{
    $indent = '    ';

    if ($primitiveType === 'int' && !$isNullable) {
        // Caso a) int no null
        return <<<PHP
public function $methodName($voType|int \$valor): void
$indent{
$indent    \$this->$property = \$valor instanceof $voType
$indent        ? \$valor
$indent        : new $voType(\$valor);
$indent}
PHP;
    }

    if ($primitiveType === 'int' && $isNullable) {
        // Caso b) int null
        return <<<PHP
public function $methodName($voType|int|null \$valor = null): void
$indent{
$indent    \$this->$property = \$valor instanceof $voType
$indent        ? \$valor
$indent        : $voType::fromNullable(\$valor);
$indent}
PHP;
    }

    if ($primitiveType === 'string' && !$isNullable) {
        // Caso c) string no null
        return <<<PHP
public function $methodName($voType|string \$texto): void
$indent{
$indent    \$this->$property = \$texto instanceof $voType
$indent        ? \$texto
$indent        : $voType::fromString(\$texto);
$indent}
PHP;
    }

    if ($primitiveType === 'string' && $isNullable) {
        // Caso d) string null
        return <<<PHP
public function $methodName($voType|string|null \$texto = null): void
$indent{
$indent    \$this->$property = \$texto instanceof $voType
$indent        ? \$texto
$indent        : $voType::fromNullableString(\$texto);
$indent}
PHP;
    }

    return '';
}
