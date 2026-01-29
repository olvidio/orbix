<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\entity\Casa;
use Tests\myTest;
use Tests\factories\ubis\CasaFactory;

class PgCasaRepositoryTest extends myTest
{
    private CasaRepositoryInterface $repository;
    private CasaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CasaRepositoryInterface::class);
        $this->factory = new CasaFactory();
    }

    public function test_guardar_nuevo_casa()
    {
        // Crear instancia usando factory
        $oCasa = $this->factory->createSimple();
        $id = $oCasa->getId_ubi();

        // Guardar
        $result = $this->repository->Guardar($oCasa);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCasaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCasaGuardado);
        $this->assertEquals($id, $oCasaGuardado->getId_ubi());

        // Limpiar
        $this->repository->Eliminar($oCasaGuardado);
    }

    public function test_actualizar_casa_existente()
    {
        // Crear y guardar instancia usando factory
        $oCasa = $this->factory->createSimple();
        $id = $oCasa->getId_ubi();
        $this->repository->Guardar($oCasa);

        // Crear otra instancia con datos diferentes para actualizar
        $oCasaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCasaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCasaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCasaActualizado);

        // Limpiar
        $this->repository->Eliminar($oCasaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCasa = $this->factory->createSimple();
        $id = $oCasa->getId_ubi();
        $this->repository->Guardar($oCasa);

        // Buscar por ID
        $oCasaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCasaEncontrado);
        $this->assertInstanceOf(Casa::class, $oCasaEncontrado);
        $this->assertEquals($id, $oCasaEncontrado->getId_ubi());

        // Limpiar
        $this->repository->Eliminar($oCasaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCasa = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oCasa);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCasa = $this->factory->createSimple();
        $id = $oCasa->getId_ubi();
        $this->repository->Guardar($oCasa);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_ubi', $aDatos);
        $this->assertEquals($id, $aDatos['id_ubi']);

        // Limpiar
        $oCasaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCasaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_casa()
    {
        // Crear y guardar instancia usando factory
        $oCasa = $this->factory->createSimple();
        $id = $oCasa->getId_ubi();
        $this->repository->Guardar($oCasa);

        // Verificar que existe
        $oCasaExiste = $this->repository->findById($id);
        $this->assertNotNull($oCasaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCasaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCasaEliminado = $this->repository->findById($id);
        $this->assertNull($oCasaEliminado);
    }

    public function test_get_array_casas_sin_filtros()
    {
        $result = $this->repository->getArrayCasas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_casas_sin_filtros()
    {
        $result = $this->repository->getCasas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
