<?php

namespace src\ubis\application\services;

use src\ubis\domain\criteria\DelegacionField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
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
        $criteria = Criteria::create()->where(Filter::eq(F::Active, true))->orderBy(F::Dl);
        if (!empty($regionesStgr)) {
            $criteria = $criteria->andWhere(Filter::in(F::RegionStgr, array_values($regionesStgr)));
        }
        $delegaciones = $this->delegacionRepository->matching($criteria);
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
