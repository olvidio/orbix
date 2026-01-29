<?php

namespace Tests\integration\actividadescentro\infrastructure\repositories;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use Tests\myTest;
use Tests\factories\actividadescentro\CentroEncargadoFactory;

class PgCentroEncargadoRepositoryTest extends myTest
{
    private CentroEncargadoRepositoryInterface $repository;
    private CentroEncargadoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
        $this->factory = new CentroEncargadoFactory();
    }

    public function test_guardar_nuevo_centroEncargado()
    {
        // Crear instancia usando factory
        $oCentroEncargado = $this->factory->createSimple();
        $id = $oCentroEncargado->getCentroEncargadoPk();

        // Guardar
        $result = $this->repository->Guardar($oCentroEncargado);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCentroEncargadoGuardado = $this->repository->findByPk($id);
        $this->assertNotNull($oCentroEncargadoGuardado);
        $this->assertEquals($id, $oCentroEncargadoGuardado->getCentroEncargadoPk());

        // Limpiar
        $this->repository->Eliminar($oCentroEncargadoGuardado);
    }

    public function test_actualizar_centroEncargado_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroEncargado = $this->factory->createSimple();
        $id = $oCentroEncargado->getCentroEncargadoPk();
        $this->repository->Guardar($oCentroEncargado);

        // Crear otra instancia con datos diferentes para actualizar
        $oCentroEncargadoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCentroEncargadoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCentroEncargadoActualizado = $this->repository->findByPk($id);
        $this->assertNotNull($oCentroEncargadoActualizado);

        // Limpiar
        $this->repository->Eliminar($oCentroEncargadoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroEncargado = $this->factory->createSimple();
        $id = $oCentroEncargado->getCentroEncargadoPk();
        $this->repository->Guardar($oCentroEncargado);

        // Buscar por ID
        $oCentroEncargadoEncontrado = $this->repository->findByPk($id);
        $this->assertNotNull($oCentroEncargadoEncontrado);
        $this->assertInstanceOf(CentroEncargado::class, $oCentroEncargadoEncontrado);
        $this->assertEquals($id, $oCentroEncargadoEncontrado->getCentroEncargadoPk());

        // Limpiar
        $this->repository->Eliminar($oCentroEncargadoEncontrado);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroEncargado = $this->factory->createSimple();
        $id = $oCentroEncargado->getCentroEncargadoPk();
        $this->repository->Guardar($oCentroEncargado);

        // Obtener datos por ID
        $aDatos = $this->repository->datosByPk($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_activ', $aDatos);
        $this->assertEquals($id->IdActiv(), $aDatos['id_activ']);

        // Limpiar
        $oCentroEncargadoParaborrar = $this->repository->findByPk($id);
        $this->repository->Eliminar($oCentroEncargadoParaborrar);
    }

    public function test_eliminar_centroEncargado()
    {
        // Crear y guardar instancia usando factory
        $oCentroEncargado = $this->factory->createSimple();
        $id = $oCentroEncargado->getCentroEncargadoPk();
        $this->repository->Guardar($oCentroEncargado);

        // Verificar que existe
        $oCentroEncargadoExiste = $this->repository->findByPk($id);
        $this->assertNotNull($oCentroEncargadoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCentroEncargadoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCentroEncargadoEliminado = $this->repository->findByPk($id);
        $this->assertNull($oCentroEncargadoEliminado);
    }

    public function test_get_actividades_de_centros_sin_filtros()
    {
        $result = $this->repository->getActividadesDeCentros(3001145);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_centros_encargados_actividad_sin_filtros()
    {
        $result = $this->repository->getCentrosEncargadosActividad(3001145);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_centros_encargados_sin_filtros()
    {
        $result = $this->repository->getCentrosEncargados();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
