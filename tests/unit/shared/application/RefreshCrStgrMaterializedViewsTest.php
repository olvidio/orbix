<?php

declare(strict_types=1);

namespace tests\unit\shared\application;

use PHPUnit\Framework\TestCase;
use src\shared\application\RefreshCrStgrMaterializedViews;

final class RefreshCrStgrMaterializedViewsTest extends TestCase
{
    public function test_esquema_requiere_refresh_mv_region_stgr_sv(): void
    {
        $this->assertTrue(RefreshCrStgrMaterializedViews::esquemaRequiereRefreshMv('H-Hv', 'sv'));
        $this->assertTrue(RefreshCrStgrMaterializedViews::esquemaRequiereRefreshMv('M-Mv', 'sv'));
    }

    public function test_esquema_no_requiere_refresh_mv_delegacion_dl(): void
    {
        $this->assertFalse(RefreshCrStgrMaterializedViews::esquemaRequiereRefreshMv('H-dlpv', 'sv'));
        $this->assertFalse(RefreshCrStgrMaterializedViews::esquemaRequiereRefreshMv('H-dlbv', 'sv'));
    }

    public function test_esquema_no_requiere_refresh_mv_cr_region(): void
    {
        $this->assertFalse(RefreshCrStgrMaterializedViews::esquemaRequiereRefreshMv('Aut-crAutv', 'sv'));
        $this->assertFalse(RefreshCrStgrMaterializedViews::esquemaRequiereRefreshMv('Pla-crPlav', 'sv'));
    }
}
