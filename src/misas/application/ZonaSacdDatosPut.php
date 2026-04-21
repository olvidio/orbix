<?php

namespace src\misas\application;

use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

class ZonaSacdDatosPut
{
    /**
     * @param array{dw1?:string,dw2?:string,dw3?:string,dw4?:string,dw5?:string,dw6?:string,dw7?:string} $dw
     * @return array{error: string}
     */
    public static function execute(int $id_zona, int $id_sacd, array $dw): array
    {
        $aWhere = ['id_zona' => $id_zona, 'id_nom' => $id_sacd];
        $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
        $cZonaSacd = $ZonaSacdRepository->getZonasSacds($aWhere);
        if (empty($cZonaSacd)) {
            return ['error' => _('No existe')];
        }

        $oZonaSacd = $cZonaSacd[0];
        $b = static function ($v): bool {
            return filter_var($v, FILTER_VALIDATE_BOOLEAN);
        };
        $oZonaSacd->setDw1($b($dw['dw1'] ?? false));
        $oZonaSacd->setDw2($b($dw['dw2'] ?? false));
        $oZonaSacd->setDw3($b($dw['dw3'] ?? false));
        $oZonaSacd->setDw4($b($dw['dw4'] ?? false));
        $oZonaSacd->setDw5($b($dw['dw5'] ?? false));
        $oZonaSacd->setDw6($b($dw['dw6'] ?? false));
        $oZonaSacd->setDw7($b($dw['dw7'] ?? false));

        if ($ZonaSacdRepository->Guardar($oZonaSacd) === false) {
            return ['error' => $ZonaSacdRepository->getErrorTxt()];
        }

        return ['error' => ''];
    }
}
