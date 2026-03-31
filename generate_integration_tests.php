<?php
/**
 * Script para generar tests de integración de repositorios
 * Genera tests básicos para repositorios que implementan las operaciones CRUD estándar
 */

// Configuración
$baseDir = __DIR__;
$srcDir = $baseDir . '/src';
$testsDir = $baseDir . '/tests/integration';

// Módulos a procesar (excluir algunos que no tienen estructura estándar)
$excludedModules = ['usuarios', 'shared', 'utils_database', 'pasarela', 'certificados', 'configuracion', 'procesos'];

// Función para obtener todos los módulos
function getModules(string $srcDir, array $excluded): array
{
    $modules = [];
    $items = scandir($srcDir);

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $srcDir . '/' . $item;
        if (is_dir($path) && !in_array($item, $excluded)) {
            // Verificar que tiene directorio infrastructure/persistence/postgresql
            if (is_dir($path . '/infrastructure/persistence/postgresql')) {
                $modules[] = $item;
            }
        }
    }

    return $modules;
}

// Función para obtener todos los repositorios de un módulo
function getRepositories(string $module, string $srcDir): array
{
    $repositoriesDir = $srcDir . '/' . $module . '/infrastructure/persistence/postgresql';
    $repositories = [];

    if (!is_dir($repositoriesDir)) {
        return $repositories;
    }

    $files = glob($repositoriesDir . '/Pg*Repository.php');

    foreach ($files as $file) {
        $className = basename($file, '.php');
        $repositories[] = $className;
    }

    return $repositories;
}

// Función para obtener la interfaz del repositorio
function getRepositoryInterface(string $module, string $repository, string $srcDir): ?string
{
    $repositoryFile = $srcDir . '/' . $module . '/infrastructure/persistence/postgresql/' . $repository . '.php';

    if (!file_exists($repositoryFile)) {
        return null;
    }

    $content = file_get_contents($repositoryFile);

    // Buscar implements Interface
    if (preg_match('/implements\s+([A-Za-z0-9_]+Interface)/', $content, $matches)) {
        return $matches[1];
    }

    return null;
}

// Función para obtener la entidad del repositorio
function getRepositoryEntity(string $module, string $repository, string $srcDir): ?string
{
    $repositoryFile = $srcDir . '/' . $module . '/infrastructure/persistence/postgresql/' . $repository . '.php';

    if (!file_exists($repositoryFile)) {
        return null;
    }

    $content = file_get_contents($repositoryFile);

    // Buscar el método findById para determinar qué entidad retorna
    if (preg_match('/function\s+findById[^:]*:\s*\?([A-Za-z0-9_]+)/', $content, $matches)) {
        return $matches[1];
    }

    // Alternativa: buscar en el método Guardar
    if (preg_match('/function\s+Guardar\s*\(\s*([A-Za-z0-9_]+)\s+/', $content, $matches)) {
        return $matches[1];
    }

    return null;
}

// Función para analizar métodos del repositorio
function analyzeRepositoryMethods(string $module, string $repository, string $srcDir): array
{
    $repositoryFile = $srcDir . '/' . $module . '/infrastructure/persistence/postgresql/' . $repository . '.php';

    if (!file_exists($repositoryFile)) {
        return [];
    }

    $content = file_get_contents($repositoryFile);
    $methods = [];

    // Métodos comunes a buscar
    $commonMethods = [
        'Guardar' => 'guardar',
        'Eliminar' => 'eliminar',
        'findById' => 'find_by_id',
        'datosById' => 'datos_by_id',
        'getNewId' => 'get_new_id',
    ];

    foreach ($commonMethods as $methodName => $testName) {
        if (preg_match('/function\s+' . preg_quote($methodName, '/') . '\s*\(/', $content)) {
            $methods[$testName] = $methodName;
        }
    }

    // Buscar métodos get con prefijo get y que retornen array
    if (preg_match_all('/public\s+function\s+(get[A-Z][a-zA-Z0-9_]*)\s*\([^)]*\)\s*:\s*array/', $content, $matches)) {
        foreach ($matches[1] as $methodName) {
            $testName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $methodName));
            $methods[$testName] = $methodName;
        }
    }

    return $methods;
}

// Función para generar el código del test
function generateTestCode(string $module, string $repository, string $interface, string $entity, array $methods): string
{
    $namespace = "Tests\\integration\\" . $module . "\\infrastructure\\repositories";
    $entityNamespace = "src\\" . $module . "\\domain\\entity\\" . $entity;
    $interfaceNamespace = "src\\" . $module . "\\domain\\contracts\\" . $interface;

    $className = $repository . "Test";
    $varName = lcfirst(str_replace('Repository', '', str_replace('Pg', '', $repository)));

    $code = "<?php\n\n";
    $code .= "namespace $namespace;\n\n";
    $code .= "use $interfaceNamespace;\n";
    $code .= "use $entityNamespace;\n";
    $code .= "use Tests\\myTest;\n\n";
    $code .= "class $className extends myTest\n";
    $code .= "{\n";
    $code .= "    private $interface \$repository;\n\n";
    $code .= "    public function setUp(): void\n";
    $code .= "    {\n";
    $code .= "        parent::setUp();\n";
    $code .= "        \$this->repository = \$GLOBALS['container']->get($interface::class);\n";
    $code .= "    }\n\n";

    // Generar tests básicos
    if (isset($methods['guardar']) && isset($methods['find_by_id']) && isset($methods['eliminar'])) {
        $code .= "    public function test_guardar_nuevo_$varName()\n";
        $code .= "    {\n";
        $code .= "        \$this->markTestIncomplete('Este test necesita ser implementado con datos específicos del $entity');\n";
        $code .= "        \n";
        $code .= "        // TODO: Crear una instancia de $entity con datos válidos\n";
        $code .= "        // \$o".$entity." = new $entity();\n";
        $code .= "        // \$o".$entity."->set...();\n";
        $code .= "        \n";
        $code .= "        // \$result = \$this->repository->{$methods['guardar']}(\$o$entity);\n";
        $code .= "        // \$this->assertTrue(\$result);\n";
        $code .= "        \n";
        $code .= "        // Verificar que se guardó\n";
        $code .= "        // \$o" . $entity . "Guardado = \$this->repository->{$methods['find_by_id']}(\$id);\n";
        $code .= "        // \$this->assertNotNull(\$o" . $entity . "Guardado);\n";
        $code .= "        \n";
        $code .= "        // Limpiar\n";
        $code .= "        // \$this->repository->{$methods['eliminar']}(\$o" . $entity . "Guardado);\n";
        $code .= "    }\n\n";

        $code .= "    public function test_actualizar_$varName" . "_existente()\n";
        $code .= "    {\n";
        $code .= "        \$this->markTestIncomplete('Este test necesita ser implementado con datos específicos del $entity');\n";
        $code .= "        \n";
        $code .= "        // TODO: Implementar test de actualización\n";
        $code .= "    }\n\n";
    }

    if (isset($methods['find_by_id'])) {
        $code .= "    public function test_find_by_id_existente()\n";
        $code .= "    {\n";
        $code .= "        \$this->markTestIncomplete('Este test necesita ser implementado con un ID válido existente');\n";
        $code .= "        \n";
        $code .= "        // TODO: Usar un ID que exista en la base de datos de test\n";
        $code .= "        // \$id = 1;\n";
        $code .= "        // \$o$entity = \$this->repository->{$methods['find_by_id']}(\$id);\n";
        $code .= "        // \$this->assertNotNull(\$o$entity);\n";
        $code .= "        // \$this->assertInstanceOf($entity::class, \$o$entity);\n";
        $code .= "    }\n\n";

        $code .= "    public function test_find_by_id_no_existente()\n";
        $code .= "    {\n";
        $code .= "        \$id_inexistente = 99999999;\n";
        $code .= "        \$o$entity = \$this->repository->{$methods['find_by_id']}(\$id_inexistente);\n";
        $code .= "        \n";
        $code .= "        \$this->assertNull(\$o$entity);\n";
        $code .= "    }\n\n";
    }

    if (isset($methods['datos_by_id'])) {
        $code .= "    public function test_datos_by_id_existente()\n";
        $code .= "    {\n";
        $code .= "        \$this->markTestIncomplete('Este test necesita ser implementado con un ID válido existente');\n";
        $code .= "        \n";
        $code .= "        // TODO: Usar un ID que exista en la base de datos de test\n";
        $code .= "        // \$id = 1;\n";
        $code .= "        // \$aDatos = \$this->repository->{$methods['datos_by_id']}(\$id);\n";
        $code .= "        // \$this->assertIsArray(\$aDatos);\n";
        $code .= "    }\n\n";

        $code .= "    public function test_datos_by_id_no_existente()\n";
        $code .= "    {\n";
        $code .= "        \$id_inexistente = 99999999;\n";
        $code .= "        \$aDatos = \$this->repository->{$methods['datos_by_id']}(\$id_inexistente);\n";
        $code .= "        \n";
        $code .= "        \$this->assertFalse(\$aDatos);\n";
        $code .= "    }\n\n";
    }

    if (isset($methods['eliminar'])) {
        $code .= "    public function test_eliminar_$varName()\n";
        $code .= "    {\n";
        $code .= "        \$this->markTestIncomplete('Este test necesita ser implementado');\n";
        $code .= "        \n";
        $code .= "        // TODO: Crear, guardar, eliminar y verificar\n";
        $code .= "    }\n\n";
    }

    // Tests para métodos get que retornan arrays
    foreach ($methods as $testName => $methodName) {
        if (strpos($testName, 'get_') === 0 && !in_array($testName, ['get_new_id'])) {
            $code .= "    public function test_{$testName}_sin_filtros()\n";
            $code .= "    {\n";
            $code .= "        \$result = \$this->repository->{$methodName}();\n";
            $code .= "        \n";
            $code .= "        \$this->assertIsArray(\$result);\n";
            $code .= "        // TODO: Añadir más aserciones según la estructura esperada\n";
            $code .= "    }\n\n";
        }
    }

    if (isset($methods['get_new_id'])) {
        $code .= "    public function test_get_new_id()\n";
        $code .= "    {\n";
        $code .= "        \$newId = \$this->repository->{$methods['get_new_id']}();\n";
        $code .= "        \n";
        $code .= "        \$this->assertNotNull(\$newId);\n";
        $code .= "        \$this->assertIsNumeric(\$newId);\n";
        $code .= "    }\n\n";
    }

    $code .= "}\n";

    return $code;
}

// Función para escribir el archivo de test
function writeTestFile(string $module, string $repository, string $code, string $testsDir): bool
{
    $testPath = $testsDir . '/' . $module . '/infrastructure/persistence/postgresql';

    // Crear directorios si no existen
    if (!is_dir($testPath)) {
        mkdir($testPath, 0755, true);
    }

    $filename = $testPath . '/' . $repository . 'Test.php';

    // No sobrescribir archivos existentes
    if (file_exists($filename)) {
        echo "⚠️  Test ya existe: $filename\n";
        return false;
    }

    file_put_contents($filename, $code);
    echo "✅ Creado: $filename\n";
    return true;
}

// Main
echo "=== Generador de Tests de Integración de Repositorios ===\n\n";

// Verificar si se pasó un módulo como argumento
$targetModule = null;
if ($argc > 1) {
    $targetModule = $argv[1];
    echo "Módulo especificado: $targetModule\n";

    // Verificar que el módulo existe
    if (!is_dir($srcDir . '/' . $targetModule)) {
        echo "❌ Error: El módulo '$targetModule' no existe\n";
        exit(1);
    }

    if (in_array($targetModule, $excludedModules)) {
        echo "⚠️  Advertencia: El módulo '$targetModule' está en la lista de excluidos\n";
    }

    $modules = [$targetModule];
} else {
    $modules = getModules($srcDir, $excludedModules);
    echo "Módulos encontrados: " . count($modules) . "\n";
    echo "Módulos: " . implode(', ', $modules) . "\n";
}

echo "\n";

$totalGenerated = 0;
$totalSkipped = 0;
$totalErrors = 0;

foreach ($modules as $module) {
    echo "\n--- Procesando módulo: $module ---\n";

    $repositories = getRepositories($module, $srcDir);
    echo "Repositorios encontrados: " . count($repositories) . "\n";

    foreach ($repositories as $repository) {
        $interface = getRepositoryInterface($module, $repository, $srcDir);
        $entity = getRepositoryEntity($module, $repository, $srcDir);

        if (!$interface) {
            echo "⚠️  No se encontró interfaz para: $repository\n";
            $totalSkipped++;
            continue;
        }

        if (!$entity) {
            echo "⚠️  No se encontró entidad para: $repository\n";
            $totalSkipped++;
            continue;
        }

        $methods = analyzeRepositoryMethods($module, $repository, $srcDir);

        if (empty($methods)) {
            echo "⚠️  No se encontraron métodos para: $repository\n";
            $totalSkipped++;
            continue;
        }

        $code = generateTestCode($module, $repository, $interface, $entity, $methods);

        if (writeTestFile($module, $repository, $code, $testsDir)) {
            $totalGenerated++;
        } else {
            $totalSkipped++;
        }
    }
}

echo "\n\n=== Resumen ===\n";
echo "Tests generados: $totalGenerated\n";
echo "Tests omitidos (ya existen): $totalSkipped\n";
echo "Errores: $totalErrors\n";
echo "\nNOTA: Los tests generados están marcados como 'incomplete' y necesitan ser completados con datos específicos.\n";
