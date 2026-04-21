<?php

namespace src\encargossacd\application;

use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use web\Desplegable;

/**
 * Payload JSON para el desplegable de zonas (grupo «zonas misas»).
 */
final class EncargoZonasSelectData
{
    /**
     * @return array{label_prefix: string, id: string, name: string, opciones: array<string, string>, selected: string, blanco: bool, val_blanco: string, action: string}
     */
    public static function execute(int $id_zona_selected): array
    {
        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $aOpciones = $ZonaRepository->getArrayZonas();
        $oDesplZonas = new Desplegable();
        $oDesplZonas->setOpciones($aOpciones);
        $oDesplZonas->setBlanco(false);
        $oDesplZonas->setNombre('id_zona_sel');
        $oDesplZonas->setAction('fnjs_lista_ctrs_por_zona()');
        $oDesplZonas->setOpcion_sel($id_zona_selected);

        $exp = $oDesplZonas->export();
        $opciones = [];
        foreach ($exp['options'] as $k => $v) {
            $opciones[(string)$k] = (string)$v;
        }

        return [
            'label_prefix' => _('zona') . ': ',
            'id' => (string)$exp['nombre'],
            'name' => (string)$exp['nombre'],
            'opciones' => $opciones,
            'selected' => (string)($exp['opcion_sel'] ?? ''),
            'blanco' => (bool)$exp['blanco'],
            'val_blanco' => (string)($exp['valorBlanco'] ?? ''),
            'action' => (string)$exp['action'],
        ];
    }
}
