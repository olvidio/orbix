<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

final class CalendarioPeriodosGetData
{
    /**
     * @return list<array{id_item: int, id_ubi: int, f_ini: string, f_fin: string, sfsv: int}>
     */
    public static function execute(int $idUbi): array
    {
        if ($idUbi <= 0) {
            return [];
        }
        $repo = $GLOBALS['container']->get(CasaPeriodoRepositoryInterface::class);
        $cCasaPeriodos = $repo->getCasaPeriodos(['id_ubi' => $idUbi, '_ordre' => 'f_ini']);

        $rows = [];
        foreach ($cCasaPeriodos as $oCasaPeriodo) {
            $rows[] = [
                'id_item' => $oCasaPeriodo->getId_item(),
                'id_ubi' => $oCasaPeriodo->getId_ubi(),
                'f_ini' => $oCasaPeriodo->getF_ini()?->getFromLocal() ?? '',
                'f_fin' => $oCasaPeriodo->getF_fin()?->getFromLocal() ?? '',
                'sfsv' => (int)($oCasaPeriodo->getSfsv() ?? 0),
            ];
        }

        return ['rows' => $rows];
    }
}
