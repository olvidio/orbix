<?php

declare(strict_types=1);

namespace src\ubis\application;

use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Delegaciones de una región STGR para desplegables (id_dl => sigla_dl).
 */
final class DelegacionesRegionStgrData
{
    public function __construct(
        private DelegacionRepositoryInterface $delegacionRepository,
    ) {
    }

    /**
     * @return array{a_delegaciones: array<int|string, string>}
     */
    /**
     * @return array<string, mixed>
     */
    public function execute(string $region_stgr): array
    {
        return ['a_delegaciones' => $this->delegacionRepository->getArrayDlRegionStgr([$region_stgr])];
    }
}
