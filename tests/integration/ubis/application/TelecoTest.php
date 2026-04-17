<?php

namespace Tests\integration\ubis\application;

use src\ubis\application\TelecoEliminar;
use src\ubis\application\TelecoGuardar;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoUbiRepositoryInterface;
use src\ubis\domain\entity\TelecoUbi;
use Tests\myTest;

/**
 * Tests de integración para TelecoGuardar y TelecoEliminar (obj_pau = CentroDl).
 */
class TelecoTest extends myTest
{
    private TelecoUbiRepositoryInterface $repository;
    private array $idsCreadosParaLimpiar = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TelecoCtrDlRepositoryInterface::class);
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

    public function test_guardar_crea_un_teleco_nuevo(): void
    {
        $idUbi = 200001;
        $result = TelecoGuardar::execute(
            'CentroDl',
            $idUbi,
            [],
            3,
            0,
            '600000001',
            'test-crear'
        );
        $this->assertSame(['ok' => true], $result);

        $nuevos = $this->repository->getTelecos(['id_ubi' => $idUbi]);
        $this->assertNotEmpty($nuevos, 'No se creó el teleco nuevo');
        foreach ($nuevos as $o) {
            /** @var TelecoUbi $o */
            $this->idsCreadosParaLimpiar[] = $o->getId_item();
            $this->assertSame('600000001', $o->getNum_teleco());
            $this->assertSame(3, $o->getId_tipo_teleco());
        }
    }

    public function test_guardar_actualiza_un_teleco_existente(): void
    {
        $idUbi = 200002;
        TelecoGuardar::execute('CentroDl', $idUbi, [], 1, 0, '600000002', 'inicial');
        $cOriginal = $this->repository->getTelecos(['id_ubi' => $idUbi]);
        $this->assertNotEmpty($cOriginal);
        $pkey = $cOriginal[0]->getId_item();
        $this->idsCreadosParaLimpiar[] = $pkey;

        $result = TelecoGuardar::execute('CentroDl', $idUbi, [$pkey], 2, 5, '600000999', 'actualizado');
        $this->assertSame(['ok' => true], $result);

        $oActualizado = $this->repository->findById($pkey);
        $this->assertNotNull($oActualizado);
        $this->assertSame('600000999', $oActualizado->getNum_teleco());
        $this->assertSame(2, $oActualizado->getId_tipo_teleco());
        $this->assertSame(5, $oActualizado->getId_desc_teleco());
        $this->assertSame('actualizado', $oActualizado->getObserv());
    }

    public function test_eliminar_borra_los_telecos_indicados(): void
    {
        $idUbi = 200003;
        TelecoGuardar::execute('CentroDl', $idUbi, [], 1, 0, '600000003', 'para borrar');
        $telecos = $this->repository->getTelecos(['id_ubi' => $idUbi]);
        $ids = array_map(fn($t) => $t->getId_item(), $telecos);
        $this->assertNotEmpty($ids);

        $result = TelecoEliminar::execute('CentroDl', $ids);
        $this->assertSame(['ok' => true], $result);

        foreach ($ids as $id) {
            $this->assertNull(
                $this->repository->findById($id),
                "El teleco $id debería estar eliminado."
            );
        }
    }

    public function test_eliminar_con_array_vacio_no_falla(): void
    {
        $result = TelecoEliminar::execute('CentroDl', []);
        $this->assertSame(['ok' => true], $result);
    }
}
