<?php

namespace Tests\integration\inventario\infrastructure\persistence\postgresql;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use Tests\factories\inventario\DocumentoFactory;
use Tests\myTest;

class PgDocumentoRepositoryTest extends myTest
{
    private DocumentoRepositoryInterface $repository;
    private DocumentoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(DocumentoRepositoryInterface::class);
        $this->factory = new DocumentoFactory();
    }

    public function test_guardar_eliminar_documento()
    {
        $o = $this->factory->createSimple();
        $id = $o->getId_doc();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertSame($id, $oGuardado->getId_doc());

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($id));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }
}
