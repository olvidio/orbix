<?php

namespace Tests\integration\dbextern\infrastructure\repositories;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\criteria\IdMatchPersonaField;
use src\dbextern\domain\entity\IdMatchPersona;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\factories\dbextern\IdMatchPersonaFactory;
use Tests\myTest;

class PgIdMatchPersonaRepositoryTest extends myTest
{
    private IdMatchPersonaRepositoryInterface $repository;
    private IdMatchPersonaFactory $factory;

    /** @var list<IdMatchPersona> */
    private array $creados = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(IdMatchPersonaRepositoryInterface::class);
        $this->factory = new IdMatchPersonaFactory();
        $this->creados = [];
    }

    public function tearDown(): void
    {
        foreach ($this->creados as $o) {
            $this->repository->Eliminar($o);
        }
        parent::tearDown();
    }

    private function guardar(IdMatchPersona $o): IdMatchPersona
    {
        $this->assertTrue($this->repository->Guardar($o), $this->repository->getErrorTxt());
        $this->creados[] = $o;

        return $o;
    }

    public function test_guardar_nuevo(): void
    {
        $o = $this->guardar($this->factory->createSimple(null, null, 'agd'));

        $leido = $this->repository->findById($o->getId_listas());
        $this->assertNotNull($leido);
        $this->assertSame($o->getId_listas(), $leido->getId_listas());
        $this->assertNull($leido->getId_orbix());
        $this->assertSame('agd', $leido->getId_tabla());
    }

    public function test_guardar_con_id_orbix(): void
    {
        $idOrbix = 990000000 + random_int(1, 999999);
        $o = $this->guardar($this->factory->createSimple(null, $idOrbix, 'n'));

        $this->assertSame($idOrbix, $this->repository->findById($o->getId_listas())?->getId_orbix());
    }

    public function test_actualizar_existente(): void
    {
        $o = $this->guardar($this->factory->createSimple(null, null, 'n'));

        $o->setId_tabla('sup');
        $o->setId_orbix(990000000 + random_int(1, 999999));
        $this->assertTrue($this->repository->Guardar($o), $this->repository->getErrorTxt());

        $leido = $this->repository->findById($o->getId_listas());
        $this->assertSame('sup', $leido?->getId_tabla());
        $this->assertSame($o->getId_orbix(), $leido?->getId_orbix());
        $this->assertCount(
            1,
            $this->repository->matching(Criteria::create()->where(Filter::eq(IdMatchPersonaField::IdListas, $o->getId_listas())))
        );
    }

    public function test_actualizar_id_orbix_a_null(): void
    {
        $o = $this->guardar($this->factory->createSimple(null, 990000000 + random_int(1, 999999)));

        $o->setId_orbix(null);
        $this->assertTrue($this->repository->Guardar($o), $this->repository->getErrorTxt());

        $this->assertNull($this->repository->findById($o->getId_listas())?->getId_orbix());
    }

    public function test_find_by_id_no_existente(): void
    {
        $this->assertNull($this->repository->findById(989999999));
    }

    public function test_datos_by_id(): void
    {
        $o = $this->guardar($this->factory->createSimple(null, null, 'n'));

        $datos = $this->repository->datosById($o->getId_listas());
        $this->assertIsArray($datos);
        $this->assertSame('n', $datos['id_tabla']);
        $this->assertFalse($this->repository->datosById(989999999));
    }

    public function test_eliminar(): void
    {
        $o = $this->factory->createSimple(null, null, 'n');
        $this->assertTrue($this->repository->Guardar($o));
        $this->assertNotNull($this->repository->findById($o->getId_listas()));

        $this->assertTrue($this->repository->Eliminar($o));
        $this->assertNull($this->repository->findById($o->getId_listas()));
    }

    public function test_eliminar_inexistente_no_falla(): void
    {
        $this->assertTrue($this->repository->Eliminar($this->factory->createSimple(989999998)));
    }

    public function test_id_orbix_duplicado_lanza_excepcion_y_no_guarda(): void
    {
        $idOrbix = 990000000 + random_int(1, 999999);
        $this->guardar($this->factory->createSimple(null, $idOrbix));

        $segundo = $this->factory->createSimple(null, $idOrbix);
        try {
            $this->repository->Guardar($segundo);
            $this->fail('id_orbix es único: debía lanzar RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('conv_id_personas_id_orbix_key', $e->getMessage());
        }
        $this->assertNull($this->repository->findById($segundo->getId_listas()));
    }

    public function test_matching_por_id_orbix_y_por_id_tabla(): void
    {
        $idOrbix = 990000000 + random_int(1, 999999);
        $a = $this->guardar($this->factory->createSimple(null, $idOrbix, 'agd'));
        $this->guardar($this->factory->createSimple(null, null, 'agd'));

        $porOrbix = $this->repository->matching(Criteria::create()->where(Filter::eq(IdMatchPersonaField::IdOrbix, $idOrbix)));
        $this->assertCount(1, $porOrbix);
        $this->assertSame($a->getId_listas(), $porOrbix[0]->getId_listas());

        $porTabla = $this->repository->matching(
            Criteria::create()
                ->where(Filter::eq(IdMatchPersonaField::IdTabla, 'agd'))
                ->andWhere(Filter::gte(IdMatchPersonaField::IdListas, IdMatchPersonaFactory::ID_BASE))
                ->orderBy(IdMatchPersonaField::IdListas)
        );
        $this->assertCount(2, $porTabla);
        $this->assertLessThan($porTabla[1]->getId_listas(), $porTabla[0]->getId_listas());
    }

    public function test_matching_sin_resultados(): void
    {
        $this->assertSame([], $this->repository->matching(
            Criteria::create()->where(Filter::eq(IdMatchPersonaField::IdListas, 989999997))
        ));
    }

    public function test_get_nom_tabla(): void
    {
        $this->assertSame('conv_id_personas', $this->repository->getNomTabla());
    }
}
