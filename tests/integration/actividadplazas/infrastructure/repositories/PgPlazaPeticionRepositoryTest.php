<?php

namespace Tests\integration\actividadplazas\infrastructure\repositories;

use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\entity\PlazaPeticion;
use Tests\myTest;
use Tests\factories\actividadplazas\PlazaPeticionFactory;

class PgPlazaPeticionRepositoryTest extends myTest
{
    private PlazaPeticionRepositoryInterface $repository;
    private PlazaPeticionFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PlazaPeticionRepositoryInterface::class);
        $this->factory = new PlazaPeticionFactory();
    }

    public function test_guardar_nuevo_plazaPeticion()
    {
        // Crear instancia usando factory
        $oPlazaPeticion = $this->factory->createSimple();
        $id = $oPlazaPeticion->getPlazaPeticionPk();

        // Guardar
        $result = $this->repository->Guardar($oPlazaPeticion);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oPlazaPeticionGuardado = $this->repository->findByPk($id);
        $this->assertNotNull($oPlazaPeticionGuardado);
        $this->assertEquals($id, $oPlazaPeticionGuardado->getPlazaPeticionPk());

        // Limpiar
        $this->repository->Eliminar($oPlazaPeticionGuardado);
    }

    public function test_actualizar_plazaPeticion_existente()
    {
        // Crear y guardar instancia usando factory
        $oPlazaPeticion = $this->factory->createSimple();
        $id = $oPlazaPeticion->getPlazaPeticionPk();
        $this->repository->Guardar($oPlazaPeticion);

        // Crear otra instancia con datos diferentes para actualizar
        $oPlazaPeticionUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oPlazaPeticionUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oPlazaPeticionActualizado = $this->repository->findByPk($id);
        $this->assertNotNull($oPlazaPeticionActualizado);

        // Limpiar
        $this->repository->Eliminar($oPlazaPeticionActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPlazaPeticion = $this->factory->createSimple();
        $id = $oPlazaPeticion->getPlazaPeticionPk();
        $this->repository->Guardar($oPlazaPeticion);

        // Buscar por ID
        $oPlazaPeticionEncontrado = $this->repository->findByPk($id);
        $this->assertNotNull($oPlazaPeticionEncontrado);
        $this->assertInstanceOf(PlazaPeticion::class, $oPlazaPeticionEncontrado);
        $this->assertEquals($id, $oPlazaPeticionEncontrado->getPlazaPeticionPk());

        // Limpiar
        $this->repository->Eliminar($oPlazaPeticionEncontrado);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPlazaPeticion = $this->factory->createSimple();
        $id = $oPlazaPeticion->getPlazaPeticionPk();
        $this->repository->Guardar($oPlazaPeticion);

        // Obtener datos por ID
        $aDatos = $this->repository->datosByPk($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_nom', $aDatos);
        $this->assertEquals($id->idNom(), $aDatos['id_nom']);

        // Limpiar
        $oPlazaPeticionParaborrar = $this->repository->findByPk($id);
        $this->repository->Eliminar($oPlazaPeticionParaborrar);
    }

    public function test_eliminar_plazaPeticion()
    {
        // Crear y guardar instancia usando factory
        $oPlazaPeticion = $this->factory->createSimple();
        $id = $oPlazaPeticion->getPlazaPeticionPk();
        $this->repository->Guardar($oPlazaPeticion);

        // Verificar que existe
        $oPlazaPeticionExiste = $this->repository->findByPk($id);
        $this->assertNotNull($oPlazaPeticionExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oPlazaPeticionExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oPlazaPeticionEliminado = $this->repository->findByPk($id);
        $this->assertNull($oPlazaPeticionEliminado);
    }

    public function test_get_plazas_peticion_sin_filtros()
    {
        $result = $this->repository->getPlazasPeticion();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
