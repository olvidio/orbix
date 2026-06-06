<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\TipoTarifaListaData;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\permisos\domain\XPermisos;

final class TipoTarifaListaDataTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_lista_vacia_y_sin_permiso_modificar(): void
    {
        $_SESSION['oPerm'] = $this->oPermStub([]);

        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('getTipoTarifas')->willReturn([]);

        $out = (new TipoTarifaListaData($repo))->execute();
        $this->assertSame([], $out['a_valores']);
        $this->assertFalse($out['puede_editar']);
        $this->assertFalse($out['puede_anadir']);
    }

    public function test_fila_con_enlace_modificar_si_coincide_sfsv_y_permiso(): void
    {
        $_SESSION['oPerm'] = $this->oPermStub(['adl' => true]);

        $o = $this->createMock(TipoTarifa::class);
        $o->method('getId_tarifa')->willReturn(10);
        $o->method('getModo')->willReturn(1);
        $o->method('getLetra')->willReturn('Z');
        $o->method('getSfsv')->willReturn(1);
        $o->method('getObserv')->willReturn('ok');

        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('getTipoTarifas')->willReturn([$o]);

        $out = (new TipoTarifaListaData($repo))->execute();
        $this->assertCount(1, $out['a_valores']);
        $this->assertIsArray($out['a_valores'][1][6]);
        $this->assertStringContainsString('fnjs_modificar(10)', $out['a_valores'][1][6]['script']);
        $this->assertTrue($out['puede_editar']);
        $this->assertTrue($out['puede_anadir']);
    }

    /**
     * @param array<string, bool> $perms
     */
    private function oPermStub(array $perms): XPermisos
    {
        $stub = $this->createMock(XPermisos::class);
        $stub->method('have_perm_oficina')->willReturnCallback(
            static fn (string $p): bool => $perms[$p] ?? false
        );

        return $stub;
    }
}
