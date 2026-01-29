<?php

namespace Tests\integration\zonassacd\infrastructure\repositories;

use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;
use src\zonassacd\domain\entity\ZonaGrupo;
use Tests\myTest;
use Tests\factories\zonassacd\ZonaGrupoFactory;

class PgZonaGrupoRepositoryTest extends myTest
{
    private ZonaGrupoRepositoryInterface $repository;
    private ZonaGrupoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ZonaGrupoRepositoryInterface::class);
        $this->factory = new ZonaGrupoFactory();
    }

    public function test_guardar_nuevo_zonaGrupo()
    {
        // Crear instancia usando factory
        $oZonaGrupo = $this->factory->createSimple();
        $id = $oZonaGrupo->getId_grupo();

        // Guardar
        $result = $this->repository->Guardar($oZonaGrupo);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oZonaGrupoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oZonaGrupoGuardado);
        $this->assertEquals($id, $oZonaGrupoGuardado->getId_grupo());

        // Limpiar
        $this->repository->Eliminar($oZonaGrupoGuardado);
    }

    public function test_actualizar_zonaGrupo_existente()
    {
        // Crear y guardar instancia usando factory
        $oZonaGrupo = $this->factory->createSimple();
        $id = $oZonaGrupo->getId_grupo();
        $this->repository->Guardar($oZonaGrupo);

        // Crear otra instancia con datos diferentes para actualizar
        $oZonaGrupoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oZonaGrupoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oZonaGrupoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oZonaGrupoActualizado);

        // Limpiar
        $this->repository->Eliminar($oZonaGrupoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oZonaGrupo = $this->factory->createSimple();
        $id = $oZonaGrupo->getId_grupo();
        $this->repository->Guardar($oZonaGrupo);

        // Buscar por ID
        $oZonaGrupoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oZonaGrupoEncontrado);
        $this->assertInstanceOf(ZonaGrupo::class, $oZonaGrupoEncontrado);
        $this->assertEquals($id, $oZonaGrupoEncontrado->getId_grupo());

        // Limpiar
        $this->repository->Eliminar($oZonaGrupoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oZonaGrupo = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oZonaGrupo);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oZonaGrupo = $this->factory->createSimple();
        $id = $oZonaGrupo->getId_grupo();
        $this->repository->Guardar($oZonaGrupo);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_grupo', $aDatos);
        $this->assertEquals($id, $aDatos['id_grupo']);

        // Limpiar
        $oZonaGrupoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oZonaGrupoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_zonaGrupo()
    {
        // Crear y guardar instancia usando factory
        $oZonaGrupo = $this->factory->createSimple();
        $id = $oZonaGrupo->getId_grupo();
        $this->repository->Guardar($oZonaGrupo);

        // Verificar que existe
        $oZonaGrupoExiste = $this->repository->findById($id);
        $this->assertNotNull($oZonaGrupoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oZonaGrupoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oZonaGrupoEliminado = $this->repository->findById($id);
        $this->assertNull($oZonaGrupoEliminado);
    }

    public function test_get_array_zona_grupos_sin_filtros()
    {
        $result = $this->repository->getArrayZonaGrupos();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_zonas_grupo_sin_filtros()
    {
        $result = $this->repository->getZonasGrupo();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();
        
        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
    }

}
