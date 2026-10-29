<?php

namespace src\ubis\application;

use src\ubis\domain\criteria\CasaPeriodoField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;

final class CalendarioPeriodosGet2Data
{
    public function __construct(
        private CasaPeriodoRepositoryInterface $casaPeriodoRepository,
    ) {
    }
    /**
     * @return array<string, mixed>
     */
    public function execute(int $idUbi, int $year): array
    {
        $cCasaPeriodos = [];
        if ($idUbi > 0 && $year > 0) {
            $inicio = $year . '-1-1';
            $fin = $year . '-12-31';
            $repo = $this->casaPeriodoRepository;
            $cCasaPeriodos = $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(F::IdUbi, $idUbi))
                    ->andWhere(Filter::gte(F::FIni, $inicio))
                    ->andWhere(Filter::lte(F::FFin, $fin))
                    ->orderBy(F::FIni)
            );
        }

        $a_cabeceras = [];
        $a_cabeceras[] = _("desde");
        $a_cabeceras[] = _("hasta");
        $a_cabeceras[] = _("asignado a");

        $i = 0;
        $a_valores = [];
        $cPeriodos = [];
        $oTipoActividad = new TiposActividades();
        foreach ($cCasaPeriodos as $oCasaPeriodo) {
            $i++;
            $id_item = $oCasaPeriodo->getId_item();
            $oF_ini = $oCasaPeriodo->getF_ini();
            $oF_fin = $oCasaPeriodo->getF_fin();
            if ($oF_ini === null || $oF_fin === null) {
                continue;
            }
            $f_ini = $oF_ini->getFromLocal();
            $f_fin = $oF_fin->getFromLocal();
            $sfsv = $oCasaPeriodo->getSfsv() ?? 0;

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
