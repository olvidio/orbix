<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\TrasladarUbis;
use src\ubis\domain\contracts\TrasladoUbiRepositoryInterface;

final class TrasladarUbisTest extends TestCase
{
    private TrasladarUbis $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new TrasladarUbis($this->createMock(TrasladoUbiRepositoryInterface::class));
    }

    public function test_sin_seleccion_devuelve_mensaje_error(): void
    {
        $msg = $this->useCase->execute(['dl_dst' => 'cr', 'sel' => []]);
        $this->assertSame(_('No se han seleccionado ubis.'), $msg);
    }

    public function test_sin_clave_sel_devuelve_mensaje_error(): void
    {
        $msg = $this->useCase->execute(['dl_dst' => 'cr']);
        $this->assertSame(_('No se han seleccionado ubis.'), $msg);
    }

    public function test_sel_no_array_se_trata_como_vacio(): void
    {
        $msg = $this->useCase->execute(['dl_dst' => 'cr', 'sel' => 'no-array']);
        $this->assertSame(_('No se han seleccionado ubis.'), $msg);
    }
}
