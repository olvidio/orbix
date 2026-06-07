<?php

namespace src\notas\application;


use src\shared\config\ConfigGlobal;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Lista las actas en un rango de fechas (ISO) ordenadas por nivel y
 * fecha. En ambito `rstgr` considera todas las delegaciones de la
 * region de stgr; en los demas ambitos, solo la delegacion actual.
 *
 * Cada item es un array asociativo `{id_nivel, acta, f_acta, nombre_corto}`.
 */
final class ListadoAnualActasData
{

    public function __construct(
        private readonly DelegacionRepositoryInterface $delegacionRepository,
        private readonly ActaRepositoryInterface $actaRepository,
        private readonly ActaDlRepositoryInterface $actaDlRepository,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
    }
    /**
     * @param string $inicioIso Fecha inicio inclusive (Y-m-d)
     * @param string $finIso    Fecha fin inclusive (Y-m-d)
     * @return array<int, array{id_nivel:int, acta:string, f_acta:string, nombre_corto:string}>
     */
    public function execute(string $inicioIso, string $finIso): array
    {
        $aWhere = [
            'f_acta' => "'$inicioIso','$finIso'",
        ];
        $aOperador = ['f_acta' => 'BETWEEN'];

        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $mi_dele = ConfigGlobal::mi_delef();
            $repoDl = $this->delegacionRepository;
            $aDl = array_values($repoDl->getArrayDlRegionStgr([$mi_dele]));
            $Qacta_dl = '';
            foreach ($aDl as $dl) {
                $Qacta_dl .= empty($Qacta_dl) ? '' : '|';
                $Qacta_dl .= "$dl ";
            }
            $aWhere['acta'] = '^(' . $Qacta_dl . ')';
            $aOperador['acta'] = '~';
            $ActaRepository = $this->actaRepository;
        } else {
            $ActaRepository = $this->actaDlRepository;
        }

        $cActas = $ActaRepository->getActas($aWhere, $aOperador);

        $AsignaturaRepository = $this->asignaturaRepository;
        $aActas = [];
        $aNivel = [];
        $aFecha = [];
        $i = 0;
        foreach ($cActas as $oActa) {
            $i++;
            $id_asignatura = $oActa->getId_asignatura();
            if ($id_asignatura === null) {
                continue;
            }
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
            $fActaLocal = $oF_acta instanceof DateTimeLocal ? $oF_acta->getFromLocal() : '';
            $fActaIso = $oF_acta instanceof DateTimeLocal ? $oF_acta->format('Y-m-d') : '';
            $aActas[$i] = [
                'id_nivel' => $id_nivel,
                'acta' => $oActa->getActa(),
                'f_acta' => $fActaLocal,
                'nombre_corto' => $nombre_corto,
            ];
            $aNivel[$i] = $id_nivel;
            $aFecha[$i] = $fActaIso;
        }

        if (!empty($aActas)) {
            array_multisort($aNivel, SORT_NUMERIC, $aFecha, SORT_NUMERIC, $aActas);
        }

        return $aActas;
    }
}
