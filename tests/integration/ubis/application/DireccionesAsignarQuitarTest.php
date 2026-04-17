<?php

namespace Tests\integration\ubis\application;

use src\ubis\application\DireccionesAsignar;
use src\ubis\application\DireccionesQuitar;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use Tests\factories\ubis\CentroDlFactory;
use Tests\factories\ubis\DireccionFactory;
use Tests\myTest;

/**
 * Tests de integración para DireccionesAsignar y DireccionesQuitar.
 *
 * Caso base: centro dl + dirección dl → asignar → quitar.
 */
class DireccionesAsignarQuitarTest extends myTest
{
    private CentroDlRepositoryInterface $centroRepo;
    private DireccionCentroDlRepositoryInterface $direccionRepo;
    private RelacionCentroDlDireccionRepositoryInterface $relacionRepo;
    private int $id_ubi;
    private int $id_direccion;

    public function setUp(): void
    {
        parent::setUp();
        $this->centroRepo = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $this->direccionRepo = $GLOBALS['container']->get(DireccionCentroDlRepositoryInterface::class);
        $this->relacionRepo = $GLOBALS['container']->get(RelacionCentroDlDireccionRepositoryInterface::class);

        $oCentro = (new CentroDlFactory())->createSimple();
        $this->id_ubi = $oCentro->getId_ubi();
        $this->centroRepo->Guardar($oCentro);

        $oDireccion = (new DireccionFactory())->createSimple();
        $this->id_direccion = $oDireccion->getId_direccion();
        $this->direccionRepo->Guardar($oDireccion);
    }

    public function tearDown(): void
    {
        $this->relacionRepo->desasociarDireccion($this->id_ubi, $this->id_direccion);

        $oDir = $this->direccionRepo->findById($this->id_direccion);
        if ($oDir !== null) {
            $this->direccionRepo->Eliminar($oDir);
        }
        $oCentro = $this->centroRepo->findById($this->id_ubi);
        if ($oCentro !== null) {
            $this->centroRepo->Eliminar($oCentro);
        }
        parent::tearDown();
    }

    public function test_asignar_y_quitar_direccion_a_centroDl(): void
    {
        $this->assertFalse(
            $this->relacionRepo->existeRelacion($this->id_ubi, $this->id_direccion),
            'La relación no debería existir antes de asignar.'
        );

        $result = DireccionesAsignar::execute($this->id_ubi, 'DireccionCentroDl', $this->id_direccion);
        $this->assertSame(['ok' => true], $result);

        $this->assertTrue(
            $this->relacionRepo->existeRelacion($this->id_ubi, $this->id_direccion),
            'La relación debería existir tras asignar.'
        );

        $csv = (string)$this->id_direccion;
        $result = DireccionesQuitar::execute($this->id_ubi, 0, 'DireccionCentroDl', $csv);
        $this->assertSame(['ok' => true], $result);

        $this->assertFalse(
            $this->relacionRepo->existeRelacion($this->id_ubi, $this->id_direccion),
            'La relación debería estar quitada tras DireccionesQuitar.'
        );
    }

    public function test_quitar_con_idx_fuera_de_rango_no_toca_relaciones_existentes(): void
    {
        DireccionesAsignar::execute($this->id_ubi, 'DireccionCentroDl', $this->id_direccion);

        $csv = '11111,22222';
        $result = DireccionesQuitar::execute($this->id_ubi, 5, 'DireccionCentroDl', $csv);
        $this->assertSame(['ok' => true], $result);

        $this->assertTrue(
            $this->relacionRepo->existeRelacion($this->id_ubi, $this->id_direccion),
            'La relación real no debería verse afectada por un idx fuera de rango.'
        );
    }

    public function test_asignar_con_obj_dir_desconocido_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DireccionesAsignar::execute($this->id_ubi, 'NoExiste', $this->id_direccion);
    }

    public function test_quitar_con_obj_dir_desconocido_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DireccionesQuitar::execute($this->id_ubi, 0, 'NoExiste', '1');
    }
}
