<?php

namespace Tests\integration\ubis\application;

use src\ubis\application\CentrosUpdate;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use Tests\factories\ubis\CentroDlFactory;
use Tests\myTest;

/**
 * Tests de integración para CentrosUpdate (CentroDl).
 */
class CentrosUpdateTest extends myTest
{
    private CentroDlRepositoryInterface $repository;
    private CentroDlFactory $factory;
    private CentrosUpdate $useCase;
    private int $id_ubi;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $this->useCase = new CentrosUpdate($this->repository);
        $this->factory = new CentroDlFactory();

        $oCentro = $this->factory->createSimple();
        $this->id_ubi = $oCentro->getId_ubi();
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

    public function test_sin_id_ubi_retorna_vacio_y_no_modifica(): void
    {
        $msg = $this->useCase->execute([]);
        $this->assertSame('', $msg);
    }

    public function test_id_ubi_inexistente_retorna_vacio_sin_errores(): void
    {
        $msg = $this->useCase->execute(['id_ubi' => 99999999, 'tipo_ctr' => 'X']);
        $this->assertSame('', $msg);
    }

    public function test_actualiza_campos_de_labor(): void
    {
        $oOriginal = $this->repository->findById($this->id_ubi);
        $oOriginal->setN_buzon(4);
        $oOriginal->setNum_pi(5);
        $oOriginal->setNum_cartas(6);
        $oOriginal->setPlazas(25);
        $this->repository->Guardar($oOriginal);

        $msg = $this->useCase->execute([
            'id_ubi' => $this->id_ubi,
            'tipo_ctr' => 'nj',
            'labor' => 'si',
            'tipo_labor' => [4, 2],
        ]);
        $this->assertSame('', $msg);

        $oCentro = $this->repository->findById($this->id_ubi);
        $this->assertSame('nj', $oCentro->getTipo_ctr());
        $this->assertSame(6, $oCentro->getTipo_labor());
        $this->assertSame(4, $oCentro->getN_buzon());
        $this->assertSame(5, $oCentro->getNum_pi());
        $this->assertSame(6, $oCentro->getNum_cartas());
        $this->assertSame(25, $oCentro->getPlazas());
    }

    public function test_no_actualiza_tipo_labor_si_labor_no_es_si(): void
    {
        $oOriginal = $this->repository->findById($this->id_ubi);
        $oOriginal->setTipo_labor(8);
        $this->repository->Guardar($oOriginal);

        $this->useCase->execute([
            'id_ubi' => $this->id_ubi,
            'tipo_ctr' => 'X',
            'labor' => 'no',
            'tipo_labor' => [1, 2],
        ]);

        $oCentro = $this->repository->findById($this->id_ubi);
        $this->assertSame(8, $oCentro->getTipo_labor(), 'No debe tocar tipo_labor si labor!=si');
    }

    public function test_actualiza_solo_campos_num_sin_tocar_plazas(): void
    {
        $oOriginal = $this->repository->findById($this->id_ubi);
        $oOriginal->setNum_habit_indiv(9);
        $oOriginal->setPlazas(30);
        $oOriginal->setSede(true);
        $this->repository->Guardar($oOriginal);

        $msg = $this->useCase->execute([
            'id_ubi' => $this->id_ubi,
            'n_buzon' => 3,
            'num_pi' => 7,
            'num_cartas' => 11,
        ]);
        $this->assertSame('', $msg);

        $oCentro = $this->repository->findById($this->id_ubi);
        $this->assertSame(3, $oCentro->getN_buzon());
        $this->assertSame(7, $oCentro->getNum_pi());
        $this->assertSame(11, $oCentro->getNum_cartas());
        $this->assertSame(9, $oCentro->getNum_habit_indiv());
        $this->assertSame(30, $oCentro->getPlazas());
        $this->assertTrue($oCentro->isSede());
    }

    public function test_actualiza_solo_campos_plazas_sin_tocar_num(): void
    {
        $oOriginal = $this->repository->findById($this->id_ubi);
        $oOriginal->setN_buzon(4);
        $oOriginal->setNum_pi(8);
        $oOriginal->setNum_cartas(12);
        $this->repository->Guardar($oOriginal);

        $msg = $this->useCase->execute([
            'id_ubi' => $this->id_ubi,
            'num_habit_indiv' => 5,
            'plazas' => 20,
            'sede' => 'true',
        ]);
        $this->assertSame('', $msg);

        $oCentro = $this->repository->findById($this->id_ubi);
        $this->assertSame(4, $oCentro->getN_buzon());
        $this->assertSame(8, $oCentro->getNum_pi());
        $this->assertSame(12, $oCentro->getNum_cartas());
        $this->assertSame(5, $oCentro->getNum_habit_indiv());
        $this->assertSame(20, $oCentro->getPlazas());
        $this->assertTrue($oCentro->isSede());
    }

    public function test_sede_false_si_formulario_plazas_no_marca_checkbox(): void
    {
        $oOriginal = $this->repository->findById($this->id_ubi);
        $oOriginal->setSede(true);
        $this->repository->Guardar($oOriginal);

        $this->useCase->execute([
            'id_ubi' => $this->id_ubi,
            'num_habit_indiv' => 2,
            'plazas' => 10,
            'sede' => 'false',
        ]);

        $oCentro = $this->repository->findById($this->id_ubi);
        $this->assertFalse($oCentro->isSede());
    }
}
