<?php

declare(strict_types=1);

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\zonassacd\application\ZonaCtrUpdate;
use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;

final class ZonaCtrUpdateTest extends TestCase
{
    public function test_escribe_solo_en_zonas_ctr(): void
    {
        $zonaCtr = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zonaCtr->expects($this->once())->method('asignar')->with(1001, 7)->willReturn(true);

        $out = (new ZonaCtrUpdate($zonaCtr))->execute('7', ['1001']);
        $this->assertSame(['tipo' => 'update', 'mensaje' => '', 'error' => ''], $out);
    }

    public function test_id_zona_no_se_normaliza_a_null(): void
    {
        $zonaCtr = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zonaCtr->expects($this->once())->method('asignar')->with(1042, null)->willReturn(true);

        (new ZonaCtrUpdate($zonaCtr))->execute('no', ['1042']);
    }

    public function test_fallo_en_zonas_ctr_acumula_error(): void
    {
        $zonaCtr = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zonaCtr->method('asignar')->willReturn(false);

        $out = (new ZonaCtrUpdate($zonaCtr))->execute('9', ['1001']);

        $this->assertSame("hay un error, no se ha guardado.", $out['mensaje']);
    }
}
