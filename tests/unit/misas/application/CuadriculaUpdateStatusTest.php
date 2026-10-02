<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\misas\application\CuadriculaUpdate;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\misas\domain\value_objects\PlantillaConfig;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

final class CuadriculaUpdateStatusTest extends TestCase
{
    private const UUID = '11111111-1111-4111-8111-111111111111';

    public function test_plan_sin_estado_guarda_propuesta(): void
    {
        $saved = $this->guardar('p', null, null);

        $this->assertSame(EncargoDiaStatus::STATUS_PROPUESTA, $saved->getStatus());
    }

    public function test_plan_guarda_el_estado_elegido_y_su_color(): void
    {
        $saved = null;
        $repo = $this->repositoryCapturing(null, $saved);
        $out = $this->useCase($repo)->execute(
            self::UUID,
            'AB#10',
            '08:00',
            '08:30',
            '',
            42,
            '2026-08-10',
            PlantillaConfig::PLAN_DE_MISAS,
            3,
            EncargoDiaStatus::STATUS_COMUNICADO_CTR,
        );

        $this->assertSame('', $out['error']);
        $this->assertSame(EncargoDiaStatus::STATUS_COMUNICADO_CTR, $out['meta']['status']);
        $this->assertSame('verdeclaro', $out['meta']['color_misa']);
    }

    public function test_plan_con_estado_invalido_guarda_propuesta(): void
    {
        $saved = $this->guardar(PlantillaConfig::PLAN_DE_MISAS, null, 9);

        $this->assertSame(EncargoDiaStatus::STATUS_PROPUESTA, $saved->getStatus());
    }

    public function test_plantilla_no_cambia_el_estado(): void
    {
        $existente = new EncargoDia();
        $existente->setUuidItemVo(self::UUID);
        $existente->setStatus(EncargoDiaStatus::STATUS_COMUNICADO_SACD);

        $saved = $this->guardar('s1', $existente, EncargoDiaStatus::STATUS_COMUNICADO_CTR);

        $this->assertSame(EncargoDiaStatus::STATUS_COMUNICADO_SACD, $saved->getStatus());
    }

    private function guardar(string $tipo, ?EncargoDia $existente, ?int $status): EncargoDia
    {
        $saved = null;
        $repo = $this->repositoryCapturing($existente, $saved);
        $this->useCase($repo)->execute(
            self::UUID,
            'AB#10',
            '08:00',
            '08:30',
            'nota',
            42,
            '2026-08-10',
            $tipo,
            3,
            $status,
        );

        $this->assertInstanceOf(EncargoDia::class, $saved);

        return $saved;
    }

    /**
     * @param-out EncargoDia|null $saved
     */
    private function repositoryCapturing(?EncargoDia $existente, ?EncargoDia &$saved): EncargoDiaRepositoryInterface
    {
        $repo = $this->createMock(EncargoDiaRepositoryInterface::class);
        $repo->method('findById')->willReturn($existente);
        $repo->method('getEncargoDias')->willReturn([]);
        $repo->method('Guardar')->willReturnCallback(function (EncargoDia $encargo) use (&$saved): bool {
            $saved = $encargo;

            return true;
        });

        return $repo;
    }

    private function useCase(EncargoDiaRepositoryInterface $encargoDiaRepository): CuadriculaUpdate
    {
        $zona = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zona->method('getZonasSacds')->willReturn([]);

        $cargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargos->method('getAsistenteCargoDeActividad')->willReturn([]);

        $horarios = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $horarios->method('getEncargoSacdHorarios')->willReturn([]);

        return new CuadriculaUpdate(
            $encargoDiaRepository,
            $zona,
            $this->createMock(ActividadRepositoryInterface::class),
            $cargos,
            $this->createMock(EncargoRepositoryInterface::class),
            $horarios,
            $this->createStub(InicialesSacdService::class),
        );
    }
}
