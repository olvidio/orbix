<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use src\ubis\application\CentrosFormData;
use src\ubis\domain\CuadrosLaborBits;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;

final class CentrosFormDataTest extends TestCase
{
    private bool $hadSessionSfsv = false;
    private mixed $previousSessionSfsv = null;

    protected function setUp(): void
    {
        parent::setUp();

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
        parent::tearDown();
    }

    public function test_modo_desconocido_lanza_InvalidArgumentException(): void
    {
        $useCase = $this->createUseCase(null);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Modo desconocido: foo');
        $useCase->execute(1, 'foo');
    }

    public function test_centro_no_encontrado_devuelve_defaults_en_labor(): void
    {
        $result = $this->createUseCase(null)->execute(123, CentrosFormData::MODO_LABOR);

        $this->assertSame([
            'id_ubi' => 123,
            'nombre_ubi' => '',
            'tipo_ctr' => '',
            'tipo_labor' => 0,
            'tipo_labor_bit_map' => CuadrosLaborBits::labeledMap(1),
        ], $result);
    }

    public function test_modo_labor_devuelve_campos_labor(): void
    {
        $centro = $this->centro([
            'getNombre_ubi' => 'Centro Uno',
            'getTipo_ctr' => 'Z',
            'getTipo_labor' => 42,
        ]);

        $result = $this->createUseCase($centro)->execute(7, CentrosFormData::MODO_LABOR);

        $this->assertSame([
            'id_ubi' => 7,
            'nombre_ubi' => 'Centro Uno',
            'tipo_ctr' => 'Z',
            'tipo_labor' => 42,
            'tipo_labor_bit_map' => CuadrosLaborBits::labeledMap(1),
        ], $result);
    }

    public function test_modo_num_devuelve_campos_numero(): void
    {
        $centro = $this->centro([
            'getNombre_ubi' => 'Centro Dos',
            'getN_buzon' => 10,
            'getNum_pi' => 5,
            'getNum_cartas' => 3,
        ]);

        $result = $this->createUseCase($centro)->execute(8, CentrosFormData::MODO_NUM);

        $this->assertSame([
            'id_ubi' => 8,
            'nombre_ubi' => 'Centro Dos',
            'n_buzon' => 10,
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

        $result = $this->createUseCase($centro)->execute(9, CentrosFormData::MODO_PLAZAS);

        $this->assertSame([
            'id_ubi' => 9,
            'nombre_ubi' => 'Centro Tres',
            'num_habit_indiv' => 2,
            'plazas' => 12,
            'sede' => true,
        ], $result);
    }

    private function createUseCase(?CentroDl $centro): CentrosFormData
    {
        $repo = $this->createMock(CentroDlRepositoryInterface::class);
        $repo->method('findById')->willReturn($centro);

        return new CentrosFormData($repo);
    }

    /**
     * @param array<string, mixed> $props
     */
    private function centro(array $props): CentroDl
    {
        $centro = $this->createMock(CentroDl::class);
        foreach ($props as $method => $value) {
            $centro->method($method)->willReturn($value);
        }

        return $centro;
    }
}
