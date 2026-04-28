<?php

declare(strict_types=1);

namespace src\ubis\application;

use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Delegaciones de una región STGR para desplegables (id_dl => sigla_dl).
 */
final class DelegacionesRegionStgrData
{
    /**
     * @return array{a_delegaciones: array<int|string, string>}
     */
    public static function execute(string $region_stgr): array
    {
        $repo = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);

        return ['a_delegaciones' => $repo->getArrayDlRegionStgr([$region_stgr])];
    }
}
