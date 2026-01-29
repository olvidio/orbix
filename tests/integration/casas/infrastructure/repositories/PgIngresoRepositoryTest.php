<?php

namespace Tests\integration\casas\infrastructure\repositories;

use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\entity\Ingreso;
use Tests\factories\actividades\ActividadAllFactory;
use Tests\myTest;
use Tests\factories\casas\IngresoFactory;

class PgIngresoRepositoryTest extends myTest
{
    private IngresoRepositoryInterface $repository;
    private IngresoFactory $factory;
    private ActividadDlRepositoryInterface $actividadRepository;
    private array $actividadesCreadas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(IngresoRepositoryInterface::class);
        $this->actividadRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $this->factory = new IngresoFactory();

        // crear las actividades necesarias para los foreign keys
        $actividad = new ActividadAllFactory();
        $this->actividadesCreadas[] = $actividad->create(10);

        $this->actividadRepository->Guardar($this->actividadesCreadas[0]);

    }

    public function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->actividadesCreadas as $actividad) {
            $this->actividadRepository->Eliminar($actividad);
        }
    }

    public function test_guardar_nuevo_ingreso()
    {
        // Crear instancia usando factory
        $oIngreso = $this->factory->createSimple(10);
        $id = $oIngreso->getId_activ();

        // Guardar
        $result = $this->repository->Guardar($oIngreso);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oIngresoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oIngresoGuardado);
        $this->assertEquals($id, $oIngresoGuardado->getId_activ());

        // Limpiar
        $this->repository->Eliminar($oIngresoGuardado);
    }

    public function test_actualizar_ingreso_existente()
    {
        // Crear y guardar instancia usando factory
        $oIngreso = $this->factory->createSimple(10);
        $id = $oIngreso->getId_activ();
        $this->repository->Guardar($oIngreso);

        // Crear otra instancia con datos diferentes para actualizar
        $oIngresoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oIngresoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oIngresoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oIngresoActualizado);

        // Limpiar
        $this->repository->Eliminar($oIngresoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oIngreso = $this->factory->createSimple(10);
        $id = $oIngreso->getId_activ();
        $this->repository->Guardar($oIngreso);

        // Buscar por ID
        $oIngresoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oIngresoEncontrado);
        $this->assertInstanceOf(Ingreso::class, $oIngresoEncontrado);
        $this->assertEquals($id, $oIngresoEncontrado->getId_activ());

        // Limpiar
        $this->repository->Eliminar($oIngresoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oIngreso = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oIngreso);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oIngreso = $this->factory->createSimple(10);
        $id = $oIngreso->getId_activ();
        $this->repository->Guardar($oIngreso);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_activ', $aDatos);
        $this->assertEquals($id, $aDatos['id_activ']);

        // Limpiar
        $oIngresoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oIngresoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_ingreso()
    {
        // Crear y guardar instancia usando factory
        $oIngreso = $this->factory->createSimple(10);
        $id = $oIngreso->getId_activ();
        $this->repository->Guardar($oIngreso);

        // Verificar que existe
        $oIngresoExiste = $this->repository->findById($id);
        $this->assertNotNull($oIngresoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oIngresoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oIngresoEliminado = $this->repository->findById($id);
        $this->assertNull($oIngresoEliminado);
    }

    public function test_get_ingresos_sin_filtros()
    {
        $result = $this->repository->getIngresos();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
