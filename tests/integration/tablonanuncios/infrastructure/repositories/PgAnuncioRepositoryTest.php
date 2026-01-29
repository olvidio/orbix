<?php

namespace Tests\integration\tablonanuncios\infrastructure\repositories;

use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\domain\entity\Anuncio;
use src\tablonanuncios\domain\value_objects\AnuncioId;
use Tests\factories\tablonanuncios\AnuncioFactory;
use Tests\myTest;

class PgAnuncioRepositoryTest extends myTest
{
    private AnuncioRepositoryInterface $repository;
    private AnuncioFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(AnuncioRepositoryInterface::class);
        $this->factory = new AnuncioFactory();
    }

    public function test_guardar_nuevo_anuncio()
    {
        // Crear instancia usando factory
        $oAnuncio = $this->factory->createSimple();
        $id = $oAnuncio->getUuid_item();

        // Guardar
        $result = $this->repository->Guardar($oAnuncio);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oAnuncioGuardado = $this->repository->findById($id);
        $this->assertNotNull($oAnuncioGuardado);
        $this->assertEquals($id, $oAnuncioGuardado->getUuid_item());

        // Limpiar
        $this->repository->Eliminar($oAnuncioGuardado);
    }

    public function test_actualizar_anuncio_existente()
    {
        // Crear y guardar instancia usando factory
        $oAnuncio = $this->factory->createSimple();
        $id = $oAnuncio->getUuid_item();
        $this->repository->Guardar($oAnuncio);

        // Crear otra instancia con datos diferentes para actualizar
        $oAnuncioUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oAnuncioUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oAnuncioActualizado = $this->repository->findById($id);
        $this->assertNotNull($oAnuncioActualizado);

        // Limpiar
        $this->repository->Eliminar($oAnuncioActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oAnuncio = $this->factory->createSimple();
        $id = $oAnuncio->getUuid_item();
        $this->repository->Guardar($oAnuncio);

        // Buscar por ID
        $oAnuncioEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oAnuncioEncontrado);
        $this->assertInstanceOf(Anuncio::class, $oAnuncioEncontrado);
        $this->assertEquals($id, $oAnuncioEncontrado->getUuid_item());

        // Limpiar
        $this->repository->Eliminar($oAnuncioEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = '550e8400-e29b-41d4-a716-446655440000';
        $oAnuncio = $this->repository->findById(AnuncioId::fromString($id_inexistente));

        $this->assertNull($oAnuncio);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oAnuncio = $this->factory->createSimple();
        $id = $oAnuncio->getUuid_item();
        $this->repository->Guardar($oAnuncio);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('uuid_item', $aDatos);
        $this->assertEquals($id, $aDatos['uuid_item']);

        // Limpiar
        $oAnuncioParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oAnuncioParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = '550e8400-e29b-41d4-a716-446655440000';
        $aDatos = $this->repository->datosById(AnuncioId::fromString($id_inexistente));

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_anuncio()
    {
        // Crear y guardar instancia usando factory
        $oAnuncio = $this->factory->createSimple();
        $id = $oAnuncio->getUuid_item();
        $this->repository->Guardar($oAnuncio);

        // Verificar que existe
        $oAnuncioExiste = $this->repository->findById($id);
        $this->assertNotNull($oAnuncioExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oAnuncioExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oAnuncioEliminado = $this->repository->findById($id);
        $this->assertNull($oAnuncioEliminado);
    }

    public function test_get_anuncios_sin_filtros()
    {
        $result = $this->repository->getAnuncios();

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
