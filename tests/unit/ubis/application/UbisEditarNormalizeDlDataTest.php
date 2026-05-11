<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\UbisEditarNormalizeDlData;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

final class UbisEditarNormalizeDlDataTest extends TestCase
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

    public function test_ctrdl_nombre_coincide_devuelve_CentroDl(): void
    {
        $centro = $this->createMock(\src\ubis\domain\entity\CentroDl::class);
        $centro->method('getNombre_ubi')->willReturn('Casa madre');

        $repo = $this->createMock(CentroDlRepositoryInterface::class);
        $repo->method('findById')->with(9)->willReturn($centro);

        $GLOBALS['container'] = $this->containerFromMap([
            CentroDlRepositoryInterface::class => $repo,
            CasaDlRepositoryInterface::class => $this->createMock(CasaDlRepositoryInterface::class),
        ]);

        $this->assertSame(
            'CentroDl',
            UbisEditarNormalizeDlData::execute(9, 'ctrdl', 'Casa madre', 'PersonaGlobal')
        );
    }

    public function test_ctrdl_nombre_distinto_mantiene_obj_pau(): void
    {
        $centro = $this->createMock(\src\ubis\domain\entity\CentroDl::class);
        $centro->method('getNombre_ubi')->willReturn('Otro nombre');

        $repo = $this->createMock(CentroDlRepositoryInterface::class);
        $repo->method('findById')->willReturn($centro);

        $GLOBALS['container'] = $this->containerFromMap([
            CentroDlRepositoryInterface::class => $repo,
            CasaDlRepositoryInterface::class => $this->createMock(CasaDlRepositoryInterface::class),
        ]);

        $this->assertSame(
            'PersonaX',
            UbisEditarNormalizeDlData::execute(1, 'ctrdl', 'Cache', 'PersonaX')
        );
    }

    public function test_cdcdl_nombre_coincide_devuelve_CasaDl(): void
    {
        $casa = $this->createMock(\src\ubis\domain\entity\Casa::class);
        $casa->method('getNombre_ubi')->willReturn('Villa');

        $repo = $this->createMock(CasaDlRepositoryInterface::class);
        $repo->method('findById')->with(3)->willReturn($casa);

        $GLOBALS['container'] = $this->containerFromMap([
            CentroDlRepositoryInterface::class => $this->createMock(CentroDlRepositoryInterface::class),
            CasaDlRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(
            'CasaDl',
            UbisEditarNormalizeDlData::execute(3, 'cdcdl', 'Villa', 'Foo')
        );
    }

    public function test_tipo_desconocido_devuelve_obj_pau(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            CentroDlRepositoryInterface::class => $this->createMock(CentroDlRepositoryInterface::class),
            CasaDlRepositoryInterface::class => $this->createMock(CasaDlRepositoryInterface::class),
        ]);

        $this->assertSame(
            'Original',
            UbisEditarNormalizeDlData::execute(1, 'otro', 'x', 'Original')
        );
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
