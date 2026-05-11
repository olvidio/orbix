<?php

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\PlazasBalanceQueData;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Con `id_tipo_activ` fijado se evita la rama de {@see TiposActividades}.
 * {@see \src\ubis\application\services\DelegacionDropdown::activasOrdenNombre} usa el contenedor.
 */
final class PlazasBalanceQueDataTest extends TestCase
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

    public function test_id_tipo_explicito_y_delegaciones_vacias(): void
    {
        $repo = $this->createMock(DelegacionRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getDelegaciones')
            ->with(['active' => true, '_ordre' => 'nombre_dl'])
            ->willReturn([]);

        $GLOBALS['container'] = $this->containerOne(DelegacionRepositoryInterface::class, $repo);

        $out = PlazasBalanceQueData::execute(['id_tipo_activ' => '123456']);
        $this->assertSame('123456', $out['id_tipo_activ']);
        $this->assertSame([], $out['delegaciones_opciones']);
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
