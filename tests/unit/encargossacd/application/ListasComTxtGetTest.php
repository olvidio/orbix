<?php

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\ListasComTxtGet;
use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\entity\EncargoTexto;

final class ListasComTxtGetTest extends TestCase
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

    public function test_sin_filas_devuelve_texto_vacio(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getEncargoTextos')
            ->with(['clave' => 'c1', 'idioma' => 'es_ES.UTF-8'])
            ->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoTextoRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(['texto' => ''], ListasComTxtGet::execute('c1', 'es_ES.UTF-8'));
    }

    public function test_getEncargoTextos_false_trata_como_vacio(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoTextoRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(['texto' => ''], ListasComTxtGet::execute('k', 'ca_ES.UTF-8'));
    }

    public function test_primera_fila(): void
    {
        $row = $this->createMock(EncargoTexto::class);
        $row->method('getTexto')->willReturn('Hola');

        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn([$row]);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoTextoRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(['texto' => 'Hola'], ListasComTxtGet::execute('x', 'es_ES.UTF-8'));
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
