<?php

namespace src\ubis\application;

use DateInterval;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

final class CalendarioPeriodosNuevoData
{
    public static function execute(int $idUbi, int $year): array
    {
        $f_next = '';
        $sf_chk = '';
        $sv_chk = '';
        if ($idUbi > 0 && $year > 0) {
            $inicio = $year . '-1-1';
            $fin = $year . '-12-31';
            $aWhere = [
                'id_ubi' => $idUbi,
                'f_ini' => $inicio,
                'f_fin' => $fin,
                '_ordre' => 'f_fin DESC',
            ];
            $aOperador = [
                'f_ini' => '>=',
                'f_fin' => '<=',
            ];
            $repo = $GLOBALS['container']->get(CasaPeriodoRepositoryInterface::class);
            $cCasaPeriodos = $repo->getCasaPeriodos($aWhere, $aOperador);
            if (!empty($cCasaPeriodos)) {
                $oCasaPeriodo = $cCasaPeriodos[0];
                $oF_fin = $oCasaPeriodo->getF_fin();
                if ($oF_fin instanceof DateTimeLocal) {
                    $oF_next = $oF_fin->add(new DateInterval('P1D'));
                    $f_next = $oF_next->getFromLocal();
                }
                $sfsv = $oCasaPeriodo->getSfsv();
                if ($sfsv === 1) {
                    $sf_chk = 'selected';
                    $sv_chk = '';
                } else {
                    $sf_chk = '';
                    $sv_chk = 'selected';
                }
            }
        }

        return [
            'f_next' => $f_next,
            'sf_chk' => $sf_chk,
            'sv_chk' => $sv_chk,
        ];
    }
}
