<?php

namespace src\ubis\application;

use src\ubis\domain\criteria\CasaPeriodoField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

final class CalendarioPeriodosGetData
{
    public function __construct(
        private CasaPeriodoRepositoryInterface $casaPeriodoRepository,
    ) {
    }

    /**
     * @return list<array{id_item: int, id_ubi: int, f_ini: string, f_fin: string, sfsv: int}>
     */
    /**
     * @return array<string, mixed>
     */
    public function execute(int $idUbi): array
    {
        if ($idUbi <= 0) {
            return [];
        }
        $repo = $this->casaPeriodoRepository;
        $cCasaPeriodos = $repo->matching(
            Criteria::create()->where(Filter::eq(F::IdUbi, $idUbi))->orderBy(F::FIni)
        );

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
