<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\CentrosGetLaborData;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\CuadrosLaborBits;
use src\shared\config\ConfigGlobal;

/**
 * Lista de centros para la vista "labor" (payload JSON vía {@see CentrosGetLaborData::execute}).
 */
final class CentrosGetLaborDataTest extends TestCase
{
    private mixed $previousContainer = null;
    private bool $hadSessionSfsv = false;
    private mixed $previousSessionSfsv = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            $_SESSION['session_auth'] = [];
        }
        $this->hadSessionSfsv = array_key_exists('sfsv', $_SESSION['session_auth']);
        $this->previousSessionSfsv = $this->hadSessionSfsv ? $_SESSION['session_auth']['sfsv'] : null;
        $_SESSION['session_auth']['sfsv'] = 1;
    }

    protected function tearDown(): void
    {
        if ($this->hadSessionSfsv) {
            $_SESSION['session_auth']['sfsv'] = $this->previousSessionSfsv;
        } else {
            unset($_SESSION['session_auth']['sfsv']);
        }
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
        $data = CentrosGetLaborData::execute();

        $this->assertSame(['a_cabeceras', 'a_valores', 'tipo_labor_bit_map'], array_keys($data));
        $this->assertIsArray($data['a_cabeceras']);
        $this->assertIsArray($data['a_valores']);
        $this->assertSame(CuadrosLaborBits::labeledMap(ConfigGlobal::mi_sfsv()), $data['tipo_labor_bit_map']);
    }

    public function test_execute_mapea_filas_de_centros(): void
    {
        $centro = new class {
            public function getId_ubi(): int
            {
                return 7;
            }
            public function getNombre_ubi(): string
            {
                return 'C1';
            }
            public function getTipo_ctr(): string
            {
                return 'Z';
            }
            public function getTipo_labor(): int
            {
                return 42;
            }
        };
        $GLOBALS['container'] = $this->containerConCentros([$centro]);

        $data = CentrosGetLaborData::execute();

        $this->assertCount(3, $data['a_cabeceras']);
        $this->assertSame([
            [
                'id_ubi' => 7,
                'nombre_ubi' => 'C1',
                'tipo_ctr' => 'Z',
                'tipo_labor' => 42,
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
