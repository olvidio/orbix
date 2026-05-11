<?php

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\TestCase;
use src\asistentes\application\AsistentePlazaAsignar;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\entity\Asistente;

final class AsistentePlazaAsignarTest extends TestCase
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

    public function test_falta_id_activ(): void
    {
        $GLOBALS['container'] = $this->containerOne(
            AsistenteApplicationService::class,
            $this->createMock(AsistenteApplicationService::class)
        );

        $this->assertNotSame('', AsistentePlazaAsignar::execute(['id_activ' => 0, 'lista_json' => '[]']));
    }

    public function test_lista_vacia_o_invalida(): void
    {
        $GLOBALS['container'] = $this->containerOne(
            AsistenteApplicationService::class,
            $this->createMock(AsistenteApplicationService::class)
        );

        $this->assertNotSame('', AsistentePlazaAsignar::execute([
            'id_activ' => 1,
            'lista_json' => '[]',
        ]));
    }

    public function test_asigna_plaza_y_guarda(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);
        $o->expects($this->once())->method('setPlazaComprobando')->with(3);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(9, 5)->willReturn($o);
        $app->expects($this->once())->method('guardar')->with($o)->willReturn(true);

        $GLOBALS['container'] = $this->containerOne(AsistenteApplicationService::class, $app);

        $json = json_encode([(object)['value' => '5#resto']]);
        $this->assertSame('', AsistentePlazaAsignar::execute([
            'id_activ' => 9,
            'plaza' => 3,
            'lista_json' => $json,
        ]));
    }

    public function test_plaza_vacia_llama_setPlaza_null(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);
        $o->expects($this->once())->method('setPlaza')->with(null);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->method('guardar')->willReturn(true);

        $GLOBALS['container'] = $this->containerOne(AsistenteApplicationService::class, $app);

        $json = json_encode([(object)['value' => '8#']]);
        $this->assertSame('', AsistentePlazaAsignar::execute([
            'id_activ' => 1,
            'plaza' => '',
            'lista_json' => $json,
        ]));
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
