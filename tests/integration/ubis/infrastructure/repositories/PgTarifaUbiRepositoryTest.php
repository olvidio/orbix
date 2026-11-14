<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\TarifaUbi;
use Tests\myTest;
use Tests\factories\ubis\TarifaUbiFactory;

class PgTarifaUbiRepositoryTest extends myTest
{
    private TarifaUbiRepositoryInterface $repository;
    private TarifaUbiFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TarifaUbiRepositoryInterface::class);
        $this->factory = new TarifaUbiFactory();
    }

    public function test_guardar_nuevo_tarifaUbi()
    {
        // Crear instancia usando factory
        $oTarifaUbi = $this->factory->createSimple();
        $id = $oTarifaUbi->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oTarifaUbi);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oTarifaUbiGuardado = $this->repository->findById($id);
        $this->assertNotNull($oTarifaUbiGuardado);
        $this->assertEquals($id, $oTarifaUbiGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oTarifaUbiGuardado);
    }

    public function test_actualizar_tarifaUbi_existente()
    {
        // Crear y guardar instancia usando factory
        $oTarifaUbi = $this->factory->createSimple();
        $id = $oTarifaUbi->getId_item();
        $this->repository->Guardar($oTarifaUbi);

        // Crear otra instancia con datos diferentes para actualizar
        $oTarifaUbiUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oTarifaUbiUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oTarifaUbiActualizado = $this->repository->findById($id);
        $this->assertNotNull($oTarifaUbiActualizado);

        // Limpiar
        $this->repository->Eliminar($oTarifaUbiActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTarifaUbi = $this->factory->createSimple();
        $id = $oTarifaUbi->getId_item();
        $this->repository->Guardar($oTarifaUbi);

        // Buscar por ID
        $oTarifaUbiEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oTarifaUbiEncontrado);
        $this->assertInstanceOf(TarifaUbi::class, $oTarifaUbiEncontrado);
        $this->assertEquals($id, $oTarifaUbiEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oTarifaUbiEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oTarifaUbi = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oTarifaUbi);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTarifaUbi = $this->factory->createSimple();
        $id = $oTarifaUbi->getId_item();
        $this->repository->Guardar($oTarifaUbi);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oTarifaUbiParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oTarifaUbiParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_tarifaUbi()
    {
        // Crear y guardar instancia usando factory
        $oTarifaUbi = $this->factory->createSimple();
        $id = $oTarifaUbi->getId_item();
        $this->repository->Guardar($oTarifaUbi);

        // Verificar que existe
        $oTarifaUbiExiste = $this->repository->findById($id);
        $this->assertNotNull($oTarifaUbiExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oTarifaUbiExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oTarifaUbiEliminado = $this->repository->findById($id);
        $this->assertNull($oTarifaUbiEliminado);
    }

    public function test_get_tarifa_ubis_sin_filtros()
    {
        $result = $this->repository->matching(\src\shared\domain\criteria\Criteria::create());
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();
        
        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
    }

    /**
     * Regresión: `Eliminar` borraba por `id_ubi`, así que al borrar una tarifa se borraban todas las de
     * esa casa (todos los años y tipos de tarifa).
     */
    public function test_eliminar_una_tarifa_no_borra_las_demas_de_la_casa(): void
    {
        $una = $this->factory->createSimple();
        $otra = $this->factory->createSimple($una->getId_item() + 1);
        $otra->setId_ubi($una->getId_ubi());
        $this->assertNull($this->repository->findById($una->getId_item()), 'el id de prueba no existe en la BD');
        $this->assertNull($this->repository->findById($otra->getId_item()), 'el id de prueba no existe en la BD');

        try {
            $this->assertTrue($this->repository->Guardar($una));
            $this->assertTrue($this->repository->Guardar($otra));

            $this->assertTrue($this->repository->Eliminar($una));

            $this->assertNull($this->repository->findById($una->getId_item()));
            $this->assertNotNull($this->repository->findById($otra->getId_item()), 'la otra tarifa de la misma casa sigue ahí');
        } finally {
            $this->repository->Eliminar($una);
            $this->repository->Eliminar($otra);
        }
    }
}
