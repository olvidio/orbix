<?php

namespace Tests\integration\asistentes\infrastructure\persistence\postgresql;

use InvalidArgumentException;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\criteria\AsistenteField as F;
use src\asistentes\domain\entity\Asistente;
use src\asistentes\domain\value_objects\AsistentePk;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\NullsPosition;
use src\shared\domain\criteria\OrderDirection;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use Tests\factories\asistentes\AsistenteFactory;
use Tests\myTest;

/**
 * `matchingConjunto()` de PgAsistenteRepository (pasos 2.2 y revisión del piloto de
 * docs/dev/refactor_busquedas_criteria.md): unión de Dl/Out/Ex, orden y paginación sobre el conjunto.
 */
class PgAsistenteMatchingConjuntoTest extends myTest
{
    private const ACT = 998820001;
    private const NOM = 998820100;   // id_nom = NOM + n

    private const REPOS = [
        AsistenteDlRepositoryInterface::class,
        AsistenteOutRepositoryInterface::class,
        AsistenteExRepositoryInterface::class,
    ];

    private AsistenteRepositoryInterface $repo;
    private AsistenteFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $c = $GLOBALS['container'];
        $this->repo = $c->get(AsistenteDlRepositoryInterface::class);
        $this->factory = new AsistenteFactory();
        $this->limpiar();

        // n => [repositorio, propio, cfi_con]
        $this->sembrar(1, AsistenteDlRepositoryInterface::class, true, 10);
        $this->sembrar(2, AsistenteOutRepositoryInterface::class, false, 9);
        $this->sembrar(3, AsistenteExRepositoryInterface::class, true, null);
        $this->sembrar(4, AsistenteOutRepositoryInterface::class, false, 10);   // empata con el 1
    }

    public function tearDown(): void
    {
        $this->limpiar();
        parent::tearDown();
    }

    private function sembrar(int $n, string $repoInterface, bool $propio, ?int $cfiCon): void
    {
        $a = $this->factory->createSimple(new AsistentePk(self::ACT, self::NOM + $n));
        $a->setPropio($propio);
        $a->setCfi_con($cfiCon);
        $this->assertTrue($GLOBALS['container']->get($repoInterface)->Guardar($a, false));
    }

    private function limpiar(): void
    {
        $criteria = Criteria::create()->where(Filter::eq(F::IdActiv, self::ACT));
        foreach (self::REPOS as $repoInterface) {
            $repo = $GLOBALS['container']->get($repoInterface);
            foreach ($repo->matching($criteria) as $a) {
                $repo->Eliminar($a, false);
            }
        }
    }

    private function base(): Criteria
    {
        return Criteria::create()->where(Filter::eq(F::IdActiv, self::ACT));
    }

    /**
     * @param list<Asistente> $lista
     * @return list<int>
     */
    private function ns(array $lista): array
    {
        return array_map(static fn(Asistente $a): int => $a->getId_nom() - self::NOM, $lista);
    }

    public function test_une_los_repositorios_en_su_orden(): void
    {
        $r = $this->repo->matchingConjunto(self::REPOS, $this->base());
        $this->assertSame([1, 2, 4, 3], $this->ns($r));   // Dl, Out (2 y 4), Ex
    }

    public function test_solo_los_repositorios_indicados(): void
    {
        $r = $this->repo->matchingConjunto([AsistenteOutRepositoryInterface::class], $this->base());
        $this->assertSame([2, 4], $this->ns($r));
        $this->assertSame([], $this->repo->matchingConjunto([], $this->base()));
    }

    public function test_aplica_los_filtros_en_cada_repositorio(): void
    {
        $c = $this->base()->andWhere(Filter::eq(F::Propio, true));
        $this->assertSame([1, 3], $this->ns($this->repo->matchingConjunto(self::REPOS, $c)));
    }

    public function test_orden_numerico_no_alfabetico(): void
    {
        // 9 antes que 10 (como texto, "10" < "9"); el nulo al final en ASC; empate 1/4 por orden de repos
        $c = $this->base()->orderBy(F::CfiCon);
        $this->assertSame([2, 1, 4, 3], $this->ns($this->repo->matchingConjunto(self::REPOS, $c)));
    }

    public function test_orden_descendente_pone_los_nulos_primero_como_postgres(): void
    {
        $c = $this->base()->orderBy(F::CfiCon, OrderDirection::Desc);
        $this->assertSame([3, 1, 4, 2], $this->ns($this->repo->matchingConjunto(self::REPOS, $c)));
    }

    public function test_posicion_explicita_de_los_nulos(): void
    {
        $c = $this->base()->orderBy(F::CfiCon, OrderDirection::Asc, NullsPosition::First);
        $this->assertSame([3, 2, 1, 4], $this->ns($this->repo->matchingConjunto(self::REPOS, $c)));
    }

    public function test_ordena_por_campo_booleano(): void
    {
        // isPropio(): false (2, 4) antes que true (1, 3); desempate por id_nom
        $c = $this->base()->orderBy(F::Propio)->addOrderBy(F::IdNom);
        $this->assertSame([2, 4, 1, 3], $this->ns($this->repo->matchingConjunto(self::REPOS, $c)));
    }

    public function test_limit_y_offset_se_aplican_al_conjunto(): void
    {
        $c = $this->base()->orderBy(F::IdNom)->offset(1)->limit(2);
        $this->assertSame([2, 3], $this->ns($this->repo->matchingConjunto(self::REPOS, $c)));
        // sin orden: sobre el resultado de la unión
        $c = $this->base()->limit(3);
        $this->assertSame([1, 2, 4], $this->ns($this->repo->matchingConjunto(self::REPOS, $c)));
        $c = $this->base()->offset(3);
        $this->assertSame([3], $this->ns($this->repo->matchingConjunto(self::REPOS, $c)));
    }

    public function test_repositorio_que_no_es_de_asistentes_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->repo->matchingConjunto([\stdClass::class], $this->base());
    }

    public function test_orden_crudo_del_adaptador_lanza_excepcion(): void
    {
        $criteria = (new LegacyWhereAdapter(F::class))->toCriteria(['id_activ' => self::ACT, '_ordre' => 'substring(observ::text,1)']);
        $this->expectException(InvalidArgumentException::class);
        $this->repo->matchingConjunto(self::REPOS, $criteria);
    }
}
