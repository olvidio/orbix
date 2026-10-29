<?php

namespace src\ubis\application;

use src\ubis\domain\criteria\CasaPeriodoField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use DateInterval;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

final class CalendarioPeriodosNuevoData
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
        $f_next = '';
        $sf_chk = '';
        $sv_chk = '';
        if ($idUbi > 0 && $year > 0) {
            $inicio = $year . '-1-1';
            $fin = $year . '-12-31';
            $repo = $this->casaPeriodoRepository;
            $cCasaPeriodos = $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(F::IdUbi, $idUbi))
                    ->andWhere(Filter::gte(F::FIni, $inicio))
                    ->andWhere(Filter::lte(F::FFin, $fin))
                    ->orderBy(F::FFin, OrderDirection::Desc)
            );
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
            'ctx_guardar' => HashB::sign('calendario_periodo_guardar', ['id_item' => 0, 'id_ubi' => $idUbi]),
        ];
    }
}
