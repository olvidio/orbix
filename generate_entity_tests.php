<?php

/**
 * Script para generar tests de entidades basándose en la estructura de una entidad existente
 *
 * Uso: php generate_entity_tests.php <modulo>
 * Ejemplo: php generate_entity_tests.php zonassacd
 */

if ($argc < 2) {
    echo "Uso: php generate_entity_tests.php <modulo>\n";
    echo "Ejemplo: php generate_entity_tests.php zonassacd\n";
    exit(1);
}

$modulo = $argv[1];
$entityDir = "src/{$modulo}/domain/entity";
$testDir = "tests/unit/{$modulo}/domain/entity";
$configFile = "tests/unit/{$modulo}/domain/entity/test_values_config.php";

if (!is_dir($entityDir)) {
    echo "Error: No existe el directorio de entidades: {$entityDir}\n";
    exit(1);
}

// Crear directorio de tests si no existe
if (!is_dir($testDir)) {
    mkdir($testDir, 0755, true);
    echo "✓ Creado directorio: {$testDir}\n";
}

// Cargar configuración de valores si existe
$customValues = [];
if (file_exists($configFile)) {
    $customValues = loadConfigFile($configFile);
    echo "✓ Usando configuración de valores: {$configFile}\n";
} else {
    echo "⚠ No se encontró archivo de configuración: {$configFile}\n";
    echo "  Puedes generarlo con: php generate_test_values_config.php {$modulo}\n";
}

// Buscar todas las entidades
$entityFiles = glob("{$entityDir}/*.php");

foreach ($entityFiles as $entityFile) {
    $entityName = basename($entityFile, '.php');
    $testFile = "{$testDir}/{$entityName}Test.php";

    // Saltar si el test ya existe
    if (file_exists($testFile)) {
        echo "⊘ Ya existe: {$testFile}\n";
        continue;
    }

    // Analizar la entidad
    $entityContent = file_get_contents($entityFile);
    $analysis = analyzeEntity($entityContent, $modulo, $entityName);

    // Generar el test
    $testContent = generateTest($modulo, $entityName, $analysis, $customValues);

    // Guardar el test
    file_put_contents($testFile, $testContent);
    echo "✓ Generado: {$testFile}\n";
}

echo "\n✓ Proceso completado\n";

/**
 * Carga el archivo de configuración
 */
function loadConfigFile(string $configFile): array
{
    if (!file_exists($configFile)) {
        return [];
    }

    $config = include $configFile;
    return is_array($config) ? $config : [];
}

/**
 * Analiza una entidad para extraer sus propiedades y métodos
 */
function analyzeEntity(string $content, string $modulo, string $entityName): array
{
    $analysis = [
        'properties' => [],
        'valueObjects' => [],
        'valueObjectModules' => [],
        'primaryKey' => null
    ];

    // Extraer namespace de value objects del módulo actual (formato normal)
    preg_match_all('/use src\\\\' . preg_quote($modulo) . '\\\\domain\\\\value_objects\\\\(\w+);/', $content, $voMatches);
    $analysis['valueObjects'] = $voMatches[1] ?? [];

    // Extraer value objects del formato con llaves: use src\modulo\domain\value_objects\{Vo1, Vo2};
    if (preg_match('/use src\\\\' . preg_quote($modulo) . '\\\\domain\\\\value_objects\\\\\{([^}]+)\};/', $content, $blockMatch)) {
        $vosInBlock = array_map('trim', explode(',', $blockMatch[1]));
        $analysis['valueObjects'] = array_merge($analysis['valueObjects'], $vosInBlock);
    }

    // Extraer value objects de shared (formato normal)
    preg_match_all('/use src\\\\shared\\\\domain\\\\value_objects\\\\(\w+);/', $content, $voSharedMatches);
    if (!empty($voSharedMatches[1])) {
        $analysis['valueObjects'] = array_merge($analysis['valueObjects'], $voSharedMatches[1]);
    }

    // Extraer value objects de shared (formato con llaves)
    if (preg_match('/use src\\\\shared\\\\domain\\\\value_objects\\\\\{([^}]+)\};/', $content, $sharedBlockMatch)) {
        $vosInSharedBlock = array_map('trim', explode(',', $sharedBlockMatch[1]));
        $analysis['valueObjects'] = array_merge($analysis['valueObjects'], $vosInSharedBlock);
    }

    // Extraer value objects de otros módulos (formato normal)
    preg_match_all('/use src\\\\(\w+)\\\\domain\\\\value_objects\\\\(\w+);/', $content, $otherModulesMatches, PREG_SET_ORDER);
    foreach ($otherModulesMatches as $match) {
        $otherModule = $match[1];
        $voName = $match[2];
        // Solo agregar si no es del módulo actual ni de shared (ya los tenemos)
        if ($otherModule !== $modulo && $otherModule !== 'shared') {
            $analysis['valueObjects'][] = $voName;
            // Guardar también el módulo del que proviene
            $analysis['valueObjectModules'][$voName] = $otherModule;
        }
    }

    // Extraer propiedades privadas y protegidas
    preg_match_all('/(private|protected)\s+(\??)(int|string|bool|float|array|\w+)\s+\$(\w+)(\s*=\s*([^;]+))?;/', $content, $propertyMatches, PREG_SET_ORDER);

    foreach ($propertyMatches as $match) {
        $isNullable = $match[2] === '?';
        $type = $match[3];
        $name = $match[4];
        $defaultValue = isset($match[6]) ? trim($match[6]) : null;

        // Skip id_auto - es autogenerado por la base de datos
        if ($name === 'id_auto') {
            continue;
        }

        // Detectar primary key (normalmente id_xxx)
        if (preg_match('/^id_' . strtolower($entityName) . '$/', $name)) {
            $analysis['primaryKey'] = $name;
        }

        // Detectar si es DateTimeLocal o fecha
        $isDateTime = ($type === 'DateTimeLocal' || preg_match('/^f_/', $name));

        // Detectar si es value object
        $isVO = in_array($type, $analysis['valueObjects']);

        // Buscar qué método setter existe realmente
        $methodName = ucfirst($name); // Capitalizar primera letra manteniendo guiones bajos
        $camelCaseName = str_replace('_', '', ucwords($name, '_')); // Convertir a camelCase

        // Verificar si existe método con Vo (camelCase)
        $voSetterPattern = '/public function set' . preg_quote($camelCaseName, '/') . 'Vo\(/';
        $hasVoMethod = preg_match($voSetterPattern, $content);

        // Verificar si existe método sin Vo (snake_case)
        $normalSetterPattern = '/public function set' . preg_quote($methodName, '/') . '\(/';
        $hasNormalMethod = preg_match($normalSetterPattern, $content);

        // Decidir cuál método usar
        if ($hasVoMethod) {
            // Usa método con Vo en camelCase
            $isVO = true;
        } elseif ($hasNormalMethod) {
            // Usa método sin Vo en snake_case
            // Analizar el tipo del parámetro del setter
            $setterPattern = '/public function set' . preg_quote($methodName, '/') . '\(([^)]+)\)/';
            if (preg_match($setterPattern, $content, $setterMatch)) {
                // Extraer el tipo del parámetro del setter
                if (preg_match('/(\w+)\s*\|?\s*\w*/', $setterMatch[1], $paramMatch)) {
                    $paramType = $paramMatch[1];
                    // Si acepta el value object o acepta string, es VO
                    if (in_array($paramType, $analysis['valueObjects'])) {
                        $isVO = true;
                    } elseif (in_array($paramType, ['int', 'string', 'bool', 'float', 'array'])) {
                        // Si el setter solo acepta primitivos, no usar Vo
                        $type = $paramType;
                        $isVO = false;
                    }
                }
            }
        }

        $analysis['properties'][$name] = [
            'type' => $type,
            'nullable' => $isNullable,
            'default' => $defaultValue,
            'isValueObject' => $isVO,
            'isBool' => $type === 'bool',
            'isDateTime' => $isDateTime,
            'isPrimary' => false,
            'useVoMethod' => $hasVoMethod  // Si debe usar método con Vo en camelCase
        ];
    }

    // Marcar primary key
    if ($analysis['primaryKey']) {
        $analysis['properties'][$analysis['primaryKey']]['isPrimary'] = true;
    }

    return $analysis;
}

/**
 * Genera el contenido del test
 */
function generateTest(string $modulo, string $entityName, array $analysis, array $customValues): string
{
    $useStatements = [];
    $useStatements[] = "use src\\{$modulo}\\domain\\entity\\{$entityName};";

    // Leer la entidad original para detectar de dónde vienen los value objects
    $entityFile = "src/{$modulo}/domain/entity/{$entityName}.php";
    $entityContent = file_get_contents($entityFile);

    // Agregar imports de value objects del módulo actual
    preg_match_all('/use src\\\\' . preg_quote($modulo) . '\\\\domain\\\\value_objects\\\\(\w+);/', $entityContent, $voModuleMatches);
    foreach ($voModuleMatches[1] as $vo) {
        $useStatements[] = "use src\\{$modulo}\\domain\\value_objects\\{$vo};";
    }

    // Agregar imports de value objects con llaves del módulo actual
    if (preg_match('/use src\\\\' . preg_quote($modulo) . '\\\\domain\\\\value_objects\\\\\{([^}]+)\};/', $entityContent, $blockMatch)) {
        $vosInBlock = array_map('trim', explode(',', $blockMatch[1]));
        foreach ($vosInBlock as $vo) {
            $useStatements[] = "use src\\{$modulo}\\domain\\value_objects\\{$vo};";
        }
    }

    // Agregar imports de value objects de shared
    preg_match_all('/use src\\\\shared\\\\domain\\\\value_objects\\\\(\w+);/', $entityContent, $voSharedMatches);
    foreach ($voSharedMatches[1] as $vo) {
        $useStatements[] = "use src\\shared\\domain\\value_objects\\{$vo};";
    }

    // Agregar imports de value objects con llaves de shared
    if (preg_match('/use src\\\\shared\\\\domain\\\\value_objects\\\\\{([^}]+)\};/', $entityContent, $sharedBlockMatch)) {
        $vosInSharedBlock = array_map('trim', explode(',', $sharedBlockMatch[1]));
        foreach ($vosInSharedBlock as $vo) {
            $useStatements[] = "use src\\shared\\domain\\value_objects\\{$vo};";
        }
    }

    // Agregar imports de value objects de otros módulos
    preg_match_all('/use src\\\\(\w+)\\\\domain\\\\value_objects\\\\(\w+);/', $entityContent, $otherModulesMatches, PREG_SET_ORDER);
    foreach ($otherModulesMatches as $match) {
        $otherModule = $match[1];
        $voName = $match[2];
        // Solo agregar si no es del módulo actual ni de shared
        if ($otherModule !== $modulo && $otherModule !== 'shared') {
            $useStatements[] = "use src\\{$otherModule}\\domain\\value_objects\\{$voName};";
        }
    }

    // Agregar DateTimeLocal si hay propiedades de fecha
    $hasDateTime = false;
    foreach ($analysis['properties'] as $propInfo) {
        if ($propInfo['isDateTime']) {
            $hasDateTime = true;
            break;
        }
    }
    if ($hasDateTime) {
        $useStatements[] = "use src\\shared\\domain\\value_objects\\DateTimeLocal;";
    }

    sort($useStatements);
    $useStatementsStr = implode("\n", $useStatements);

    // Generar setUp
    $setUpCode = generateSetUp($entityName, $analysis, $customValues);

    // Generar tests para cada propiedad
    $testMethods = [];
    foreach ($analysis['properties'] as $propertyName => $propertyInfo) {
        if ($propertyInfo['isPrimary']) {
            $testMethods[] = generatePrimaryKeyTest($propertyName, $entityName);
        } elseif ($propertyInfo['isValueObject']) {
            $testMethods[] = generateValueObjectTest($propertyName, $propertyInfo['type'], $entityName, $customValues, $propertyInfo['useVoMethod']);
        } elseif ($propertyInfo['isBool']) {
            $testMethods[] = generateBooleanTest($propertyName, $entityName);
        } elseif ($propertyInfo['isDateTime']) {
            $testMethods[] = generateDateTimeTest($propertyName, $propertyInfo['type'], $entityName);
        } else {
            $testMethods[] = generateSimplePropertyTest($propertyName, $propertyInfo['type'], $entityName, $customValues);
        }
    }

    // Generar test de setAllAttributes
    $testMethods[] = generateSetAllAttributesTest($entityName, $analysis, $customValues);
    $testMethods[] = generateSetAllAttributesWithStringsTest($entityName, $analysis, $customValues);

    $testMethodsStr = implode("\n\n", $testMethods);

    return <<<PHP
<?php

namespace Tests\\unit\\{$modulo}\\domain\\entity;

{$useStatementsStr}
use Tests\\myTest;

class {$entityName}Test extends myTest
{
    private {$entityName} \${$entityName};

{$setUpCode}

{$testMethodsStr}
}

PHP;
}

/**
 * Genera el método setUp
 */
function generateSetUp(string $entityName, array $analysis, array $customValues): string
{
    $lines = [];
    $lines[] = "    public function setUp(): void";
    $lines[] = "    {";
    $lines[] = "        parent::setUp();";
    $lines[] = "        \$this->{$entityName} = new {$entityName}();";

    // Setear primary key y primeros campos no nullable
    $count = 0;
    foreach ($analysis['properties'] as $propertyName => $propertyInfo) {
        if ($count >= 2) break;

        if ($propertyInfo['isPrimary']) {
            $lines[] = "        \$this->{$entityName}->set" . toMethodName($propertyName) . "(1);";
            $count++;
        } elseif (!$propertyInfo['nullable'] && $propertyInfo['isValueObject']) {
            $sampleValue = generateSampleValue($propertyInfo['type'], true, $customValues);
            if ($propertyInfo['useVoMethod']) {
                $lines[] = "        \$this->{$entityName}->set" . toCamelCase($propertyName) . "Vo(new {$propertyInfo['type']}({$sampleValue}));";
            } else {
                $lines[] = "        \$this->{$entityName}->set" . toMethodName($propertyName) . "(new {$propertyInfo['type']}({$sampleValue}));";
            }
            $count++;
        } elseif (!$propertyInfo['nullable'] && $propertyInfo['isDateTime']) {
            $lines[] = "        \$this->{$entityName}->set" . toMethodName($propertyName) . "(new DateTimeLocal('2024-01-15 10:30:00'));";
            $count++;
        } elseif (!$propertyInfo['nullable'] && !$propertyInfo['isBool']) {
            $sampleValue = generateSampleValue($propertyInfo['type'], false, $customValues);
            $lines[] = "        \$this->{$entityName}->set" . toMethodName($propertyName) . "({$sampleValue});";
            $count++;
        }
    }

    $lines[] = "    }";

    return implode("\n", $lines);
}

/**
 * Genera un test para la primary key
 */
function generatePrimaryKeyTest(string $propertyName, string $entityName): string
{
    $methodName = toMethodName($propertyName);
    return <<<PHP
    public function test_get_{$propertyName}()
    {
        \$this->assertEquals(1, \$this->{$entityName}->get{$methodName}());
    }
PHP;
}

/**
 * Genera un test para un value object
 */
function generateValueObjectTest(string $propertyName, string $voType, string $entityName, array $customValues, bool $useVoMethod = true): string
{
    $sampleValue = generateSampleValue($voType, true, $customValues);

    if ($useVoMethod) {
        $methodName = toCamelCase($propertyName);
        return <<<PHP
    public function test_set_and_get_{$propertyName}()
    {
        \${$propertyName}Vo = new {$voType}({$sampleValue});
        \$this->{$entityName}->set{$methodName}Vo(\${$propertyName}Vo);
        \$this->assertInstanceOf({$voType}::class, \$this->{$entityName}->get{$methodName}Vo());
        \$this->assertEquals({$sampleValue}, \$this->{$entityName}->get{$methodName}Vo()->value());
    }
PHP;
    } else {
        $methodName = toMethodName($propertyName);
        return <<<PHP
    public function test_set_and_get_{$propertyName}()
    {
        \${$propertyName}Vo = new {$voType}({$sampleValue});
        \$this->{$entityName}->set{$methodName}(\${$propertyName}Vo);
        \$this->assertInstanceOf({$voType}::class, \$this->{$entityName}->get{$methodName}());
        \$this->assertEquals({$sampleValue}, \$this->{$entityName}->get{$methodName}()->value());
    }
PHP;
    }
}

/**
 * Genera un test para un booleano
 */
function generateBooleanTest(string $propertyName, string $entityName): string
{
    $methodName = toMethodName($propertyName);
    $isMethod = "is" . toMethodName($propertyName);

    return <<<PHP
    public function test_set_and_get_{$propertyName}()
    {
        \$this->{$entityName}->set{$methodName}(true);
        \$this->assertTrue(\$this->{$entityName}->{$isMethod}());
    }
PHP;
}

/**
 * Genera un test para una fecha/datetime
 */
function generateDateTimeTest(string $propertyName, string $type, string $entityName): string
{
    $methodName = toMethodName($propertyName);

    return <<<PHP
    public function test_set_and_get_{$propertyName}()
    {
        \$date = new DateTimeLocal('2024-01-15 10:30:00');
        \$this->{$entityName}->set{$methodName}(\$date);
        \$this->assertInstanceOf(DateTimeLocal::class, \$this->{$entityName}->get{$methodName}());
        \$this->assertEquals('2024-01-15 10:30:00', \$this->{$entityName}->get{$methodName}()->format('Y-m-d H:i:s'));
    }
PHP;
}

/**
 * Genera un test para una propiedad simple
 */
function generateSimplePropertyTest(string $propertyName, string $type, string $entityName, array $customValues): string
{
    $methodName = toMethodName($propertyName);
    $sampleValue = generateSampleValue($type, false, $customValues);

    return <<<PHP
    public function test_set_and_get_{$propertyName}()
    {
        \$this->{$entityName}->set{$methodName}({$sampleValue});
        \$this->assertEquals({$sampleValue}, \$this->{$entityName}->get{$methodName}());
    }
PHP;
}

/**
 * Genera test de setAllAttributes con value objects
 */
function generateSetAllAttributesTest(string $entityName, array $analysis, array $customValues): string
{
    $entityVar = lcfirst($entityName);
    $attributes = [];
    $assertions = [];

    foreach ($analysis['properties'] as $propertyName => $propertyInfo) {
        $value = generateSampleValue($propertyInfo['type'], $propertyInfo['isValueObject'], $customValues);

        if ($propertyInfo['isValueObject']) {
            $attributes[] = "            '{$propertyName}' => new {$propertyInfo['type']}({$value}),";
            if ($propertyInfo['useVoMethod']) {
                $assertions[] = "        \$this->assertEquals({$value}, \${$entityVar}->get" . toCamelCase($propertyName) . "Vo()->value());";
            } else {
                $assertions[] = "        \$this->assertEquals({$value}, \${$entityVar}->get" . toMethodName($propertyName) . "()->value());";
            }
        } elseif ($propertyInfo['isBool']) {
            $attributes[] = "            '{$propertyName}' => true,";
            $assertions[] = "        \$this->assertTrue(\${$entityVar}->is" . toMethodName($propertyName) . "());";
        } elseif ($propertyInfo['isDateTime']) {
            $attributes[] = "            '{$propertyName}' => new DateTimeLocal('2024-01-15 10:30:00'),";
            $assertions[] = "        \$this->assertEquals('2024-01-15 10:30:00', \${$entityVar}->get" . toMethodName($propertyName) . "()->format('Y-m-d H:i:s'));";
        } else {
            $attributes[] = "            '{$propertyName}' => {$value},";
            $assertions[] = "        \$this->assertEquals({$value}, \${$entityVar}->get" . toMethodName($propertyName) . "());";
        }
    }

    $attributesStr = implode("\n", $attributes);
    $assertionsStr = implode("\n", $assertions);

    return <<<PHP
    public function test_set_all_attributes()
    {
        \${$entityVar} = new {$entityName}();
        \$attributes = [
{$attributesStr}
        ];
        \${$entityVar}->setAllAttributes(\$attributes);

{$assertionsStr}
    }
PHP;
}

/**
 * Genera test de setAllAttributes con strings
 */
function generateSetAllAttributesWithStringsTest(string $entityName, array $analysis, array $customValues): string
{
    $entityVar = lcfirst($entityName);
    $attributes = [];
    $assertions = [];

    foreach ($analysis['properties'] as $propertyName => $propertyInfo) {
        $value = generateSampleValue($propertyInfo['type'], false, $customValues);

        if ($propertyInfo['isValueObject']) {
            $attributes[] = "            '{$propertyName}' => {$value},";
            if ($propertyInfo['useVoMethod']) {
                $assertions[] = "        \$this->assertEquals({$value}, \${$entityVar}->get" . toCamelCase($propertyName) . "Vo()->value());";
            } else {
                $assertions[] = "        \$this->assertEquals({$value}, \${$entityVar}->get" . toMethodName($propertyName) . "()->value());";
            }
        } elseif ($propertyInfo['isBool']) {
            $attributes[] = "            '{$propertyName}' => true,";
            $assertions[] = "        \$this->assertTrue(\${$entityVar}->is" . toMethodName($propertyName) . "());";
        } elseif ($propertyInfo['isDateTime']) {
            $attributes[] = "            '{$propertyName}' => new DateTimeLocal('2024-01-15 10:30:00'),";
            $assertions[] = "        \$this->assertEquals('2024-01-15 10:30:00', \${$entityVar}->get" . toMethodName($propertyName) . "()->format('Y-m-d H:i:s'));";
        } else {
            $attributes[] = "            '{$propertyName}' => {$value},";
            $assertions[] = "        \$this->assertEquals({$value}, \${$entityVar}->get" . toMethodName($propertyName) . "());";
        }
    }

    $attributesStr = implode("\n", $attributes);
    $assertionsStr = implode("\n", $assertions);

    return <<<PHP
    public function test_set_all_attributes_with_string_values()
    {
        \${$entityVar} = new {$entityName}();
        \$attributes = [
{$attributesStr}
        ];
        \${$entityVar}->setAllAttributes(\$attributes);

{$assertionsStr}
    }
PHP;
}

/**
 * Formatea un valor para usar en código PHP generado
 */
function formatValueForCode($value): string
{
    if (is_int($value) || is_float($value)) {
        return (string)$value;
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_string($value)) {
        return var_export($value, true);
    }

    if (is_array($value)) {
        return '[]';
    }

    return var_export($value, true);
}

/**
 * Convierte un nombre con guiones bajos a CamelCase para value objects
 */
function toCamelCase(string $name): string
{
    // Convertir nombre_variable a NombreVariable
    return str_replace('_', '', ucwords($name, '_'));
}

/**
 * Convierte un nombre a formato de método (mantiene guiones bajos)
 */
function toMethodName(string $name): string
{
    // Mantener formato snake_case: id_grupo -> Id_grupo
    return ucfirst($name);
}


/**
 * Genera un valor de muestra para un tipo dado
 */
function generateSampleValue(string $type, bool $asValueObject = true, array $customValues = []): string
{
    // Primero buscar en valores personalizados (coincidencia exacta)
    if (isset($customValues[$type])) {
        return formatValueForCode($customValues[$type]);
    }

    // Buscar coincidencias parciales en valores personalizados
    foreach ($customValues as $key => $value) {
        if (stripos($type, $key) !== false) {
            return formatValueForCode($value);
        }
    }

    // Si empieza con "Delegacion", generar código de 3 caracteres
    if (stripos($type, 'Delegacion') === 0) {
        return "'TST'";
    }

    // Si contiene "Order" u "Orden", es un número
    if (stripos($type, 'Order') !== false || stripos($type, 'Orden') !== false) {
        return "1";
    }

    // Si contiene "Code" o "Codigo", generar código corto (3 caracteres para delegaciones)
    if (stripos($type, 'Code') !== false || stripos($type, 'Codigo') !== false) {
        return "'TST'";
    }

    // Si contiene "Text", generar texto corto
    if (stripos($type, 'Text') !== false) {
        return "'Test'";
    }

    // Si contiene "Id", generar número (sin comillas, es integer)
    if (stripos($type, 'Id') !== false) {
        return "1";
    }

    // Valores por tipo primitivo
    return match($type) {
        'int' => '1',
        'string' => "'test'",
        'bool' => 'true',
        'float' => '1.5',
        'array' => "[]",
        default => "'test'"
    };
}
