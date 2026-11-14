<?php

namespace Tests\integration\inventario\infrastructure\persistence\postgresql;

use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Equipaje;
use Tests\myTest;
use Tests\factories\inventario\EquipajeFactory;

class PgEquipajeRepositoryTest extends myTest
{
    private EquipajeRepositoryInterface $repository;
    private EquipajeFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
        $this->factory = new EquipajeFactory();
    }

    public function test_guardar_nuevo_equipaje()
    {
        $oEquipaje = $this->factory->createSimple();
        $id = $oEquipaje->getId_equipaje();

        $result = $this->repository->Guardar($oEquipaje);
        $this->assertTrue($result);

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertEquals($id, $oGuardado->getId_equipaje());

        $this->repository->Eliminar($oGuardado);
    }

    public function test_actualizar_equipaje_existente()
    {
        $oEquipaje = $this->factory->createSimple();
        $id = $oEquipaje->getId_equipaje();
        $this->repository->Guardar($oEquipaje);

        $oActualizado = $this->factory->createSimple($id);
        $result = $this->repository->Guardar($oActualizado);
        $this->assertTrue($result);

        $oObtenido = $this->repository->findById($id);
        $this->assertNotNull($oObtenido);

        $this->repository->Eliminar($oObtenido);
    }

    public function test_find_by_id_existente()
    {
        $oEquipaje = $this->factory->createSimple();
        $id = $oEquipaje->getId_equipaje();
        $this->repository->Guardar($oEquipaje);

        $oEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncontrado);
        $this->assertInstanceOf(Equipaje::class, $oEncontrado);
        $this->assertEquals($id, $oEncontrado->getId_equipaje());

        $this->repository->Eliminar($oEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $oEquipaje = $this->repository->findById(999999);
        $this->assertNull($oEquipaje);
    }

    public function test_eliminar_equipaje()
    {
        $oEquipaje = $this->factory->createSimple();
        $id = $oEquipaje->getId_equipaje();
        $this->repository->Guardar($oEquipaje);

        $oGuardado = $this->repository->findById($id);
        $result = $this->repository->Eliminar($oGuardado);
        $this->assertTrue($result);

        $oEliminado = $this->repository->findById($id);
        $this->assertNull($oEliminado);
    }

    private function equipajeEntre(string $ini, string $fin): \src\inventario\domain\entity\Equipaje
    {
        $o = $this->factory->createSimple();
        $o->setF_ini(new \src\shared\domain\value_objects\DateTimeLocal($ini));
        $o->setF_fin(new \src\shared\domain\value_objects\DateTimeLocal($fin));
        $this->repository->Guardar($o);

        return $o;
    }

    public function test_equipajes_desde_una_fecha_y_coincidentes_con_un_periodo(): void
    {
        $antiguo = $this->equipajeEntre('2091-01-10', '2091-01-20');
        $nuevo = $this->equipajeEntre('2091-03-10', '2091-03-20');
        try {
            $desde = $this->repository->getArrayEquipajes('2091-02-01');
            $this->assertArrayHasKey($nuevo->getId_equipaje(), $desde);
            $this->assertArrayNotHasKey($antiguo->getId_equipaje(), $desde);

            $coinciden = $this->repository->getEquipajesCoincidentes('2091-01-15', '2091-02-15');
            $this->assertContains($antiguo->getId_equipaje(), $coinciden);
            $this->assertNotContains($nuevo->getId_equipaje(), $coinciden);
            $this->assertContains($antiguo->getId_equipaje(), $this->repository->getEquipajesCoincidentes('2091-01-12', '2091-01-14'), 'un periodo dentro del equipaje');
            $this->assertContains($antiguo->getId_equipaje(), $this->repository->getEquipajesCoincidentes('2091-01-01', '2091-01-31'), 'un periodo que abarca el equipaje');
            $this->assertNotContains($antiguo->getId_equipaje(), $this->repository->getEquipajesCoincidentes('2091-01-21', '2091-01-31'), 'empieza al día siguiente');
        } finally {
            $this->repository->Eliminar($antiguo);
            $this->repository->Eliminar($nuevo);
        }
    }

    /**
     * Regresión: la fecha llega tal cual del POST (`lista_equipajes_desde_fecha`) y se interpolaba en el SQL.
     */
    public function test_la_fecha_no_se_interpola_en_el_sql(): void
    {
        $o = $this->equipajeEntre('2091-01-10', '2091-01-20');
        try {
            $this->assertArrayNotHasKey($o->getId_equipaje(), $this->repository->getArrayEquipajes("2999-01-01' OR '1'='1"));
        } catch (\RuntimeException $e) {
            // Como parámetro, la cadena es una fecha inválida: PostgreSQL la rechaza sin ejecutar nada inyectado.
            $this->assertStringContainsString('invalid input syntax for type date', $e->getMessage());
        } finally {
            $this->repository->Eliminar($o);
        }
    }
}
