<?php

namespace Tests\integration\encargossacd\infrastructure\repositories;

use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\EncargoTipo;
use Tests\myTest;
use Tests\factories\encargossacd\EncargoTipoFactory;

class PgEncargoTipoRepositoryTest extends myTest
{
    private EncargoTipoRepositoryInterface $repository;
    private EncargoTipoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);
        $this->factory = new EncargoTipoFactory();
    }

    public function test_guardar_nuevo_encargoTipo()
    {
        // Crear instancia usando factory
        $oEncargoTipo = $this->factory->createSimple();
        $id = $oEncargoTipo->getId_tipo_enc();

        // Guardar
        $result = $this->repository->Guardar($oEncargoTipo);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oEncargoTipoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoTipoGuardado);
        $this->assertEquals($id, $oEncargoTipoGuardado->getId_tipo_enc());

        // Limpiar
        $this->repository->Eliminar($oEncargoTipoGuardado);
    }

    public function test_actualizar_encargoTipo_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoTipo = $this->factory->createSimple();
        $id = $oEncargoTipo->getId_tipo_enc();
        $this->repository->Guardar($oEncargoTipo);

        // Crear otra instancia con datos diferentes para actualizar
        $oEncargoTipoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oEncargoTipoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oEncargoTipoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoTipoActualizado);

        // Limpiar
        $this->repository->Eliminar($oEncargoTipoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoTipo = $this->factory->createSimple();
        $id = $oEncargoTipo->getId_tipo_enc();
        $this->repository->Guardar($oEncargoTipo);

        // Buscar por ID
        $oEncargoTipoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoTipoEncontrado);
        $this->assertInstanceOf(EncargoTipo::class, $oEncargoTipoEncontrado);
        $this->assertEquals($id, $oEncargoTipoEncontrado->getId_tipo_enc());

        // Limpiar
        $this->repository->Eliminar($oEncargoTipoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oEncargoTipo = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oEncargoTipo);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoTipo = $this->factory->createSimple();
        $id = $oEncargoTipo->getId_tipo_enc();
        $this->repository->Guardar($oEncargoTipo);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_tipo_enc', $aDatos);
        $this->assertEquals($id, $aDatos['id_tipo_enc']);

        // Limpiar
        $oEncargoTipoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oEncargoTipoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_encargoTipo()
    {
        // Crear y guardar instancia usando factory
        $oEncargoTipo = $this->factory->createSimple();
        $id = $oEncargoTipo->getId_tipo_enc();
        $this->repository->Guardar($oEncargoTipo);

        // Verificar que existe
        $oEncargoTipoExiste = $this->repository->findById($id);
        $this->assertNotNull($oEncargoTipoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oEncargoTipoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oEncargoTipoEliminado = $this->repository->findById($id);
        $this->assertNull($oEncargoTipoEliminado);
    }

    public function test_get_encargo_tipos_sin_filtros()
    {
        $result = $this->repository->getEncargoTipos();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
