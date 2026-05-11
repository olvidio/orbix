<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\TipoTarifaListaData;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;

final class TipoTarifaListaDataTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_lista_vacia_y_sin_permiso_modificar(): void
    {
        $_SESSION['oPerm'] = $this->permStub(false);

        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('getTipoTarifas')->willReturn([]);

        $GLOBALS['container'] = $this->containerOne(TipoTarifaRepositoryInterface::class, $repo);

        $out = TipoTarifaListaData::execute();
        $this->assertSame([], $out['a_valores']);
        $this->assertFalse($out['puede_editar']);
        $this->assertFalse($out['puede_anadir']);
    }

    public function test_fila_con_enlace_modificar_si_coincide_sfsv_y_permiso(): void
    {
        $_SESSION['oPerm'] = $this->permStub(true);

        $o = $this->createMock(TipoTarifa::class);
        $o->method('getId_tarifa')->willReturn(10);
        $o->method('getModo')->willReturn(1);
        $o->method('getLetra')->willReturn('Z');
        $o->method('getSfsv')->willReturn(1);
        $o->method('getObserv')->willReturn('ok');

        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('getTipoTarifas')->willReturn([$o]);

        $GLOBALS['container'] = $this->containerOne(TipoTarifaRepositoryInterface::class, $repo);

        $out = TipoTarifaListaData::execute();
        $this->assertCount(1, $out['a_valores']);
        $this->assertIsArray($out['a_valores'][1][6]);
        $this->assertStringContainsString('fnjs_modificar(10)', $out['a_valores'][1][6]['script']);
        $this->assertTrue($out['puede_editar']);
        $this->assertTrue($out['puede_anadir']);
    }

    private function permStub(bool $adl): object
    {
        return new class($adl) {
            public function __construct(private readonly bool $adl) {}

            public function have_perm_oficina(string $p): bool
            {
                if ($p === 'adl') {
                    return $this->adl;
                }
                return false;
            }
        };
    }

    /**
     * @param class-string $iface
     */
    private function containerOne(string $iface, object $service): object
    {
        return new class($iface, $service) {
            public function __construct(
                private readonly string $iface,
                private readonly object $service
            ) {}

            public function get(string $id): object
            {
                if ($id !== $this->iface) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->service;
            }
        };
    }
}
