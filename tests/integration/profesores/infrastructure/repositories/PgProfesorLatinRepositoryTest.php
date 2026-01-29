<?php

namespace Tests\integration\profesores\infrastructure\repositories;

use src\profesores\domain\contracts\ProfesorLatinRepositoryInterface;
use src\profesores\domain\entity\ProfesorLatin;
use Tests\myTest;
use Tests\factories\profesores\ProfesorLatinFactory;

class PgProfesorLatinRepositoryTest extends myTest
{
    private ProfesorLatinRepositoryInterface $repository;
    private ProfesorLatinFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ProfesorLatinRepositoryInterface::class);
        $this->factory = new ProfesorLatinFactory();
    }

    public function test_guardar_nuevo_profesorLatin()
    {
        // Crear instancia usando factory
        $oProfesorLatin = $this->factory->createSimple();
        $id = $oProfesorLatin->getId_nom();

        // Guardar
        $result = $this->repository->Guardar($oProfesorLatin);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oProfesorLatinGuardado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorLatinGuardado);
        $this->assertEquals($id, $oProfesorLatinGuardado->getId_nom());

        // Limpiar
        $this->repository->Eliminar($oProfesorLatinGuardado);
    }

    public function test_actualizar_profesorLatin_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorLatin = $this->factory->createSimple();
        $id = $oProfesorLatin->getId_nom();
        $this->repository->Guardar($oProfesorLatin);

        // Crear otra instancia con datos diferentes para actualizar
        $oProfesorLatinUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oProfesorLatinUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oProfesorLatinActualizado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorLatinActualizado);

        // Limpiar
        $this->repository->Eliminar($oProfesorLatinActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorLatin = $this->factory->createSimple();
        $id = $oProfesorLatin->getId_nom();
        $this->repository->Guardar($oProfesorLatin);

        // Buscar por ID
        $oProfesorLatinEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oProfesorLatinEncontrado);
        $this->assertInstanceOf(ProfesorLatin::class, $oProfesorLatinEncontrado);
        $this->assertEquals($id, $oProfesorLatinEncontrado->getId_nom());

        // Limpiar
        $this->repository->Eliminar($oProfesorLatinEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oProfesorLatin = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oProfesorLatin);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProfesorLatin = $this->factory->createSimple();
        $id = $oProfesorLatin->getId_nom();
        $this->repository->Guardar($oProfesorLatin);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_nom', $aDatos);
        $this->assertEquals($id, $aDatos['id_nom']);

        // Limpiar
        $oProfesorLatinParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oProfesorLatinParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_profesorLatin()
    {
        // Crear y guardar instancia usando factory
        $oProfesorLatin = $this->factory->createSimple();
        $id = $oProfesorLatin->getId_nom();
        $this->repository->Guardar($oProfesorLatin);

        // Verificar que existe
        $oProfesorLatinExiste = $this->repository->findById($id);
        $this->assertNotNull($oProfesorLatinExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oProfesorLatinExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oProfesorLatinEliminado = $this->repository->findById($id);
        $this->assertNull($oProfesorLatinEliminado);
    }

    public function test_get_profesores_latin_sin_filtros()
    {
        $result = $this->repository->getProfesoresLatin();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
