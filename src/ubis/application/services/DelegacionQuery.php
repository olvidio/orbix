<?php

namespace src\ubis\application\services;

use src\ubis\domain\contracts\DelegacionRepositoryInterface;

final class DelegacionQuery
{
    public function __construct(
        private DelegacionRepositoryInterface $delegacionRepository,
    ) {
    }

    /**
     * @param list<string> $regionesStgr
     * @return array<int|string, string>
     */
    public function arrayDlByRegionStgr(array $regionesStgr = []): array
    {
        $aWhere = ['status' => true, '_ordre' => 'dl'];
        $aOper = [];
        if (!empty($regionesStgr)) {
            $aWhere['region_stgr'] = "'" . implode("','", $regionesStgr) . "'";
            $aOper['region_stgr'] = 'IN';
        }
        $delegaciones = $this->delegacionRepository->getDelegaciones($aWhere, $aOper) ?: [];
        $out = [];
        foreach ($delegaciones as $dl) {
            $idDl = $dl->getIdDlVo()->value();
            $dlCode = $dl->getDlVo()->value();
            if ($dlCode === null) {
                continue;
            }
            $out[$idDl] = $dlCode;
        }

        return $out;
    }
}
