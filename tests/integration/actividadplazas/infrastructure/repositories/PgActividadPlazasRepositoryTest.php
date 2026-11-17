<?php

namespace Tests\integration\actividadplazas\infrastructure\persistence\postgresql;

use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use Tests\myTest;
use Tests\factories\actividadplazas\ActividadPlazasFactory;

class PgActividadPlazasRepositoryTest extends myTest
{
    private ActividadPlazasRepositoryInterface $repository;
    private ActividadPlazasFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActividadPlazasRepositoryInterface::class);
        $this->factory = new ActividadPlazasFactory();
    }

    public function test_guardar_nuevo_actividadPlazas()
    {
        // Crear instancia usando factory
        $oActividadPlazas = $this->factory->createSimple();
        $id = $oActividadPlazas->getId_activ();

        // Guardar
        $result = $this->repository->Guardar($oActividadPlazas);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oActividadPlazasGuardado = $this->repository->findById($id, 1, 'test_dl');
        $this->assertNotNull($oActividadPlazasGuardado);
        $this->assertEquals($id, $oActividadPlazasGuardado->getId_activ());

        // Limpiar
        $this->repository->Eliminar($oActividadPlazasGuardado);
    }

    public function test_actualizar_actividadPlazas_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadPlazas = $this->factory->createSimple();
        $id = $oActividadPlazas->getId_activ();
        $this->repository->Guardar($oActividadPlazas);

        // Crear otra instancia con datos diferentes para actualizar
        $oActividadPlazasUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oActividadPlazasUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oActividadPlazasActualizado = $this->repository->findById($id, 1, 'test_dl');
        $this->assertNotNull($oActividadPlazasActualizado);

        // Limpiar
        $this->repository->Eliminar($oActividadPlazasActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadPlazas = $this->factory->createSimple();
        $id = $oActividadPlazas->getId_activ();
        $this->repository->Guardar($oActividadPlazas);

        // Buscar por ID
        $oActividadPlazasEncontrado = $this->repository->findById($id, 1, 'test_dl');
        $this->assertNotNull($oActividadPlazasEncontrado);
        $this->assertInstanceOf(ActividadPlazas::class, $oActividadPlazasEncontrado);
        $this->assertEquals($id, $oActividadPlazasEncontrado->getId_activ());

        // Limpiar
        $this->repository->Eliminar($oActividadPlazasEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oActividadPlazas = $this->repository->findById($id_inexistente, 1, 'test_dl');
        
        $this->assertNull($oActividadPlazas);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadPlazas = $this->factory->createSimple();
        $id = $oActividadPlazas->getId_activ();
        $this->repository->Guardar($oActividadPlazas);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id, 1, 'test_dl');
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_activ', $aDatos);
        $this->assertEquals($id, $aDatos['id_activ']);

        // Limpiar
        $oActividadPlazasParaborrar = $this->repository->findById($id, 1, 'test_dl');
        $this->repository->Eliminar($oActividadPlazasParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente, 1, 'test_dl');
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_actividadPlazas()
    {
        // Crear y guardar instancia usando factory
        $oActividadPlazas = $this->factory->createSimple();
        $id = $oActividadPlazas->getId_activ();
        $this->repository->Guardar($oActividadPlazas);

        // Verificar que existe
        $oActividadPlazasExiste = $this->repository->findById($id, 1, 'test_dl');
        $this->assertNotNull($oActividadPlazasExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oActividadPlazasExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oActividadPlazasEliminado = $this->repository->findById($id, 1, 'test_dl');
        $this->assertNull($oActividadPlazasEliminado);
    }

    public function test_get_actividades_plazas_sin_filtros()
    {
        $result = $this->repository->matching(\src\shared\domain\criteria\Criteria::create());
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    /** @return list<ActividadPlazas> */
    private function filasDe(int $id_activ): array
    {
        return $this->repository->matching(
            \src\shared\domain\criteria\Criteria::create()
                ->where(\src\shared\domain\criteria\Filter::eq(\src\actividadplazas\domain\criteria\ActividadPlazasField::IdActiv, $id_activ))
                ->orderBy(\src\actividadplazas\domain\criteria\ActividadPlazasField::IdDl)
        );
    }

    /** La clave es (id_activ, id_dl, dl_tabla): dos dl en la misma actividad son dos filas independientes. */
    public function test_dos_dl_en_la_misma_actividad_se_guardan_y_borran_por_separado(): void
    {
        $una = $this->factory->createSimple();
        $id = $una->getId_activ();
        $una->setPlazas(10);
        $otra = $this->factory->createSimple($id);
        $otra->setId_dl(2);
        $otra->setPlazas(20);

        try {
            $this->assertTrue($this->repository->Guardar($una));
            $this->assertTrue($this->repository->Guardar($otra));
            $this->assertSame([[1, '10'], [2, '20']], array_map(fn (ActividadPlazas $p) => [$p->getId_dl(), $p->getPlazas()], $this->filasDe($id)), 'guardar la de otra dl no pisa la primera');

            $una->setPlazas(11);
            $this->assertTrue($this->repository->Guardar($una));
            $this->assertSame([[1, '11'], [2, '20']], array_map(fn (ActividadPlazas $p) => [$p->getId_dl(), $p->getPlazas()], $this->filasDe($id)), 'actualizar una no cambia la otra');

            $this->assertTrue($this->repository->Eliminar($una));
            $this->assertSame([2], array_map(fn (ActividadPlazas $p) => $p->getId_dl(), $this->filasDe($id)), 'borrar una no borra la otra');
        } finally {
            foreach ($this->filasDe($id) as $p) {
                $this->repository->Eliminar($p);
            }
        }
    }
}
