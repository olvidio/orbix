<?php
/**
 * Script para completar tests de integración incompletos
 * Analiza las entidades y genera código de test funcional con datos de prueba
 */

// Configuración
$baseDir = __DIR__;
$srcDir = $baseDir . '/src';
$testsDir = $baseDir . '/tests/integration';

// Función para analizar los setters de una entidad
function analyzeEntitySetters(string $entityFile): array
{
    if (!file_exists($entityFile)) {
        return [];
    }

    $content = file_get_contents($entityFile);
    $setters = [];

    // Buscar todos los setters públicos
    preg_match_all('/public\s+function\s+(set[A-Z][a-zA-Z0-9_]*)\s*\(([^)]+)\)/', $content, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $methodName = $match[1];
        $params = $match[2];

        // Analizar el tipo del parámetro
        $type = null;
        $nullable = false;
        $hasDefault = false;

        // Buscar tipo y si es nullable
        if (preg_match('/(\?)?([A-Za-z0-9_\\\\|]+)\s+\$/', $params, $typeMatch)) {
            $nullable = !empty($typeMatch[1]);
            $type = $typeMatch[2];
        }

        // Ver si tiene valor por defecto
        if (strpos($params, '=') !== false) {
            $hasDefault = true;
        }

        $setters[$methodName] = [
            'type' => $type,
            'nullable' => $nullable,
            'hasDefault' => $hasDefault,
            'params' => $params
        ];
    }

    return $setters;
}

// Función para obtener el ID field de una entidad
function getEntityIdField(string $entityFile): ?string
{
    if (!file_exists($entityFile)) {
        return null;
    }

    $content = file_get_contents($entityFile);

    // Buscar getId_xxx
    if (preg_match('/function\s+get(Id_[a-z_]+)\s*\(\)/', $content, $match)) {
        return $match[1];
    }

    return null;
}

// Función para generar valor de prueba según el tipo
function generateTestValue(string $type, bool $nullable, string $fieldName): string
{
    // Si contiene |, es un union type, tomamos el primer tipo que no sea null
    if (strpos($type, '|') !== false) {
        $types = explode('|', $type);
        foreach ($types as $t) {
            if (trim($t) !== 'null') {
                $type = trim($t);
                break;
            }
        }
    }

    // Tipos básicos
    if ($type === 'int') {
        if (strpos($fieldName, 'orden') !== false) {
            return '1';
        }
        return 'random_int(1, 100)';
    }

    if ($type === 'string') {
        // Generar nombre basado en el campo
        $baseName = preg_replace('/^set/', '', $fieldName);
        $baseName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $baseName));
        return "'test_$baseName' . randdom_int(1000, 9999)";
    }

    if ($type === 'bool') {
        return 'true';
    }

    if ($type === 'float') {
        return '10.5';
    }

    if ($type === 'array') {
        return '[]';
    }

    // Value Objects - intentar instanciar
    if (strpos($type, '\\') !== false || ctype_upper($type[0])) {
        // Es una clase, probablemente un Value Object
        $voName = basename(str_replace('\\', '/', $type));
        $baseName = preg_replace('/^set/', '', $fieldName);
        $baseName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $baseName));

        return "new $voName('test_$baseName' . random_int(1000, 9999))";
    }

    return "'test_value'";
}

// Función para completar un test file
function completeTestFile(string $testFile, string $module, string $srcDir): bool
{
    if (!file_exists($testFile)) {
        return false;
    }

    $content = file_get_contents($testFile);

    // Verificar si ya tiene markTestIncomplete - si no, ya está completo
    if (strpos($content, 'markTestIncomplete') === false) {
        echo "   ℹ️  Test ya completo: $testFile\n";
        return false;
    }

    // Extraer el nombre de la entidad del test
    preg_match('/use src\\\\' . preg_quote($module, '/') . '\\\\domain\\\\entity\\\\([A-Za-z0-9_]+);/', $content, $matches);
    if (!$matches) {
        echo "   ⚠️  No se pudo extraer nombre de entidad\n";
        return false;
    }

    $entityName = $matches[1];
    $entityFile = $srcDir . '/' . $module . '/domain/entity/' . $entityName . '.php';

    // Analizar la entidad
    $setters = analyzeEntitySetters($entityFile);
    $idField = getEntityIdField($entityFile);

    if (empty($setters) || !$idField) {
        echo "   ⚠️  No se pudieron analizar setters o ID field\n";
        return false;
    }

    // Extraer namespace de Value Objects si existen
    preg_match_all('/use src\\\\' . preg_quote($module, '/') . '\\\\domain\\\\value_objects\\\\([A-Za-z0-9_]+);/', $content, $voMatches);
    $existingVOs = $voMatches[1] ?? [];

    // Detectar VOs que faltan
    $missingVOs = [];
    foreach ($setters as $setterName => $info) {
        $type = $info['type'];
        if ($type && strpos($type, '|') !== false) {
            $types = explode('|', $type);
            foreach ($types as $t) {
                $t = trim($t);
                if ($t !== 'null' && $t !== 'string' && $t !== 'int' && $t !== 'bool' && ctype_upper($t[0])) {
                    if (!in_array($t, $existingVOs) && !in_array($t, $missingVOs)) {
                        $missingVOs[] = $t;
                    }
                }
            }
        } elseif ($type && ctype_upper($type[0]) && !in_array($type, ['string', 'int', 'bool', 'array'])) {
            if (!in_array($type, $existingVOs) && !in_array($type, $missingVOs)) {
                $missingVOs[] = $type;
            }
        }
    }

    // Agregar imports de VOs faltantes
    if (!empty($missingVOs)) {
        $useStatements = "\nuse Tests\\myTest;\n";
        foreach ($missingVOs as $vo) {
            $useStatements .= "use src\\$module\\domain\\value_objects\\$vo;\n";
        }

        $content = str_replace("\nuse Tests\\myTest;\n", $useStatements, $content);
    }

    // Generar código para crear instancia
    $createInstanceCode = "\$o$entityName = new $entityName();\n";
    $createInstanceCode .= "        \$id = 9900000 + randdom_int(1000, 9999);\n";
    $createInstanceCode .= "        \$o{$entityName}->set" . ucfirst($idField) . "(\$id);\n";

    foreach ($setters as $setterName => $info) {
        // Saltar el setter del ID
        if (stripos($setterName, $idField) !== false) {
            continue;
        }

        $value = generateTestValue($info['type'], $info['nullable'], $setterName);
        $createInstanceCode .= "        \$o{$entityName}->{$setterName}($value);\n";
    }

    // Reemplazar el test de guardar
    $pattern = '/(public function test_guardar_nuevo_[a-zA-Z0-9_]+\(\)\s*\{)\s*\$this->markTestIncomplete[^}]+(\})/s';
    $replacement = '$1
        // Crear instancia de ' . $entityName . '
        ' . $createInstanceCode . '
        // Guardar
        $result = $this->repository->Guardar($o' . $entityName . ');
        $this->assertTrue($result);

        // Verificar que se guardó
        $o' . $entityName . 'Guardado = $this->repository->findById($id);
        $this->assertNotNull($o' . $entityName . 'Guardado);
        $this->assertEquals($id, $o' . $entityName . 'Guardado->get' . ucfirst($idField) . '());

        // Limpiar
        $this->repository->Eliminar($o' . $entityName . 'Guardado);
    $2';

    $content = preg_replace($pattern, $replacement, $content);

    // Reemplazar test de actualizar
    $pattern = '/(public function test_actualizar_[a-zA-Z0-9_]+_existente\(\)\s*\{)\s*\$this->markTestIncomplete[^}]+(\})/s';
    $replacement = '$1
        // Crear y guardar instancia
        ' . $createInstanceCode . '
        $this->repository->Guardar($o' . $entityName . ');

        // Modificar un campo
        $firstSetter = "' . array_key_first($setters) . '";
        if (method_exists($o' . $entityName . ', $firstSetter)) {
            $o' . $entityName . '->$firstSetter(' . generateTestValue($setters[array_key_first($setters)]['type'], $setters[array_key_first($setters)]['nullable'], array_key_first($setters)) . ');
        }

        // Actualizar
        $result = $this->repository->Guardar($o' . $entityName . ');
        $this->assertTrue($result);

        // Verificar actualización
        $o' . $entityName . 'Actualizado = $this->repository->findById($id);
        $this->assertNotNull($o' . $entityName . 'Actualizado);

        // Limpiar
        $this->repository->Eliminar($o' . $entityName . 'Actualizado);
    $2';

    $content = preg_replace($pattern, $replacement, $content);

    // Reemplazar test find_by_id_existente
    $pattern = '/(public function test_find_by_id_existente\(\)\s*\{)\s*\$this->markTestIncomplete[^}]+(\})/s';
    $replacement = '$1
        // Crear y guardar instancia
        ' . $createInstanceCode . '
        $this->repository->Guardar($o' . $entityName . ');

        // Buscar por ID
        $o' . $entityName . 'Encontrado = $this->repository->findById($id);
        $this->assertNotNull($o' . $entityName . 'Encontrado);
        $this->assertInstanceOf(' . $entityName . '::class, $o' . $entityName . 'Encontrado);
        $this->assertEquals($id, $o' . $entityName . 'Encontrado->get' . ucfirst($idField) . '());

        // Limpiar
        $this->repository->Eliminar($o' . $entityName . 'Encontrado);
    $2';

    $content = preg_replace($pattern, $replacement, $content);

    // Reemplazar test datos_by_id_existente
    $pattern = '/(public function test_datos_by_id_existente\(\)\s*\{)\s*\$this->markTestIncomplete[^}]+(\})/s';
    $replacement = '$1
        // Crear y guardar instancia
        ' . $createInstanceCode . '
        $this->repository->Guardar($o' . $entityName . ');

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey(\'' . $idField . '\', $aDatos);
        $this->assertEquals($id, $aDatos[\'' . $idField . '\']);

        // Limpiar
        $o' . $entityName . 'Para borrar = $this->repository->findById($id);
        $this->repository->Eliminar($o' . $entityName . 'Paraborrar);
    $2';

    $content = preg_replace($pattern, $replacement, $content);

    // Reemplazar test eliminar
    $pattern = '/(public function test_eliminar_[a-zA-Z0-9_]+\(\)\s*\{)\s*\$this->markTestIncomplete[^}]+(\})/s';
    $replacement = '$1
        // Crear y guardar instancia
        ' . $createInstanceCode . '
        $this->repository->Guardar($o' . $entityName . ');

        // Verificar que existe
        $o' . $entityName . 'Existe = $this->repository->findById($id);
        $this->assertNotNull($o' . $entityName . 'Existe);

        // Eliminar
        $result = $this->repository->Eliminar($o' . $entityName . 'Existe);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $o' . $entityName . 'Eliminado = $this->repository->findById($id);
        $this->assertNull($o' . $entityName . 'Eliminado);
    $2';

    $content = preg_replace($pattern, $replacement, $content);

    // Escribir el archivo actualizado
    file_put_contents($testFile, $content);
    echo "   ✅ Test completado: $testFile\n";
    return true;
}

// Main
echo "=== Completador de Tests de Integración ===\n\n";

// Verificar si se pasó un módulo como argumento
$targetModule = null;
if ($argc > 1) {
    $targetModule = $argv[1];
    echo "Módulo especificado: $targetModule\n\n";

    $testModuleDir = $testsDir . '/' . $targetModule . '/infrastructure/persistence/postgresql';
    if (!is_dir($testModuleDir)) {
        echo "❌ Error: No existe directorio de tests para '$targetModule'\n";
        exit(1);
    }

    $modules = [$targetModule];
} else {
    // Obtener todos los módulos con tests
    $modules = [];
    $items = scandir($testsDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $testsDir . '/' . $item;
        if (is_dir($path)) {
            $modules[] = $item;
        }
    }
}

$totalCompleted = 0;
$totalSkipped = 0;

foreach ($modules as $module) {
    $testModuleDir = $testsDir . '/' . $module . '/infrastructure/persistence/postgresql';

    if (!is_dir($testModuleDir)) {
        continue;
    }

    echo "--- Procesando módulo: $module ---\n";

    $testFiles = glob($testModuleDir . '/*Test.php');

    foreach ($testFiles as $testFile) {
        $testName = basename($testFile);
        echo " 📝 Procesando: $testName\n";

        if (completeTestFile($testFile, $module, $srcDir)) {
            $totalCompleted++;
        } else {
            $totalSkipped++;
        }
    }

    echo "\n";
}

echo "=== Resumen ===\n";
echo "Tests completados: $totalCompleted\n";
echo "Tests omitidos: $totalSkipped\n";
