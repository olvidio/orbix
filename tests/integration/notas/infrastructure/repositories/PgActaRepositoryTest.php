<?php

namespace Tests\integration\notas\infrastructure\repositories;

use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\entity\Acta;
use Tests\factories\notas\ActaFactory;
use Tests\myTest;

class PgActaRepositoryTest extends myTest
{
    private ActaDlRepositoryInterface $repository;
    private ActaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActaDlRepositoryInterface::class);
        $this->factory = new ActaFactory();
    }

    public function test_guardar_nuevo_acta()
    {
        // Crear instancia usando factory
        $oActa = $this->factory->createSimple();
        $id = $oActa->getActa();

        // Guardar
        $result = $this->repository->Guardar($oActa);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oActaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oActaGuardado);
        $this->assertEquals($id, $oActaGuardado->getActa());

        // Limpiar
        $this->repository->Eliminar($oActaGuardado);
    }

    public function test_actualizar_acta_existente()
    {
        // Crear y guardar instancia usando factory
        $oActa = $this->factory->createSimple();
        $id = $oActa->getActa();
        $this->repository->Guardar($oActa);

        // Crear otra instancia con datos diferentes para actualizar
        $oActaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oActaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oActaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oActaActualizado);

        // Limpiar
        $this->repository->Eliminar($oActaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActa = $this->factory->createSimple();
        $id = $oActa->getActa();
        $this->repository->Guardar($oActa);

        // Buscar por ID
        $oActaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oActaEncontrado);
        $this->assertInstanceOf(Acta::class, $oActaEncontrado);
        $this->assertEquals($id, $oActaEncontrado->getActa());

        // Limpiar
        $this->repository->Eliminar($oActaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 'dlb 1/50';
        $oActa = $this->repository->findById($id_inexistente);

        $this->assertNull($oActa);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActa = $this->factory->createSimple();
        $id = $oActa->getActa();
        $this->repository->Guardar($oActa);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('acta', $aDatos);
        $this->assertEquals($id, $aDatos['acta']);

        // Limpiar
        $oActaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oActaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 'dlb 1/50';
        $aDatos = $this->repository->datosById($id_inexistente);

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_acta()
    {
        // Crear y guardar instancia usando factory
        $oActa = $this->factory->createSimple();
        $id = $oActa->getActa();
        $this->repository->Guardar($oActa);

        // Verificar que existe
        $oActaExiste = $this->repository->findById($id);
        $this->assertNotNull($oActaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oActaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oActaEliminado = $this->repository->findById($id);
        $this->assertNull($oActaEliminado);
    }

    public function test_get_actas_sin_filtros()
    {
        $result = $this->repository->getActas();

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
