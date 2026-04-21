<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;

/**
 * Lectura del texto de comunicacion para un par (clave, idioma).
 *
 * Extraido de `EncargoTextoListasComAjax` (rama `que=get_texto`) para eliminar
 * el dispatcher multiproposito (criterio `refactor.md`).
 */
final class ListasComTxtGet
{
    /**
     * @return array{texto: string}
     */
    public static function execute(string $clave, string $idioma): array
    {
        $EncargoTextoRepository = $GLOBALS['container']->get(EncargoTextoRepositoryInterface::class);
        $cEncargoTextos = $EncargoTextoRepository->getEncargoTextos([
            'clave' => $clave,
            'idioma' => $idioma,
        ]);

        $texto = '';
        if (is_array($cEncargoTextos) && count($cEncargoTextos) > 0) {
            $texto = (string)$cEncargoTextos[0]->getTexto();
        }

        return ['texto' => $texto];
    }
}
