<?php

namespace Tests\integration\configuracion\infrastructure\repositories;

use src\configuracion\domain\contracts\ModuloInstaladoRepositoryInterface;
use src\configuracion\domain\entity\ModuloInstalado;
use Tests\myTest;
use Tests\factories\configuracion\ModuloInstaladoFactory;

class PgModuloInstaladoRepositoryTest extends myTest
{
    private ModuloInstaladoRepositoryInterface $repository;
    private ModuloInstaladoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ModuloInstaladoRepositoryInterface::class);
        $this->factory = new ModuloInstaladoFactory();
    }

    public function test_guardar_nuevo_moduloInstalado()
    {
        // Crear instancia usando factory
        $oModuloInstalado = $this->factory->createSimple();
        $id = $oModuloInstalado->getId_mod();

        // Guardar
        $result = $this->repository->Guardar($oModuloInstalado);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oModuloInstaladoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oModuloInstaladoGuardado);
        $this->assertEquals($id, $oModuloInstaladoGuardado->getId_mod());

        // Limpiar
        $this->repository->Eliminar($oModuloInstaladoGuardado);
    }

    public function test_actualizar_moduloInstalado_existente()
    {
        // Crear y guardar instancia usando factory
        $oModuloInstalado = $this->factory->createSimple();
        $id = $oModuloInstalado->getId_mod();
        $this->repository->Guardar($oModuloInstalado);

        // Crear otra instancia con datos diferentes para actualizar
        $oModuloInstaladoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oModuloInstaladoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oModuloInstaladoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oModuloInstaladoActualizado);

        // Limpiar
        $this->repository->Eliminar($oModuloInstaladoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oModuloInstalado = $this->factory->createSimple();
        $id = $oModuloInstalado->getId_mod();
        $this->repository->Guardar($oModuloInstalado);

        // Buscar por ID
        $oModuloInstaladoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oModuloInstaladoEncontrado);
        $this->assertInstanceOf(ModuloInstalado::class, $oModuloInstaladoEncontrado);
        $this->assertEquals($id, $oModuloInstaladoEncontrado->getId_mod());

        // Limpiar
        $this->repository->Eliminar($oModuloInstaladoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oModuloInstalado = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oModuloInstalado);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oModuloInstalado = $this->factory->createSimple();
        $id = $oModuloInstalado->getId_mod();
        $this->repository->Guardar($oModuloInstalado);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_mod', $aDatos);
        $this->assertEquals($id, $aDatos['id_mod']);

        // Limpiar
        $oModuloInstaladoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oModuloInstaladoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_moduloInstalado()
    {
        // Crear y guardar instancia usando factory
        $oModuloInstalado = $this->factory->createSimple();
        $id = $oModuloInstalado->getId_mod();
        $this->repository->Guardar($oModuloInstalado);

        // Verificar que existe
        $oModuloInstaladoExiste = $this->repository->findById($id);
        $this->assertNotNull($oModuloInstaladoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oModuloInstaladoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oModuloInstaladoEliminado = $this->repository->findById($id);
        $this->assertNull($oModuloInstaladoEliminado);
    }

    public function test_get_array_modulos_instalados_sin_filtros()
    {
        $result = $this->repository->getArrayModulosInstalados();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_modulo_instalados_sin_filtros()
    {
        $result = $this->repository->getModuloInstalados();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
