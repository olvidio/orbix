<?php

namespace Tests\integration\actividadcargos\infrastructure\repositories;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use Tests\myTest;
use Tests\factories\actividadcargos\ActividadCargoFactory;

class PgActividadCargoDlRepositoryTest extends myTest
{
    private ActividadCargoRepositoryInterface $repository;
    private ActividadCargoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $this->factory = new ActividadCargoFactory();
    }

    public function test_guardar_nuevo_actividadCargo()
    {
        // Crear instancia usando factory
        $oActividadCargo = $this->factory->createSimple();
        $id = $oActividadCargo->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oActividadCargo);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oActividadCargoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oActividadCargoGuardado);
        $this->assertEquals($id, $oActividadCargoGuardado->getId_item());
        $id_activ = $oActividadCargoGuardado->getId_activ();

        // Limpiar
        $this->repository->Eliminar($oActividadCargoGuardado);

        // Verificar que se registró en av_cambios
        // id_tipo_cambio = 1 -> INSERT
        $sql = "SELECT * FROM av_cambios 
            WHERE id_activ = :id_activ 
            AND (objeto = 'ActividadCargoSacd' OR objeto = 'ActividadCargoNoSacd') 
            AND id_tipo_cambio = 1
            ORDER BY timestamp_cambio DESC LIMIT 1";

        $stmt = $GLOBALS['oDBPC']->prepare($sql);
        $stmt->execute(['id_activ' => $id_activ]);
        $cambio = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotEmpty($cambio);
        $this->assertEquals(1, $cambio['id_tipo_cambio']);
    }

    public function test_actualizar_actividadCargo_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadCargo = $this->factory->createSimple();
        $id = $oActividadCargo->getId_item();
        $this->repository->Guardar($oActividadCargo);

        // Crear otra instancia con datos diferentes para actualizar
        $oActividadCargoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oActividadCargoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oActividadCargoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oActividadCargoActualizado);

        // Limpiar
        $this->repository->Eliminar($oActividadCargoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadCargo = $this->factory->createSimple();
        $id = $oActividadCargo->getId_item();
        $this->repository->Guardar($oActividadCargo);

        // Buscar por ID
        $oActividadCargoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oActividadCargoEncontrado);
        $this->assertInstanceOf(ActividadCargo::class, $oActividadCargoEncontrado);
        $this->assertEquals($id, $oActividadCargoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oActividadCargoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oActividadCargo = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oActividadCargo);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadCargo = $this->factory->createSimple();
        $id = $oActividadCargo->getId_item();
        $this->repository->Guardar($oActividadCargo);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oActividadCargoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oActividadCargoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_actividadCargo()
    {
        // Crear y guardar instancia usando factory
        $oActividadCargo = $this->factory->createSimple();
        $id = $oActividadCargo->getId_item();
        $this->repository->Guardar($oActividadCargo);

        // Verificar que existe
        $oActividadCargoExiste = $this->repository->findById($id);
        $this->assertNotNull($oActividadCargoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oActividadCargoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oActividadCargoEliminado = $this->repository->findById($id);
        $this->assertNull($oActividadCargoEliminado);
    }

    public function test_get_actividad_id_sacds_sin_filtros()
    {
        $result = $this->repository->getActividadIdSacds(30011345);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_actividad_sacds_sin_filtros()
    {
        $result = $this->repository->getActividadSacds(30011345);
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    /*
    public function test_get_actividad_cargos_de_asistente_sin_filtros()
    {
        $result = $this->repository->getActividadCargosDeAsistente();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }
    */

    /*
    public function test_get_asistente_cargo_de_actividad_sin_filtros()
    {
        $result = $this->repository->getAsistenteCargoDeActividad();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }
    */

    /*
    public function test_get_cargo_de_actividad_sin_filtros()
    {
        $result = $this->repository->getCargoDeActividad();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }
    */

    public function test_get_actividad_cargos_sin_filtros()
    {
        $result = $this->repository->getActividadCargos();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();

        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
    }

}
