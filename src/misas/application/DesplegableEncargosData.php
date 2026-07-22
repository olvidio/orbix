<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\shared\domain\helpers\OpcionesDesplegable;

class DesplegableEncargosData
{

    public function __construct(
        private readonly EncargoTipoRepositoryInterface $encargoTipoRepository,
        private readonly EncargoRepositoryInterface $encargoRepository,
    ) {
    }
    /**
     * Payload JSON para el desplegable dinamico de encargos de una zona.
     *
     * @param int      $id_zona       Zona de la que sacar los encargos.
     * @param int|null $id_enc_sel    Encargo preseleccionado (opcional).
     * @return array<string, mixed>
     */
    public function getData(int $id_zona, ?int $id_enc_sel = null): array
    {
        
        $cEncargoTipos = $this->encargoTipoRepository->getEncargoTipos(
            ['id_tipo_enc' => '^8...'],
            ['id_tipo_enc' => '~'],
        );
        $a_tipo_enc = [];
        foreach ($cEncargoTipos as $oEncargoTipo) {
            if ($oEncargoTipo->getId_tipo_enc() >= 8100) {
                $a_tipo_enc[] = $oEncargoTipo->getId_tipo_enc();
            }
        }

        $opciones = [];

        if ($id_enc_sel !== null && $id_enc_sel > 0) {
            $oEncSel = $this->encargoRepository->findById($id_enc_sel);
            if ($oEncSel !== null) {
                $opciones[(string)$id_enc_sel] = (string) ($oEncSel->getDesc_enc() ?? '');
            }
        }

        if (!empty($a_tipo_enc)) {
            $cond_tipo_enc = '{' . implode(', ', $a_tipo_enc) . '}';
            $cEncargos = $this->encargoRepository->getEncargos(
                [
                    'id_tipo_enc' => $cond_tipo_enc,
                    'id_zona' => $id_zona,
                ],
                ['id_tipo_enc' => 'ANY'],
            );
            foreach ($cEncargos as $oEncargo) {
                $opciones[(string)$oEncargo->getId_enc()] = (string) ($oEncargo->getDesc_enc() ?? '');
            }
        }

        return [
            'id' => 'id_enc',
            'opciones' => OpcionesDesplegable::enOrden($opciones),
            'selected' => $id_enc_sel !== null ? (string)$id_enc_sel : '',
            'blanco' => true,
            'val_blanco' => '',
            'action' => 'fnjs_prepara_select_centro()',
        ];
    }
}
