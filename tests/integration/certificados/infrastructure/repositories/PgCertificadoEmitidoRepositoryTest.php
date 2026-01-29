<?php

namespace Tests\integration\certificados\infrastructure\repositories;

use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoEmitido;
use Tests\factories\certificados\CertificadoEmitidoFactory;
use Tests\myTest;

class PgCertificadoEmitidoRepositoryTest extends myTest
{
    private CertificadoEmitidoRepositoryInterface $repository;
    private CertificadoEmitidoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);
        $this->factory = new CertificadoEmitidoFactory();

        // TODO: hay que cambiar la conexión a una region que tenga la tabla e_certificados_rstgr
    }

    public function test_guardar_nuevo_certificadoEmitido()
    {
        // Crear instancia usando factory
        $oCertificadoEmitido = $this->factory->createSimple();
        $id = $oCertificadoEmitido->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oCertificadoEmitido);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCertificadoEmitidoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCertificadoEmitidoGuardado);
        $this->assertEquals($id, $oCertificadoEmitidoGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCertificadoEmitidoGuardado);
    }

    public function test_actualizar_certificadoEmitido_existente()
    {
        // Crear y guardar instancia usando factory
        $oCertificadoEmitido = $this->factory->createSimple();
        $id = $oCertificadoEmitido->getId_item();
        $this->repository->Guardar($oCertificadoEmitido);

        // Crear otra instancia con datos diferentes para actualizar
        $oCertificadoEmitidoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCertificadoEmitidoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCertificadoEmitidoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCertificadoEmitidoActualizado);

        // Limpiar
        $this->repository->Eliminar($oCertificadoEmitidoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCertificadoEmitido = $this->factory->createSimple();
        $id = $oCertificadoEmitido->getId_item();
        $this->repository->Guardar($oCertificadoEmitido);

        // Buscar por ID
        $oCertificadoEmitidoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCertificadoEmitidoEncontrado);
        $this->assertInstanceOf(CertificadoEmitido::class, $oCertificadoEmitidoEncontrado);
        $this->assertEquals($id, $oCertificadoEmitidoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCertificadoEmitidoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCertificadoEmitido = $this->repository->findById($id_inexistente);

        $this->assertNull($oCertificadoEmitido);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCertificadoEmitido = $this->factory->createSimple();
        $id = $oCertificadoEmitido->getId_item();
        $this->repository->Guardar($oCertificadoEmitido);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oCertificadoEmitidoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCertificadoEmitidoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_certificadoEmitido()
    {
        // Crear y guardar instancia usando factory
        $oCertificadoEmitido = $this->factory->createSimple();
        $id = $oCertificadoEmitido->getId_item();
        $this->repository->Guardar($oCertificadoEmitido);

        // Verificar que existe
        $oCertificadoEmitidoExiste = $this->repository->findById($id);
        $this->assertNotNull($oCertificadoEmitidoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCertificadoEmitidoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCertificadoEmitidoEliminado = $this->repository->findById($id);
        $this->assertNull($oCertificadoEmitidoEliminado);
    }

    public function test_get_certificados_sin_filtros()
    {
        $result = $this->repository->getCertificados();

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
