<?php

namespace Tests\integration\ubis\application;

use src\ubis\application\CentrosFormData;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use Tests\factories\ubis\CentroDlFactory;
use Tests\myTest;

/**
 * Tests de integración para CentrosFormData.
 *
 * Comprueba los 3 modos (labor / num / plazas) sobre un CentroDl real.
 */
class CentrosFormDataTest extends myTest
{
    private CentroDlRepositoryInterface $repository;
    private CentroDlFactory $factory;
    private int $id_ubi;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $this->factory = new CentroDlFactory();

        $oCentro = $this->factory->createSimple();
        $this->id_ubi = $oCentro->getId_ubi();

        $oCentro->setTipo_ctr('nj');
        $oCentro->setTipo_labor(12);
        $oCentro->setN_buzon(9);
        $oCentro->setNum_pi(4);
        $oCentro->setNum_cartas(2);
        $oCentro->setNum_habit_indiv(1);
        $oCentro->setPlazas(50);
        $oCentro->setSede(true);

        $this->repository->Guardar($oCentro);
    }

    public function tearDown(): void
    {
        $oCentro = $this->repository->findById($this->id_ubi);
        if ($oCentro !== null) {
            $this->repository->Eliminar($oCentro);
        }
        parent::tearDown();
    }

    public function test_modo_labor_devuelve_campos_labor(): void
    {
        $result = CentrosFormData::execute($this->id_ubi, CentrosFormData::MODO_LABOR);

        $this->assertSame($this->id_ubi, $result['id_ubi']);
        $this->assertArrayHasKey('nombre_ubi', $result);
        $this->assertSame('nj', $result['tipo_ctr']);
        $this->assertSame(12, $result['tipo_labor']);
        $this->assertArrayNotHasKey('n_buzon', $result);
        $this->assertArrayNotHasKey('plazas', $result);
    }

    public function test_modo_num_devuelve_campos_num(): void
    {
        $result = CentrosFormData::execute($this->id_ubi, CentrosFormData::MODO_NUM);

        $this->assertSame($this->id_ubi, $result['id_ubi']);
        $this->assertSame(9, (int)$result['n_buzon']);
        $this->assertSame(4, (int)$result['num_pi']);
        $this->assertSame(2, (int)$result['num_cartas']);
        $this->assertArrayNotHasKey('tipo_ctr', $result);
    }

    public function test_modo_plazas_devuelve_campos_plazas(): void
    {
        $result = CentrosFormData::execute($this->id_ubi, CentrosFormData::MODO_PLAZAS);

        $this->assertSame($this->id_ubi, $result['id_ubi']);
        $this->assertSame(1, (int)$result['num_habit_indiv']);
        $this->assertSame(50, (int)$result['plazas']);
        $this->assertTrue($result['sede']);
        $this->assertArrayNotHasKey('tipo_ctr', $result);
    }

    public function test_modo_desconocido_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        CentrosFormData::execute($this->id_ubi, 'xxx');
    }

    public function test_id_inexistente_devuelve_defaults(): void
    {
        $result = CentrosFormData::execute(99999999, CentrosFormData::MODO_LABOR);

        $this->assertSame(99999999, $result['id_ubi']);
        $this->assertSame('', $result['nombre_ubi']);
        $this->assertSame('', $result['tipo_ctr']);
        $this->assertSame(0, $result['tipo_labor']);
    }
}
