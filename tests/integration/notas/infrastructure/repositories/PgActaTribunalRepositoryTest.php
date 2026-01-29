<?php

namespace Tests\integration\notas\infrastructure\repositories;

use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\entity\ActaTribunal;
use Tests\factories\notas\ActaTribunalFactory;
use Tests\myTest;

class PgActaTribunalRepositoryTest extends myTest
{
    private ActaTribunalDlRepositoryInterface $repository;
    private ActaTribunalFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActaTribunalDlRepositoryInterface::class);
        $this->factory = new ActaTribunalFactory();
    }

    public function test_guardar_nuevo_actaTribunal()
    {
        // Crear instancia usando factory
        $oActaTribunal = $this->factory->createSimple();
        $id = $oActaTribunal->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oActaTribunal);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oActaTribunalGuardado = $this->repository->findById($id);
        $this->assertNotNull($oActaTribunalGuardado);
        $this->assertEquals($id, $oActaTribunalGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oActaTribunalGuardado);
    }

    public function test_actualizar_actaTribunal_existente()
    {
        // Crear y guardar instancia usando factory
        $oActaTribunal = $this->factory->createSimple();
        $id = $oActaTribunal->getId_item();
        $this->repository->Guardar($oActaTribunal);

        // Crear otra instancia con datos diferentes para actualizar
        $oActaTribunalUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oActaTribunalUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oActaTribunalActualizado = $this->repository->findById($id);
        $this->assertNotNull($oActaTribunalActualizado);

        // Limpiar
        $this->repository->Eliminar($oActaTribunalActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActaTribunal = $this->factory->createSimple();
        $id = $oActaTribunal->getId_item();
        $this->repository->Guardar($oActaTribunal);

        // Buscar por ID
        $oActaTribunalEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oActaTribunalEncontrado);
        $this->assertInstanceOf(ActaTribunal::class, $oActaTribunalEncontrado);
        $this->assertEquals($id, $oActaTribunalEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oActaTribunalEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oActaTribunal = $this->repository->findById($id_inexistente);

        $this->assertNull($oActaTribunal);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActaTribunal = $this->factory->createSimple();
        $id = $oActaTribunal->getId_item();
        $this->repository->Guardar($oActaTribunal);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oActaTribunalParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oActaTribunalParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_actaTribunal()
    {
        // Crear y guardar instancia usando factory
        $oActaTribunal = $this->factory->createSimple();
        $id = $oActaTribunal->getId_item();
        $this->repository->Guardar($oActaTribunal);

        // Verificar que existe
        $oActaTribunalExiste = $this->repository->findById($id);
        $this->assertNotNull($oActaTribunalExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oActaTribunalExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oActaTribunalEliminado = $this->repository->findById($id);
        $this->assertNull($oActaTribunalEliminado);
    }

    public function test_get_actas_tribunales_sin_filtros()
    {
        $result = $this->repository->getActasTribunales();

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
