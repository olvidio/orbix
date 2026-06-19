<?php

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
                    $pos += 2;
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

$archivo = 'src/actividades/domain/value_objects/NivelStgrBreve.php';
$content = file_get_contents($archivo);
$contentOriginal = $content;

$nuevoMetodoTemplate = <<<'PHP'
public static function fromNullableString(?string $value): ?self
{
    if ($value === null) {
        return null;
    }
    $value_trimmed = trim($value);
    if ($value_trimmed === '') {
        return null;
    }
    return new self($value_trimmed);
}
PHP;

echo "ANTES:\n";
echo "Longitud: " . strlen($content) . "\n";
echo "Últimos 50 caracteres:\n" . var_export(substr($content, -50), true) . "\n\n";

$pattern = '/(?:public\s+)?static\s+function\s+fromNullableString\s*\(\s*\?string\s+\$value\s*\)(?:\s*:\s*\?self)?\s*\{/';

if (preg_match($pattern, $content, $match, PREG_OFFSET_CAPTURE)) {
    $startPos = $match[0][1];
    $openBracePos = $startPos + strlen($match[0][0]) - 1;

    // Detectar indentación
    $lineStart = strrpos(substr($content, 0, $startPos), "\n");
    if ($lineStart !== false) {
        $indentation = substr($content, $lineStart + 1, $startPos - $lineStart - 1);
    } else {
        $indentation = '';
    }

    echo "Indentación detectada: [" . var_export($indentation, true) . "]\n\n";

    // Aplicar indentación
    $nuevoMetodo = $nuevoMetodoTemplate;
    if ($indentation !== '') {
        $lines = explode("\n", $nuevoMetodo);
        $indentedLines = [$indentation . $lines[0]];
        for ($i = 1; $i < count($lines); $i++) {
            if (trim($lines[$i]) !== '') {
                $indentedLines[] = $indentation . $lines[$i];
            } else {
                $indentedLines[] = '';
            }
        }
        $nuevoMetodo = implode("\n", $indentedLines);
    }

    echo "Nuevo método con indentación:\n";
    echo var_export($nuevoMetodo, true) . "\n\n";

    $closeBracePos = findClosingBrace($content, $openBracePos);

    if ($closeBracePos !== false) {
        $methodLength = $closeBracePos - $startPos + 1;
        echo "Reemplazando desde $startPos longitud $methodLength\n";
        echo "Método viejo:\n" . var_export(substr($content, $startPos, $methodLength), true) . "\n\n";

        $content = substr_replace($content, $nuevoMetodo, $startPos, $methodLength);

        // Preservar newline final
        $endsWithNewline = substr($contentOriginal, -1) === "\n";
        if ($endsWithNewline && substr($content, -1) !== "\n") {
            $content .= "\n";
        }

        echo "DESPUÉS:\n";
        echo "Longitud: " . strlen($content) . "\n";
        echo "Últimos 50 caracteres:\n" . var_export(substr($content, -50), true) . "\n\n";
    }
}
