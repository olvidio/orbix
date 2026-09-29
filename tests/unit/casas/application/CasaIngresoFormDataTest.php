<?php

declare(strict_types=1);

namespace Tests\unit\casas\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\casas\application\CasaIngresoFormData;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\shared\security\HashB;

final class CasaIngresoFormDataTest extends TestCase
{
    private mixed $previousOPermActividades = null;
    private bool $hadOPermActividades = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hadOPermActividades = array_key_exists('oPermActividades', $_SESSION ?? []);
        $this->previousOPermActividades = $_SESSION['oPermActividades'] ?? null;
        unset($_SESSION['oPermActividades']);
    }

    protected function tearDown(): void
    {
        if ($this->hadOPermActividades) {
            $_SESSION['oPermActividades'] = $this->previousOPermActividades;
        } else {
            unset($_SESSION['oPermActividades']);
        }
        parent::tearDown();
    }

    public function test_sin_id_activ_devuelve_error(): void
    {
        $useCase = new CasaIngresoFormData(
            $this->createMock(TipoTarifaRepositoryInterface::class),
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(IngresoRepositoryInterface::class),
        );

        $rta = $useCase->execute([]);
        $this->assertFalse($rta['ok']);
    }

    public function test_ok_emite_ctx_guardar_y_ctx_eliminar_atados_a_id_activ(): void
    {
        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getNom_activ')->willReturn('Curso test');
        $actividad->method('getId_tipo_activ')->willReturn(112001);
        $actividad->method('getDl_org')->willReturn('mad');
        $actividad->method('getTarifa')->willReturn(null);
        $actividad->method('getPrecio')->willReturn(0.0);

        $actividadRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actividadRepo->method('findById')->with(42)->willReturn($actividad);

        $ingresoRepo = $this->createMock(IngresoRepositoryInterface::class);
        $ingresoRepo->method('findById')->willReturn(null);

        $useCase = new CasaIngresoFormData(
            $this->createMock(TipoTarifaRepositoryInterface::class),
            $actividadRepo,
            $ingresoRepo,
        );

        $rta = $useCase->execute(['id_activ' => 42]);

        $this->assertTrue($rta['ok']);
        $this->assertSame(42, $rta['id_activ']);
        $this->assertSame(['id_activ' => 42], HashB::open($rta['ctx_guardar'], 'casa_ingreso_update'));
        $this->assertSame(['id_activ' => 42], HashB::open($rta['ctx_eliminar'], 'casa_ingreso_eliminar'));
    }
}
