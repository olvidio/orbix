<?php

namespace Tests\integration\ubis\application;

use src\ubis\application\DireccionUpdate;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use Tests\factories\ubis\CentroDlFactory;
use Tests\factories\ubis\DireccionFactory;
use Tests\myTest;

/**
 * Tests de integración para DireccionUpdate (flujo create + update de direcciones
 * sobre un centro dl).
 */
class DireccionUpdateTest extends myTest
{
    private CentroDlRepositoryInterface $centroRepo;
    private DireccionCentroDlRepositoryInterface $direccionRepo;
    private RelacionCentroDlDireccionRepositoryInterface $relacionRepo;
    private int $id_ubi;
    /** @var list<int> */
    private array $idsDireccionesParaLimpiar = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->centroRepo = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $this->direccionRepo = $GLOBALS['container']->get(DireccionCentroDlRepositoryInterface::class);
        $this->relacionRepo = $GLOBALS['container']->get(RelacionCentroDlDireccionRepositoryInterface::class);

        $oCentro = (new CentroDlFactory())->createSimple();
        $this->id_ubi = $oCentro->getId_ubi();
        $this->centroRepo->Guardar($oCentro);
    }

    public function tearDown(): void
    {
        foreach ($this->idsDireccionesParaLimpiar as $idDir) {
            $this->relacionRepo->desasociarDireccion($this->id_ubi, $idDir);
            $oDir = $this->direccionRepo->findById($idDir);
            if ($oDir !== null) {
                $this->direccionRepo->Eliminar($oDir);
            }
        }

        $oCentro = $this->centroRepo->findById($this->id_ubi);
        if ($oCentro !== null) {
            $this->centroRepo->Eliminar($oCentro);
        }
        parent::tearDown();
    }

    public function test_obj_dir_desconocido_devuelve_mensaje(): void
    {
        $msg = DireccionUpdate::execute(['obj_dir' => 'Foo']);
        $this->assertSame('obj_dir desconocido: Foo', $msg);
    }

    public function test_ubi_inexistente_devuelve_mensaje(): void
    {
        $msg = DireccionUpdate::execute([
            'obj_dir' => 'DireccionCentroDl',
            'id_ubi' => 99999999,
            'idx' => 'nuevo',
        ]);
        $this->assertSame(_('no se encuentra el ubi'), $msg);
    }

    public function test_direccion_inexistente_en_indice_devuelve_mensaje(): void
    {
        $msg = DireccionUpdate::execute([
            'obj_dir' => 'DireccionCentroDl',
            'id_ubi' => $this->id_ubi,
            'idx' => '0',
            'id_direccion' => '99999998,99999999',
        ]);
        $this->assertSame(_('no se encuentra la dirección'), $msg);
    }

    public function test_idx_nuevo_crea_direccion_y_establece_propietario_y_principal(): void
    {
        $msg = DireccionUpdate::execute([
            'obj_dir' => 'DireccionCentroDl',
            'id_ubi' => $this->id_ubi,
            'idx' => 'nuevo',
            'direccion' => 'C/ Prueba 1',
            'poblacion' => 'Ciudad test',
            'propietario' => 'true',
            'principal' => 'true',
            'latitud' => '41.5',
            'longitud' => '2.1',
        ]);
        $this->assertSame('', $msg);

        $relaciones = $this->relacionRepo->getRelacionesPorUbi($this->id_ubi);
        $this->assertCount(1, $relaciones, 'Debe haber exactamente una relación creada.');

        $row = $relaciones[0];
        $idDireccion = (int)$row['id_direccion'];
        $this->idsDireccionesParaLimpiar[] = $idDireccion;

        $this->assertTrue(
            $row['principal'] === true || $row['principal'] === 't' || $row['principal'] === 1,
            'La fila debería estar marcada como principal.'
        );
        $this->assertTrue(
            $row['propietario'] === true || $row['propietario'] === 't' || $row['propietario'] === 1,
            'La fila debería estar marcada como propietario.'
        );

        $oDireccion = $this->direccionRepo->findById($idDireccion);
        $this->assertNotNull($oDireccion);
        $this->assertSame('C/ Prueba 1', $oDireccion->getDireccion());
        $this->assertSame('Ciudad test', $oDireccion->getPoblacion());
        $this->assertSame(41.5, $oDireccion->getLatitud());
        $this->assertSame(2.1, $oDireccion->getLongitud());
    }

    public function test_idx_numerico_actualiza_direccion_existente(): void
    {
        $oDireccion = (new DireccionFactory())->createSimple();
        $idDireccion = $oDireccion->getId_direccion();
        $this->direccionRepo->Guardar($oDireccion);
        $this->relacionRepo->asociarDireccion($this->id_ubi, $idDireccion, false);
        $this->idsDireccionesParaLimpiar[] = $idDireccion;

        $msg = DireccionUpdate::execute([
            'obj_dir' => 'DireccionCentroDl',
            'id_ubi' => $this->id_ubi,
            'idx' => '0',
            'id_direccion' => (string)$idDireccion,
            'direccion' => 'C/ Actualizada 42',
            'poblacion' => 'Nueva Ciudad',
            'propietario' => 'true',
        ]);
        $this->assertSame('', $msg);

        $oActualizada = $this->direccionRepo->findById($idDireccion);
        $this->assertSame('C/ Actualizada 42', $oActualizada->getDireccion());
        $this->assertSame('Nueva Ciudad', $oActualizada->getPoblacion());

        $relaciones = $this->relacionRepo->getRelacionesPorUbi($this->id_ubi);
        $this->assertNotEmpty($relaciones);
    }
}
