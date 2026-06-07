<?php

namespace src\encargossacd\application;

use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

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
     * @return array{label_prefix: string, id: string, name: string, opciones: array<string, string>, selected: string, blanco: bool, val_blanco: string, action: string}
     */
    public function execute(int $id_zona_selected): array
    {
        $aOpciones = $this->zonaRepository->getArrayZonas();
        $opciones = [];
        foreach ($aOpciones as $k => $v) {
            $opciones[(string)$k] = (string)$v;
        }

        return [
            'label_prefix' => _('zona') . ': ',
            'id' => 'id_zona_sel',
            'name' => 'id_zona_sel',
            'opciones' => $opciones,
            'selected' => (string)$id_zona_selected,
            'blanco' => false,
            'val_blanco' => '',
            'action' => 'fnjs_lista_ctrs_por_zona()',
        ];
    }
}
