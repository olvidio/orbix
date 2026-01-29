<?php

namespace Tests\integration\configuracion\infrastructure\repositories;

use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\entity\App;
use Tests\myTest;
use Tests\factories\configuracion\AppFactory;

class PgAppRepositoryTest extends myTest
{
    private AppRepositoryInterface $repository;
    private AppFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(AppRepositoryInterface::class);
        $this->factory = new AppFactory();
    }

    public function test_guardar_nuevo_app()
    {
        // Crear instancia usando factory
        $oApp = $this->factory->createSimple();
        $id = $oApp->getId_app();

        // Guardar
        $result = $this->repository->Guardar($oApp);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oAppGuardado = $this->repository->findById($id);
        $this->assertNotNull($oAppGuardado);
        $this->assertEquals($id, $oAppGuardado->getId_app());

        // Limpiar
        $this->repository->Eliminar($oAppGuardado);
    }

    public function test_actualizar_app_existente()
    {
        // Crear y guardar instancia usando factory
        $oApp = $this->factory->createSimple();
        $id = $oApp->getId_app();
        $this->repository->Guardar($oApp);

        // Crear otra instancia con datos diferentes para actualizar
        $oAppUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oAppUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oAppActualizado = $this->repository->findById($id);
        $this->assertNotNull($oAppActualizado);

        // Limpiar
        $this->repository->Eliminar($oAppActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oApp = $this->factory->createSimple();
        $id = $oApp->getId_app();
        $this->repository->Guardar($oApp);

        // Buscar por ID
        $oAppEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oAppEncontrado);
        $this->assertInstanceOf(App::class, $oAppEncontrado);
        $this->assertEquals($id, $oAppEncontrado->getId_app());

        // Limpiar
        $this->repository->Eliminar($oAppEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oApp = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oApp);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oApp = $this->factory->createSimple();
        $id = $oApp->getId_app();
        $this->repository->Guardar($oApp);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_app', $aDatos);
        $this->assertEquals($id, $aDatos['id_app']);

        // Limpiar
        $oAppParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oAppParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_app()
    {
        // Crear y guardar instancia usando factory
        $oApp = $this->factory->createSimple();
        $id = $oApp->getId_app();
        $this->repository->Guardar($oApp);

        // Verificar que existe
        $oAppExiste = $this->repository->findById($id);
        $this->assertNotNull($oAppExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oAppExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oAppEliminado = $this->repository->findById($id);
        $this->assertNull($oAppEliminado);
    }

    public function test_get_apps_sin_filtros()
    {
        $result = $this->repository->getApps();
        
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
