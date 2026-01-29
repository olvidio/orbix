<?php

namespace Tests\integration\actividades\infrastructure\repositories;

use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\entity\Importada;
use Tests\myTest;
use Tests\factories\actividades\ImportadaFactory;

class PgImportadaRepositoryTest extends myTest
{
    private ImportadaRepositoryInterface $repository;
    private ImportadaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ImportadaRepositoryInterface::class);
        $this->factory = new ImportadaFactory();
    }

    public function test_guardar_nuevo_importada()
    {
        // Crear instancia usando factory
        $oImportada = $this->factory->createSimple();
        $id = $oImportada->getId_activ();

        // Guardar
        $result = $this->repository->Guardar($oImportada);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oImportadaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oImportadaGuardado);
        $this->assertEquals($id, $oImportadaGuardado->getId_activ());

        // Limpiar
        $this->repository->Eliminar($oImportadaGuardado);
    }

    public function test_actualizar_importada_existente()
    {
        // Crear y guardar instancia usando factory
        $oImportada = $this->factory->createSimple();
        $id = $oImportada->getId_activ();
        $this->repository->Guardar($oImportada);

        // Crear otra instancia con datos diferentes para actualizar
        $oImportadaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oImportadaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oImportadaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oImportadaActualizado);

        // Limpiar
        $this->repository->Eliminar($oImportadaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oImportada = $this->factory->createSimple();
        $id = $oImportada->getId_activ();
        $this->repository->Guardar($oImportada);

        // Buscar por ID
        $oImportadaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oImportadaEncontrado);
        $this->assertInstanceOf(Importada::class, $oImportadaEncontrado);
        $this->assertEquals($id, $oImportadaEncontrado->getId_activ());

        // Limpiar
        $this->repository->Eliminar($oImportadaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oImportada = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oImportada);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oImportada = $this->factory->createSimple();
        $id = $oImportada->getId_activ();
        $this->repository->Guardar($oImportada);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_activ', $aDatos);
        $this->assertEquals($id, $aDatos['id_activ']);

        // Limpiar
        $oImportadaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oImportadaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_importada()
    {
        // Crear y guardar instancia usando factory
        $oImportada = $this->factory->createSimple();
        $id = $oImportada->getId_activ();
        $this->repository->Guardar($oImportada);

        // Verificar que existe
        $oImportadaExiste = $this->repository->findById($id);
        $this->assertNotNull($oImportadaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oImportadaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oImportadaEliminado = $this->repository->findById($id);
        $this->assertNull($oImportadaEliminado);
    }

    public function test_get_importadas_sin_filtros()
    {
        $result = $this->repository->getImportadas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
