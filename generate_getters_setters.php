<?php

/**
 * Script para generar automáticamente métodos get/set para entidades
 * siguiendo el patrón de ActaTribunal
 *
 * Uso: php generate_getters_setters.php <ruta_a_entidad>
 * Ejemplo: php generate_getters_setters.php src/profesores/domain/entity/ProfesorAmpliacion.php
 */

if ($argc < 2) {
    echo "Uso: php generate_getters_setters.php <ruta_a_entidad>\n";
    exit(1);
}

$filePath = $argv[1];

if (!file_exists($filePath)) {
    echo "Error: El archivo no existe: $filePath\n";
    exit(1);
}

$content = file_get_contents($filePath);

// Extraer namespace y clase
preg_match('/namespace\s+([\w\\\\]+);/', $content, $namespaceMatch);
preg_match('/class\s+(\w+)/', $content, $classMatch);

$namespace = $namespaceMatch[1] ?? '';
$className = $classMatch[1] ?? '';

echo "Procesando clase: $namespace\\$className\n\n";

// Extraer las propiedades privadas de la clase
preg_match_all('/private\s+(\??)([\w\\\\|]+)\s+\$(\w+)(?:\s*=\s*[^;]+)?;/', $content, $matches, PREG_SET_ORDER);

$properties = [];
foreach ($matches as $match) {
    $isNullable = $match[1] === '?';
    $type = $match[2];
    $propertyName = $match[3];

    $properties[] = [
        'name' => $propertyName,
        'type' => $type,
        'isNullable' => $isNullable,
        'fullType' => $match[1] . $match[2],
    ];
}

// Separar propiedades que son Value Objects de las que no lo son
$voProperties = [];
$scalarProperties = [];

foreach ($properties as $property) {
    $type = $property['type'];

    // Considerar VO si el tipo empieza con mayúscula y contiene backslash o es una clase personalizada
    // Excluir tipos como DateTimeLocal, int, string, bool, array
    $isScalar = in_array(strtolower($type), ['int', 'string', 'bool', 'float', 'array'])
                || strpos($type, 'DateTimeLocal') !== false
                || strpos($type, '|') !== false && (strpos($type, 'DateTimeLocal') !== false);

    if ($isScalar) {
        $scalarProperties[] = $property;
    } else {
        $voProperties[] = $property;
    }
}

echo "=== PROPIEDADES VALUE OBJECT ===\n";
foreach ($voProperties as $prop) {
    echo "- {$prop['name']}: {$prop['fullType']}\n";
}

echo "\n=== PROPIEDADES ESCALARES ===\n";
foreach ($scalarProperties as $prop) {
    echo "- {$prop['name']}: {$prop['fullType']}\n";
}

echo "\n\n=== CÓDIGO GENERADO ===\n\n";

// Generar métodos para Value Objects
foreach ($voProperties as $property) {
    $propertyName = $property['name'];
    $type = $property['type'];
    $isNullable = $property['isNullable'];
    $fullType = $property['fullType'];

    // Para métodos VO: convertir snake_case a camelCase (sin guiones bajos)
    $methodSuffixCamel = str_replace('_', '', ucwords($propertyName, '_'));
    // Para métodos legacy: mantener snake_case (con guiones bajos)
    $methodSuffixSnake = ucfirst($propertyName);

    // Determinar el tipo primitivo del VO
    $primitiveType = detectPrimitiveType($type);

    echo generateVoMethods($propertyName, $methodSuffixCamel, $methodSuffixSnake, $type, $isNullable, $primitiveType);
    echo "\n";
}

// Generar métodos para propiedades escalares
foreach ($scalarProperties as $property) {
    $propertyName = $property['name'];
    $type = $property['type'];
    $isNullable = $property['isNullable'];
    $fullType = $property['fullType'];

    // Para métodos escalares: mantener snake_case (con guiones bajos)
    $methodSuffixSnake = ucfirst($propertyName);

    echo generateScalarMethods($propertyName, $methodSuffixSnake, $fullType);
    echo "\n";
}

/**
 * Detecta el tipo primitivo de un Value Object analizando su nombre
 */
function detectPrimitiveType(string $voClassName): string
{
    // Patrones comunes
    if (preg_match('/(Id|Number|Numero|Orden|Max|Min|Limite|Count)$/i', $voClassName)) {
        return 'int';
    }
    if (preg_match('/(Text|Name|Nombre|Descripcion|Desc|Observ|Code|Breve|Detalle|Lugar)$/i', $voClassName)) {
        return 'string';
    }
    if (preg_match('/(Fecha|Date|Time)$/i', $voClassName)) {
        return 'DateTimeLocal';
    }

    // Por defecto, asumir string
    return 'string';
}

/**
 * Genera métodos para propiedades Value Object
 */
function generateVoMethods(string $propertyName, string $methodSuffixCamel, string $methodSuffixSnake, string $voType, bool $isNullable, string $primitiveType): string
{
    $nullableReturn = $isNullable ? '?' : '';
    $nullableParam = $isNullable ? '|null' : '';
    $nullCheck = $isNullable ? '?' : '';

    // Determinar el método factory del VO
    $factoryMethod = 'fromNullableString';
    if ($primitiveType === 'int') {
        $factoryMethod = 'fromNullable';
    } elseif ($primitiveType === 'DateTimeLocal') {
        $factoryMethod = 'fromNullable';
    }

    $code = '';

    // Método getXxXxVo() - camelCase
    $code .= "    public function get{$methodSuffixCamel}Vo(): {$nullableReturn}{$voType}\n";
    $code .= "    {\n";
    $code .= "        return \$this->{$propertyName};\n";
    $code .= "    }\n\n";

    // Método setXxXxVo() - camelCase
    $code .= "    public function set{$methodSuffixCamel}Vo({$voType}|{$primitiveType}{$nullableParam} \$valor = null): void\n";
    $code .= "    {\n";
    $code .= "        \$this->{$propertyName} = \$valor instanceof {$voType}\n";
    $code .= "            ? \$valor\n";
    $code .= "            : {$voType}::{$factoryMethod}(\$valor);\n";
    $code .= "    }\n\n";

    // Método getXx_xx() deprecated - snake_case
    $returnType = $primitiveType === 'DateTimeLocal' ? "{$nullableReturn}string" : "{$nullableReturn}{$primitiveType}";
    $code .= "    /**\n";
    $code .= "     * @deprecated use get{$methodSuffixCamel}Vo()\n";
    $code .= "     */\n";
    $code .= "    public function get{$methodSuffixSnake}(): {$returnType}\n";
    $code .= "    {\n";
    $code .= "        return \$this->{$propertyName}{$nullCheck}->value();\n";
    $code .= "    }\n\n";

    // Método setXx_xx() deprecated - snake_case
    $paramType = $primitiveType === 'DateTimeLocal' ? "?string" : "{$nullableReturn}{$primitiveType}";
    $code .= "    /**\n";
    $code .= "     * @deprecated use set{$methodSuffixCamel}Vo()\n";
    $code .= "     */\n";
    $code .= "    public function set{$methodSuffixSnake}({$paramType} \$valor = null): void\n";
    $code .= "    {\n";
    $code .= "        \$this->{$propertyName} = {$voType}::{$factoryMethod}(\$valor);\n";
    $code .= "    }\n";

    return $code;
}

/**
 * Genera métodos para propiedades escalares
 */
function generateScalarMethods(string $propertyName, string $methodSuffix, string $type): string
{
    $code = '';

    // Método getXX()
    $code .= "    public function get{$methodSuffix}(): {$type}\n";
    $code .= "    {\n";
    $code .= "        return \$this->{$propertyName};\n";
    $code .= "    }\n\n";

    // Método setXX()
    $code .= "    public function set{$methodSuffix}({$type} \$valor): void\n";
    $code .= "    {\n";
    $code .= "        \$this->{$propertyName} = \$valor;\n";
    $code .= "    }\n";

    return $code;
}

echo "\n=== FIN DEL CÓDIGO GENERADO ===\n\n";

// Ahora insertamos el código generado en el archivo original
echo "¿Deseas insertar estos métodos en el archivo? (s/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim(strtolower($line)) !== 's') {
    echo "Operación cancelada.\n";
    exit(0);
}

// Buscar la posición donde termina la declaración de propiedades
// Buscamos el último "private" seguido de un cierre de sección de atributos
preg_match_all('/private\s+\??[\w\\\\|]+\s+\$\w+(?:\s*=\s*[^;]+)?;/', $content, $allPrivates, PREG_OFFSET_CAPTURE);

if (empty($allPrivates[0])) {
    echo "Error: No se encontraron propiedades privadas.\n";
    exit(1);
}

// Obtener la posición del último private
$lastPrivate = end($allPrivates[0]);
$insertPosition = $lastPrivate[1] + strlen($lastPrivate[0]);

// Buscar el comentario de sección "MÉTODOS PÚBLICOS" si existe
preg_match('/\/\*\s*MÉTODOS PÚBLICOS\s*-+\s*\*\//', $content, $metodosMatch, PREG_OFFSET_CAPTURE, $insertPosition);

if (!empty($metodosMatch[0])) {
    // Si existe la sección de métodos públicos, insertar antes de ella
    $insertPosition = $metodosMatch[0][1];
} else {
    // Buscar el siguiente método público después de las propiedades
    preg_match('/\n\s*public\s+function/', $content, $firstMethod, PREG_OFFSET_CAPTURE, $insertPosition);
    if (!empty($firstMethod[0])) {
        $insertPosition = $firstMethod[0][1];
    } else {
        // Si no hay métodos, insertar antes del cierre de clase
        preg_match('/\n}[^}]*$/', $content, $classEnd, PREG_OFFSET_CAPTURE);
        if (!empty($classEnd[0])) {
            $insertPosition = $classEnd[0][1];
        }
    }
}

// Generar el código completo a insertar
$generatedCode = "\n\n    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/\n\n";

// Generar métodos para Value Objects
foreach ($voProperties as $property) {
    $propertyName = $property['name'];
    $type = $property['type'];
    $isNullable = $property['isNullable'];
    $primitiveType = detectPrimitiveType($type);
    // Para métodos VO: camelCase (sin guiones bajos)
    $methodSuffixCamel = str_replace('_', '', ucwords($propertyName, '_'));
    // Para métodos legacy: snake_case (con guiones bajos)
    $methodSuffixSnake = ucfirst($propertyName);
    $generatedCode .= generateVoMethods($propertyName, $methodSuffixCamel, $methodSuffixSnake, $type, $isNullable, $primitiveType);
    $generatedCode .= "\n";
}

// Generar métodos para propiedades escalares
foreach ($scalarProperties as $property) {
    $propertyName = $property['name'];
    $fullType = $property['fullType'];
    // Para métodos escalares: snake_case (con guiones bajos)
    $methodSuffixSnake = ucfirst($propertyName);
    $generatedCode .= generateScalarMethods($propertyName, $methodSuffixSnake, $fullType);
    $generatedCode .= "\n";
}

// Eliminar métodos existentes si los hay para evitar duplicados
// Buscar y eliminar la sección de MÉTODOS PÚBLICOS existente
$contentBeforeInsert = substr($content, 0, $insertPosition);
$contentAfterInsert = substr($content, $insertPosition);

// Si ya existe una sección de MÉTODOS PÚBLICOS, la eliminamos
$contentAfterInsert = preg_replace('/\s*\/\*\s*MÉTODOS PÚBLICOS\s*-+\s*\*\/.*?(?=\/\*|$)/s', '', $contentAfterInsert, 1);

// Insertar el código generado
$newContent = $contentBeforeInsert . $generatedCode . $contentAfterInsert;

// Guardar el archivo
file_put_contents($filePath, $newContent);

echo "\n✓ Métodos insertados correctamente en: $filePath\n";
