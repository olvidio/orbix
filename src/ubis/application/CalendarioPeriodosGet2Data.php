<?php

namespace src\ubis\application;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use web\TiposActividades;

final class CalendarioPeriodosGet2Data
{
    public static function execute(int $idUbi, int $year): array
    {
        $cCasaPeriodos = [];
        if ($idUbi > 0 && $year > 0) {
            $inicio = $year . '-1-1';
            $fin = $year . '-12-31';
            $aWhere = [
                'id_ubi' => $idUbi,
                'f_ini' => $inicio,
                'f_fin' => $fin,
                '_ordre' => 'f_ini',
            ];
            $aOperador = [
                'f_ini' => '>=',
                'f_fin' => '<=',
            ];
            $repo = $GLOBALS['container']->get(CasaPeriodoRepositoryInterface::class);
            $cCasaPeriodos = $repo->getCasaPeriodos($aWhere, $aOperador);
        }

        $a_cabeceras = [];
        $a_cabeceras[] = _('desde');
        $a_cabeceras[] = _('hasta');
        $a_cabeceras[] = _('asignado a');

        $i = 0;
        $a_valores = [];
        $cPeriodos = [];
        $oTipoActividad = new TiposActividades();
        foreach ($cCasaPeriodos as $oCasaPeriodo) {
            $i++;
            $id_item = $oCasaPeriodo->getId_item();
            $oF_ini = $oCasaPeriodo->getF_ini();
            $oF_fin = $oCasaPeriodo->getF_fin();
            $f_ini = $oF_ini->getFromLocal();
            $f_fin = $oF_fin->getFromLocal();
            $sfsv = $oCasaPeriodo->getSfsv();

            $cPeriodos[] = ['inicio' => $oF_ini, 'fin' => $oF_fin, 'desc' => 'periodo cdc'];

            $oTipoActividad->setSfsvId($sfsv);
            $ssfsv = $oTipoActividad->getSfsvText();

            $a_valores[$i][1] = $f_ini;
            $a_valores[$i][2] = $f_fin;
            $script = "fnjs_modificar($id_item)";
            $a_valores[$i][3] = ['script' => $script, 'valor' => $ssfsv];
        }

        $oDate = new DateTimeLocal();
        $overlap_error = $oDate->comprobarSolapes($cPeriodos);

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'overlap_error' => $overlap_error ?: '',
            'show_nuevo' => true,
        ];
    }
}
