<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\cambios\application\RegistrarCambio;
use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\shared\application\listeners\RegistrarCambioListener;
use src\shared\domain\events\EntidadModificada;
use src\shared\domain\value_objects\DateTimeLocal;

final class RegistrarCambioListenerTest extends TestCase
{
    /** @var list<Cambio> */
    private array $guardadosDl = [];

    protected function setUp(): void
    {
        $_SESSION['session_auth'] = [
            'id_usuario' => 443,
            'sfsv' => 1,
        ];
        $_SESSION['config'] = [
            'a_apps' => ['cambios' => 99002],
            'app_installed' => [99002],
        ];
        $this->guardadosDl = [];
    }

    public function test_listener_registra_update_actividad_dl_en_repositorio_dl(): void
    {
        $listener = new RegistrarCambioListener($this->createRegistrarCambio());

        $datosBase = [
            'id_tipo_activ' => 112401,
            'status' => 2,
            'dl_org' => 'dlb',
            'nom_activ' => 'Curso test',
        ];

        $listener(new EntidadModificada(
            objeto: 'ActividadDl',
            tipoCambio: 'UPDATE',
            idActiv: 300123817,
            datosNuevos: $datosBase + ['f_ini' => new DateTimeLocal('2026-07-07')],
            datosActuales: $datosBase + ['f_ini' => new DateTimeLocal('2026-01-15')],
        ));

        $this->assertCount(1, $this->guardadosDl);
        $this->assertSame('ActividadDl', $this->guardadosDl[0]->getObjeto());
        $this->assertSame('f_ini', $this->guardadosDl[0]->getPropiedad());
        $this->assertSame('2026-01-15', $this->guardadosDl[0]->getValor_old());
        $this->assertSame('2026-07-07', $this->guardadosDl[0]->getValor_new());
    }

    private function createRegistrarCambio(): RegistrarCambio
    {
        $cambioRepository = $this->createMock(CambioRepositoryInterface::class);
        $cambioRepository->expects($this->never())->method('Guardar');

        $cambioDlRepository = $this->createMock(CambioDlRepositoryInterface::class);
        $cambioDlRepository->method('getNewId')->willReturn(900001);
        $cambioDlRepository->expects($this->any())
            ->method('Guardar')
            ->willReturnCallback(function (Cambio $cambio): bool {
                $this->guardadosDl[] = $cambio;

                return true;
            });

        return new RegistrarCambio(
            $this->createMock(ActividadAllRepositoryInterface::class),
            $cambioDlRepository,
            $cambioRepository,
            $this->createMock(ActividadProcesoTareaRepositoryInterface::class),
        );
    }
}
