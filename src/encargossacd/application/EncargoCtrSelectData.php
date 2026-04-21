<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\value_objects\EncargoGrupo;
use web\Desplegable;

/**
 * Payload JSON para el desplegable de centros según filtro (y zona opcional).
 */
final class EncargoCtrSelectData
{
    /**
     * @return array{id: string, name: string, opciones: array<string, string>, selected: string, blanco: bool, val_blanco: string, action: string}
     */
    public static function execute(int $id_ubi, int $filtro_ctr, int $id_zona): array
    {
        $oDesplCtr = self::desplegableBase($id_ubi, $filtro_ctr, $id_zona);
        $exp = $oDesplCtr->export();
        $opciones = [];
        foreach ($exp['options'] as $k => $v) {
            $opciones[(string)$k] = (string)$v;
        }

        return [
            'id' => (string)$exp['nombre'],
            'name' => (string)$exp['nombre'],
            'opciones' => $opciones,
            'selected' => (string)($exp['opcion_sel'] ?? ''),
            'blanco' => (bool)$exp['blanco'],
            'val_blanco' => (string)($exp['valorBlanco'] ?? ''),
            'action' => (string)$exp['action'],
        ];
    }

    private static function desplegableBase(int $id_ubi, int $filtro_ctr, int $id_zona): Desplegable
    {
        $filtro_eff = $id_zona !== 0 ? EncargoGrupo::ZONAS_MISAS : $filtro_ctr;

        $oDesplCtr = new Desplegable();
        $oDesplCtr->setOpciones(CentrosPorFiltroOpciones::getOpciones($filtro_eff, $id_zona));
        if ($filtro_eff === EncargoGrupo::CGI
            || ($filtro_eff === EncargoGrupo::ZONAS_MISAS && $id_zona !== 0)) {
            $oDesplCtr->setBlanco(true);
        }
        $oDesplCtr->setNombre('lst_ctrs');
        $oDesplCtr->setAction('fnjs_ver_ficha()');
        $oDesplCtr->setOpcion_sel($id_ubi);

        return $oDesplCtr;
    }
}
