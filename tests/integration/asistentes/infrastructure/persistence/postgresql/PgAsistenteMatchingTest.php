<?php

namespace Tests\integration\asistentes\infrastructure\persistence\postgresql;

use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\criteria\AsistenteField as F;
use src\asistentes\domain\entity\Asistente;
use src\asistentes\domain\value_objects\AsistentePk;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\FilterGroup;
use Tests\factories\asistentes\AsistenteFactory;
use Tests\myTest;

/**
 * `matching()` de PgAsistenteRepository (paso 2.1 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgAsistenteMatchingTest extends myTest
{
    private const ACT_A = 998810001;
    private const ACT_B = 998810002;
    private const NOM_1 = 998810101;
    private const NOM_2 = 998810102;
    private const NOM_3 = 998810103;

    private AsistenteRepositoryInterface $repoAll;
    private AsistenteDlRepositoryInterface $repoDl;
    private AsistenteFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $c = $GLOBALS['container'];
        $this->repoAll = $c->get(AsistenteRepositoryInterface::class);
        $this->repoDl = $c->get(AsistenteDlRepositoryInterface::class);
        $this->factory = new AsistenteFactory();
        $this->limpiar();

        $this->sembrar(self::ACT_A, self::NOM_1, true, 20000);
        $this->sembrar(self::ACT_A, self::NOM_2, false, null);
        $this->sembrar(self::ACT_B, self::NOM_3, true, 30000);
    }

    public function tearDown(): void
    {
        $this->limpiar();
        parent::tearDown();
    }

    private function sembrar(int $idActiv, int $idNom, bool $propio, ?int $cfiCon): void
    {
        $a = $this->factory->createSimple(new AsistentePk($idActiv, $idNom));
        $a->setPropio($propio);
        $a->setCfi_con($cfiCon);
        $this->assertTrue($this->repoDl->Guardar($a, false));
    }

    private function limpiar(): void
    {
        $criteria = Criteria::create()->where(Filter::between(F::IdActiv, 998810000, 998810009));
        foreach ($this->repoDl->matching($criteria) as $a) {
            $this->repoDl->Eliminar($a, false);
        }
    }

    private function base(): Criteria
    {
        return Criteria::create()->where(Filter::between(F::IdActiv, 998810000, 998810009));
    }

    /**
     * @param list<Asistente> $lista
     * @return list<string>
     */
    private function claves(array $lista): array
    {
        return array_map(
            static fn(Asistente $a): string => ($a->getId_activ() - 998810000) . '-' . ($a->getId_nom() - 998810100),
            $lista
        );
    }

    public function test_filtra_y_ordena(): void
    {
        $c = $this->base()->andWhere(Filter::eq(F::Propio, true))->orderBy(F::IdNom);
        $this->assertSame(['1-1', '2-3'], $this->claves($this->repoAll->matching($c)));
    }

    public function test_orden_descendente_y_limite(): void
    {
        $c = $this->base()->orderBy(F::IdNom, \src\shared\domain\criteria\OrderDirection::Desc)->limit(2);
        $this->assertSame(['2-3', '1-2'], $this->claves($this->repoAll->matching($c)));
    }

    public function test_in_e_is_null(): void
    {
        $c = $this->base()->andWhere(Filter::in(F::IdNom, [self::NOM_1, self::NOM_2]))
            ->andWhere(Filter::isNull(F::CfiCon));
        $this->assertSame(['1-2'], $this->claves($this->repoAll->matching($c)));
    }

    public function test_grupo_or(): void
    {
        $c = $this->base()->andWhere(FilterGroup::anyOf(
            Filter::isNull(F::CfiCon),
            Filter::gte(F::CfiCon, 30000),
        ))->orderBy(F::IdNom);
        $this->assertSame(['1-2', '2-3'], $this->claves($this->repoAll->matching($c)));
    }

    public function test_devuelve_entidades_hidratadas(): void
    {
        $r = $this->repoAll->matching($this->base()->andWhere(Filter::eq(F::IdNom, self::NOM_1)));
        $this->assertCount(1, $r);
        $this->assertInstanceOf(Asistente::class, $r[0]);
        $this->assertSame(20000, $r[0]->getCfi_con());
    }

    public function test_get_asistentes_equivale_a_matching(): void
    {
        $viejo = $this->repoAll->getAsistentes(
            ['id_activ' => self::ACT_A, 'propio' => 't', '_ordre' => 'id_nom'],
        );
        $nuevo = $this->repoAll->matching(
            Criteria::create()->where(Filter::eq(F::IdActiv, self::ACT_A))
                ->andWhere(Filter::eq(F::Propio, true))->orderBy(F::IdNom)
        );
        $this->assertSame($this->claves($nuevo), $this->claves($viejo));
        $this->assertSame(['1-1'], $this->claves($viejo));
    }

    public function test_sin_resultados(): void
    {
        $this->assertSame([], $this->repoAll->matching(
            Criteria::create()->where(Filter::eq(F::IdActiv, 999999991))
        ));
    }
}
