#!/usr/bin/env php
<?php
/**
 * Script para actualizar métodos fromNullableString/fromNullable en Value Objects
 * según el tipo del constructor (string o int)
 *
 * Uso:
 *   php update_from_nullable_string.php <modulo>
 *
 * Ejemplo:
 *   php update_from_nullable_string.php actividades
 */

if ($argc < 2) {
    echo "Uso: php update_from_nullable_string.php <modulo>\n";
    echo "Ejemplo: php update_from_nullable_string.php actividades\n";
    exit(1);
}

$modulo = $argv[1];
$directorio = "src/{$modulo}/domain/value_objects/";

if (!is_dir($directorio)) {
    echo "Error: El directorio {$directorio} no existe\n";
    exit(1);
}

$archivos = glob($directorio . "*.php");
$totalArchivos = 0;
$totalReemplazos = 0;

foreach ($archivos as $archivo) {
    echo "\nProcesando: " . basename($archivo) . "\n";

    $content = file_get_contents($archivo);
    $contentOriginal = $content;

    // Detectar el tipo del constructor
    $constructorType = detectConstructorType($content);

    if ($constructorType === null) {
        echo "  ⊘ No se pudo detectar el tipo del constructor\n";
        continue;
    }

    echo "  → Tipo detectado: $constructorType\n";

    // Determinar qué método buscar y generar
    if ($constructorType === 'string') {
        $methodPattern = '/(?:public\s+)?static\s+function\s+fromNullableString\s*\(\s*\?string\s+\$value\s*\)(?:\s*:\s*\?self)?\s*\{/';
        $methodName = 'fromNullableString';
    } else { // int
        $methodPattern = '/(?:public\s+)?static\s+function\s+fromNullable\s*\(\s*\?int\s+\$value\s*\)(?:\s*:\s*\?self)?\s*\{/';
        $methodName = 'fromNullable';
    }

    // Buscar todos los métodos existentes
    $matches = [];
    $offset = 0;
    while (preg_match($methodPattern, $content, $match, PREG_OFFSET_CAPTURE, $offset)) {
        $matches[] = $match;
        $offset = $match[0][1] + strlen($match[0][0]);
    }

    $reemplazos = 0;

    // Si no existe el método, verificar si debemos crearlo
    if (empty($matches)) {
        echo "  → No se encontró el método $methodName, creándolo...\n";
        $content = createMethod($content, $constructorType);
        $reemplazos = 1;
    } else {
        // Reemplazar de atrás hacia adelante para mantener las posiciones correctas
        $matches = array_reverse($matches);

        foreach ($matches as $match) {
            $startPos = $match[0][1];
            $openBracePos = $startPos + strlen($match[0][0]) - 1;

            // Detectar la indentación del método original
            $lineStart = strrpos(substr($content, 0, $startPos), "\n");
            if ($lineStart !== false) {
                $indentation = substr($content, $lineStart + 1, $startPos - $lineStart - 1);
            } else {
                $indentation = '';
            }

            // Generar el nuevo método
            $nuevoMetodo = generateMethod($constructorType, $indentation);

            // Encontrar la llave de cierre balanceada
            $closeBracePos = findClosingBrace($content, $openBracePos);

            if ($closeBracePos !== false) {
                $methodLength = $closeBracePos - $startPos + 1;
                $content = substr_replace($content, $nuevoMetodo, $startPos, $methodLength);
                $reemplazos++;
            } else {
                echo "  ✗ No se pudo encontrar el cierre del método\n";
            }
        }
    }

    if ($reemplazos > 0) {
        // Preservar si el archivo original terminaba con newline
        $endsWithNewline = substr($contentOriginal, -1) === "\n";
        if ($endsWithNewline && substr($content, -1) !== "\n") {
            $content .= "\n";
        }

        file_put_contents($archivo, $content);
        echo "  ✓ Método(s) actualizado(s): $reemplazos\n";
        $totalArchivos++;
        $totalReemplazos += $reemplazos;
    }
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "--- Resumen ---\n";
echo "Archivos modificados: {$totalArchivos}\n";
echo "Total de métodos actualizados: {$totalReemplazos}\n";
echo str_repeat('=', 50) . "\n";

if ($totalReemplazos === 0) {
    echo "No se encontraron métodos para actualizar\n";
}

// ============================================================================
// FUNCIONES AUXILIARES
// ============================================================================

/**
 * Detecta el tipo del parámetro del constructor (__construct)
 * Retorna 'string', 'int' o null si no se puede detectar
 */
function detectConstructorType(string $content): ?string
{
    // Buscar el constructor: public function __construct(tipo $value)
    if (preg_match('/public\s+function\s+__construct\s*\(\s*(string|int)\s+\$/', $content, $match)) {
        return $match[1];
    }

    // Aceptar que pueda ser nulo: ?int o ?string
    if (preg_match('/public\s+function\s+__construct\s*\(\s*\?(string|int)\s+\$/', $content, $match)) {
        return $match[1];
    }

    return null;
}

/**
 * Genera el método correcto según el tipo
 */
function generateMethod(string $type, string $indentation): string
{
    if ($type === 'string') {
        return <<<PHP
public static function fromNullableString(?string \$value): ?self
$indentation{
$indentation    if (\$value === null) {
$indentation        return null;
$indentation    }
$indentation    \$value_trimmed = trim(\$value);
$indentation    if (\$value_trimmed === '') {
$indentation        return null;
$indentation    }
$indentation    return new self(\$value_trimmed);
$indentation}
PHP;
    } else { // int
        return <<<PHP
public static function fromNullable(?int \$value): ?self
$indentation{
$indentation    if (\$value === null) {
$indentation        return null;
$indentation    }
$indentation    return new self(\$value);
$indentation}
PHP;
    }
}

/**
 * Crea el método en el archivo si no existe
 * Lo inserta antes de la última llave de cierre de la clase
 */
function createMethod(string $content, string $type): string
{
    // Encontrar la última llave de cierre (cierre de la clase)
    $lastBracePos = strrpos($content, '}');

    if ($lastBracePos === false) {
        echo "  ✗ No se pudo encontrar el cierre de la clase\n";
        return $content;
    }

    // Detectar la indentación de los métodos existentes
    // Buscar algún método public para detectar la indentación
    if (preg_match('/\n([ \t]+)public\s+function\s+/', $content, $match)) {
        $indentation = $match[1];
    } else {
        $indentation = '    '; // Por defecto 4 espacios
    }

    // Generar el método
    $nuevoMetodo = generateMethod($type, $indentation);

    // Insertar el método antes de la última llave con una línea en blanco antes
    $insert = "\n" . $nuevoMetodo . "\n";
    $content = substr_replace($content, $insert, $lastBracePos, 0);

    return $content;
}

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
