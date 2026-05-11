<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\CentrosGetNumData;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Lista de centros para la vista "números" (payload JSON vía {@see CentrosGetNumData::execute}).
 */
final class CentrosGetNumDataTest extends TestCase
{
    private mixed $previousContainer = null;

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

    public function test_execute_cumple_contrato_de_claves(): void
    {
        $GLOBALS['container'] = $this->containerConCentros([]);
        $data = CentrosGetNumData::execute();

        $this->assertSame(['a_cabeceras', 'a_valores'], array_keys($data));
        $this->assertCount(4, $data['a_cabeceras']);
        $this->assertIsArray($data['a_valores']);
    }

    public function test_execute_construye_filas_indexadas_y_rellena_ceros_en_vacios(): void
    {
        $centro = new class {
            public function getId_ubi(): int
            {
                return 3;
            }
            public function getNombre_ubi(): string
            {
                return 'N1';
            }
            public function getN_buzon(): string
            {
                return 'B1';
            }
            public function getNum_pi(): null
            {
                return null;
            }
            public function getNum_cartas(): null
            {
                return null;
            }
        };
        $GLOBALS['container'] = $this->containerConCentros([$centro]);

        $data = CentrosGetNumData::execute();

        $this->assertSame([
            1 => [
                1 => ['script' => 'fnjs_modificar(3,"num")', 'valor' => 'N1'],
                2 => 'B1',
                3 => '0',
                4 => '0',
            ],
        ], $data['a_valores']);
    }

    /**
     * @param list<object> $centros
     */
    private function containerConCentros(array $centros): object
    {
        return new class($centros) {
            public function __construct(private readonly array $centros) {}

            public function get(string $key): object
            {
                if ($key !== CentroDlRepositoryInterface::class) {
                    throw new \RuntimeException("Clave inesperada: $key");
                }
                return new class($this->centros) {
                    public function __construct(private readonly array $centros) {}

                    public function getCentros(array $_where = [], array $_operators = []): array
                    {
                        return $this->centros;
                    }
                };
            }
        };
    }
}
