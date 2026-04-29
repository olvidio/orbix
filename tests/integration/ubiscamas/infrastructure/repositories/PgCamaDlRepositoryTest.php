<?php

namespace Tests\integration\ubiscamas\infrastructure\persistence\postgresql;

use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;
use Tests\factories\ubiscamas\CamaFactory;
use Tests\factories\ubiscamas\HabitacionFactory;
use Tests\myTest;

class PgCamaDlRepositoryTest extends myTest
{
    private CamaDlRepositoryInterface $repository;
    private HabitacionDlRepositoryInterface $habitacionRepository;
    private CamaFactory $camaFactory;
    private HabitacionFactory $habitacionFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CamaDlRepositoryInterface::class);
        $this->habitacionRepository = $GLOBALS['container']->get(HabitacionDlRepositoryInterface::class);
        $this->camaFactory = new CamaFactory();
        $this->habitacionFactory = new HabitacionFactory();
    }

    public function test_guardar_eliminar_cama()
    {
        $oHab = $this->habitacionFactory->createSimple();
        $this->assertTrue($this->habitacionRepository->Guardar($oHab));
        $idHabitacion = $oHab->getId_habitacion();

        $oCama = $this->camaFactory->createSimple(null, $idHabitacion);
        $idCama = $oCama->getIdCama();
        $this->assertTrue($this->repository->Guardar($oCama));

        $oGuardado = $this->repository->findById($idCama);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(Cama::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));

        $oHabBorrable = $this->habitacionRepository->findById($idHabitacion);
        if ($oHabBorrable !== null) {
            $this->habitacionRepository->Eliminar($oHabBorrable);
        }
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById('00000000-0000-4000-8000-000000009998'));
    }
}
