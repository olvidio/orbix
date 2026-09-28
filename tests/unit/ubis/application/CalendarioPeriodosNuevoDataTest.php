<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\shared\security\HashB;
use src\ubis\application\CalendarioPeriodosNuevoData;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;

/**
 * Datos del formulario de alta de un periodo de calendario
 * ({@see CalendarioPeriodosNuevoData::execute}).
 */
final class CalendarioPeriodosNuevoDataTest extends TestCase
{
    public function test_execute_sin_periodos_previos_devuelve_campos_vacios(): void
    {
        $repo = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $repo->method('getCasaPeriodos')->willReturn([]);

        $useCase = new CalendarioPeriodosNuevoData($repo);
        $data = $useCase->execute(5, 2024);

        $this->assertSame('', $data['f_next']);
        $this->assertSame('', $data['sf_chk']);
        $this->assertSame('', $data['sv_chk']);
    }

    public function test_execute_emite_ctx_guardar_atado_a_id_ubi_con_id_item_cero(): void
    {
        $repo = $this->createMock(CasaPeriodoRepositoryInterface::class);
        $repo->method('getCasaPeriodos')->willReturn([]);

        $useCase = new CalendarioPeriodosNuevoData($repo);
        $data = $useCase->execute(7, 2024);

        $this->assertSame(
            ['id_item' => 0, 'id_ubi' => 7],
            HashB::open($data['ctx_guardar'], 'calendario_periodo_guardar')
        );
    }
}
