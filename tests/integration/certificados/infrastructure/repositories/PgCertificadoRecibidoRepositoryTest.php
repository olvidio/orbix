<?php

namespace Tests\integration\certificados\infrastructure\repositories;

use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoRecibido;
use Tests\myTest;
use Tests\factories\certificados\CertificadoRecibidoFactory;

class PgCertificadoRecibidoRepositoryTest extends myTest
{
    private CertificadoRecibidoRepositoryInterface $repository;
    private CertificadoRecibidoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CertificadoRecibidoRepositoryInterface::class);
        $this->factory = new CertificadoRecibidoFactory();
    }

    public function test_guardar_nuevo_certificadoRecibido()
    {
        // Crear instancia usando factory
        $oCertificadoRecibido = $this->factory->createSimple();
        $id = $oCertificadoRecibido->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oCertificadoRecibido);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCertificadoRecibidoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCertificadoRecibidoGuardado);
        $this->assertEquals($id, $oCertificadoRecibidoGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCertificadoRecibidoGuardado);
    }

    public function test_actualizar_certificadoRecibido_existente()
    {
        // Crear y guardar instancia usando factory
        $oCertificadoRecibido = $this->factory->createSimple();
        $id = $oCertificadoRecibido->getId_item();
        $this->repository->Guardar($oCertificadoRecibido);

        // Crear otra instancia con datos diferentes para actualizar
        $oCertificadoRecibidoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCertificadoRecibidoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCertificadoRecibidoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCertificadoRecibidoActualizado);

        // Limpiar
        $this->repository->Eliminar($oCertificadoRecibidoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCertificadoRecibido = $this->factory->createSimple();
        $id = $oCertificadoRecibido->getId_item();
        $this->repository->Guardar($oCertificadoRecibido);

        // Buscar por ID
        $oCertificadoRecibidoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCertificadoRecibidoEncontrado);
        $this->assertInstanceOf(CertificadoRecibido::class, $oCertificadoRecibidoEncontrado);
        $this->assertEquals($id, $oCertificadoRecibidoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCertificadoRecibidoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCertificadoRecibido = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oCertificadoRecibido);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCertificadoRecibido = $this->factory->createSimple();
        $id = $oCertificadoRecibido->getId_item();
        $this->repository->Guardar($oCertificadoRecibido);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oCertificadoRecibidoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCertificadoRecibidoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_certificadoRecibido()
    {
        // Crear y guardar instancia usando factory
        $oCertificadoRecibido = $this->factory->createSimple();
        $id = $oCertificadoRecibido->getId_item();
        $this->repository->Guardar($oCertificadoRecibido);

        // Verificar que existe
        $oCertificadoRecibidoExiste = $this->repository->findById($id);
        $this->assertNotNull($oCertificadoRecibidoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCertificadoRecibidoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCertificadoRecibidoEliminado = $this->repository->findById($id);
        $this->assertNull($oCertificadoRecibidoEliminado);
    }

    public function test_get_certificados_sin_filtros()
    {
        $result = $this->repository->getCertificados();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
