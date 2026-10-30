<?php

namespace Tests\integration\asistentes\infrastructure\persistence\postgresql;

use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\asistentes\domain\value_objects\AsistenteObserv;
use src\asistentes\domain\criteria\AsistenteField;
use src\asistentes\domain\value_objects\AsistentePk;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use Tests\factories\asistentes\AsistenteFactory;
use Tests\myTest;

/**
 * Test de caracterización (paso 2.0 de docs/dev/refactor_busquedas_criteria.md).
 *
 * Fija con expectativas escritas a mano el comportamiento actual de
 * de la traducción `LegacyWhereAdapter` + `matching()` (antes `getAsistentes($aWhere, $aOperators)`,
 * borrado en 5.1.ad) y de `matchingConjunto()` sobre datos sembrados.
 */
class PgAsistenteGetAsistentesCaracterizacionTest extends myTest
{
    private const ACT_A = 998800001;
    private const ACT_B = 998800002;
    private const ACT_C = 998800003;
    private const NOM_1 = 998800101;
    private const NOM_2 = 998800102;
    private const NOM_3 = 998800103;
    private const NOM_4 = 998800104;

    private const RANGO_ACT = '998800000,998800009';
    private const RANGO_NOM = '998800101,998800104';

    private AsistenteRepositoryInterface $repoAll;
    private AsistenteDlRepositoryInterface $repoDl;
    private AsistenteOutRepositoryInterface $repoOut;
    private AsistenteExRepositoryInterface $repoEx;
    private AsistenteFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $c = $GLOBALS['container'];
        $this->repoAll = $c->get(AsistenteRepositoryInterface::class);
        $this->repoDl = $c->get(AsistenteDlRepositoryInterface::class);
        $this->repoOut = $c->get(AsistenteOutRepositoryInterface::class);
        $this->repoEx = $c->get(AsistenteExRepositoryInterface::class);
        $this->factory = new AsistenteFactory();
        $this->limpiar();

        // Dl: (A,1) propio, est_ok, cfi_con 20000, observ "alfa uno"
        $this->sembrar($this->repoDl, self::ACT_A, self::NOM_1, true, true, 20000, 'alfa uno');
        // Dl: (A,2) no propio, cfi_con null, observ null
        $this->sembrar($this->repoDl, self::ACT_A, self::NOM_2, false, false, null, null);
        // Dl: (B,1) propio, cfi_con 30000, observ "beta uno"
        $this->sembrar($this->repoDl, self::ACT_B, self::NOM_1, true, false, 30000, 'beta uno');
        // Out: (A,3)
        $this->sembrar($this->repoOut, self::ACT_A, self::NOM_3, false, false, 40000, 'gamma tres');
        // Ex: (C,4)
        $this->sembrar($this->repoEx, self::ACT_C, self::NOM_4, true, true, 50000, 'delta cuatro');
    }

    public function tearDown(): void
    {
        $this->limpiar();
        parent::tearDown();
    }

    private function sembrar(object $repo, int $idActiv, int $idNom, bool $propio, bool $estOk, ?int $cfiCon, ?string $observ): void
    {
        $a = $this->factory->createSimple(new AsistentePk($idActiv, $idNom));
        $a->setPropio($propio);
        $a->setEst_ok($estOk);
        $a->setCfi_con($cfiCon);
        if ($observ !== null) {
            $a->setObservVo(new AsistenteObserv($observ));
        }
        $this->assertTrue($repo->Guardar($a, false));
    }

    /**
     * Equivalente del antiguo `getAsistentes($aWhere, $aOperators)`: traduce con `LegacyWhereAdapter` y llama a `matching()`.
     *
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Asistente>
     */
    private function legacy(AsistenteRepositoryInterface $repo, array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(AsistenteField::class, []);
        return $repo->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    private function limpiar(): void
    {
        foreach ([$this->repoDl, $this->repoOut, $this->repoEx] as $repo) {
            foreach ($this->legacy($repo, ['id_activ' => self::RANGO_ACT], ['id_activ' => 'BETWEEN']) as $a) {
                $repo->Eliminar($a, false);
            }
        }
    }

    /**
     * @param list<Asistente> $lista
     * @return list<string> "activ-nom" (relativos a la base de los datos sembrados), en el orden recibido
     */
    private function claves(array $lista): array
    {
        return array_map(
            static fn(Asistente $a): string => ($a->getId_activ() - 998800000) . '-' . ($a->getId_nom() - 998800100),
            $lista
        );
    }

    public function test_between_en_dos_columnas_acota_y_ordena(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_activ' => self::RANGO_ACT, 'id_nom' => self::RANGO_NOM, '_ordre' => 'id_activ, id_nom'],
            ['id_activ' => 'BETWEEN', 'id_nom' => 'BETWEEN']
        ));
        $this->assertSame(['1-1', '1-2', '1-3', '2-1', '3-4'], $r);
    }

    public function test_igualdad_implicita(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,['id_activ' => self::ACT_A, '_ordre' => 'id_nom']));
        $this->assertSame(['1-1', '1-2', '1-3'], $r);
    }

    public function test_igualdad_con_operador_explicito_y_varios_campos(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_activ' => self::ACT_A, 'id_nom' => self::NOM_1],
            ['id_activ' => '=', 'id_nom' => '=']
        ));
        $this->assertSame(['1-1'], $r);
    }

    public function test_clave_inexistente_devuelve_lista_vacia(): void
    {
        $this->assertSame([], $this->legacy($this->repoAll,['id_activ' => 999999991, 'id_nom' => 999999992]));
    }

    public function test_booleano_con_t(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_nom' => self::RANGO_NOM, 'propio' => 't', '_ordre' => 'id_activ, id_nom'],
            ['id_nom' => 'BETWEEN']
        ));
        $this->assertSame(['1-1', '2-1', '3-4'], $r);
    }

    public function test_booleano_con_f(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_nom' => self::RANGO_NOM, 'propio' => 'f', '_ordre' => 'id_activ, id_nom'],
            ['id_nom' => 'BETWEEN']
        ));
        $this->assertSame(['1-2', '1-3'], $r);
    }

    public function test_distinto(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_activ' => self::ACT_A, 'id_nom' => self::NOM_1, '_ordre' => 'id_nom'],
            ['id_nom' => '!=']
        ));
        $this->assertSame(['1-2', '1-3'], $r);
    }

    public function test_menor_y_mayor(): void
    {
        $ops = ['cfi_con' => '<', 'id_activ' => 'BETWEEN', 'id_nom' => 'BETWEEN'];
        $where = ['cfi_con' => 30000, 'id_activ' => self::RANGO_ACT, 'id_nom' => self::RANGO_NOM, '_ordre' => 'id_activ, id_nom'];
        $this->assertSame(['1-1'], $this->claves($this->legacy($this->repoAll,$where, $ops)));
        $ops['cfi_con'] = '>';
        $this->assertSame(['1-3', '3-4'], $this->claves($this->legacy($this->repoAll,$where, $ops)));
    }

    public function test_between_con_cadena_coma(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_nom' => '998800102,998800103', '_ordre' => 'id_nom'],
            ['id_nom' => 'BETWEEN']
        ));
        $this->assertSame(['1-2', '1-3'], $r);
    }

    public function test_is_null_y_is_not_null(): void
    {
        $where = ['id_activ' => self::RANGO_ACT, 'cfi_con' => '', '_ordre' => 'id_activ, id_nom'];
        $nulos = $this->claves($this->legacy($this->repoAll,$where, ['id_activ' => 'BETWEEN', 'cfi_con' => 'IS NULL']));
        $this->assertSame(['1-2'], $nulos);
        $noNulos = $this->claves($this->legacy($this->repoAll,$where, ['id_activ' => 'BETWEEN', 'cfi_con' => 'IS NOT NULL']));
        $this->assertSame(['1-1', '1-3', '2-1', '3-4'], $noNulos);
    }

    public function test_in_con_lista_de_enteros(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_activ' => self::ACT_A, 'id_nom' => [self::NOM_1, self::NOM_3], '_ordre' => 'id_nom'],
            ['id_nom' => 'IN']
        ));
        $this->assertSame(['1-1', '1-3'], $r);
    }

    public function test_not_in(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_activ' => self::ACT_A, 'id_nom' => [self::NOM_1], '_ordre' => 'id_nom'],
            ['id_nom' => 'NOT IN']
        ));
        $this->assertSame(['1-2', '1-3'], $r);
    }

    public function test_regex_tilde_sobre_texto(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_activ' => self::RANGO_ACT, 'observ' => 'uno$', '_ordre' => 'id_activ, id_nom'],
            ['id_activ' => 'BETWEEN', 'observ' => '~']
        ));
        $this->assertSame(['1-1', '2-1'], $r);
    }

    public function test_regex_tilde_asterisco_ignora_mayusculas(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_activ' => self::RANGO_ACT, 'observ' => '^GAMMA', '_ordre' => 'id_activ, id_nom'],
            ['id_activ' => 'BETWEEN', 'observ' => '~*']
        ));
        $this->assertSame(['1-3'], $r);
    }

    public function test_regex_negada(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_activ' => self::RANGO_ACT, 'observ' => 'uno', '_ordre' => 'id_activ, id_nom'],
            ['id_activ' => 'BETWEEN', 'observ' => '!~']
        ));
        $this->assertSame(['1-3', '3-4'], $r);
    }

    public function test_orden_descendente_y_limite(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_activ' => self::RANGO_ACT, '_ordre' => 'id_nom DESC', '_limit' => 2],
            ['id_activ' => 'BETWEEN']
        ));
        $this->assertSame(['3-4', '1-3'], $r);
    }

    public function test_orden_con_dos_columnas(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_activ' => self::RANGO_ACT, '_ordre' => 'id_nom DESC, id_activ ASC'],
            ['id_activ' => 'BETWEEN']
        ));
        $this->assertSame(['3-4', '1-3', '1-2', '1-1', '2-1'], $r);
    }

    public function test_ordre_y_limit_vacios_se_ignoran(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_activ' => self::ACT_C, '_ordre' => '', '_limit' => '']
        ));
        $this->assertSame(['3-4'], $r);
    }

    public function test_entidad_hidratada_con_sus_valores(): void
    {
        $r = $this->legacy($this->repoAll,['id_activ' => self::ACT_A, 'id_nom' => self::NOM_1]);
        $this->assertCount(1, $r);
        $this->assertInstanceOf(Asistente::class, $r[0]);
        $this->assertTrue($r[0]->isPropio());
        $this->assertTrue($r[0]->isEst_ok());
        $this->assertSame(20000, $r[0]->getCfi_con());
        $this->assertSame('alfa uno', $r[0]->getObservVo()?->value());
    }

    public function test_repositorios_hijos_solo_ven_su_tabla(): void
    {
        $where = ['id_activ' => self::RANGO_ACT, '_ordre' => 'id_activ, id_nom'];
        $ops = ['id_activ' => 'BETWEEN'];
        $this->assertSame(['1-1', '1-2', '2-1'], $this->claves($this->legacy($this->repoDl,$where, $ops)));
        $this->assertSame(['1-3'], $this->claves($this->legacy($this->repoOut,$where, $ops)));
        $this->assertSame(['3-4'], $this->claves($this->legacy($this->repoEx,$where, $ops)));
    }

    public function test_all_ve_las_filas_de_las_tablas_hijas(): void
    {
        $r = $this->claves($this->legacy($this->repoAll,
            ['id_activ' => self::RANGO_ACT, '_ordre' => 'id_activ, id_nom'],
            ['id_activ' => 'BETWEEN']
        ));
        $this->assertSame(['1-1', '1-2', '1-3', '2-1', '3-4'], $r);
    }

    /* ---- getConjunt ---- */

    /**
     * @param list<class-string> $repos
     * @param array<string, mixed> $where
     * @param array<string, string> $ops
     * @return list<string>
     */
    private function conjunto(array $repos, array $where, array $ops): array
    {
        $criteria = (new LegacyWhereAdapter(AsistenteField::class, []))->toCriteria($where, $ops);
        return $this->claves($this->repoAll->matchingConjunto($repos, $criteria));
    }

    public function test_conjunto_une_varias_tablas_en_orden_de_repos(): void
    {
        $r = $this->conjunto(
            [AsistenteDlRepositoryInterface::class, AsistenteOutRepositoryInterface::class, AsistenteExRepositoryInterface::class],
            ['id_activ' => self::RANGO_ACT, '_ordre' => ''],
            ['id_activ' => 'BETWEEN']
        );
        $this->assertCount(5, $r);
        $this->assertEqualsCanonicalizing(['1-1', '1-2', '2-1'], array_slice($r, 0, 3));
        $this->assertSame(['1-3', '3-4'], array_slice($r, 3));
    }

    public function test_conjunto_con_subconjunto_de_repos(): void
    {
        $r = $this->conjunto(
            [AsistenteOutRepositoryInterface::class, AsistenteExRepositoryInterface::class],
            ['id_activ' => self::ACT_A],
            []
        );
        $this->assertSame(['1-3'], $r);
    }

    public function test_conjunto_por_persona(): void
    {
        $r = $this->conjunto(
            [AsistenteDlRepositoryInterface::class, AsistenteOutRepositoryInterface::class, AsistenteExRepositoryInterface::class],
            ['id_nom' => self::NOM_1, '_ordre' => 'id_activ'],
            []
        );
        $this->assertSame(['1-1', '2-1'], $r);
    }

    public function test_conjunto_con_ordre_reordena_el_resultado_unido(): void
    {
        $r = $this->conjunto(
            [AsistenteDlRepositoryInterface::class, AsistenteOutRepositoryInterface::class, AsistenteExRepositoryInterface::class],
            ['id_activ' => self::RANGO_ACT, '_ordre' => 'id_nom DESC'],
            ['id_activ' => 'BETWEEN']
        );
        $this->assertSame(['3-4', '1-3', '1-2', '1-1', '2-1'], array_values($r));
    }

    public function test_conjunto_sin_resultados(): void
    {
        $r = $this->conjunto(
            [AsistenteDlRepositoryInterface::class, AsistenteExRepositoryInterface::class],
            ['id_activ' => 999999991],
            []
        );
        $this->assertSame([], $r);
    }
}
