<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\entity\EncargoTexto;

/**
 * Textos de comunicación en listas (ajax: get_texto / update).
 */
final class EncargoTextoListasComAjax
{
    /**
     * @return array{texto?: string, ok?: true}
     */
    public static function ejecutar(
        string $que,
        string $clave,
        string $idioma,
        ?string $comunicacion = null
    ): array {
        $EncargoTextoRepository = $GLOBALS['container']->get(EncargoTextoRepositoryInterface::class);

        if ($que === 'get_texto') {
            $aWhere = ['clave' => $clave, 'idioma' => $idioma];
            $cEncargoTextos = $EncargoTextoRepository->getEncargoTextos($aWhere);
            $txt = '';
            if (is_array($cEncargoTextos) && count($cEncargoTextos) > 0) {
                $txt = $cEncargoTextos[0]->getTexto();
            }

            return ['texto' => $txt];
        }

        if ($que === 'update') {
            $Qcomunicacion = $comunicacion ?? '';
            $aWhere = ['clave' => $clave, 'idioma' => $idioma];
            $cEncargoTextos = $EncargoTextoRepository->getEncargoTextos($aWhere);
            if (is_array($cEncargoTextos) && count($cEncargoTextos) > 0) {
                $oEncargoTexto = $cEncargoTextos[0];
                if ($Qcomunicacion === '') {
                    $EncargoTextoRepository->Eliminar($oEncargoTexto);
                } else {
                    $oEncargoTexto->setTexto($Qcomunicacion);
                    $EncargoTextoRepository->Guardar($oEncargoTexto);
                }
            } else {
                $newId = $EncargoTextoRepository->getNewId();
                $oEncargoTexto = new EncargoTexto();
                $oEncargoTexto->setId_item($newId);
                $oEncargoTexto->setClave($clave);
                $oEncargoTexto->setIdioma($idioma);
                $oEncargoTexto->setTexto($Qcomunicacion);
                $EncargoTextoRepository->Guardar($oEncargoTexto);
            }

            return ['ok' => true];
        }

        return [];
    }
}
