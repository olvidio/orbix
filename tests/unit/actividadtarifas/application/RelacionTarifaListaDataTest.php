<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\RelacionTarifaListaData;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\permisos\domain\XPermisos;

/**
 * Con coleccion vacia no se instancia fila con {@see TiposActividades}.
 */
final class RelacionTarifaListaDataTest extends TestCase
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

    public function test_sin_relaciones(): void
    {
        $_SESSION['oPerm'] = $this->oPermStub([]);

        $repoRel = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repoRel->method('getTipoActivTarifas')->willReturn([]);

        $repoTipo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repoTipo->expects($this->never())->method('findById');

        $out = (new RelacionTarifaListaData($repoRel, $repoTipo))->execute();
        $this->assertSame([], $out['a_valores']);
        $this->assertCount(3, $out['a_cabeceras']);
        $this->assertFalse($out['puede_anadir']);
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
