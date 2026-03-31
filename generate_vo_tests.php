<?php
/**
 * Script to generate unit tests for Value Objects
 *
 * This script scans all modules for value objects and generates PHPUnit tests
 * following the pattern established in the usuarios module tests.
 */

// Configuration
const SRC_PATH = __DIR__ . '/src';
const TEST_PATH = __DIR__ . '/tests/unit';
const DRY_RUN = false; // Set to true to see what would be generated without creating files

// Colors for console output
const COLOR_GREEN = "\033[0;32m";
const COLOR_YELLOW = "\033[1;33m";
const COLOR_BLUE = "\033[0;34m";
const COLOR_RED = "\033[0;31m";
const COLOR_RESET = "\033[0m";

function log_info(string $message): void {
    echo COLOR_BLUE . "[INFO] " . COLOR_RESET . $message . PHP_EOL;
}

function log_success(string $message): void {
    echo COLOR_GREEN . "[SUCCESS] " . COLOR_RESET . $message . PHP_EOL;
}

function log_warning(string $message): void {
    echo COLOR_YELLOW . "[WARNING] " . COLOR_RESET . $message . PHP_EOL;
}

function log_error(string $message): void {
    echo COLOR_RED . "[ERROR] " . COLOR_RESET . $message . PHP_EOL;
}

/**
 * Find all value object files in the project
 */
function findValueObjects(): array {
    $valueObjects = [];
    $modules = glob(SRC_PATH . '/*', GLOB_ONLYDIR);

    foreach ($modules as $module) {
        $moduleName = basename($module);
        $voPath = $module . '/domain/value_objects';

        if (!is_dir($voPath)) {
            continue;
        }

        $voFiles = glob($voPath . '/*.php');
        foreach ($voFiles as $voFile) {
            $valueObjects[] = [
                'module' => $moduleName,
                'file' => $voFile,
                'class' => basename($voFile, '.php'),
            ];
        }
    }

    return $valueObjects;
}

/**
 * Analyze a value object to determine its properties and methods
 */
function analyzeValueObject(string $filePath): array {
    $content = file_get_contents($filePath);
    $info = [
        'namespace' => '',
        'class' => '',
        'type' => 'string', // Default
        'hasValidation' => false,
        'hasEquals' => false,
        'hasToString' => false,
        'hasFromNullable' => false,
        'isNullable' => false,
        'validationRules' => [],
        'constants' => [],
        'hasConstantValidation' => false,
    ];

    // Extract namespace
    if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
        $info['namespace'] = $matches[1];
    }

    // Extract class name
    if (preg_match('/class\s+(\w+)/', $content, $matches)) {
        $info['class'] = $matches[1];
    }

    // Detect property type
    if (preg_match('/private\s+(\??)(\w+)\s+\$value;/', $content, $matches)) {
        $info['isNullable'] = $matches[1] === '?';
        $info['type'] = $matches[2];
    }

    // Extract public constants
    if (preg_match_all('/public\s+const\s+(\w+)\s*=\s*([^;]+);/', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $constName = $match[1];
            $constValue = trim($match[2]);
            // Remove comments
            $constValue = preg_replace('/\/\/.*$/', '', $constValue);
            $constValue = trim($constValue);
            // Remove quotes if string
            $constValue = trim($constValue, '\'"');

            $info['constants'][$constName] = $constValue;
        }
    }

    // Check for validation method
    $info['hasValidation'] = strpos($content, 'private function validate(') !== false;

    // Check if validation uses constants (enum-like pattern)
    if ($info['hasValidation'] && !empty($info['constants'])) {
        $info['hasConstantValidation'] =
            strpos($content, 'in_array($value') !== false ||
            strpos($content, '$allowedValues') !== false;
    }

    // Check for equals method
    $info['hasEquals'] = strpos($content, 'public function equals(') !== false;

    // Check for __toString method
    $info['hasToString'] = strpos($content, 'public function __toString()') !== false;

    // Check for fromNullable methods
    $info['hasFromNullable'] =
        strpos($content, 'fromNullableString') !== false ||
        strpos($content, 'fromNullableInt') !== false ||
        strpos($content, 'fromNullableFloat') !== false ||
        strpos($content, 'fromNullableBool') !== false;

    // Detect validation rules
    if (preg_match('/empty\(\$/', $content)) {
        $info['validationRules'][] = 'empty';
    }
    if (preg_match('/FILTER_VALIDATE_EMAIL/', $content)) {
        $info['validationRules'][] = 'email';
    }
    if (preg_match('/strlen\(/', $content) || preg_match('/mb_strlen\(/', $content)) {
        $info['validationRules'][] = 'length';
    }
    if ($info['hasConstantValidation']) {
        $info['validationRules'][] = 'constant';
    }

    return $info;
}

/**
 * Generate test class content for a value object
 */
function generateTestClass(array $voInfo, array $analysis): string {
    $module = $voInfo['module'];
    $className = $voInfo['class'];
    $namespace = "Tests\\unit\\{$module}\\domain\\value_objects";
    $voNamespace = "src\\{$module}\\domain\\value_objects\\{$className}";

    $testClass = "<?php\n\n";
    $testClass .= "namespace {$namespace};\n\n";
    $testClass .= "use {$voNamespace};\n";
    $testClass .= "use Tests\\myTest;\n\n";
    $testClass .= "class {$className}Test extends myTest\n";
    $testClass .= "{\n";

    // Generate test for valid creation
    $testClass .= generateValidCreationTest($className, $analysis);

    // Generate validation tests
    if ($analysis['hasValidation']) {
        foreach ($analysis['validationRules'] as $rule) {
            $testClass .= generateValidationTest($className, $rule, $analysis);
        }
    }

    // Generate equals tests
    if ($analysis['hasEquals']) {
        $testClass .= generateEqualsTests($className, $analysis);
    }

    // Generate toString test
    if ($analysis['hasToString']) {
        $testClass .= generateToStringTest($className, $analysis);
    }

    // Generate fromNullable tests
    if ($analysis['hasFromNullable']) {
        $testClass .= generateFromNullableTests($className, $analysis);
    }

    $testClass .= "}\n";

    return $testClass;
}

/**
 * Generate a test for valid value object creation
 */
function generateValidCreationTest(string $className, array $analysis): string {
    // Use first constant if available
    if (!empty($analysis['constants'])) {
        $firstConstName = array_key_first($analysis['constants']);
        $firstConstValue = $analysis['constants'][$firstConstName];
        $testValue = "{$className}::{$firstConstName}";
        $expectedValue = $firstConstValue;
    } else {
        $testValue = getTestValue($analysis['type']);
        $expectedValue = $testValue;

        // For string types, quote the value
        if ($analysis['type'] === 'string') {
            $testValue = "'{$testValue}'";
            $expectedValue = "'{$expectedValue}'";
        }
    }

    $camelCase = lcfirst($className);

    return <<<TEST
    public function test_create_valid_{$camelCase}()
    {
        \${$camelCase} = new {$className}({$testValue});
        \$this->assertEquals({$expectedValue}, \${$camelCase}->value());
    }


TEST;
}

/**
 * Generate validation test based on rule type
 */
function generateValidationTest(string $className, string $rule, array $analysis): string {
    $camelCase = lcfirst($className);

    switch ($rule) {
        case 'empty':
            $emptyValue = $analysis['type'] === 'string' ? "''" : '0';
            return <<<TEST
    public function test_empty_{$camelCase}_throws_exception()
    {
        \$this->expectException(\\InvalidArgumentException::class);
        new {$className}({$emptyValue});
    }


TEST;

        case 'email':
            return <<<TEST
    public function test_invalid_email_format_throws_exception()
    {
        \$this->expectException(\\InvalidArgumentException::class);
        new {$className}('invalid-email');
    }


TEST;

        case 'length':
            return <<<TEST
    public function test_invalid_length_throws_exception()
    {
        \$this->expectException(\\InvalidArgumentException::class);
        new {$className}(str_repeat('a', 1000)); // Assuming max length validation
    }


TEST;

        case 'constant':
            // Generate test for invalid constant value
            $invalidValue = $analysis['type'] === 'string' ? "'invalid_value'" : '999';
            return <<<TEST
    public function test_invalid_{$camelCase}_throws_exception()
    {
        \$this->expectException(\\InvalidArgumentException::class);
        new {$className}({$invalidValue});
    }


TEST;

        default:
            return '';
    }
}

/**
 * Generate equals method tests
 */
function generateEqualsTests(string $className, array $analysis): string {
    // Use constants if available
    if (!empty($analysis['constants'])) {
        $constNames = array_keys($analysis['constants']);
        $firstConstName = $constNames[0];
        $secondConstName = count($constNames) > 1 ? $constNames[1] : $constNames[0];

        $testValue1 = "{$className}::{$firstConstName}";
        $testValue2 = "{$className}::{$secondConstName}";
    } else {
        $testValue1 = getTestValue($analysis['type']);
        $testValue2 = getTestValue($analysis['type'], true);

        if ($analysis['type'] === 'string') {
            $testValue1 = "'{$testValue1}'";
            $testValue2 = "'{$testValue2}'";
        }
    }

    $camelCase = lcfirst($className);

    return <<<TEST
    public function test_equals_returns_true_for_same_{$camelCase}()
    {
        \${$camelCase}1 = new {$className}({$testValue1});
        \${$camelCase}2 = new {$className}({$testValue1});
        \$this->assertTrue(\${$camelCase}1->equals(\${$camelCase}2));
    }

    public function test_equals_returns_false_for_different_{$camelCase}()
    {
        \${$camelCase}1 = new {$className}({$testValue1});
        \${$camelCase}2 = new {$className}({$testValue2});
        \$this->assertFalse(\${$camelCase}1->equals(\${$camelCase}2));
    }


TEST;
}

/**
 * Generate __toString test
 */
function generateToStringTest(string $className, array $analysis): string {
    $testValue = getTestValue($analysis['type']);
    $expectedValue = $testValue;

    if ($analysis['type'] === 'string') {
        $testValue = "'{$testValue}'";
        $expectedValue = "'{$expectedValue}'";
    }

    $camelCase = lcfirst($className);

    return <<<TEST
    public function test_to_string_returns_{$camelCase}_value()
    {
        \${$camelCase} = new {$className}({$testValue});
        \$this->assertEquals({$expectedValue}, (string)\${$camelCase});
    }


TEST;
}

/**
 * Generate fromNullable tests
 */
function generateFromNullableTests(string $className, array $analysis): string {
    // Use first constant if available
    if (!empty($analysis['constants'])) {
        $firstConstName = array_key_first($analysis['constants']);
        $testValue = "{$className}::{$firstConstName}";
    } else {
        $testValue = getTestValue($analysis['type']);
        if ($analysis['type'] === 'string') {
            $testValue = "'{$testValue}'";
        }
    }

    if ($analysis['type'] === 'string') {
        $methodName = 'fromNullableString';
    } elseif ($analysis['type'] === 'int') {
        $methodName = 'fromNullableInt';
    } elseif ($analysis['type'] === 'float') {
        $methodName = 'fromNullableFloat';
    } elseif ($analysis['type'] === 'bool') {
        $methodName = 'fromNullableBool';
    } else {
        return '';
    }

    $camelCase = lcfirst($className);

    return <<<TEST
    public function test_{$methodName}_returns_instance_for_valid_value()
    {
        \${$camelCase} = {$className}::{$methodName}({$testValue});
        \$this->assertInstanceOf({$className}::class, \${$camelCase});
    }

    public function test_{$methodName}_returns_null_for_null_value()
    {
        \${$camelCase} = {$className}::{$methodName}(null);
        \$this->assertNull(\${$camelCase});
    }


TEST;
}

/**
 * Get a test value for a given type
 */
function getTestValue(string $type, bool $alternative = false): string {
    switch ($type) {
        case 'string':
            return $alternative ? 'alternative value' : 'test value';
        case 'int':
            return $alternative ? '456' : '123';
        case 'float':
            return $alternative ? '9.99' : '5.5';
        case 'bool':
            return $alternative ? 'false' : 'true';
        default:
            return $alternative ? 'alternative' : 'test';
    }
}

/**
 * Check if test file already exists
 */
function testFileExists(string $module, string $className): bool {
    $testPath = TEST_PATH . "/{$module}/domain/value_objects/{$className}Test.php";
    return file_exists($testPath);
}

/**
 * Create test file
 */
function createTestFile(string $module, string $className, string $content): bool {
    $testDir = TEST_PATH . "/{$module}/domain/value_objects";
    $testPath = $testDir . "/{$className}Test.php";

    if (!is_dir($testDir)) {
        if (!mkdir($testDir, 0755, true)) {
            log_error("Failed to create directory: {$testDir}");
            return false;
        }
    }

    if (DRY_RUN) {
        log_info("DRY RUN: Would create file: {$testPath}");
        echo "\n" . $content . "\n";
        return true;
    }

    if (file_put_contents($testPath, $content) === false) {
        log_error("Failed to write file: {$testPath}");
        return false;
    }

    return true;
}

// Main execution
echo "\n";
log_info("Starting Value Objects test generation...");
echo "\n";

$valueObjects = findValueObjects();
log_info("Found " . count($valueObjects) . " value objects");

$stats = [
    'total' => count($valueObjects),
    'skipped' => 0,
    'created' => 0,
    'failed' => 0,
];

foreach ($valueObjects as $vo) {
    $module = $vo['module'];
    $className = $vo['class'];

    // Skip if test already exists
    if (testFileExists($module, $className)) {
        log_warning("Test already exists for {$module}/{$className}, skipping...");
        $stats['skipped']++;
        continue;
    }

    log_info("Processing {$module}/{$className}...");

    // Analyze the value object
    $analysis = analyzeValueObject($vo['file']);

    // Generate test class
    $testContent = generateTestClass($vo, $analysis);

    // Create test file
    if (createTestFile($module, $className, $testContent)) {
        log_success("Created test for {$module}/{$className}");
        $stats['created']++;
    } else {
        log_error("Failed to create test for {$module}/{$className}");
        $stats['failed']++;
    }
}

echo "\n";
log_info("===== Summary =====");
log_info("Total value objects: {$stats['total']}");
log_success("Tests created: {$stats['created']}");
log_warning("Tests skipped: {$stats['skipped']}");
log_error("Tests failed: {$stats['failed']}");
echo "\n";
