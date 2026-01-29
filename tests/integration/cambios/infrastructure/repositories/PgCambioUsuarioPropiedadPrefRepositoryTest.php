<?php

namespace Tests\integration\cambios\infrastructure\repositories;

use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\domain\entity\CambioUsuarioPropiedadPref;
use Tests\factories\cambios\CambioUsuarioObjetoPrefFactory;
use Tests\myTest;
use Tests\factories\cambios\CambioUsuarioPropiedadPrefFactory;

class PgCambioUsuarioPropiedadPrefRepositoryTest extends myTest
{
    private CambioUsuarioPropiedadPrefRepositoryInterface $repository;
    private CambioUsuarioPropiedadPrefFactory $factory;
    private CambioUsuarioObjetoPrefRepositoryInterface $repositoryObjetoPref;
    private array $objetosCreados = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CambioUsuarioPropiedadPrefRepositoryInterface::class);
        $this->repositoryObjetoPref = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $this->factory = new CambioUsuarioPropiedadPrefFactory();

        // crear objetos necesarios para las foreign keys
        $CambioUsuarioObjetoPrefFactory = new CambioUsuarioObjetoPrefFactory();
        $oCambioUsuarioObjetoPref1 = $CambioUsuarioObjetoPrefFactory->createSimple(1111);

        $this->repositoryObjetoPref->Guardar($oCambioUsuarioObjetoPref1);

        $this->objetosCreados[] = $oCambioUsuarioObjetoPref1;
    }

    public function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->objetosCreados as $oCambioUsuarioObjetoPref) {
            $this->repositoryObjetoPref->Eliminar($oCambioUsuarioObjetoPref);
        }
    }

    public function test_guardar_nuevo_cambioUsuarioPropiedadPref()
    {
        // Crear instancia usando factory
        $oCambioUsuarioPropiedadPref = $this->factory->createSimple();
        $id = $oCambioUsuarioPropiedadPref->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oCambioUsuarioPropiedadPref);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCambioUsuarioPropiedadPrefGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCambioUsuarioPropiedadPrefGuardado);
        $this->assertEquals($id, $oCambioUsuarioPropiedadPrefGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCambioUsuarioPropiedadPrefGuardado);
    }

    public function test_actualizar_cambioUsuarioPropiedadPref_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambioUsuarioPropiedadPref = $this->factory->createSimple();
        $id = $oCambioUsuarioPropiedadPref->getId_item();
        $this->repository->Guardar($oCambioUsuarioPropiedadPref);

        // Crear otra instancia con datos diferentes para actualizar
        $oCambioUsuarioPropiedadPrefUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCambioUsuarioPropiedadPrefUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCambioUsuarioPropiedadPrefActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCambioUsuarioPropiedadPrefActualizado);

        // Limpiar
        $this->repository->Eliminar($oCambioUsuarioPropiedadPrefActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambioUsuarioPropiedadPref = $this->factory->createSimple();
        $id = $oCambioUsuarioPropiedadPref->getId_item();
        $this->repository->Guardar($oCambioUsuarioPropiedadPref);

        // Buscar por ID
        $oCambioUsuarioPropiedadPrefEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCambioUsuarioPropiedadPrefEncontrado);
        $this->assertInstanceOf(CambioUsuarioPropiedadPref::class, $oCambioUsuarioPropiedadPrefEncontrado);
        $this->assertEquals($id, $oCambioUsuarioPropiedadPrefEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCambioUsuarioPropiedadPrefEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCambioUsuarioPropiedadPref = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oCambioUsuarioPropiedadPref);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambioUsuarioPropiedadPref = $this->factory->createSimple();
        $id = $oCambioUsuarioPropiedadPref->getId_item();
        $this->repository->Guardar($oCambioUsuarioPropiedadPref);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oCambioUsuarioPropiedadPrefParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCambioUsuarioPropiedadPrefParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_cambioUsuarioPropiedadPref()
    {
        // Crear y guardar instancia usando factory
        $oCambioUsuarioPropiedadPref = $this->factory->createSimple();
        $id = $oCambioUsuarioPropiedadPref->getId_item();
        $this->repository->Guardar($oCambioUsuarioPropiedadPref);

        // Verificar que existe
        $oCambioUsuarioPropiedadPrefExiste = $this->repository->findById($id);
        $this->assertNotNull($oCambioUsuarioPropiedadPrefExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCambioUsuarioPropiedadPrefExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCambioUsuarioPropiedadPrefEliminado = $this->repository->findById($id);
        $this->assertNull($oCambioUsuarioPropiedadPrefEliminado);
    }

    public function test_get_cambio_usuario_propiedad_prefs_sin_filtros()
    {
        $result = $this->repository->getCambioUsuarioPropiedadPrefs();
        
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
