<?php

function findClosingBrace(string $content, int $openBracePos): int|false
{
    $length = strlen($content);
    $braceCount = 1;
    $pos = $openBracePos + 1;

    echo "Iniciando búsqueda desde posición $pos\n";
    echo "Longitud total: $length\n";

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
            echo "  Pos $pos: '{' - nivel $braceCount\n";
        } elseif ($char === '}') {
            $braceCount--;
            echo "  Pos $pos: '}' - nivel $braceCount\n";
            if ($braceCount === 0) {
                echo "Encontrado cierre en posición $pos\n";
                return $pos;
            }
        }

        $pos++;
    }

    return false;
}

$content = file_get_contents('src/actividades/domain/value_objects/ActividadNomText.php');
$pattern = '/(?:public\s+)?static\s+function\s+fromNullableString\s*\(\s*\?string\s+\$value\s*\)(?:\s*:\s*\?self)?\s*\{/';

if (preg_match($pattern, $content, $match, PREG_OFFSET_CAPTURE)) {
    $startPos = $match[0][1];
    $openBracePos = $startPos + strlen($match[0][0]) - 1;

    echo "Inicio del método: $startPos\n";
    echo "Posición llave apertura: $openBracePos\n\n";

    $closeBracePos = findClosingBrace($content, $openBracePos);

    if ($closeBracePos !== false) {
        echo "\nMétodo completo:\n";
        echo "====================\n";
        $methodLength = $closeBracePos - $startPos + 1;
        echo substr($content, $startPos, $methodLength);
        echo "\n====================\n";

        echo "\nLongitud archivo original: " . strlen($content) . "\n";
        echo "Siguiente carácter después del cierre: [" . substr($content, $closeBracePos + 1, 10) . "]\n";
    }
}
