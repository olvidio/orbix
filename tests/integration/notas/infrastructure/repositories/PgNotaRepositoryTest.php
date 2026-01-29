<?php

namespace Tests\integration\notas\infrastructure\repositories;

use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\entity\Nota;
use Tests\myTest;
use Tests\factories\notas\NotaFactory;

class PgNotaRepositoryTest extends myTest
{
    private NotaRepositoryInterface $repository;
    private NotaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(NotaRepositoryInterface::class);
        $this->factory = new NotaFactory();
    }

    public function test_guardar_nuevo_nota()
    {
        // Crear instancia usando factory
        $oNota = $this->factory->createSimple();
        $id = $oNota->getId_situacion();

        // Guardar
        $result = $this->repository->Guardar($oNota);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oNotaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oNotaGuardado);
        $this->assertEquals($id, $oNotaGuardado->getId_situacion());

        // Limpiar
        $this->repository->Eliminar($oNotaGuardado);
    }

    public function test_actualizar_nota_existente()
    {
        // Crear y guardar instancia usando factory
        $oNota = $this->factory->createSimple();
        $id = $oNota->getId_situacion();
        $this->repository->Guardar($oNota);

        // Crear otra instancia con datos diferentes para actualizar
        $oNotaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oNotaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oNotaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oNotaActualizado);

        // Limpiar
        $this->repository->Eliminar($oNotaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oNota = $this->factory->createSimple();
        $id = $oNota->getId_situacion();
        $this->repository->Guardar($oNota);

        // Buscar por ID
        $oNotaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oNotaEncontrado);
        $this->assertInstanceOf(Nota::class, $oNotaEncontrado);
        $this->assertEquals($id, $oNotaEncontrado->getId_situacion());

        // Limpiar
        $this->repository->Eliminar($oNotaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oNota = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oNota);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oNota = $this->factory->createSimple();
        $id = $oNota->getId_situacion();
        $this->repository->Guardar($oNota);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_situacion', $aDatos);
        $this->assertEquals($id, $aDatos['id_situacion']);

        // Limpiar
        $oNotaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oNotaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_nota()
    {
        // Crear y guardar instancia usando factory
        $oNota = $this->factory->createSimple();
        $id = $oNota->getId_situacion();
        $this->repository->Guardar($oNota);

        // Verificar que existe
        $oNotaExiste = $this->repository->findById($id);
        $this->assertNotNull($oNotaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oNotaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oNotaEliminado = $this->repository->findById($id);
        $this->assertNull($oNotaEliminado);
    }

    public function test_get_array_notas_no_superadas_sin_filtros()
    {
        $result = $this->repository->getArrayNotasNoSuperadas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_array_notas_superadas_sin_filtros()
    {
        $result = $this->repository->getArrayNotasSuperadas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_array_notas_sin_filtros()
    {
        $result = $this->repository->getArrayNotas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_notas_sin_filtros()
    {
        $result = $this->repository->getNotas();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
