<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoField;
use src\encargossacd\domain\criteria\EncargoTipoField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
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
        
        $cEncargoTipos = $this->encargoTipoRepository->matching(
            Criteria::create()->where(Filter::matches(EncargoTipoField::IdTipoEnc, '^8...'))
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
            $cEncargos = $this->encargoRepository->matching(
                Criteria::create()
                    ->where(Filter::in(EncargoField::IdTipoEnc, $a_tipo_enc))
                    ->andWhere(Filter::eq(EncargoField::IdZona, $id_zona))
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
