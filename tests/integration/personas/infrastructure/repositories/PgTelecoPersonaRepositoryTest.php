<?php

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaRepositoryInterface;
use src\personas\domain\entity\TelecoPersona;
use Tests\myTest;
use Tests\factories\personas\TelecoPersonaFactory;

class PgTelecoPersonaRepositoryTest extends myTest
{
    private TelecoPersonaRepositoryInterface $repository;
    private TelecoPersonaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TelecoPersonaDlRepositoryInterface::class);
        $this->factory = new TelecoPersonaFactory();
    }

    public function test_guardar_nuevo_telecoPersona()
    {
        // Crear instancia usando factory
        $oTelecoPersona = $this->factory->createSimple();
        $id = $oTelecoPersona->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oTelecoPersona);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oTelecoPersonaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oTelecoPersonaGuardado);
        $this->assertEquals($id, $oTelecoPersonaGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oTelecoPersonaGuardado);
    }

    public function test_actualizar_telecoPersona_existente()
    {
        // Crear y guardar instancia usando factory
        $oTelecoPersona = $this->factory->createSimple();
        $id = $oTelecoPersona->getId_item();
        $this->repository->Guardar($oTelecoPersona);

        // Crear otra instancia con datos diferentes para actualizar
        $oTelecoPersonaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oTelecoPersonaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oTelecoPersonaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oTelecoPersonaActualizado);

        // Limpiar
        $this->repository->Eliminar($oTelecoPersonaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTelecoPersona = $this->factory->createSimple();
        $id = $oTelecoPersona->getId_item();
        $this->repository->Guardar($oTelecoPersona);

        // Buscar por ID
        $oTelecoPersonaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oTelecoPersonaEncontrado);
        $this->assertInstanceOf(TelecoPersona::class, $oTelecoPersonaEncontrado);
        $this->assertEquals($id, $oTelecoPersonaEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oTelecoPersonaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oTelecoPersona = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oTelecoPersona);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTelecoPersona = $this->factory->createSimple();
        $id = $oTelecoPersona->getId_item();
        $this->repository->Guardar($oTelecoPersona);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oTelecoPersonaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oTelecoPersonaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_telecoPersona()
    {
        // Crear y guardar instancia usando factory
        $oTelecoPersona = $this->factory->createSimple();
        $id = $oTelecoPersona->getId_item();
        $this->repository->Guardar($oTelecoPersona);

        // Verificar que existe
        $oTelecoPersonaExiste = $this->repository->findById($id);
        $this->assertNotNull($oTelecoPersonaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oTelecoPersonaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oTelecoPersonaEliminado = $this->repository->findById($id);
        $this->assertNull($oTelecoPersonaEliminado);
    }

    public function test_get_telecos_persona_sin_filtros()
    {
        $result = $this->repository->getTelecosPersona();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
