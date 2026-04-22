<?php

namespace src\notas\application;

use core\ConfigGlobal;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Lista las actas en un rango de fechas (ISO) ordenadas por nivel y
 * fecha. En ambito `rstgr` considera todas las delegaciones de la
 * region de stgr; en los demas ambitos, solo la delegacion actual.
 *
 * Cada item es un array asociativo `{id_nivel, acta, f_acta, nombre_corto}`.
 */
final class ListadoAnualActasData
{
    /**
     * @param string $inicioIso Fecha inicio inclusive (Y-m-d)
     * @param string $finIso    Fecha fin inclusive (Y-m-d)
     * @return array<int, array{id_nivel:int, acta:string, f_acta:string, nombre_corto:string}>
     */
    public static function execute(string $inicioIso, string $finIso): array
    {
        $aWhere = [
            'f_acta' => "'$inicioIso','$finIso'",
        ];
        $aOperador = ['f_acta' => 'BETWEEN'];

        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $mi_dele = ConfigGlobal::mi_delef();
            $repoDl = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
            $aDl = array_values($repoDl->getArrayDlRegionStgr([$mi_dele]));
            $Qacta_dl = '';
            foreach ($aDl as $dl) {
                $Qacta_dl .= empty($Qacta_dl) ? '' : '|';
                $Qacta_dl .= "$dl ";
            }
            $aWhere['acta'] = '^(' . $Qacta_dl . ')';
            $aOperador['acta'] = '~';
            $ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
        } else {
            $ActaRepository = $GLOBALS['container']->get(ActaDlRepositoryInterface::class);
        }

        $cActas = $ActaRepository->getActas($aWhere, $aOperador);

        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $aActas = [];
        $aNivel = [];
        $aFecha = [];
        $i = 0;
        foreach ($cActas as $oActa) {
            $i++;
            $id_asignatura = $oActa->getId_asignatura();
            $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \RuntimeException(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            $nombre_corto = $oAsignatura->getNombre_corto();
            if ($nombre_corto === null) {
                $nombre_corto = '???';
                $id_nivel = 0;
            } else {
                $id_nivel = (int)$oAsignatura->getId_nivel();
            }

            $oF_acta = $oActa->getF_acta();
            $aActas[$i] = [
                'id_nivel' => $id_nivel,
                'acta' => $oActa->getActa(),
                'f_acta' => $oF_acta->getFromLocal(),
                'nombre_corto' => $nombre_corto,
            ];
            $aNivel[$i] = $id_nivel;
            $aFecha[$i] = $oF_acta->format('Y-m-d');
        }

        if (!empty($aActas)) {
            array_multisort($aNivel, SORT_NUMERIC, $aFecha, SORT_NUMERIC, $aActas);
        }

        return $aActas;
    }
}
