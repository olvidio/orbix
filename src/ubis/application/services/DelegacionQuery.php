<?php

namespace src\ubis\application\services;

use src\ubis\application\repositories\DelegacionRepository;

final class DelegacionQuery
{
    /**
     * Devuelve un array [id_dl => dl] de delegaciones activas filtradas por region_stgr.
     * Si $regionesStgr está vacío, devuelve todas las delegaciones (activas).
     *
     * @param array $regionesStgr Lista de códigos region_stgr
     * @return array [id_dl => dl]
     */
    public static function arrayDlByRegionStgr(array $regionesStgr = []): array
    {
        $repo = new DelegacionRepository();
        $aWhere = ['status' => true, '_ordre' => 'dl'];
        $aOper = [];
        if (!empty($regionesStgr)) {
            // Usamos operador IN sobre region_stgr
            $aWhere['region_stgr'] = "'" . implode("','", $regionesStgr) . "'";
            $aOper['region_stgr'] = 'IN';
        }
        $delegaciones = $repo->getDelegaciones($aWhere, $aOper) ?: [];
        $out = [];
        foreach ($delegaciones as $dl) {
            $out[$dl->getIdDlVo()?->value() ?? 0] = $dl->getDlVo()?->value() ?? '';
        }
        return $out;
    }
}
