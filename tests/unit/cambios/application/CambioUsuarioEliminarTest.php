<?php

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\cambios\application\CambioUsuarioEliminar;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\domain\entity\CambioUsuario;

final class CambioUsuarioEliminarTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_sel_vacio_devuelve_ok(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            CambioUsuarioRepositoryInterface::class => $this->createMock(CambioUsuarioRepositoryInterface::class),
        ]);

        $this->assertSame(
            ['ok' => true, 'mensaje' => ''],
            CambioUsuarioEliminar::execute(['sel' => []])
        );
    }

    public function test_si_getCambiosUsuario_devuelve_false_continua_sin_error(): void
    {
        $repo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getCambiosUsuario')
            ->with([
                'id_item_cambio' => 10,
                'id_usuario' => 20,
                'sfsv' => 1,
                'aviso_tipo' => 3,
            ])
            ->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            CambioUsuarioRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(
            ['ok' => true, 'mensaje' => ''],
            CambioUsuarioEliminar::execute(['sel' => ['10#20#1#3']])
        );
    }

    public function test_exito_cuando_dbeliminar_true(): void
    {
        $o = $this->getMockBuilder(CambioUsuario::class)->addMethods(['DBEliminar', 'getErrorTxt'])->getMock();
        $o->method('DBEliminar')->willReturn(true);

        $repo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $repo->method('getCambiosUsuario')->willReturn([$o]);

        $GLOBALS['container'] = $this->containerFromMap([
            CambioUsuarioRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(
            ['ok' => true, 'mensaje' => ''],
            CambioUsuarioEliminar::execute(['sel' => ['1#2#0#0']])
        );
    }

    public function test_error_cuando_dbeliminar_false(): void
    {
        $o = $this->getMockBuilder(CambioUsuario::class)->addMethods(['DBEliminar', 'getErrorTxt'])->getMock();
        $o->method('DBEliminar')->willReturn(false);
        $o->method('getErrorTxt')->willReturn('fallo sql');

        $repo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $repo->method('getCambiosUsuario')->willReturn([$o]);

        $GLOBALS['container'] = $this->containerFromMap([
            CambioUsuarioRepositoryInterface::class => $repo,
        ]);

        $rta = CambioUsuarioEliminar::execute(['sel' => ['9#8#7#6']]);
        $this->assertFalse($rta['ok']);
        $this->assertStringContainsString('fallo sql', $rta['mensaje']);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
