<?php

namespace Tests\integration\pasarela\infrastructure\repositories;

use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\criteria\PasarelaConfigField;
use src\pasarela\domain\entity\PasarelaConfig;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\factories\pasarela\PasarelaConfigFactory;
use Tests\myTest;

class PgPasarelaConfigRepositoryTest extends myTest
{
    private PasarelaConfigRepositoryInterface $repository;
    private PasarelaConfigFactory $factory;

    /** @var list<PasarelaConfig> */
    private array $creadas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PasarelaConfigRepositoryInterface::class);
        $this->factory = new PasarelaConfigFactory();
        $this->creadas = [];
    }

    public function tearDown(): void
    {
        foreach ($this->creadas as $o) {
            $this->repository->Eliminar($o);
        }
        parent::tearDown();
    }

    private function guardar(PasarelaConfig $o): PasarelaConfig
    {
        $this->assertTrue($this->repository->Guardar($o), $this->repository->getErrorTxt());
        $this->creadas[] = $o;

        return $o;
    }

    public function test_guardar_nuevo(): void
    {
        $o = $this->guardar($this->factory->createSimple());

        $leido = $this->repository->findById($o->getNom_parametro());

        $this->assertNotNull($leido);
        $this->assertSame($o->getNom_parametro(), $leido->getNom_parametro());
        $this->assertSame(['valor' => 'test'], $leido->getJson_valor(true));
    }

    public function test_actualizar_existente_no_duplica_la_fila(): void
    {
        $o = $this->guardar($this->factory->createSimple());

        $o->setJson_valor(['valor' => 'cambiado', 'extra' => 2]);
        $this->assertTrue($this->repository->Guardar($o), $this->repository->getErrorTxt());

        // jsonb no conserva el orden de las claves: se compara sin orden.
        $this->assertEquals(['valor' => 'cambiado', 'extra' => 2], $this->repository->findById($o->getNom_parametro())->getJson_valor(true));
        $this->assertCount(
            1,
            $this->repository->matching(Criteria::create()->where(Filter::eq(PasarelaConfigField::NomParametro, $o->getNom_parametro())))
        );
    }

    public function test_json_anidado_con_unicode_y_tipos_ida_y_vuelta(): void
    {
        $o = $this->factory->createSimple();
        $valor = ['txt' => 'añadir ñ “comillas”', 'activo' => true, 'n' => 3, 'cero' => 0, 'nulo' => null, 'lista' => [1, 2, 3], 'sub' => ['a' => ['b' => 'c']]];
        $o->setJson_valor($valor);
        $this->guardar($o);

        // jsonb no conserva el orden de las claves: se compara sin orden (los tipos se verifican abajo).
        $leido = $this->repository->findById($o->getNom_parametro())->getJson_valor(true);
        $this->assertEquals($valor, $leido);
        $this->assertTrue($leido['activo']);
        $this->assertSame(3, $leido['n']);
        $this->assertSame(0, $leido['cero']);
        $this->assertNull($leido['nulo']);
        $this->assertSame([1, 2, 3], $leido['lista']);
    }

    public function test_json_vacio_se_guarda_como_null(): void
    {
        $o = $this->factory->createSimple();
        $o->setJson_valor([]);
        $this->guardar($o);

        $leido = $this->repository->findById($o->getNom_parametro());

        $this->assertNotNull($leido);
        $this->assertNull($leido->getJson_valor());
        $this->assertSame([], $leido->getJson_valor(true));
    }

    public function test_actualizar_json_a_null(): void
    {
        $o = $this->guardar($this->factory->createSimple());

        $o->setJson_valor(null);
        $this->assertTrue($this->repository->Guardar($o), $this->repository->getErrorTxt());

        $this->assertNull($this->repository->findById($o->getNom_parametro())->getJson_valor());
    }

    public function test_find_by_id_no_existente(): void
    {
        $this->assertNull($this->repository->findById('tst_no_existe_9999'));
    }

    public function test_datos_by_id(): void
    {
        $o = $this->guardar($this->factory->createSimple());

        $datos = $this->repository->datosById($o->getNom_parametro());

        $this->assertIsArray($datos);
        $this->assertSame($o->getNom_parametro(), $datos['nom_parametro']);
        $this->assertFalse($this->repository->datosById('tst_no_existe_9999'));
    }

    public function test_eliminar(): void
    {
        $o = $this->factory->createSimple();
        $this->assertTrue($this->repository->Guardar($o));
        $this->assertNotNull($this->repository->findById($o->getNom_parametro()));

        $this->assertTrue($this->repository->Eliminar($o));
        $this->assertNull($this->repository->findById($o->getNom_parametro()));
    }

    public function test_eliminar_inexistente_no_falla(): void
    {
        $this->assertTrue($this->repository->Eliminar($this->factory->createSimple('tst_no_existe_9998')));
    }

    public function test_nombres_con_guion_y_mayusculas(): void
    {
        $o = $this->guardar($this->factory->createSimple('Tst-Param_Mixto-1'));

        $this->assertSame('Tst-Param_Mixto-1', $this->repository->findById('Tst-Param_Mixto-1')?->getNom_parametro());
    }

    public function test_el_nombre_con_caracteres_de_sql_ni_llega_a_la_consulta(): void
    {
        // El repositorio interpola nom_parametro en el SQL: lo protege el value object (solo [A-Za-z0-9_-]).
        $this->expectException(\InvalidArgumentException::class);
        $o = new PasarelaConfig();
        $o->setNom_parametro("x'; DROP TABLE pasarela_dl; --");
    }

    public function test_matching_por_nombre_y_sin_resultados(): void
    {
        $a = $this->guardar($this->factory->createSimple('tst_match_a'));
        $this->guardar($this->factory->createSimple('tst_match_b'));

        $res = $this->repository->matching(Criteria::create()->where(Filter::eq(PasarelaConfigField::NomParametro, 'tst_match_a')));

        $this->assertCount(1, $res);
        $this->assertSame($a->getNom_parametro(), $res[0]->getNom_parametro());
        $this->assertSame([], $this->repository->matching(Criteria::create()->where(Filter::eq(PasarelaConfigField::NomParametro, 'tst_match_zzz'))));
    }

    public function test_get_nom_tabla(): void
    {
        $this->assertSame('pasarela_dl', $this->repository->getNomTabla());
    }
}
