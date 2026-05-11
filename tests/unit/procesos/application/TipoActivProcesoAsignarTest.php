<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TipoDeActividad;
use src\procesos\application\TipoActivProcesoAsignar;

/**
 * Solo la rama `propio` falsa: usa {@see TipoDeActividad::setId_tipo_proceso_ex}.
 * La rama `propio` verdadera llama a un método privado en la entidad (código roto).
 */
final class TipoActivProcesoAsignarTest extends TestCase
{
    private mixed $previousContainer;

    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], ['sfsv' => 2]);
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_exito_proceso_no_propio(): void
    {
        $tipo = $this->createMock(TipoDeActividad::class);
        $tipo->expects($this->once())
            ->method('setId_tipo_proceso_ex')
            ->with(7, 2);

        $repo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $repo->method('findById')->with(100)->willReturn($tipo);
        $repo->expects($this->once())->method('Guardar')->with($tipo)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            TipoDeActividadRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', (new TipoActivProcesoAsignar())->execute([
            'id_tipo_activ' => 100,
            'propio' => 'f',
            'id_tipo_proceso' => 7,
        ]));
    }

    public function test_falla_guardar(): void
    {
        $tipo = $this->createMock(TipoDeActividad::class);
        $tipo->method('setId_tipo_proceso_ex');

        $repo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $repo->method('findById')->willReturn($tipo);
        $repo->method('Guardar')->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            TipoDeActividadRepositoryInterface::class => $repo,
        ]);

        $this->assertNotSame('', (new TipoActivProcesoAsignar())->execute([
            'id_tipo_activ' => 1,
            'propio' => '',
            'id_tipo_proceso' => 2,
        ]));
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
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
