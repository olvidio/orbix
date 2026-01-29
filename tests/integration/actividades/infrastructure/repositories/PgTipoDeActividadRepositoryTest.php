<?php

namespace Tests\integration\actividades\infrastructure\repositories;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TipoDeActividad;
use Tests\factories\actividades\TipoDeActividadFactory;
use Tests\myTest;

class PgTipoDeActividadRepositoryTest extends myTest
{
    private TipoDeActividadRepositoryInterface $repository;
    private TipoDeActividadFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $this->factory = new TipoDeActividadFactory();
    }

    public function test_guardar_nuevo_tipoDeActividad()
    {
        // Crear instancia usando factory
        $oTipoDeActividad = $this->factory->createSimple();
        $id = $oTipoDeActividad->getId_tipo_activ();

        // Guardar
        $result = $this->repository->Guardar($oTipoDeActividad);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oTipoDeActividadGuardado = $this->repository->findById($id);
        $this->assertNotNull($oTipoDeActividadGuardado);
        $this->assertEquals($id, $oTipoDeActividadGuardado->getId_tipo_activ());

        // Limpiar
        $this->repository->Eliminar($oTipoDeActividadGuardado);
    }

    public function test_actualizar_tipoDeActividad_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoDeActividad = $this->factory->createSimple();
        $id = $oTipoDeActividad->getId_tipo_activ();
        $this->repository->Guardar($oTipoDeActividad);

        // Crear otra instancia con datos diferentes para actualizar
        $oTipoDeActividadUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oTipoDeActividadUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oTipoDeActividadActualizado = $this->repository->findById($id);
        $this->assertNotNull($oTipoDeActividadActualizado);

        // Limpiar
        $this->repository->Eliminar($oTipoDeActividadActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoDeActividad = $this->factory->createSimple();
        $id = $oTipoDeActividad->getId_tipo_activ();
        $this->repository->Guardar($oTipoDeActividad);

        // Buscar por ID
        $oTipoDeActividadEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oTipoDeActividadEncontrado);
        $this->assertInstanceOf(TipoDeActividad::class, $oTipoDeActividadEncontrado);
        $this->assertEquals($id, $oTipoDeActividadEncontrado->getId_tipo_activ());

        // Limpiar
        $this->repository->Eliminar($oTipoDeActividadEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oTipoDeActividad = $this->repository->findById($id_inexistente);

        $this->assertNull($oTipoDeActividad);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoDeActividad = $this->factory->createSimple();
        $id = $oTipoDeActividad->getId_tipo_activ();
        $this->repository->Guardar($oTipoDeActividad);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_tipo_activ', $aDatos);
        $this->assertEquals($id, $aDatos['id_tipo_activ']);

        // Limpiar
        $oTipoDeActividadParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oTipoDeActividadParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_tipoDeActividad()
    {
        // Crear y guardar instancia usando factory
        $oTipoDeActividad = $this->factory->createSimple();
        $id = $oTipoDeActividad->getId_tipo_activ();
        $this->repository->Guardar($oTipoDeActividad);

        // Verificar que existe
        $oTipoDeActividadExiste = $this->repository->findById($id);
        $this->assertNotNull($oTipoDeActividadExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oTipoDeActividadExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oTipoDeActividadEliminado = $this->repository->findById($id);
        $this->assertNull($oTipoDeActividadEliminado);
    }

    public function test_get_array_tipos_actividad_sin_filtros()
    {
        $result = $this->repository->getArrayTiposActividad();

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_tipos_de_procesos_sin_filtros()
    {
        $result = $this->repository->getTiposDeProcesos();

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_id_tipo_posibles_sin_filtros()
    {
        $result = $this->repository->getId_tipoPosibles('^...', '^');

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_nom_tipo_posibles_sin_filtros()
    {
        $result = $this->repository->getNom_tipoPosibles(3, '^');

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_asistentes_posibles_sin_filtros()
    {
        $aAsistentes = [
            "n" => 1,
            "nax" => 2,
            "agd" => 3,
            "s" => 4,
            "sg" => 5,
            "sss+" => 6,
            "sr" => 7,
            "sr-nax" => 8,
            "sr-agd" => 9,
            "all" => '.'
        ];
        $afAsistentes = array_flip($aAsistentes);
        $result = $this->repository->getAsistentesPosibles($afAsistentes, '^');

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_actividades_posibles_sin_filtros()
    {
        $aAsistentes = [
            "n" => 1,
            "nax" => 2,
            "agd" => 3,
            "s" => 4,
            "sg" => 5,
            "sss+" => 6,
            "sr" => 7,
            "sr-nax" => 8,
            "sr-agd" => 9,
            "all" => '.'
        ];
        $afAsistentes = array_flip($aAsistentes);
        $result = $this->repository->getActividadesPosibles(1, $afAsistentes, '^');

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_sfsv_posibles_sin_filtros()
    {
        $aAsistentes = [
            "n" => 1,
            "nax" => 2,
            "agd" => 3,
            "s" => 4,
            "sg" => 5,
            "sss+" => 6,
            "sr" => 7,
            "sr-nax" => 8,
            "sr-agd" => 9,
            "all" => '.'
        ];
        $afAsistentes = array_flip($aAsistentes);
        $result = $this->repository->getSfsvPosibles($afAsistentes);

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_tipos_de_actividades_sin_filtros()
    {
        $result = $this->repository->getTiposDeActividades();

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
