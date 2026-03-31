<?php

/**
 * Script para generar archivo de configuración de valores de test para un módulo
 *
 * Uso: php generate_test_values_config.php <modulo>
 * Ejemplo: php generate_test_values_config.php ubis
 */

if ($argc < 2) {
    echo "Uso: php generate_test_values_config.php <modulo>\n";
    echo "Ejemplo: php generate_test_values_config.php ubis\n";
    exit(1);
}

$modulo = $argv[1];
$testDir = "tests/unit/{$modulo}/domain/entity";
$configFile = "{$testDir}/test_values_config.php";
$voDir = "src/{$modulo}/domain/value_objects";

// Crear directorio de tests si no existe
if (!is_dir($testDir)) {
    mkdir($testDir, 0755, true);
    echo "✓ Creado directorio: {$testDir}\n";
}

// Verificar si ya existe el archivo
if (file_exists($configFile)) {
    echo "⚠ Ya existe el archivo: {$configFile}\n";
    echo "¿Deseas regenerarlo? Esto sobrescribirá el archivo actual. (s/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) !== 's') {
        echo "Operación cancelada.\n";
        exit(0);
    }
    fclose($handle);
}

// Analizar value objects del módulo
$valueObjects = [];
if (is_dir($voDir)) {
    $voFiles = glob("{$voDir}/*.php");
    foreach ($voFiles as $voFile) {
        $voName = basename($voFile, '.php');
        $voContent = file_get_contents($voFile);

        // Detectar tipo del value object analizando el constructor o propiedad
        $suggestedValue = suggestValueForVO($voName, $voContent);
        $valueObjects[$voName] = $suggestedValue;
    }
}

// Analizar value objects de otros módulos que se usan en las entidades
$entityDir = "src/{$modulo}/domain/entity";
if (is_dir($entityDir)) {
    $entityFiles = glob("{$entityDir}/*.php");
    foreach ($entityFiles as $entityFile) {
        $entityContent = file_get_contents($entityFile);

        // Buscar imports de value objects de otros módulos
        preg_match_all('/use src\\\\(\w+)\\\\domain\\\\value_objects\\\\(\w+);/', $entityContent, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $otherModule = $match[1];
            $voName = $match[2];

            // Si no lo tenemos ya, agregarlo
            if (!isset($valueObjects[$voName])) {
                $voFile = "src/{$otherModule}/domain/value_objects/{$voName}.php";
                if (file_exists($voFile)) {
                    $voContent = file_get_contents($voFile);
                    $suggestedValue = suggestValueForVO($voName, $voContent);
                    $valueObjects[$voName] = $suggestedValue;
                }
            }
        }
    }
}

// Crear archivo de configuración
createConfigFile($configFile, $valueObjects);
echo "✓ Generado: {$configFile}\n";
echo "\nPuedes editar este archivo para personalizar los valores de test.\n";

/**
 * Sugiere un valor apropiado para un value object basándose en su nombre y contenido
 * Retorna el valor ya formateado como código PHP válido
 */
function suggestValueForVO(string $name, string $content): string
{
    // Detectar si el constructor acepta int
    if (preg_match('/public function __construct\(\s*(int|float)\s+/i', $content)) {
        // Analizar restricciones de longitud o rango en el contenido
        if (preg_match('/\$value\s*[<>=]{1,2}\s*(\d+)/', $content, $matches)) {
            $limit = (int)$matches[1];
            if ($limit >= 1000) {
                return "1001"; // 4 dígitos
            }
        }

        // Analizar nombres específicos
        if (stripos($name, 'Asignatura') !== false) {
            return "1001";
        }

        if (stripos($name, 'Year') !== false || stripos($name, 'Año') !== false) {
            return "2024";
        }

        return "1";
    }

    // Detectar si el constructor acepta bool
    if (preg_match('/public function __construct\(\s*bool\s+/i', $content)) {
        return "true";
    }

    // Detectar si es UUID
    if (preg_match('/Uuid|UUID/i', $name) || preg_match('/Ramsey\\\\Uuid|uuid_create|UuidInterface/i', $content)) {
        return var_export('550e8400-e29b-41d4-a716-446655440000', true);
    }

    // Analizar constructor que acepta string
    if (preg_match('/public function __construct\(\s*string\s+/i', $content)) {
        // Detectar longitud máxima
        $maxLength = null;
        if (preg_match('/strlen.*?[<>=]{1,2}\s*(\d+)/', $content, $matches)) {
            $maxLength = (int)$matches[1];
        } elseif (preg_match('/mb_strlen.*?[<>=]{1,2}\s*(\d+)/', $content, $matches)) {
            $maxLength = (int)$matches[1];
        }

        // Generar valor apropiado según longitud detectada
        if ($maxLength !== null) {
            if ($maxLength <= 3) {
                return var_export('TST', true);
            } elseif ($maxLength <= 10) {
                return var_export('Test', true);
            } elseif ($maxLength <= 50) {
                return var_export('Test value', true);
            }
        }
    }

    // Patrones específicos por nombre
    if (stripos($name, 'Email') !== false) {
        return var_export('test@example.com', true);
    }

    if (stripos($name, 'Telefono') !== false || stripos($name, 'Phone') !== false) {
        return var_export('123456789', true);
    }

    if (stripos($name, 'Dni') !== false || stripos($name, 'Nif') !== false) {
        return var_export('12345678A', true);
    }

    if (stripos($name, 'Delegacion') === 0) {
        return var_export('TST', true);
    }

    if (stripos($name, 'Code') !== false || stripos($name, 'Codigo') !== false) {
        return var_export('TST', true);
    }

    if (stripos($name, 'Pais') !== false || stripos($name, 'Country') !== false) {
        return var_export('Spain', true);
    }

    if (stripos($name, 'Password') !== false || stripos($name, 'Contraseña') !== false) {
        return var_export('securepassword', true);
    }

    if (stripos($name, 'Plazas') !== false) {
        if (stripos($name, 'Min') !== false) {
            return "5";
        }
        return "10";
    }

    if (stripos($name, 'Year') !== false || stripos($name, 'Año') !== false) {
        return "2024";
    }

    if (stripos($name, 'Order') !== false || stripos($name, 'Orden') !== false) {
        return "1";
    }

    // IDs genéricos
    if (stripos($name, 'Id') !== false) {
        return "1";
    }

    // Si contiene "Text", texto corto
    if (stripos($name, 'Text') !== false) {
        return var_export('Test', true);
    }

    // Si contiene "Name", nombre descriptivo
    if (stripos($name, 'Name') !== false || stripos($name, 'Nombre') !== false) {
        return var_export('Test Name', true);
    }

    // Por defecto, string corto
    return var_export('test', true);
}

/**
 * Crea el archivo de configuración
 */
function createConfigFile(string $configFile, array $valueObjects): void
{
    $voEntries = [];

    // Agrupar por categorías
    $categories = [
        'IDs' => [],
        'Códigos' => [],
        'Textos' => [],
        'Emails y contactos' => [],
        'Números y cantidades' => [],
        'Geográficos' => [],
        'Otros' => []
    ];

    foreach ($valueObjects as $name => $value) {
        // Formatear el valor correctamente para el archivo PHP
        $formattedValue = $value;

        if (stripos($name, 'Id') !== false) {
            $categories['IDs'][] = "    '{$name}' => {$formattedValue},";
        } elseif (stripos($name, 'Code') !== false || stripos($name, 'Codigo') !== false || stripos($name, 'Delegacion') === 0) {
            $categories['Códigos'][] = "    '{$name}' => {$formattedValue},";
        } elseif (stripos($name, 'Email') !== false || stripos($name, 'Telefono') !== false || stripos($name, 'Phone') !== false) {
            $categories['Emails y contactos'][] = "    '{$name}' => {$formattedValue},";
        } elseif (stripos($name, 'Plazas') !== false || stripos($name, 'Order') !== false || stripos($name, 'Orden') !== false || stripos($name, 'Num') !== false) {
            $categories['Números y cantidades'][] = "    '{$name}' => {$formattedValue},";
        } elseif (stripos($name, 'Pais') !== false || stripos($name, 'Region') !== false || stripos($name, 'Country') !== false) {
            $categories['Geográficos'][] = "    '{$name}' => {$formattedValue},";
        } elseif (stripos($name, 'Text') !== false || stripos($name, 'Nombre') !== false || stripos($name, 'Descripcion') !== false) {
            $categories['Textos'][] = "    '{$name}' => {$formattedValue},";
        } else {
            $categories['Otros'][] = "    '{$name}' => {$formattedValue},";
        }
    }

    // Construir contenido del archivo
    $content = "<?php\n\n";
    $content .= "/**\n";
    $content .= " * Configuración de valores de prueba para value objects\n";
    $content .= " *\n";
    $content .= " * Este archivo define los valores que se usarán en los tests generados.\n";
    $content .= " * Puedes personalizarlo según las necesidades de cada value object.\n";
    $content .= " *\n";
    $content .= " * Formato:\n";
    $content .= " * 'NombreDelValueObject' => valor_o_expresion\n";
    $content .= " *\n";
    $content .= " * Ejemplos:\n";
    $content .= " * 'Email' => \"'test@example.com'\"\n";
    $content .= " * 'UserId' => \"1\"\n";
    $content .= " * 'Codigo' => \"'ABC'\"\n";
    $content .= " */\n\n";
    $content .= "return [\n";

    foreach ($categories as $categoryName => $entries) {
        if (!empty($entries)) {
            $content .= "    // {$categoryName}\n";
            $content .= implode("\n", $entries) . "\n\n";
        }
    }

    $content .= "];\n";

    file_put_contents($configFile, $content);
}
