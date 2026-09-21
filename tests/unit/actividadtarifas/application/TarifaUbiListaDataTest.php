<?php

declare(strict_types=1);

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\TarifaUbiListaData;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\permisos\domain\XPermisos;
use src\shared\security\HashB;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\TarifaUbi;

final class TarifaUbiListaDataTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('tarifa-ubi-lista-data-test');
        $this->previousSession = $_SESSION ?? [];
        $_SESSION = [
            'session_auth' => ['sfsv' => 1],
            'oPerm' => $this->permissionMock(),
        ];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_editable_row_embeds_form_token_not_plain_id_item(): void
    {
        $tarifaUbi = $this->createMock(TarifaUbi::class);
        $tarifaUbi->method('getId_item')->willReturn(8);
        $tarifaUbi->method('getId_tarifa')->willReturn(4);
        $tarifaUbi->method('getId_serie')->willReturn(1);
        $tarifaUbi->method('getCantidad')->willReturn(100.0);

        $tipoTarifa = $this->createMock(TipoTarifa::class);
        $tipoTarifa->method('getSfsv')->willReturn(1);
        $tipoTarifa->method('getLetra')->willReturn('A');
        $tipoTarifa->method('getModoTxt')->willReturn('manual');

        $tarifaUbiRepository = $this->createMock(TarifaUbiRepositoryInterface::class);
        $tarifaUbiRepository->method('getTarifaUbis')->willReturn([$tarifaUbi]);
        $relacionRepository = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $relacionRepository->method('getTipoActivTarifas')->willReturn([]);
        $tipoTarifaRepository = $this->createMock(TipoTarifaRepositoryInterface::class);
        $tipoTarifaRepository->method('findById')->with(4)->willReturn($tipoTarifa);

        $data = (new TarifaUbiListaData(
            $tarifaUbiRepository,
            $relacionRepository,
            $tipoTarifaRepository,
        ))->execute(['id_ubi' => 12, 'year' => 2026]);

        $rows = array_values($data['a_valores']);
        $cell = $rows[0][2] ?? null;
        $this->assertIsArray($cell);
        $this->assertStringNotContainsString('fnjs_modificar(8,', (string) ($cell['script'] ?? ''));
        $this->assertNotSame('', $cell['token_form'] ?? '');
        $this->assertSame(
            ['id_item' => 8, 'id_ubi' => 12, 'year' => 2026, 'letra' => 'A'],
            HashB::open((string) $cell['token_form'], 'tarifa_ubi_form')
        );
    }

    private function permissionMock(): XPermisos
    {
        $permission = $this->createMock(XPermisos::class);
        $permission->method('have_perm_oficina')->willReturn(true);

        return $permission;
    }
}
