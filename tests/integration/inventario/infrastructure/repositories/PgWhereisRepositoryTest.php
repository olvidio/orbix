<?php

namespace Tests\integration\inventario\infrastructure\persistence\postgresql;

use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Egm;
use src\inventario\domain\entity\Whereis;
use Tests\factories\inventario\DocumentoFactory;
use Tests\factories\inventario\EquipajeFactory;
use Tests\myTest;

class PgWhereisRepositoryTest extends myTest
{
    private WhereisRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(WhereisRepositoryInterface::class);
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }

    public function test_datos_by_id_no_existente()
    {
        $this->assertFalse($this->repository->datosById(999999981));
    }

    public function test_get_new_id()
    {
        $nid = $this->repository->getNewId();
        $this->assertTrue(is_numeric($nid));
    }

    public function test_ciclo_guardar_actualizar_y_eliminar_un_documento_de_una_maleta(): void
    {
        $equipajeRepository = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
        $egmRepository = $GLOBALS['container']->get(EgmRepositoryInterface::class);
        $documentoRepository = $GLOBALS['container']->get(DocumentoRepositoryInterface::class);
        $oEquipaje = (new EquipajeFactory())->createSimple();
        $this->assertTrue($equipajeRepository->Guardar($oEquipaje));
        $docA = (new DocumentoFactory())->createSimple();
        $this->assertTrue($documentoRepository->Guardar($docA));
        $docB = (new DocumentoFactory())->createSimple();
        $this->assertTrue($documentoRepository->Guardar($docB));
        try {
            $oEgm = new Egm();
            $oEgm->setId_item($egmRepository->getNewId());
            $oEgm->setId_equipaje($oEquipaje->getId_equipaje());
            $oEgm->setId_grupo(1);
            $this->assertTrue($egmRepository->Guardar($oEgm));
            $idEgm = $oEgm->getId_item();

            $o = new Whereis();
            $o->setId_item_whereis($this->repository->getNewId());
            $o->setId_item_egm($idEgm);
            $o->setId_doc($docA->getId_doc());
            $this->assertTrue($this->repository->Guardar($o));

            $leido = $this->repository->findById($o->getId_item_whereis());
            $this->assertNotNull($leido);
            $this->assertSame([$idEgm, $docA->getId_doc()], [$leido->getId_item_egm(), $leido->getId_doc()]);
            $this->assertSame([(string) $docA->getId_doc()], $this->repository->getArrayIdFromIdEgms([$idEgm]));

            $leido->setId_doc($docB->getId_doc());
            $this->assertTrue($this->repository->Guardar($leido));
            $this->assertSame([(string) $docB->getId_doc()], $this->repository->getArrayIdFromIdEgms([$idEgm]));

            $this->assertTrue($this->repository->Eliminar($leido));
            $this->assertNull($this->repository->findById($o->getId_item_whereis()));
            $this->assertSame([], $this->repository->getArrayIdFromIdEgms([$idEgm]));
        } finally {
            $equipajeRepository->Eliminar($oEquipaje);
            $documentoRepository->Eliminar($docA);
            $documentoRepository->Eliminar($docB);
        }
    }
}
