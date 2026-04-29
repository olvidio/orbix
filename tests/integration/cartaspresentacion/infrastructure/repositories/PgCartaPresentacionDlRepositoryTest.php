<?php

namespace Tests\integration\cartaspresentacion\infrastructure\persistence\postgresql;

use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\cartaspresentacion\domain\entity\CartaPresentacion;
use Tests\factories\cartaspresentacion\CartaPresentacionFactory;
use Tests\myTest;

class PgCartaPresentacionDlRepositoryTest extends myTest
{
    private CartaPresentacionDlRepositoryInterface $repository;
    private CartaPresentacionFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CartaPresentacionDlRepositoryInterface::class);
        $this->factory = new CartaPresentacionFactory();
    }

    public function test_guardar_eliminar_carta_presentacion()
    {
        $o = $this->factory->createSimple();
        $idu = $o->getId_ubi();
        $idd = $o->getId_direccion();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($idu, $idd);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(CartaPresentacion::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($idu, $idd));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999991, 999999992));
    }
}
