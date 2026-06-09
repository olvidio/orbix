<?php

namespace frontend\shared\permisos;

/**
 * Vista solo lectura de bits (iconos), misma lógica que {@see \src\permisos\domain\XPermisos::cuadros_check_read()}.
 */
final class MenuPermCheckboxReadHtml
{
    /**
     * @param array<string, int> $bitMap
     */
    public static function render(int $bin, array $bitMap, string $iconsBaseUrl): string
    {
        if (empty($bin)) {
            $bin = 0;
        }
        $base = rtrim($iconsBaseUrl, '/');
        $txt = '';
        foreach ($bitMap as $nom => $num) {
            $chk = ($bin & $num) ? 'checkbox-checked.png' : 'check-box-outline-blank.png';
            $txt .= "   <img src='" . $base . '/' . $chk . "' width=10 height=10 border=0>$nom";
        }

        return $txt;
    }
}
