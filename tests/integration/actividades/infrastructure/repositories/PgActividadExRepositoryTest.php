<?php

namespace Tests\integration\actividades\infrastructure\persistence\postgresql;

use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use Tests\factories\actividades\ActividadAllFactory;
use Tests\myTest;

class PgActividadExRepositoryTest extends myTest
{
    private ActividadExRepositoryInterface $repository;
    private ActividadAllFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActividadExRepositoryInterface::class);
        $this->factory = new ActividadAllFactory();
    }

    public function test_guardar_nuevo_actividad_ex()
    {
        $o = $this->factory->createSimple();
        $id = $o->getId_activ();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertSame($id, $oGuardado->getId_activ());

        $this->repository->Eliminar($oGuardado);
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }

    public function test_datos_by_id_no_existente()
    {
        $this->assertFalse($this->repository->datosById(999999981));
    }

    public function test_get_new_id_actividad_usa_prefijo_resto_3001(): void
    {
        $idAuto = $this->repository->getNewId();
        $idActiv = $this->repository->getNewIdActividad($idAuto);

        $this->assertLessThan(0, $idActiv);
        $this->assertStringStartsWith(
            '-3001',
            (string) $idActiv,
            'Actividades ex en resto: id_activ = -3001 + id_auto',
        );
    }
}
