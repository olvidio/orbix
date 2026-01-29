<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\TipoCasaRepositoryInterface;
use src\ubis\domain\entity\TipoCasa;
use Tests\factories\ubis\TipoCasaFactory;
use Tests\myTest;

class PgTipoCasaRepositoryTest extends myTest
{
    private TipoCasaRepositoryInterface $repository;
    private TipoCasaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TipoCasaRepositoryInterface::class);
        $this->factory = new TipoCasaFactory();
    }

    public function test_guardar_nuevo_tipoCasa()
    {
        // Crear instancia usando factory
        $oTipoCasa = $this->factory->createSimple();
        $id = $oTipoCasa->getTipoCasaVo()->value();

        // Guardar
        $result = $this->repository->Guardar($oTipoCasa);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oTipoCasaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oTipoCasaGuardado);
        $this->assertEquals($id, $oTipoCasaGuardado->getTipoCasaVo()->value());

        // Limpiar
        $this->repository->Eliminar($oTipoCasaGuardado);
    }

    public function test_actualizar_tipoCasa_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipocasa = $this->factory->createSimple();
        $id = $oTipocasa->getTipoCasaVo()->value();
        $this->repository->Guardar($oTipocasa);

        // Crear otra instancia con datos diferentes para actualizar
        $oTipocasaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oTipocasaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oTipoCasaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oTipoCasaActualizado);

        // Limpiar
        $this->repository->Eliminar($oTipoCasaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoCasa = $this->factory->createSimple();
        $id = $oTipoCasa->getTipoCasaVo()->value();
        $this->repository->Guardar($oTipoCasa);

        // Buscar por ID
        $oTipoCasaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oTipoCasaEncontrado);
        $this->assertInstanceOf(TipoCasa::class, $oTipoCasaEncontrado);
        $this->assertEquals($id, $oTipoCasaEncontrado->getTipoCasaVo()->value());

        // Limpiar
        $this->repository->Eliminar($oTipoCasaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oTipoCasa = $this->repository->findById($id_inexistente);

        $this->assertNull($oTipoCasa);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoCasa = $this->factory->createSimple();
        $id = $oTipoCasa->getTipoCasaVo()->value();
        $this->repository->Guardar($oTipoCasa);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('tipo_casa', $aDatos);
        $this->assertEquals($id, $aDatos['tipo_casa']);

        // Limpiar
        $oTipoCasaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oTipoCasaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_tipoCasa()
    {
        // Crear y guardar instancia usando factory
        $oTipoCasa = $this->factory->createSimple();
        $id = $oTipoCasa->getTipoCasaVo()->value();
        $this->repository->Guardar($oTipoCasa);

        // Verificar que existe
        $oTipoCasaExiste = $this->repository->findById($id);
        $this->assertNotNull($oTipoCasaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oTipoCasaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oTipoCasaEliminado = $this->repository->findById($id);
        $this->assertNull($oTipoCasaEliminado);
    }

    public function test_get_tipos_casa_sin_filtros()
    {
        $result = $this->repository->getTiposCasa();

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
