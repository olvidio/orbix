<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\misas\application\HorarioTareaData;
use src\shared\security\HashB;

final class HorarioTareaDataTest extends TestCase
{
    protected function setUp(): void
    {
        session_id('test-session-misas-horario');
    }

    public function test_emite_ctx_guardar_y_quitar(): void
    {
        $repo = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $repo->method('findById')->with(7)->willReturn(null);
        $data = (new HorarioTareaData($repo))->getData(['id_item_h' => 7]);

        $this->assertSame('', $data['t_start']);
        $this->assertSame('', $data['t_end']);
        $this->assertSame(['id_item_h' => 7], HashB::open($data['ctx_guardar'], 'guardar_horario'));
        $this->assertSame(['id_item' => 7], HashB::open($data['ctx_quitar'], 'quitar_horario'));
    }
}
