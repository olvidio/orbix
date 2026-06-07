<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;

/**
 * Payload de desplegable de tipos de encargo filtrados por prefijo de grupo (`id_tipo_enc ~ ^grupo`).
 */
final class EncargoLstTipoEncData
{

    public function __construct(
        private EncargoTipoRepositoryInterface $encargoTipoRepository
    ) {
    }

    /**
     * @return array{id: string, opciones: array<string, string>, selected: string, blanco: bool, val_blanco: string, action: string}
     */
    public function execute(string $grupo, ?string $id_tipo_enc_selected): array
    {
        $aWhere = [];
        $aOperador = [];
        $aWhere['id_tipo_enc'] = '^' . $grupo;
        $aOperador['id_tipo_enc'] = '~';
        $cEncargoTipos = $this->encargoTipoRepository->getEncargoTipos($aWhere, $aOperador);

        $opciones = [];
        foreach ($cEncargoTipos as $oEncargoTipo) {
            $opciones[(string) $oEncargoTipo->getId_tipo_enc()] = (string) $oEncargoTipo->getTipo_enc();
        }

        return [
            'id' => 'id_tipo_enc',
            'opciones' => $this->stringKeyedOpciones($opciones),
            'selected' => $id_tipo_enc_selected ?? '',
            'blanco' => true,
            'val_blanco' => '',
            'action' => '',
        ];
    }

    /**
     * @param array<int|string, string> $opciones
     * @return array<string, string>
     */
    private function stringKeyedOpciones(array $opciones): array
    {
        $out = [];
        foreach ($opciones as $k => $v) {
            $out[(string) $k] = $v;
        }

        return $out;
    }
}
