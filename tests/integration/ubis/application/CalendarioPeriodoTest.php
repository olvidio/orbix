<?php

namespace Tests\integration\ubis\application;

use src\ubis\application\CalendarioPeriodoEliminar;
use src\ubis\application\CalendarioPeriodoGuardar;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use Tests\factories\ubis\CasaPeriodoFactory;
use Tests\myTest;

/**
 * Tests de integración para CalendarioPeriodoGuardar y CalendarioPeriodoEliminar.
 */
class CalendarioPeriodoTest extends myTest
{
    private CasaPeriodoRepositoryInterface $repository;
    private CasaPeriodoFactory $factory;
    private array $idsCreadosParaLimpiar = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CasaPeriodoRepositoryInterface::class);
        $this->factory = new CasaPeriodoFactory();
    }

    public function tearDown(): void
    {
        foreach ($this->idsCreadosParaLimpiar as $id) {
            $o = $this->repository->findById($id);
            if ($o !== null) {
                $this->repository->Eliminar($o);
            }
        }
        parent::tearDown();
    }

    public function test_guardar_crea_un_nuevo_periodo(): void
    {
        $idUbi = 999001;
        $fIni = '01/01/2030';
        $fFin = '02/01/2030';

        $msg = CalendarioPeriodoGuardar::execute(0, $idUbi, $fIni, $fFin, 1);
        $this->assertSame('', $msg);

        $arr = $this->repository->getCasaPeriodos(['id_ubi' => $idUbi]);
        $this->assertNotEmpty($arr, 'No se creó ningún periodo nuevo');

        foreach ($arr as $o) {
            $this->assertSame($idUbi, $o->getId_ubi());
            $this->idsCreadosParaLimpiar[] = $o->getId_item();
        }
    }

    public function test_guardar_actualiza_un_periodo_existente(): void
    {
        $oPeriodo = $this->factory->create();
        $id = $oPeriodo->getId_item();
        $oPeriodo->setId_ubi(999002);
        $this->repository->Guardar($oPeriodo);
        $this->idsCreadosParaLimpiar[] = $id;

        $msg = CalendarioPeriodoGuardar::execute($id, 999003, '10/05/2030', '20/05/2030', 2);
        $this->assertSame('', $msg);

        $oActualizado = $this->repository->findById($id);
        $this->assertNotNull($oActualizado);
        $this->assertSame(999003, $oActualizado->getId_ubi());
        $this->assertSame(2, $oActualizado->getSfsv());
    }

    public function test_eliminar_id_cero_devuelve_mensaje_de_error(): void
    {
        $msg = CalendarioPeriodoEliminar::execute(0);
        $this->assertSame(_('no sé cuál he de borar'), $msg);
    }

    public function test_eliminar_id_inexistente_devuelve_mensaje_de_error(): void
    {
        $msg = CalendarioPeriodoEliminar::execute(99999999);
        $this->assertSame(_('no se encuentra el periodo a borrar'), $msg);
    }

    public function test_eliminar_borra_un_periodo_existente(): void
    {
        $oPeriodo = $this->factory->create();
        $id = $oPeriodo->getId_item();
        $this->repository->Guardar($oPeriodo);

        $msg = CalendarioPeriodoEliminar::execute($id);
        $this->assertSame('', $msg, "Eliminar no fue OK: $msg");

        $this->assertNull(
            $this->repository->findById($id),
            'El periodo debería haber sido eliminado.'
        );
    }
}
