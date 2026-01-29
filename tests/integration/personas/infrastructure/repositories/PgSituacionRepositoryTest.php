<?php

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\entity\Situacion;
use Tests\myTest;

class PgSituacionRepositoryTest extends myTest
{
    private SituacionRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(SituacionRepositoryInterface::class);
    }

    public function test_guardar_nuevo_situacion()
    {
        $oSituacion = new Situacion();
        $oSituacion->setSituacion('Z');
        $oSituacion->setNombre_situacion('Nombre de la situación');
        
        $result = $this->repository->Guardar($oSituacion);
        $this->assertTrue($result);
        
        // Verificar que se guardó
        $oSituacionGuardado = $this->repository->findById('Z');
        $this->assertNotNull($oSituacionGuardado);
        
        // Limpiar
        $this->repository->Eliminar($oSituacionGuardado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oSituacion = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oSituacion);
    }

}
