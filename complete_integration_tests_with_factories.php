<?php
/**
 * Script para completar tests de integración usando Factories
 * Actualiza los tests para usar las factories generadas
 */

// Configuración
$baseDir = __DIR__;
$srcDir = $baseDir . '/src';
$testsDir = $baseDir . '/tests/integration';
$factoriesDir = $baseDir . '/tests/factories';

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

// Función para completar un test file usando factory
function completeTestFileWithFactory(string $testFile, string $module, string $srcDir, string $factoriesDir): bool
{
    if (!file_exists($testFile)) {
        return false;
    }

    $content = file_get_contents($testFile);

    // Verificar si ya tiene markTestIncomplete - si no, ya está completo
    if (strpos($content, 'markTestIncomplete') === false) {
        echo "   ℹ️  Test ya completo: " . basename($testFile) . "\n";
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
    $factoryFile = $factoriesDir . '/' . $module . '/' . $entityName . 'Factory.php';

    // Verificar si existe la factory
    if (!file_exists($factoryFile)) {
        echo "   ⚠️  No existe factory para $entityName. Ejecuta primero: php generate_entity_factories.php $module\n";
        return false;
    }

    $idField = getEntityIdField($entityFile);
    if (!$idField) {
        echo "   ⚠️  No se pudo detectar ID field\n";
        return false;
    }

    $factoryNamespace = "Tests\\factories\\$module\\{$entityName}Factory";
    $varName = lcfirst($entityName);

    // Agregar import de la factory si no existe
    if (strpos($content, $factoryNamespace) === false) {
        $useStatements = "use Tests\\myTest;\n";
        $useStatements .= "use $factoryNamespace;\n";
        $content = str_replace("use Tests\\myTest;\n", $useStatements, $content);
    }

    // Agregar propiedad factory en la clase si no existe
    if (strpos($content, 'private ' . $entityName . 'Factory') === false) {
        $pattern = '/(private\s+\w+RepositoryInterface\s+\$repository;)/';
        $replacement = '$1' . "\n    private {$entityName}Factory \$factory;";
        $content = preg_replace($pattern, $replacement, $content);
    }

    // Inicializar factory en setUp si no existe
    if (strpos($content, '$this->factory = new') === false) {
        $pattern = '/(public function setUp\(\): void\s*\{[^}]+\$this->repository[^;]+;)/';
        $replacement = '$1' . "\n        \$this->factory = new {$entityName}Factory();";
        $content = preg_replace($pattern, $replacement, $content);
    }

    // Reemplazar test de guardar
    $pattern = '/(public function test_guardar_nuevo_[a-zA-Z0-9_]+\(\)\s*\{)\s*\$this->markTestIncomplete[^}]+(\})/s';
    $replacement = '$1
        // Crear instancia usando factory
        $o' . $entityName . ' = $this->factory->createSimple();
        $id = $o' . $entityName . '->get' . ucfirst($idField) . '();

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
        // Crear y guardar instancia usando factory
        $o' . $entityName . ' = $this->factory->createSimple();
        $id = $o' . $entityName . '->get' . ucfirst($idField) . '();
        $this->repository->Guardar($o' . $entityName . ');

        // Crear otra instancia con datos diferentes para actualizar
        $o' . $entityName . 'Updated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($o' . $entityName . 'Updated);
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
        // Crear y guardar instancia usando factory
        $o' . $entityName . ' = $this->factory->createSimple();
        $id = $o' . $entityName . '->get' . ucfirst($idField) . '();
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
    // El campo en la BD está en minúsculas
    $idFieldLower = strtolower($idField);
    $pattern = '/(public function test_datos_by_id_existente\(\)\s*\{)\s*\$this->markTestIncomplete[^}]+(\})/s';
    $replacement = '$1
        // Crear y guardar instancia usando factory
        $o' . $entityName . ' = $this->factory->createSimple();
        $id = $o' . $entityName . '->get' . ucfirst($idField) . '();
        $this->repository->Guardar($o' . $entityName . ');

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey(\'' . $idFieldLower . '\', $aDatos);
        $this->assertEquals($id, $aDatos[\'' . $idFieldLower . '\']);

        // Limpiar
        $o' . $entityName . 'Paraborrar = $this->repository->findById($id);
        $this->repository->Eliminar($o' . $entityName . 'Paraborrar);
    $2';

    $content = preg_replace($pattern, $replacement, $content);

    // Reemplazar test eliminar
    $pattern = '/(public function test_eliminar_[a-zA-Z0-9_]+\(\)\s*\{)\s*\$this->markTestIncomplete[^}]+(\})/s';
    $replacement = '$1
        // Crear y guardar instancia usando factory
        $o' . $entityName . ' = $this->factory->createSimple();
        $id = $o' . $entityName . '->get' . ucfirst($idField) . '();
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
    echo "   ✅ Test completado con factory: " . basename($testFile) . "\n";
    return true;
}

// Main
echo "=== Completador de Tests de Integración usando Factories ===\n\n";

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

    $factoryModuleDir = $factoriesDir . '/' . $targetModule;
    if (!is_dir($factoryModuleDir)) {
        echo "⚠️  Advertencia: No existen factories para '$targetModule'\n";
        echo "Ejecuta primero: php generate_entity_factories.php $targetModule\n\n";
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

        if (completeTestFileWithFactory($testFile, $module, $srcDir, $factoriesDir)) {
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
echo "\nNOTA: Los tests ahora usan factories reutilizables.\n";
echo "Para crear factories faltantes ejecuta: php generate_entity_factories.php [modulo]\n";
