<?php

namespace Tests\integration\ubis\application;

use src\ubis\application\UbisEliminar;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use Tests\factories\ubis\CentroDlFactory;
use Tests\myTest;

/**
 * Tests de integración para UbisEliminar (caso CentroDl).
 */
class UbisEliminarTest extends myTest
{
    private CentroDlRepositoryInterface $repository;
    private CentroDlFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $this->factory = new CentroDlFactory();
    }

    public function test_elimina_un_centroDl_existente(): void
    {
        $oCentro = $this->factory->createSimple();
        $id = $oCentro->getId_ubi();
        $this->repository->Guardar($oCentro);
        $this->assertNotNull($this->repository->findById($id));

        $eliminar = new UbisEliminar();
        $msg = $eliminar->execute('CentroDl', $id);
        $this->assertSame('', $msg);

        $this->assertNull($this->repository->findById($id));
    }

    public function test_id_inexistente_devuelve_mensaje_de_error(): void
    {
        $eliminar = new UbisEliminar();
        $msg = $eliminar->execute('CentroDl', 99999999);
        $this->assertSame(_('no se encuentra el ubi a borrar'), $msg);
    }

    public function test_obj_pau_desconocido_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $eliminar = new UbisEliminar();
        $eliminar->execute('NoExiste', 1);
    }
}
