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

$archivo = 'src/actividades/domain/value_objects/NivelStgrBreve.php';
$content = file_get_contents($archivo);

echo "Longitud archivo: " . strlen($content) . "\n";
echo "Último carácter ASCII: " . ord(substr($content, -1)) . " [" . substr($content, -1) . "]\n";
echo "Últimos 20 caracteres:\n";
var_dump(substr($content, -20));
echo "\n";

$pattern = '/(?:public\s+)?static\s+function\s+fromNullableString\s*\(\s*\?string\s+\$value\s*\)(?:\s*:\s*\?self)?\s*\{/';

if (preg_match($pattern, $content, $match, PREG_OFFSET_CAPTURE)) {
    $startPos = $match[0][1];
    $openBracePos = $startPos + strlen($match[0][0]) - 1;

    echo "Inicio del método: $startPos\n";
    echo "Posición llave apertura: $openBracePos\n";

    $closeBracePos = findClosingBrace($content, $openBracePos);

    if ($closeBracePos !== false) {
        echo "Posición llave cierre método: $closeBracePos\n\n";

        $methodLength = $closeBracePos - $startPos + 1;
        echo "Longitud del método: $methodLength\n\n";

        echo "Método extraído:\n";
        echo "====================\n";
        echo substr($content, $startPos, $methodLength);
        echo "\n====================\n\n";

        echo "30 caracteres después del método:\n";
        echo var_export(substr($content, $closeBracePos, 30), true) . "\n";
    }
}
