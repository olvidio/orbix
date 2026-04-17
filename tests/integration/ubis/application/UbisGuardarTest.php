<?php

namespace Tests\integration\ubis\application;

use src\ubis\application\UbisGuardar;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use Tests\factories\ubis\CentroDlFactory;
use Tests\myTest;

/**
 * Tests de integración para UbisGuardar (caso CentroDl).
 */
class UbisGuardarTest extends myTest
{
    private CentroDlRepositoryInterface $repository;
    private CentroDlFactory $factory;
    private array $idsParaLimpiar = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $this->factory = new CentroDlFactory();
    }

    public function tearDown(): void
    {
        foreach ($this->idsParaLimpiar as $id) {
            $o = $this->repository->findById($id);
            if ($o !== null) {
                $this->repository->Eliminar($o);
            }
        }
        parent::tearDown();
    }

    public function test_actualiza_centroDl_existente(): void
    {
        $oCentro = $this->factory->createSimple();
        $id = $oCentro->getId_ubi();
        $this->repository->Guardar($oCentro);
        $this->idsParaLimpiar[] = $id;

        $guardar = new UbisGuardar();
        $msg = $guardar->execute([
            'obj_pau' => 'CentroDl',
            'id_ubi' => $id,
            'tipo_ubi' => 'ctrdl',
            'nombre_ubi' => 'Centro actualizado',
            'dl' => 'cr',
            'region' => 'cr',
            'active' => 'true',
            'tipo_ctr' => 'nj',
            'n_buzon' => 9,
            'num_pi' => 3,
            'num_cartas' => 4,
            'num_habit_indiv' => 2,
            'plazas' => 15,
            'num_cartas_mensuales' => 6,
        ]);
        $this->assertSame('', $msg);

        $oActualizado = $this->repository->findById($id);
        $this->assertNotNull($oActualizado);
        $this->assertSame('Centro actualizado', $oActualizado->getNombre_ubi());
        $this->assertSame('nj', $oActualizado->getTipo_ctr());
        $this->assertSame(9, $oActualizado->getN_buzon());
        $this->assertSame(15, $oActualizado->getPlazas());
        $this->assertTrue($oActualizado->isActive());
    }

    public function test_obj_pau_desconocido_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $guardar = new UbisGuardar();
        $guardar->execute([
            'obj_pau' => 'NoExiste',
            'id_ubi' => 1,
            'nombre_ubi' => 'x',
        ]);
    }
}
