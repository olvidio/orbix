<?php

namespace Tests\integration\notas\infrastructure\repositories;

use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use Tests\myTest;
use Tests\factories\notas\PersonaNotaFactory;

class PgPersonaNotaRepositoryTest extends myTest
{
    private PersonaNotaDlRepositoryInterface $repository;
    private PersonaNotaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $this->factory = new PersonaNotaFactory();
    }

    public function test_guardar_nuevo_personaNota()
    {
        // Crear instancia usando factory
        $oPersonaNota = $this->factory->createSimple();
        $id = $oPersonaNota->getPersonaNotaPk();

        // Guardar
        $result = $this->repository->Guardar($oPersonaNota);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oPersonaNotaGuardado = $this->repository->findByPk($id);
        $this->assertNotNull($oPersonaNotaGuardado);
        $this->assertEquals($id, $oPersonaNotaGuardado->getPersonaNotaPk());

        // Limpiar
        $this->repository->Eliminar($oPersonaNotaGuardado);
    }

    public function test_actualizar_personaNota_existente()
    {
        // Crear y guardar instancia usando factory
        $oPersonaNota = $this->factory->createSimple();
        $id = $oPersonaNota->getPersonaNotaPk();
        $this->repository->Guardar($oPersonaNota);

        // Crear otra instancia con datos diferentes para actualizar
        $oPersonaNotaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oPersonaNotaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oPersonaNotaActualizado = $this->repository->findByPk($id);
        $this->assertNotNull($oPersonaNotaActualizado);

        // Limpiar
        $this->repository->Eliminar($oPersonaNotaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPersonaNota = $this->factory->createSimple();
        $id = $oPersonaNota->getPersonaNotaPk();
        $this->repository->Guardar($oPersonaNota);

        // Buscar por ID
        $oPersonaNotaEncontrado = $this->repository->findByPk($id);
        $this->assertNotNull($oPersonaNotaEncontrado);
        $this->assertInstanceOf(PersonaNota::class, $oPersonaNotaEncontrado);
        $this->assertEquals($id, $oPersonaNotaEncontrado->getPersonaNotaPk());

        // Limpiar
        $this->repository->Eliminar($oPersonaNotaEncontrado);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPersonaNota = $this->factory->createSimple();
        $id = $oPersonaNota->getPersonaNotaPk();
        $this->repository->Guardar($oPersonaNota);

        // Obtener datos por ID
        $aDatos = $this->repository->datosByPk($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_nivel', $aDatos);
        $this->assertEquals($id->idNivel(), $aDatos['id_nivel']);

        // Limpiar
        $oPersonaNotaParaborrar = $this->repository->findByPk($id);
        $this->repository->Eliminar($oPersonaNotaParaborrar);
    }

    public function test_eliminar_personaNota()
    {
        // Crear y guardar instancia usando factory
        $oPersonaNota = $this->factory->createSimple();
        $id = $oPersonaNota->getPersonaNotaPk();
        $this->repository->Guardar($oPersonaNota);

        // Verificar que existe
        $oPersonaNotaExiste = $this->repository->findByPk($id);
        $this->assertNotNull($oPersonaNotaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oPersonaNotaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oPersonaNotaEliminado = $this->repository->findByPk($id);
        $this->assertNull($oPersonaNotaEliminado);
    }

}
