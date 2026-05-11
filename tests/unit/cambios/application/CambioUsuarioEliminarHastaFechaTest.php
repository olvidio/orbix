<?php

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\cambios\application\CambioUsuarioEliminarHastaFecha;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;

final class CambioUsuarioEliminarHastaFechaTest extends TestCase
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

    public function test_sin_fecha(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            CambioUsuarioRepositoryInterface::class => $this->createMock(CambioUsuarioRepositoryInterface::class),
        ]);

        $rta = CambioUsuarioEliminarHastaFecha::execute([]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_error_si_repositorio_devuelve_false(): void
    {
        $repo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $repo->method('eliminarHastaFecha')->with('2024-01-15')->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            CambioUsuarioRepositoryInterface::class => $repo,
        ]);

        $rta = CambioUsuarioEliminarHastaFecha::execute(['f_fin' => '2024-01-15']);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_exito(): void
    {
        $repo = $this->createMock(CambioUsuarioRepositoryInterface::class);
        $repo->method('eliminarHastaFecha')->with('2024-01-15')->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            CambioUsuarioRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(
            ['ok' => true, 'mensaje' => ''],
            CambioUsuarioEliminarHastaFecha::execute(['f_fin' => '2024-01-15'])
        );
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
