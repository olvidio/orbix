<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoTipoField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\helpers\OpcionesDesplegable;

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
     * @return array{id: string, opciones: list<array{0: string, 1: string}>, selected: string, blanco: bool, val_blanco: string, action: string}
     */
    public function execute(string $grupo, ?string $id_tipo_enc_selected): array
    {
        $cEncargoTipos = $this->encargoTipoRepository->matching(
            Criteria::create()->where(Filter::matches(EncargoTipoField::IdTipoEnc, '^' . $grupo))
        );

        $opciones = [];
        foreach ($cEncargoTipos as $oEncargoTipo) {
            $opciones[(string) $oEncargoTipo->getId_tipo_enc()] = (string) $oEncargoTipo->getTipo_enc();
        }

        return [
            'id' => 'id_tipo_enc',
            'opciones' => OpcionesDesplegable::enOrden($opciones),
            'selected' => $id_tipo_enc_selected ?? '',
            'blanco' => true,
            'val_blanco' => '',
            'action' => '',
        ];
    }
}
