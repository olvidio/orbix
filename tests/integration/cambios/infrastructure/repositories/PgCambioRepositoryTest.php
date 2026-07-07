<?php

namespace Tests\integration\cambios\infrastructure\persistence\postgresql;

use src\actividades\domain\value_objects\StatusId;
use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use Tests\factories\cambios\CambioFactory;
use Tests\myTest;

class PgCambioRepositoryTest extends myTest
{
    private CambioRepositoryInterface $repository;
    private CambioFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CambioRepositoryInterface::class);
        $this->factory = new CambioFactory();
    }

    public function test_guardar_eliminar_cambio()
    {
        $o = $this->factory->create();
        $id = (int) $this->repository->getNewId();
        $o->setId_item_cambio($id);
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertSame($id, $oGuardado->getId_item_cambio());

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($id));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }

    public function test_datos_by_id_no_existente()
    {
        $this->assertFalse($this->repository->datosById(999999981));
    }

    public function test_get_cambios_nuevos_devuelve_id_status(): void
    {
        /** @var CambioDlRepositoryInterface $dlRepository */
        $dlRepository = $GLOBALS['container']->get(CambioDlRepositoryInterface::class);
        $id = (int) $dlRepository->getNewId();
        $this->assertGreaterThan(0, $id);

        $oCambio = $this->factory->createSimple();
        $oCambio->setId_item_cambio($id);
        $oCambio->setIdStatusVo(new StatusId(4));
        $oCambio->setPropiedad('f_ini');
        $oCambio->setValor_old('2026-01-01');
        $oCambio->setValor_new('2026-07-07');
        $this->assertTrue($dlRepository->Guardar($oCambio));

        $encontrado = null;
        foreach ($this->repository->getCambiosNuevos() as $cambio) {
            if ($cambio->getId_item_cambio() === $id) {
                $encontrado = $cambio;
                break;
            }
        }

        $this->assertNotNull($encontrado);
        $this->assertSame(4, $encontrado->getId_status());

        $persistido = $dlRepository->findById($id);
        $this->assertNotNull($persistido);
        $this->assertTrue($dlRepository->Eliminar($persistido));
    }
}
