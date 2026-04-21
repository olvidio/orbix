<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\entity\EncargoTexto;

/**
 * Mutacion del texto de comunicacion para un par (clave, idioma).
 * Si el texto llega vacio, se elimina la fila.
 *
 * Extraido de `EncargoTextoListasComAjax` (rama `que=update`) para eliminar
 * el dispatcher multiproposito (criterio `refactor.md`).
 */
final class ListasComTxtUpdate
{
    /**
     * @return array{ok: true}
     */
    public static function execute(string $clave, string $idioma, string $comunicacion): array
    {
        $EncargoTextoRepository = $GLOBALS['container']->get(EncargoTextoRepositoryInterface::class);
        $cEncargoTextos = $EncargoTextoRepository->getEncargoTextos([
            'clave' => $clave,
            'idioma' => $idioma,
        ]);

        if (is_array($cEncargoTextos) && count($cEncargoTextos) > 0) {
            $oEncargoTexto = $cEncargoTextos[0];
            if ($comunicacion === '') {
                $EncargoTextoRepository->Eliminar($oEncargoTexto);
            } else {
                $oEncargoTexto->setTexto($comunicacion);
                $EncargoTextoRepository->Guardar($oEncargoTexto);
            }
        } else {
            $oEncargoTexto = new EncargoTexto();
            $oEncargoTexto->setId_item($EncargoTextoRepository->getNewId());
            $oEncargoTexto->setClave($clave);
            $oEncargoTexto->setIdioma($idioma);
            $oEncargoTexto->setTexto($comunicacion);
            $EncargoTextoRepository->Guardar($oEncargoTexto);
        }

        return ['ok' => true];
    }
}
