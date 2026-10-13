<?php

declare(strict_types=1);

namespace tests\unit\shared\infrastructure\persistence\postgresql;

use PHPUnit\Framework\TestCase;
use src\shared\infrastructure\persistence\postgresql\DBView;

final class DBViewAggregateSchemaTest extends TestCase
{
    public function test_h_y_m_excluyen_el_agregado_e_incluyen_el_cr(): void
    {
        $this->assertTrue(DBView::esEsquemaAgregadoRegionStgr('H-Hv', 'H'));
        $this->assertTrue(DBView::esEsquemaAgregadoRegionStgr('H-H', 'H'));
        $this->assertTrue(DBView::esEsquemaAgregadoRegionStgr('H-Hf', 'H'));
        $this->assertFalse(DBView::esEsquemaAgregadoRegionStgr('H-crHv', 'H'));
        $this->assertFalse(DBView::esEsquemaAgregadoRegionStgr('H-crH', 'H'));
        $this->assertFalse(DBView::esEsquemaAgregadoRegionStgr('H-dlbv', 'H'));

        $this->assertTrue(DBView::esEsquemaAgregadoRegionStgr('M-Mv', 'M'));
        $this->assertTrue(DBView::esEsquemaAgregadoRegionStgr('M-M', 'M'));
        $this->assertTrue(DBView::esEsquemaAgregadoRegionStgr('M-Mf', 'M'));
        $this->assertFalse(DBView::esEsquemaAgregadoRegionStgr('M-crMv', 'M'));
        $this->assertFalse(DBView::esEsquemaAgregadoRegionStgr('M-crM', 'M'));
        $this->assertFalse(DBView::esEsquemaAgregadoRegionStgr('M-crMf', 'M'));
        $this->assertFalse(DBView::esEsquemaAgregadoRegionStgr('M-dlyv', 'M'));
    }

    public function test_otras_regiones_el_agregado_es_el_cr(): void
    {
        $this->assertTrue(DBView::esEsquemaAgregadoRegionStgr('Aut-crAutv', 'Aut'));
        $this->assertTrue(DBView::esEsquemaAgregadoRegionStgr('Aut-crAut', 'Aut'));
        $this->assertTrue(DBView::esEsquemaAgregadoRegionStgr('Pla-crPlaf', 'Pla'));
        $this->assertFalse(DBView::esEsquemaAgregadoRegionStgr('Galbel-dlxv', 'Galbel'));
    }
}
