<?php

namespace Tests\integration\inventario\infrastructure\persistence\postgresql;

use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\entity\UbiInventario;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\entity\Lugar;
use Tests\myTest;
use Tests\factories\inventario\UbiInventarioFactory;

class PgUbiInventarioRepositoryTest extends myTest
{
    private UbiInventarioRepositoryInterface $repository;
    private UbiInventarioFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(UbiInventarioRepositoryInterface::class);
        $this->factory = new UbiInventarioFactory();
    }

    public function test_guardar_nuevo_ubi_inventario()
    {
        $oUbi = $this->factory->createSimple();
        $id = $oUbi->getId_ubi();

        $result = $this->repository->Guardar($oUbi);
        $this->assertTrue($result);

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertEquals($id, $oGuardado->getId_ubi());

        $this->repository->Eliminar($oGuardado);
    }

    public function test_actualizar_ubi_inventario_existente()
    {
        $oUbi = $this->factory->createSimple();
        $id = $oUbi->getId_ubi();
        $this->repository->Guardar($oUbi);

        $oActualizado = $this->factory->createSimple($id);
        $result = $this->repository->Guardar($oActualizado);
        $this->assertTrue($result);

        $oObtenido = $this->repository->findById($id);
        $this->assertNotNull($oObtenido);

        $this->repository->Eliminar($oObtenido);
    }

    public function test_find_by_id_existente()
    {
        $oUbi = $this->factory->createSimple();
        $id = $oUbi->getId_ubi();
        $this->repository->Guardar($oUbi);

        $oEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncontrado);
        $this->assertInstanceOf(UbiInventario::class, $oEncontrado);
        $this->assertEquals($id, $oEncontrado->getId_ubi());

        $this->repository->Eliminar($oEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $oUbi = $this->repository->findById(999999);
        $this->assertNull($oUbi);
    }

    public function test_eliminar_ubi_inventario()
    {
        $oUbi = $this->factory->createSimple();
        $id = $oUbi->getId_ubi();
        $this->repository->Guardar($oUbi);

        $oGuardado = $this->repository->findById($id);
        $result = $this->repository->Eliminar($oGuardado);
        $this->assertTrue($result);

        $oEliminado = $this->repository->findById($id);
        $this->assertNull($oEliminado);
    }

    public function test_centros_con_y_sin_lugares_y_lista_de_centros(): void
    {
        $lugarRepository = $GLOBALS['container']->get(LugarRepositoryInterface::class);
        $conLugar = $this->factory->createSimple();
        $this->assertTrue($this->repository->Guardar($conLugar));
        $sinLugar = $this->factory->createSimple();
        $this->assertTrue($this->repository->Guardar($sinLugar));
        $oLugar = new Lugar();
        $oLugar->setId_lugar($lugarRepository->getNewId());
        $oLugar->setId_ubi($conLugar->getId_ubi());
        $oLugar->setNom_lugar('armario test');
        $this->assertTrue($lugarRepository->Guardar($oLugar));
        try {
            $ids = static fn(array $ubis): array => array_map(static fn($u) => $u->getId_ubi(), $ubis);
            $conLugares = $ids($this->repository->getUbisInventarioLugar(true));
            $sinLugares = $ids($this->repository->getUbisInventarioLugar(false));

            $this->assertContains($conLugar->getId_ubi(), $conLugares);
            $this->assertNotContains($sinLugar->getId_ubi(), $conLugares);
            $this->assertContains($sinLugar->getId_ubi(), $sinLugares);
            $this->assertNotContains($conLugar->getId_ubi(), $sinLugares);
            $this->assertSame(count($conLugares), count(array_unique($conLugares)), 'cada centro una vez aunque tenga varios lugares');

            $opciones = $this->repository->getArrayUbisInventario();
            $this->assertSame($conLugar->getNom_ubi(), $opciones[$conLugar->getId_ubi()] ?? null);
            $this->assertSame($sinLugar->getNom_ubi(), $opciones[$sinLugar->getId_ubi()] ?? null);
        } finally {
            $lugarRepository->Eliminar($oLugar);
            $this->repository->Eliminar($conLugar);
            $this->repository->Eliminar($sinLugar);
        }
    }
}
