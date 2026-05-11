<?php

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\ListasComTxtUpdate;
use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\entity\EncargoTexto;

final class ListasComTxtUpdateTest extends TestCase
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

    public function test_actualiza_fila_existente(): void
    {
        $existing = new EncargoTexto();
        $existing->setId_item(1);
        $existing->setClave('clave_a');
        $existing->setIdioma('es_ES.UTF-8');
        $existing->setTexto('viejo');

        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn([$existing]);
        $repo->expects($this->once())->method('Guardar')->with($existing)->willReturn(true);
        $repo->expects($this->never())->method('Eliminar');

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoTextoRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(['ok' => true], ListasComTxtUpdate::execute('clave_a', 'es_ES.UTF-8', 'nuevo'));
        $this->assertSame('nuevo', $existing->getTexto());
    }

    public function test_texto_vacio_elimina_fila_existente(): void
    {
        $existing = new EncargoTexto();
        $existing->setId_item(2);
        $existing->setClave('k');
        $existing->setIdioma('es_ES.UTF-8');
        $existing->setTexto('x');

        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn([$existing]);
        $repo->expects($this->once())->method('Eliminar')->with($existing)->willReturn(true);
        $repo->expects($this->never())->method('Guardar');

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoTextoRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(['ok' => true], ListasComTxtUpdate::execute('k', 'es_ES.UTF-8', ''));
    }

    public function test_inserta_si_no_hay_filas(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn([]);
        $repo->method('getNewId')->willReturn(700);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(function (EncargoTexto $e) {
            $this->assertSame(700, $e->getId_item());
            $this->assertSame('c', $e->getClave());
            $this->assertSame('es_ES.UTF-8', $e->getIdioma());
            $this->assertSame('body', $e->getTexto());
            return true;
        });

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoTextoRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(['ok' => true], ListasComTxtUpdate::execute('c', 'es_ES.UTF-8', 'body'));
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
