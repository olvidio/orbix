<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\TipoCentroRepositoryInterface;
use src\ubis\domain\entity\TipoCentro;
use Tests\factories\ubis\TipoCentroFactory;
use Tests\myTest;

class PgTipoCentroRepositoryTest extends myTest
{
    private TipoCentroRepositoryInterface $repository;
    private TipoCentroFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TipoCentroRepositoryInterface::class);
        $this->factory = new TipoCentroFactory();
    }

    public function test_guardar_nuevo_tipoCentro()
    {
        // Crear instancia usando factory
        $oTipoCentro = $this->factory->createSimple();
        $id = $oTipoCentro->getTipoCtrVo()->value();

        // Guardar
        $result = $this->repository->Guardar($oTipoCentro);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oTipoCentroGuardado = $this->repository->findById($id);
        $this->assertNotNull($oTipoCentroGuardado);
        $this->assertEquals($id, $oTipoCentroGuardado->getTipoCtrVo()->value());

        // Limpiar
        $this->repository->Eliminar($oTipoCentroGuardado);
    }

    public function test_actualizar_tipoCentro_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoCentro = $this->factory->createSimple();
        $id = $oTipoCentro->getTipoCtrVo()->value();
        $this->repository->Guardar($oTipoCentro);

        // Crear otra instancia con datos diferentes para actualizar
        $oTipoCentroUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oTipoCentroUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oTipoCentroActualizado = $this->repository->findById($id);
        $this->assertNotNull($oTipoCentroActualizado);

        // Limpiar
        $this->repository->Eliminar($oTipoCentroActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoCentro = $this->factory->createSimple();
        $id = $oTipoCentro->getTipoCtrVo()->value();
        $this->repository->Guardar($oTipoCentro);

        // Buscar por ID
        $oTipoCentroEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oTipoCentroEncontrado);
        $this->assertInstanceOf(TipoCentro::class, $oTipoCentroEncontrado);
        $this->assertEquals($id, $oTipoCentroEncontrado->getTipoCtrVo()->value());

        // Limpiar
        $this->repository->Eliminar($oTipoCentroEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oTipoCentro = $this->repository->findById($id_inexistente);

        $this->assertNull($oTipoCentro);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoCentro = $this->factory->createSimple();
        $id = $oTipoCentro->getTipoCtrVo()->value();
        $this->repository->Guardar($oTipoCentro);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('tipo_ctr', $aDatos);
        $this->assertEquals($id, $aDatos['tipo_ctr']);

        // Limpiar
        $oTipoCentroParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oTipoCentroParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_tipoCentro()
    {
        // Crear y guardar instancia usando factory
        $oTipoCentro = $this->factory->createSimple();
        $id = $oTipoCentro->getTipoCtrVo()->value();
        $this->repository->Guardar($oTipoCentro);

        // Verificar que existe
        $oTipoCentroExiste = $this->repository->findById($id);
        $this->assertNotNull($oTipoCentroExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oTipoCentroExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oTipoCentroEliminado = $this->repository->findById($id);
        $this->assertNull($oTipoCentroEliminado);
    }

    public function test_get_tipos_centro_sin_filtros()
    {
        $result = $this->repository->getTiposCentro();

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
