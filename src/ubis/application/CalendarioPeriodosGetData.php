<?php

namespace src\ubis\application;

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
