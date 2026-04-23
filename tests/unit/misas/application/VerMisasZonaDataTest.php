<?php

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\misas\application\VerMisasZonaData;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

/**
 * Unitarios para {@see VerMisasZonaData::build()}.
 *
 * La funcion procedural `misas_ver_misas_zona_build` resuelve sus
 * dependencias via `$GLOBALS['container']->get(...)`; como PHP no valida
 * en tiempo de analisis los metodos invocados sobre el resultado del
 * contenedor, tests como estos son los que atrapan typos del tipo
 * "usar el repo equivocado" o "llamar a un metodo inexistente".
 *
 * Cubrimos en particular la rama `$Qseleccion & 2`, que es la que en
 * produccion reventaba con "Call to undefined method ... getIdSacdsDeZona()"
 * cuando la aplicacion pedia al contenedor `ZonaRepositoryInterface` en
 * lugar de `ZonaSacdRepositoryInterface`.
 */
final class VerMisasZonaDataTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        // `DateTimeLocal` no se usa en la rama bajo prueba (ISO directo),
        // pero dejamos un idioma por si alguna ruta lateral lo pidiera.
        $_SESSION['session_auth']['idioma'] = 'es_ES.UTF8';
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

    public function test_seleccion_con_bit_2_pide_sacds_al_ZonaSacdRepository_con_metodo_real(): void
    {
        // Regresion: `getIdSacdsDeZona` vive en `ZonaSacdRepositoryInterface`,
        // no en `ZonaRepositoryInterface`. Si la implementacion vuelve a pedir
        // el repo equivocado, el contenedor lanza "Unexpected DI key".
        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->expects($this->once())
            ->method('getIdSacdsDeZona')
            ->with(10)
            ->willReturn([]);

        $encargoRepo = $this->createMock(EncargoRepositoryInterface::class);
        $encargoRepo->expects($this->once())
            ->method('getEncargos')
            ->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
            PersonaSacdRepositoryInterface::class
                => $this->createStub(PersonaSacdRepositoryInterface::class),
            EncargoRepositoryInterface::class => $encargoRepo,
            EncargoDiaRepositoryInterface::class
                => $this->createStub(EncargoDiaRepositoryInterface::class),
        ]);

        $out = VerMisasZonaData::build([
            'id_zona' => 10,
            'seleccion' => 2,
            'empiezamin' => '01/01/2026',
            'empiezamax' => '02/01/2026',
        ]);

        $this->assertSame('', $out['error']);
        $this->assertSame(10, $out['id_zona']);
        $this->assertSame(2, $out['seleccion']);
        $this->assertSame([], $out['data_cuadricula']);
    }

    public function test_sin_bit_2_no_resuelve_ZonaSacdRepository(): void
    {
        // Si `$Qseleccion & 2 === 0`, el repo de zonas-sacd no se toca. Lo
        // omitimos del contenedor para garantizarlo: si el codigo lo pidiese
        // por error, el `get()` lanza una excepcion clara.
        $encargoRepo = $this->createMock(EncargoRepositoryInterface::class);
        $encargoRepo->method('getEncargos')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoRepositoryInterface::class => $encargoRepo,
            EncargoDiaRepositoryInterface::class
                => $this->createStub(EncargoDiaRepositoryInterface::class),
            PersonaSacdRepositoryInterface::class
                => $this->createStub(PersonaSacdRepositoryInterface::class),
        ]);

        $out = VerMisasZonaData::build([
            'id_zona' => 10,
            'seleccion' => 0,
            'empiezamin' => '01/01/2026',
            'empiezamax' => '02/01/2026',
        ]);

        $this->assertSame('', $out['error']);
        $this->assertSame([], $out['data_cuadricula']);
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
