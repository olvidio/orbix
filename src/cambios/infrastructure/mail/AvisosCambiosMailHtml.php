<?php

declare(strict_types=1);

namespace src\cambios\infrastructure\mail;

use src\cambios\domain\contracts\AvisosCambiosMailCuerpoInterface;

/**
 * Documento HTML del correo de avisos de cambios: una tabla simple, con el texto escapado.
 */
final class AvisosCambiosMailHtml implements AvisosCambiosMailCuerpoInterface
{
    public function cuerpo(string $titulo, array $cabeceras, array $filas): string
    {
        $html = '<html><head><title>' . self::e($titulo) . '</title></head><body>';
        $html .= '<table class="lista"><tr>';
        foreach ($cabeceras as $cabecera) {
            $html .= '<th class=cabecera>' . self::e($cabecera) . '</th>';
        }
        $html .= '</tr><tbody>';
        $f = 0;
        foreach ($filas as $fila) {
            $html .= '<tr class="' . ($f++ % 2 === 0 ? 'imp' : 'par') . '">';
            for ($col = 1, $n = count($cabeceras); $col <= $n; $col++) {
                $html .= '<td>' . self::e($fila[$col] ?? '') . '</td>';
            }
            $html .= "</tr>\n";
        }

        return $html . '</tbody></table></body></html>';
    }

    private static function e(string $texto): string
    {
        return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    }
}
