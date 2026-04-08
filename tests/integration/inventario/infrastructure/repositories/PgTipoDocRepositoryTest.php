<?php

namespace Tests\integration\inventario\infrastructure\persistence\postgresql;

use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\entity\TipoDoc;
use Tests\myTest;
use Tests\factories\inventario\TipoDocFactory;

class PgTipoDocRepositoryTest extends myTest
{
    private TipoDocRepositoryInterface $repository;
    private TipoDocFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TipoDocRepositoryInterface::class);
        $this->factory = new TipoDocFactory();
    }

    public function test_guardar_nuevo_tipo_doc()
    {
        $oTipoDoc = $this->factory->createSimple();
        $id = $oTipoDoc->getId_tipo_doc();

        $result = $this->repository->Guardar($oTipoDoc);
        $this->assertTrue($result);

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertEquals($id, $oGuardado->getId_tipo_doc());

        $this->repository->Eliminar($oGuardado);
    }

    public function test_actualizar_tipo_doc_existente()
    {
        $oTipoDoc = $this->factory->createSimple();
        $id = $oTipoDoc->getId_tipo_doc();
        $this->repository->Guardar($oTipoDoc);

        $oActualizado = $this->factory->createSimple($id);
        $result = $this->repository->Guardar($oActualizado);
        $this->assertTrue($result);

        $oObtenido = $this->repository->findById($id);
        $this->assertNotNull($oObtenido);

        $this->repository->Eliminar($oObtenido);
    }

    public function test_find_by_id_existente()
    {
        $oTipoDoc = $this->factory->createSimple();
        $id = $oTipoDoc->getId_tipo_doc();
        $this->repository->Guardar($oTipoDoc);

        $oEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncontrado);
        $this->assertInstanceOf(TipoDoc::class, $oEncontrado);
        $this->assertEquals($id, $oEncontrado->getId_tipo_doc());

        $this->repository->Eliminar($oEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $oTipoDoc = $this->repository->findById(999999);
        $this->assertNull($oTipoDoc);
    }

    public function test_eliminar_tipo_doc()
    {
        $oTipoDoc = $this->factory->createSimple();
        $id = $oTipoDoc->getId_tipo_doc();
        $this->repository->Guardar($oTipoDoc);

        $oGuardado = $this->repository->findById($id);
        $result = $this->repository->Eliminar($oGuardado);
        $this->assertTrue($result);

        $oEliminado = $this->repository->findById($id);
        $this->assertNull($oEliminado);
    }
}
