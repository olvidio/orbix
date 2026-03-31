<?php
/**
 * Script para generar Factories para entidades
 * Crea factories reutilizables que pueden ser usadas por cualquier test
 */

require_once __DIR__ . '/libs/vendor/autoload.php';

// Configuración
$baseDir = __DIR__;
$srcDir = $baseDir . '/src';
$factoriesDir = $baseDir . '/tests/factories';

// Función para analizar los setters y tipos de una entidad
function analyzeEntity(string $entityFile): array
{
    if (!file_exists($entityFile)) {
        return [];
    }

    $content = file_get_contents($entityFile);
    $info = [
        'setters' => [],
        'idField' => null,
        'namespace' => null,
        'className' => null,
        'valueObjects' => []
    ];

    // Extraer namespace
    if (preg_match('/namespace\s+([^;]+);/', $content, $match)) {
        $info['namespace'] = $match[1];
    }

    // Extraer nombre de clase
    if (preg_match('/class\s+([A-Za-z0-9_]+)/', $content, $match)) {
        $info['className'] = $match[1];
    }

    // Buscar el ID field con varios patrones
    // 1. Primero intentar getId_xxx que retorna int
    if (preg_match('/function\s+get(Id_[a-z_]+)\s*\(\)\s*:\s*int/', $content, $match)) {
        $info['idField'] = $match[1];
        $info['idType'] = 'int';
    }
    // 2. Intentar getId_xxx que retorna ?int (nullable)
    elseif (preg_match('/function\s+get(Id_[a-z_]+)\s*\(\)\s*:\s*\?int/', $content, $match)) {
        $info['idField'] = $match[1];
        $info['idType'] = '?int';
    }
    // 3. Buscar getXxxVo() no deprecated y no nullable como identificador
    elseif (preg_match('/public\s+function\s+get([A-Z][a-zA-Z0-9_]+Vo)\s*\(\)\s*:\s*([A-Za-z0-9_]+)\s/', $content, $match)) {
        // Verificar que no es nullable y no está deprecated
        $methodName = 'get' . $match[1];
        if (stripos($content, "@deprecated.*$methodName") === false) {
            // Convertir getXxxVo a xxx para el setter
            $fieldName = preg_replace('/Vo$/', '', $match[1]);
            $fieldName = lcfirst($fieldName);
            $info['idField'] = $fieldName;
            $info['idType'] = $match[2]; // El VO
            $info['isVoId'] = true;
        }
    }
    // 4. Si no encuentra nada, buscar el primer atributo privado no nullable
    if (!$info['idField']) {
        if (preg_match('/private\s+([A-Za-z0-9_\\\\]+)\s+\$([a-z_]+);/', $content, $match)) {
            $info['idField'] = $match[2];
            $info['idType'] = $match[1];
            $info['isFirstProperty'] = true;
        }
    }

    // Primero, identificar métodos deprecated
    $deprecatedMethods = [];
    // Buscar @deprecated seguido de public function setXxx (con o sin guiones bajos)
    if (preg_match_all('/@deprecated[^\n]*\n[^}]*?public\s+function\s+(set[A-Z_][a-zA-Z0-9_]*)/s', $content, $depMatches)) {
        $deprecatedMethods = $depMatches[1];
    }

    // Buscar todos los setters públicos (con o sin guiones bajos)
    preg_match_all('/public\s+function\s+(set[A-Z_][a-zA-Z0-9_]*)\s*\(([^)]+)\)/', $content, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $methodName = $match[1];
        $params = $match[2];

        // Saltar métodos deprecated
        if (in_array($methodName, $deprecatedMethods)) {
            continue;
        }

        // Analizar el tipo del parámetro
        $type = null;
        $nullable = false;
        $hasDefault = false;
        $isUnionType = false;

        // Buscar tipo y si es nullable
        if (preg_match('/(\?)?([A-Za-z0-9_\\\\|]+)\s+\$([a-zA-Z0-9_]+)/', $params, $typeMatch)) {
            $nullable = !empty($typeMatch[1]);
            $type = $typeMatch[2];
            $paramName = $typeMatch[3];

            // Detectar union types
            if (strpos($type, '|') !== false) {
                $isUnionType = true;
            }
        }

        // Ver si tiene valor por defecto
        if (strpos($params, '= null') !== false) {
            $hasDefault = true;
            $nullable = true;
        }

        $info['setters'][$methodName] = [
            'type' => $type,
            'nullable' => $nullable,
            'hasDefault' => $hasDefault,
            'isUnionType' => $isUnionType,
            'params' => $params
        ];
    }

    // Buscar use statements de value objects
    preg_match_all('/use\s+([^;]+\\\\value_objects\\\\([A-Za-z0-9_]+));/', $content, $voMatches, PREG_SET_ORDER);
    foreach ($voMatches as $match) {
        $info['valueObjects'][$match[2]] = $match[1];
    }

    return $info;
}

// Función para analizar un ValueObject y determinar qué tipo acepta en el constructor
function analyzeValueObject(string $voName, string $module, string $srcDir): ?string
{
    // Buscar el archivo del VO en varios lugares posibles
    $possiblePaths = [
        "$srcDir/$module/domain/value_objects/$voName.php",
        "$srcDir/shared/domain/value_objects/$voName.php",
    ];

    $voFile = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $voFile = $path;
            break;
        }
    }

    if (!$voFile) {
        return null;
    }

    $content = file_get_contents($voFile);

    // Buscar el constructor y su tipo de parámetro
    if (preg_match('/public\s+function\s+__construct\s*\(\s*([^)]+)\)/', $content, $match)) {
        $params = $match[1];

        // Extraer el tipo del primer parámetro
        if (preg_match('/^\s*(\?)?([a-zA-Z_\\\\]+)\s+\$/', $params, $typeMatch)) {
            $type = $typeMatch[2];
            return $type;
        }
    }

    return null;
}

// Función para generar valor Faker según el tipo y nombre del campo
function generateFakerValue(string $fieldName, ?string $type, bool $nullable, ?string $voType = null): string
{
    $fieldLower = strtolower($fieldName);

    // Si el tipo es null, retornar un valor por defecto
    if ($type === null) {
        return '$faker->word';
    }

    // Si se proporcionó el tipo del VO, usarlo para generar el valor apropiado
    if ($voType) {
        if ($voType === 'int') {
            return '$faker->numberBetween(1, 10)';
        } elseif ($voType === 'string') {
            // Continuar con la lógica basada en nombre del campo
        } elseif ($voType === 'bool') {
            return '$faker->boolean';
        } elseif ($voType === 'float') {
            return '$faker->randomFloat(2, 0, 100)';
        }
    }

    // Analizar union types
    $primaryType = $type;
    if (strpos($type, '|') !== false) {
        $types = explode('|', $type);
        foreach ($types as $t) {
            $t = trim($t);
            if ($t !== 'null' && $t !== 'string') {
                $primaryType = $t;
                break;
            }
        }
        if ($primaryType === $type) {
            $primaryType = trim($types[0]);
        }
    }

    // Casos especiales según nombre del campo

    // Detectar campos de fecha (f_xxx o F_xxx) - deben ir antes de otros checks
    if (preg_match('/^setf_/i', $fieldLower)) {
        return '$faker->date()';
    }

    if (strpos($fieldLower, 'email') !== false) {
        return '$faker->email';
    }
    if (strpos($fieldLower, 'password') !== false || strpos($fieldLower, 'pwd') !== false) {
        return '$faker->password(8, 20)';
    }
    if (strpos($fieldLower, 'nombre') !== false || strpos($fieldLower, 'name') !== false) {
        return '$faker->name';
    }
    if (strpos($fieldLower, 'descripcion') !== false || strpos($fieldLower, 'description') !== false) {
        return '$faker->sentence';
    }
    if (strpos($fieldLower, 'direccion') !== false || strpos($fieldLower, 'address') !== false) {
        return '$faker->address';
    }
    if (strpos($fieldLower, 'telefono') !== false || strpos($fieldLower, 'phone') !== false || strpos($fieldLower, 'teleco') !== false) {
        return '$faker->phoneNumber';
    }
    if (strpos($fieldLower, 'fecha') !== false || strpos($fieldLower, 'date') !== false) {
        return '$faker->date()';
    }
    if (strpos($fieldLower, 'url') !== false || strpos($fieldLower, 'web') !== false) {
        return '$faker->url';
    }
    if (strpos($fieldLower, 'orden') !== false || strpos($fieldLower, 'order') !== false) {
        return '$faker->numberBetween(1, 100)';
    }
    if (strpos($fieldLower, 'codigo') !== false || strpos($fieldLower, 'code') !== false) {
        return '$faker->numerify("CODE###")';
    }

    // Por tipo
    if ($primaryType === 'int') {
        return '$faker->numberBetween(1, 1000)';
    }
    if ($primaryType === 'float') {
        return '$faker->randomFloat(2, 0, 1000)';
    }
    if ($primaryType === 'bool') {
        return '$faker->boolean';
    }
    if ($primaryType === 'string') {
        return '$faker->word';
    }
    if ($primaryType === 'array') {
        return '[]';
    }

    // Value Objects u otras clases
    if (ctype_upper($primaryType[0])) {
        return '$faker->word';
    }

    return '$faker->word';
}

// Función para generar el código de la Factory
function generateFactoryCode(string $module, array $entityInfo, string $srcDir): string
{
    $className = $entityInfo['className'];
    $factoryName = $className . 'Factory';
    $namespace = "Tests\\factories\\$module";
    $entityNamespace = $entityInfo['namespace'] . '\\' . $className;

    $code = "<?php\n\n";
    $code .= "namespace $namespace;\n\n";
    $code .= "use Faker\\Factory;\n";
    $code .= "use $entityNamespace;\n";

    // Recolectar todos los VOs que se necesitan importar
    $vosToImport = [];
    $hasDateFields = false;

    foreach ($entityInfo['setters'] as $setter => $info) {
        // Detectar campos de fecha
        if (preg_match('/^setF_/i', $setter)) {
            $hasDateFields = true;
        }

        // Extraer tipos de los setters
        $type = $info['type'];
        if ($type && strpos($type, '|') !== false) {
            $types = explode('|', $type);
            foreach ($types as $t) {
                $t = trim($t);
                if ($t !== 'null' && $t !== 'string' && $t !== 'int' && $t !== 'bool' && $t !== 'array' && ctype_upper($t[0])) {
                    $vosToImport[$t] = true;
                }
            }
        } elseif ($type && ctype_upper($type[0]) && !in_array($type, ['string', 'int', 'bool', 'array'])) {
            $vosToImport[$type] = true;
        }
    }

    // Importar Value Objects de la entidad
    foreach ($entityInfo['valueObjects'] as $voName => $voNamespace) {
        $code .= "use $voNamespace;\n";
        // Marcar como ya importado
        unset($vosToImport[$voName]);
    }

    // Importar DatetimeLocal si hay campos de fecha y no está ya importado
    if ($hasDateFields && !isset($entityInfo['valueObjects']['DatetimeLocal']) && !isset($entityInfo['valueObjects']['DateTimeLocal'])) {
        $code .= "use src\\shared\\domain\\value_objects\\DatetimeLocal;\n";
        unset($vosToImport['DatetimeLocal']);
        unset($vosToImport['DateTimeLocal']);
    }

    // Importar VOs adicionales que se detectaron en los setters pero no están en la entidad
    // Intentar inferir el namespace basado en el módulo
    foreach ($vosToImport as $voName => $dummy) {
        // Primero intentar en el módulo actual
        $code .= "use src\\$module\\domain\\value_objects\\$voName;\n";
    }

    $code .= "\n";
    $code .= "/**\n";
    $code .= " * Factory para crear instancias de $className para tests\n";
    $code .= " * Generado automáticamente - puede ser modificado según necesidades\n";
    $code .= " */\n";
    $code .= "class $factoryName\n";
    $code .= "{\n";
    $code .= "    private int \$count = 1;\n\n";

    $code .= "    public function setCount(int \$count): void\n";
    $code .= "    {\n";
    $code .= "        \$this->count = \$count;\n";
    $code .= "    }\n\n";

    $code .= "    public function getCount(): int\n";
    $code .= "    {\n";
    $code .= "        return \$this->count;\n";
    $code .= "    }\n\n";

    // Método create simple (sin Faker, datos básicos)
    $idField = $entityInfo['idField'];
    $idType = $entityInfo['idType'] ?? 'int';
    $isVoId = $entityInfo['isVoId'] ?? false;
    $isFirstProperty = $entityInfo['isFirstProperty'] ?? false;

    // Determinar el setter correcto
    if ($isVoId) {
        $idSetter = 'set' . ucfirst($idField) . 'Vo';
        $idGetter = 'get' . ucfirst($idField) . 'Vo';
    } else {
        $idSetter = 'set' . ucfirst($idField);
        $idGetter = 'get' . ucfirst($idField);
    }

    $code .= "    /**\n";
    $code .= "     * Crea una instancia simple de $className con datos mínimos\n";
    $code .= "     * Útil para tests que no requieren datos complejos\n";
    $code .= "     */\n";

    // Determinar el tipo del parámetro
    if ($isFirstProperty && !in_array($idType, ['int', 'string', 'bool', 'float'])) {
        $paramType = 'string';
        $code .= "    public function createSimple(?string \$id = null): $className\n";
    } else {
        $paramType = 'int';
        $code .= "    public function createSimple(?int \$id = null): $className\n";
    }

    $code .= "    {\n";

    // Generar el valor del ID
    if ($paramType === 'string') {
        $code .= "        \$id = \$id ?? 'test_' . random_int(1000, 9999);\n";
    } else {
        $code .= "        \$id = \$id ?? (9900000 + random_int(1000, 9999));\n";
    }

    $code .= "        \$o$className = new $className();\n";

    // Setear el ID
    if ($isVoId || $isFirstProperty) {
        $code .= "        \$o$className->$idSetter(\$id);\n\n";
    } else {
        $code .= "        \$o$className->$idSetter(\$id);\n\n";
    }

    // Setters obligatorios (no nullable sin default)
    foreach ($entityInfo['setters'] as $setter => $info) {
        // Saltar el setter del ID (tanto snake_case como camelCase con Vo)
        // Normalizar: quitar 'set', guiones bajos y sufijo 'Vo'
        $idFieldNormalized = strtolower(str_replace('_', '', $idField));
        $setterNormalized = $setter;
        $setterNormalized = preg_replace('/^set/i', '', $setterNormalized); // Quitar 'set'
        $setterNormalized = preg_replace('/Vo$/i', '', $setterNormalized);   // Quitar sufijo 'Vo'
        $setterNormalized = strtolower(str_replace('_', '', $setterNormalized)); // Quitar guiones bajos

        if ($setterNormalized === $idFieldNormalized) {
            continue; // Saltar el ID
        }

        // Solo setters obligatorios en createSimple
        if (!$info['nullable'] && !$info['hasDefault']) {
            $type = $info['type'];
            $isUnion = $info['isUnionType'];

            // Extraer tipo base si es union
            if ($isUnion && strpos($type, '|') !== false) {
                $types = explode('|', $type);
                $type = trim($types[0]);
            }

            $fieldName = preg_replace('/^set/', '', $setter);
            $fieldName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $fieldName));

            // Detectar si es un campo de fecha
            $isDateField = preg_match('/^setF_/i', $setter);

            // Generar valor simple según tipo
            if (!$type) {
                $value = "'test_$fieldName'";
            } elseif ($type === 'int') {
                $value = '1';
            } elseif ($type === 'string') {
                $value = "'test_$fieldName'";
            } elseif ($type === 'bool') {
                $value = 'false';
            } elseif ($type === 'float') {
                $value = '1.0';
            } elseif ($type === 'array') {
                $value = '[]';
            } elseif (strlen($type) > 0 && ctype_upper($type[0])) {
                // Es un VO u otra clase
                if ($isDateField) {
                    // Campo de fecha, usar formato de fecha
                    $value = "new $type('2024-01-01')";
                } else {
                    $value = "new $type('test_$fieldName')";
                }
            } else {
                $value = "'test_$fieldName'";
            }

            $code .= "        \$o$className->$setter($value);\n";
        }
    }

    $code .= "\n        return \$o$className;\n";
    $code .= "    }\n\n";

    // Método create con Faker (datos realistas)
    $code .= "    /**\n";
    $code .= "     * Crea una instancia de $className con datos realistas usando Faker\n";

    if ($paramType === 'string') {
        $code .= "     * @param string|null \$id ID específico o null para generar uno aleatorio\n";
    } else {
        $code .= "     * @param int|null \$id ID específico o null para generar uno aleatorio\n";
    }

    $code .= "     * @return $className\n";
    $code .= "     */\n";

    if ($paramType === 'string') {
        $code .= "    public function create(?string \$id = null): $className\n";
    } else {
        $code .= "    public function create(?int \$id = null): $className\n";
    }

    $code .= "    {\n";
    $code .= "        \$faker = Factory::create('es_ES');\n";

    if ($paramType === 'string') {
        $code .= "        \$id = \$id ?? 'test_' . random_int(1000, 9999);\n\n";
    } else {
        $code .= "        \$id = \$id ?? (9900000 + random_int(1000, 9999));\n\n";
    }

    $code .= "        \$o$className = new $className();\n";
    $code .= "        \$o$className->$idSetter(\$id);\n\n";

    foreach ($entityInfo['setters'] as $setter => $info) {
        // Saltar el setter del ID (tanto snake_case como camelCase con Vo)
        // Normalizar: quitar 'set', guiones bajos y sufijo 'Vo'
        $idFieldNormalized = strtolower(str_replace('_', '', $idField));
        $setterNormalized = $setter;
        $setterNormalized = preg_replace('/^set/i', '', $setterNormalized); // Quitar 'set'
        $setterNormalized = preg_replace('/Vo$/i', '', $setterNormalized);   // Quitar sufijo 'Vo'
        $setterNormalized = strtolower(str_replace('_', '', $setterNormalized)); // Quitar guiones bajos

        if ($setterNormalized === $idFieldNormalized) {
            continue; // Saltar el ID
        }

        $fieldName = preg_replace('/^set/', '', $setter);

        // Si es un VO, envolver el valor
        $type = $info['type'];
        if (strpos($type, '|') !== false) {
            $types = explode('|', $type);
            foreach ($types as $t) {
                $t = trim($t);
                if ($t !== 'null' && $t !== 'string' && ctype_upper($t[0])) {
                    $type = $t;
                    break;
                }
            }
        }

        // Detectar si es un campo de fecha (setF_xxx) para usar DatetimeLocal aunque no esté en valueObjects
        $isDateField = preg_match('/^setF_/i', $setter);

        // Si es un VO, analizar su tipo de constructor
        $voType = null;
        if ($type && strlen($type) > 0 && ctype_upper($type[0]) && !in_array($type, ['string', 'int', 'bool', 'array'])) {
            $voType = analyzeValueObject(basename($type), $module, $srcDir);
        }

        $fakerValue = generateFakerValue($fieldName, $info['type'], $info['nullable'], $voType);

        if ($type && strlen($type) > 0 && $isDateField && ctype_upper($type[0])) {
            // Es un campo de fecha con un VO (probablemente DatetimeLocal o DateTimeLocal)
            $code .= "        \$o$className->$setter(new $type(\$faker->date()));\n";
        } elseif ($type && isset($entityInfo['valueObjects'][basename($type)])) {
            $code .= "        \$o$className->$setter(new $type($fakerValue));\n";
        } elseif ($type && strlen($type) > 0 && ctype_upper($type[0]) && !in_array($type, ['string', 'int', 'bool', 'array'])) {
            // Es otro VO que no está en valueObjects
            $code .= "        \$o$className->$setter(new $type($fakerValue));\n";
        } else {
            $code .= "        \$o$className->$setter($fakerValue);\n";
        }
    }

    $code .= "\n        return \$o$className;\n";
    $code .= "    }\n\n";

    // Método para crear múltiples
    $code .= "    /**\n";
    $code .= "     * Crea múltiples instancias de $className\n";
    $code .= "     * @param int \$count Número de instancias a crear\n";

    if ($paramType === 'string') {
        $code .= "     * @param string|null \$startId ID inicial base\n";
    } else {
        $code .= "     * @param int|null \$startId ID inicial (se incrementará)\n";
    }

    $code .= "     * @return array\n";
    $code .= "     */\n";

    if ($paramType === 'string') {
        $code .= "    public function createMany(int \$count, ?string \$startId = null): array\n";
        $code .= "    {\n";
        $code .= "        \$startId = \$startId ?? 'test_';\n";
        $code .= "        \$instances = [];\n\n";
        $code .= "        for (\$i = 0; \$i < \$count; \$i++) {\n";
        $code .= "            \$instances[] = \$this->create(\$startId . \$i);\n";
        $code .= "        }\n\n";
    } else {
        $code .= "    public function createMany(int \$count, ?int \$startId = null): array\n";
        $code .= "    {\n";
        $code .= "        \$startId = \$startId ?? (9900000 + random_int(1000, 9999));\n";
        $code .= "        \$instances = [];\n\n";
        $code .= "        for (\$i = 0; \$i < \$count; \$i++) {\n";
        $code .= "            \$instances[] = \$this->create(\$startId + \$i);\n";
        $code .= "        }\n\n";
    }

    $code .= "        return \$instances;\n";
    $code .= "    }\n";

    $code .= "}\n";

    return $code;
}

// Función para escribir archivo de factory
function writeFactoryFile(string $module, string $className, string $code, string $factoriesDir): bool
{
    $factoryPath = $factoriesDir . '/' . $module;

    if (!is_dir($factoryPath)) {
        mkdir($factoryPath, 0755, true);
    }

    $filename = $factoryPath . '/' . $className . 'Factory.php';

    // No sobrescribir archivos existentes por defecto
    if (file_exists($filename)) {
        echo "   ⚠️  Factory ya existe: $filename\n";
        return false;
    }

    file_put_contents($filename, $code);
    echo "   ✅ Creada: $filename\n";
    return true;
}

// Función para obtener todas las entidades de un módulo
function getModuleEntities(string $module, string $srcDir): array
{
    $entitiesDir = $srcDir . '/' . $module . '/domain/entity';
    $entities = [];

    if (!is_dir($entitiesDir)) {
        return $entities;
    }

    $files = glob($entitiesDir . '/*.php');

    foreach ($files as $file) {
        $className = basename($file, '.php');
        $entities[$className] = $file;
    }

    return $entities;
}

// Main
echo "=== Generador de Factories para Entidades ===\n\n";

// Verificar si se pasó un módulo como argumento
$targetModule = null;
if ($argc > 1) {
    $targetModule = $argv[1];
    echo "Módulo especificado: $targetModule\n";

    if (!is_dir($srcDir . '/' . $targetModule)) {
        echo "❌ Error: El módulo '$targetModule' no existe\n";
        exit(1);
    }

    $modules = [$targetModule];
} else {
    // Obtener todos los módulos
    $modules = [];
    $items = scandir($srcDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || $item === 'shared' || $item === 'utils_database') {
            continue;
        }
        $path = $srcDir . '/' . $item;
        if (is_dir($path) && is_dir($path . '/domain/entity')) {
            $modules[] = $item;
        }
    }
}

echo "\n";

$totalGenerated = 0;
$totalSkipped = 0;
$totalErrors = 0;

foreach ($modules as $module) {
    echo "--- Procesando módulo: $module ---\n";

    $entities = getModuleEntities($module, $srcDir);
    echo "Entidades encontradas: " . count($entities) . "\n";

    foreach ($entities as $className => $entityFile) {
        echo " 📝 Analizando: $className\n";

        $entityInfo = analyzeEntity($entityFile);

        if (empty($entityInfo['setters']) || !$entityInfo['idField']) {
            echo "   ⚠️  No se pudo analizar entidad (sin setters o sin ID field)\n";
            $totalSkipped++;
            continue;
        }

        $code = generateFactoryCode($module, $entityInfo, $srcDir);

        if (writeFactoryFile($module, $className, $code, $factoriesDir)) {
            $totalGenerated++;
        } else {
            $totalSkipped++;
        }
    }

    echo "\n";
}

echo "=== Resumen ===\n";
echo "Factories generadas: $totalGenerated\n";
echo "Factories omitidas (ya existen): $totalSkipped\n";
echo "Errores: $totalErrors\n";
echo "\nNOTA: Las factories pueden ser personalizadas según las necesidades específicas de cada test.\n";
