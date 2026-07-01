<?php

namespace src\encargossacd\application;

use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\shared\domain\helpers\OpcionesDesplegable;

/**
 * Payload JSON para el desplegable de zonas (grupo «zonas misas»).
 * Devuelve el contrato estandar definido en `refactor.md`, sin instanciar
 * `frontend\shared\web\Desplegable` (responsabilidad exclusiva del frontend).
 */
final class EncargoZonasSelectData
{

    public function __construct(
        private ZonaRepositoryInterface $zonaRepository
    ) {
    }

    /**
     * @return array{label_prefix: string, id: string, name: string, opciones: list<array{0: string, 1: string}>, selected: string, blanco: bool, val_blanco: string, action: string}
     */
    public function execute(int $id_zona_selected): array
    {
        $aOpciones = $this->zonaRepository->getArrayZonas();

        return [
            'label_prefix' => _('zona') . ': ',
            'id' => 'id_zona_sel',
            'name' => 'id_zona_sel',
            'opciones' => OpcionesDesplegable::enOrden($aOpciones),
            'selected' => (string)$id_zona_selected,
            'blanco' => false,
            'val_blanco' => '',
            'action' => 'fnjs_lista_ctrs_por_zona()',
        ];
    }
}
