<?php

namespace Tests\unit\zonassacd\domain;

use PHPUnit\Framework\TestCase;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\InfoZona;

/**
 * Unitarios para {@see InfoZona}.
 *
 * Verifica la configuracion estatica que expone la clase (clase
 * gestionada, metodo del gestor y repo) ademas del comportamiento
 * de `getColeccion()` en sus dos ramas (sin/con `k_buscar`).
 */
final class InfoZonaTest extends TestCase
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

    public function test_configuracion_por_defecto(): void
    {
        $info = new InfoZona();

        $this->assertSame('src\\zonassacd\\domain\\entity\\Zona', $info->getClase());
        $this->assertSame('getZonas', $info->getMetodoGestor());
        $this->assertSame(ZonaRepositoryInterface::class, $info->getRepositoryInterface());
        $this->assertSame('', $info->getTxtExplicacion());
        $this->assertNotSame('', $info->getTxtTitulo());
        $this->assertNotSame('', $info->getTxtEliminar());
        $this->assertNotSame('', $info->getTxtBuscar());
    }

    public function test_getColeccion_sin_k_buscar_pide_todas_ordenadas(): void
    {
        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->expects($this->once())
            ->method('getZonas')
            ->with(['_ordre' => 'orden'], [])
            ->willReturn(['zona-a', 'zona-b']);

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaRepositoryInterface::class => $zonaRepo,
        ]);

        $info = new InfoZona();
        $info->setK_buscar('');

        $this->assertSame(['zona-a', 'zona-b'], $info->getColeccion());
    }

    public function test_getColeccion_con_k_buscar_usa_filtro_sin_acentos(): void
    {
        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->expects($this->once())
            ->method('getZonas')
            ->with(['nom' => 'nord'], ['nom' => 'sin_acentos'])
            ->willReturn(['zona-nord']);

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaRepositoryInterface::class => $zonaRepo,
        ]);

        $info = new InfoZona();
        $info->setK_buscar('nord');

        $this->assertSame(['zona-nord'], $info->getColeccion());
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
