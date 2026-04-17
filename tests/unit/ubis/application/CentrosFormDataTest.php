<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use src\ubis\application\CentrosFormData;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

final class CentrosFormDataTest extends TestCase
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

    public function test_modo_desconocido_lanza_InvalidArgumentException(): void
    {
        $GLOBALS['container'] = $this->containerConCentro(null);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Modo desconocido: foo');
        CentrosFormData::execute(1, 'foo');
    }

    public function test_centro_no_encontrado_devuelve_defaults_en_labor(): void
    {
        $GLOBALS['container'] = $this->containerConCentro(null);
        $result = CentrosFormData::execute(123, CentrosFormData::MODO_LABOR);

        $this->assertSame([
            'id_ubi' => 123,
            'nombre_ubi' => '',
            'tipo_ctr' => '',
            'tipo_labor' => 0,
        ], $result);
    }

    public function test_modo_labor_devuelve_campos_labor(): void
    {
        $centro = $this->centro([
            'getNombre_ubi' => 'Centro Uno',
            'getTipo_ctr' => 'Z',
            'getTipo_labor' => 42,
        ]);
        $GLOBALS['container'] = $this->containerConCentro($centro);

        $result = CentrosFormData::execute(7, CentrosFormData::MODO_LABOR);

        $this->assertSame([
            'id_ubi' => 7,
            'nombre_ubi' => 'Centro Uno',
            'tipo_ctr' => 'Z',
            'tipo_labor' => 42,
        ], $result);
    }

    public function test_modo_num_devuelve_campos_numero(): void
    {
        $centro = $this->centro([
            'getNombre_ubi' => 'Centro Dos',
            'getN_buzon' => 'B10',
            'getNum_pi' => 5,
            'getNum_cartas' => 3,
        ]);
        $GLOBALS['container'] = $this->containerConCentro($centro);

        $result = CentrosFormData::execute(8, CentrosFormData::MODO_NUM);

        $this->assertSame([
            'id_ubi' => 8,
            'nombre_ubi' => 'Centro Dos',
            'n_buzon' => 'B10',
            'num_pi' => 5,
            'num_cartas' => 3,
        ], $result);
    }

    public function test_modo_plazas_devuelve_campos_plazas(): void
    {
        $centro = $this->centro([
            'getNombre_ubi' => 'Centro Tres',
            'getNum_habit_indiv' => 2,
            'getPlazas' => 12,
            'isSede' => true,
        ]);
        $GLOBALS['container'] = $this->containerConCentro($centro);

        $result = CentrosFormData::execute(9, CentrosFormData::MODO_PLAZAS);

        $this->assertSame([
            'id_ubi' => 9,
            'nombre_ubi' => 'Centro Tres',
            'num_habit_indiv' => 2,
            'plazas' => 12,
            'sede' => true,
        ], $result);
    }

    private function containerConCentro(?object $centro): object
    {
        return new class($centro) {
            public function __construct(private readonly ?object $centro) {}
            public function get(string $key): object
            {
                if ($key !== CentroDlRepositoryInterface::class) {
                    throw new \RuntimeException("Clave inesperada: $key");
                }
                return new class($this->centro) {
                    public function __construct(private readonly ?object $centro) {}
                    public function findById(int $id): ?object
                    {
                        return $this->centro;
                    }
                };
            }
        };
    }

    /**
     * @param array<string, mixed> $props
     */
    private function centro(array $props): object
    {
        return new class($props) {
            public function __construct(private readonly array $props) {}
            public function __call(string $name, array $args): mixed
            {
                return $this->props[$name] ?? null;
            }
        };
    }
}
