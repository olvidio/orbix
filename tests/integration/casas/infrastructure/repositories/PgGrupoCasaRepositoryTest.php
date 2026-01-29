<?php

namespace Tests\integration\casas\infrastructure\repositories;

use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\domain\entity\GrupoCasa;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use Tests\factories\casas\GrupoCasaFactory;
use Tests\factories\ubis\CasaFactory;
use Tests\myTest;

class PgGrupoCasaRepositoryTest extends myTest
{
    private GrupoCasaRepositoryInterface $repository;
    private GrupoCasaFactory $factory;
    private CasaRepositoryInterface $casaRepository;
    private array $casasCreadas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(GrupoCasaRepositoryInterface::class);
        $this->casaRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        $this->factory = new GrupoCasaFactory();

        // Crear las casas/ubis necesarias para las foreign keys
        $casaFactory = new CasaFactory();
        $casa1 = $casaFactory->createSimple(-10019001);
        $casa2 = $casaFactory->createSimple(-10019002);

        $this->casaRepository->Guardar($casa1);
        $this->casaRepository->Guardar($casa2);

        $this->casasCreadas = [$casa1, $casa2];
    }

    public function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->casasCreadas as $casa) {
            $this->casaRepository->Eliminar($casa);
        }
    }

    public function test_guardar_nuevo_grupoCasa()
    {
        // Crear instancia usando factory
        $oGrupoCasa = $this->factory->createSimple();
        $id = $oGrupoCasa->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oGrupoCasa);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oGrupoCasaGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGrupoCasaGuardado);
        $this->assertEquals($id, $oGrupoCasaGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oGrupoCasaGuardado);
    }

    public function test_actualizar_grupoCasa_existente()
    {
        // Crear y guardar instancia usando factory
        $oGrupoCasa = $this->factory->createSimple();
        $id = $oGrupoCasa->getId_item();
        $this->repository->Guardar($oGrupoCasa);

        // Crear otra instancia con datos diferentes para actualizar
        $oGrupoCasaUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oGrupoCasaUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oGrupoCasaActualizado = $this->repository->findById($id);
        $this->assertNotNull($oGrupoCasaActualizado);

        // Limpiar
        $this->repository->Eliminar($oGrupoCasaActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oGrupoCasa = $this->factory->createSimple();
        $id = $oGrupoCasa->getId_item();
        $this->repository->Guardar($oGrupoCasa);

        // Buscar por ID
        $oGrupoCasaEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oGrupoCasaEncontrado);
        $this->assertInstanceOf(GrupoCasa::class, $oGrupoCasaEncontrado);
        $this->assertEquals($id, $oGrupoCasaEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oGrupoCasaEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oGrupoCasa = $this->repository->findById($id_inexistente);

        $this->assertNull($oGrupoCasa);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oGrupoCasa = $this->factory->createSimple();
        $id = $oGrupoCasa->getId_item();
        $this->repository->Guardar($oGrupoCasa);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oGrupoCasaParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oGrupoCasaParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_grupoCasa()
    {
        // Crear y guardar instancia usando factory
        $oGrupoCasa = $this->factory->createSimple();
        $id = $oGrupoCasa->getId_item();
        $this->repository->Guardar($oGrupoCasa);

        // Verificar que existe
        $oGrupoCasaExiste = $this->repository->findById($id);
        $this->assertNotNull($oGrupoCasaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oGrupoCasaExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oGrupoCasaEliminado = $this->repository->findById($id);
        $this->assertNull($oGrupoCasaEliminado);
    }

    public function test_get_grupo_casas_sin_filtros()
    {
        $result = $this->repository->getGrupoCasas();

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
