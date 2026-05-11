<?php

namespace Tests\unit\cartaspresentacion\application;

use PHPUnit\Framework\TestCase;
use src\cartaspresentacion\application\CartasPresentacionUbisListaData;
use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;

final class CartasPresentacionUbisListaDataTest extends TestCase
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

    public function test_tipo_lista_desconocido_sin_filas(): void
    {
        $rta = CartasPresentacionUbisListaData::execute(['tipo_lista' => '']);
        $this->assertSame('', $rta['tipo_lista']);
        $this->assertSame([], $rta['a_valores']);
    }

    public function test_get_dl_sin_poblacion_solo_pide_repo_cartas(): void
    {
        $repoCarta = $this->createMock(CartaPresentacionDlRepositoryInterface::class);

        $GLOBALS['container'] = $this->containerFromMap([
            CartaPresentacionDlRepositoryInterface::class => $repoCarta,
        ]);

        $rta = CartasPresentacionUbisListaData::execute([
            'tipo_lista' => 'get_dl',
            'poblacion_sel' => '',
        ]);
        $this->assertSame('get_dl', $rta['tipo_lista']);
        $this->assertSame([], $rta['a_valores']);
        $this->assertNotSame('', $rta['explicacion']);
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
