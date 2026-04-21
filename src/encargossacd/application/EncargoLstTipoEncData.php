<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;

/**
 * Payload de desplegable de tipos de encargo filtrados por prefijo de grupo (`id_tipo_enc ~ ^grupo`).
 */
final class EncargoLstTipoEncData
{
    /**
     * @return array{id: string, opciones: array<string, string>, selected: string, blanco: bool, val_blanco: string, action: string}
     */
    public static function execute(string $grupo, ?string $id_tipo_enc_selected): array
    {
        $EncargoTipoRepository = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);
        $aWhere = [];
        $aOperador = [];
        $aWhere['id_tipo_enc'] = '^' . $grupo;
        $aOperador['id_tipo_enc'] = '~';
        $cEncargoTipos = $EncargoTipoRepository->getEncargoTipos($aWhere, $aOperador);

        $opciones = [];
        foreach ($cEncargoTipos as $oEncargoTipo) {
            $opciones[(string)$oEncargoTipo->getId_tipo_enc()] = $oEncargoTipo->getTipo_enc();
        }

        return [
            'id' => 'id_tipo_enc',
            'opciones' => $opciones,
            'selected' => $id_tipo_enc_selected ?? '',
            'blanco' => true,
            'val_blanco' => '',
            'action' => '',
        ];
    }
}
