<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;

/**
 * Datos para la pantalla de textos de comunicacion
 * (`frontend/encargossacd/controller/listas_com_txt.php`).
 *
 * Devuelve las opciones de idiomas configurados y el texto inicial
 * correspondiente a la clave/idioma por defecto (`com_sacd` / `es`).
 */
final class ListasComTxtData
{
    /**
     * @return array{ a_locales: array<string, string>, texto_inicial: string }
     */
    public static function execute(): array
    {
        $LocalRepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);
        $a_locales = $LocalRepository->getArrayLocales();

        $EncargoTextoRepository = $GLOBALS['container']->get(EncargoTextoRepositoryInterface::class);
        $cEncargoTextos = $EncargoTextoRepository->getEncargoTextos([
            'clave' => 'com_sacd',
            'idioma' => 'es',
        ]);
        $texto_inicial = '';
        if (is_array($cEncargoTextos) && count($cEncargoTextos) > 0) {
            $texto_inicial = (string)$cEncargoTextos[0]->getTexto();
        }

        return [
            'a_locales' => $a_locales,
            'texto_inicial' => $texto_inicial,
        ];
    }
}
