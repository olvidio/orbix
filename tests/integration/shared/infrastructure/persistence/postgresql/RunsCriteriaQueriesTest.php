<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure\persistence\postgresql;

use PDO;
use RuntimeException;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\FilterGroup;
use src\shared\domain\criteria\NullsPosition;
use src\shared\domain\criteria\OrderDirection;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\LegacyColumnFilter;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;
use Tests\myTest;

class RepoDePrueba extends ClaseRepository
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    public function filas(Criteria $c): array
    {
        return $this->rowsMatching($c, new PgCriteriaConverter(TablaField::class));
    }

    public function cuenta(Criteria $c): int
    {
        return $this->countMatching($c, new PgCriteriaConverter(TablaField::class));
    }

    public function filasConConversor(Criteria $c, PgCriteriaConverter $conv): array
    {
        return $this->rowsMatching($c, $conv);
    }
}

class RunsCriteriaQueriesTest extends myTest
{
    private PDO $pdo;
    private RepoDePrueba $repo;

    public function setUp(): void
    {
        parent::setUp();
        $this->pdo = $GLOBALS['oDBC'];
        $this->pdo->exec('DROP TABLE IF EXISTS pg_temp.t_criteria');
        $this->pdo->exec('CREATE TEMP TABLE t_criteria (id int PRIMARY KEY, nombre text, f_ini date, bits int, tags int[], cod varchar)');
        $this->pdo->exec(<<<'SQL'
INSERT INTO t_criteria (id, nombre, f_ini, bits, tags, cod) VALUES
 (1, 'José García', '2026-01-10', 1, '{1,2}', 'C12'),
 (2, 'jose perez', '2026-02-15', 2, '{3}',   'x'),
 (3, 'Ana López',   '2026-03-20', 3, '{}',    NULL),
 (4, 'Álvaro Gil',  NULL,         6, '{2,7}', 'c05'),
 (5, 'Luis',        '2026-05-01', 0, NULL,    'C99')
SQL);
        $this->repo = new RepoDePrueba();
        $this->repo->setoDbl_Select($this->pdo);
        $this->repo->setNomTabla('t_criteria');
    }

    public function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS pg_temp.t_criteria');
        parent::tearDown();
    }

    /** @return list<int> */
    private function ids(Criteria $c): array
    {
        return array_map(static fn (array $f): int => (int) $f['id'], $this->repo->filas($c));
    }

    public function test_sin_filtros_devuelve_todo_con_columnas_asociativas(): void
    {
        $filas = $this->repo->filas(Criteria::create()->orderBy(TablaField::Id));
        $this->assertCount(5, $filas);
        $this->assertSame('José García', $filas[0]['nombre']);
        $this->assertArrayHasKey('f_ini', $filas[0]);
    }

    public function test_filtro_y_orden(): void
    {
        $c = Criteria::create()->where(Filter::gte(TablaField::Id, 2))->orderBy(TablaField::Id, OrderDirection::Desc);
        $this->assertSame([5, 4, 3, 2], $this->ids($c));
    }

    public function test_limit_y_offset_se_enlazan(): void
    {
        $c = Criteria::create()->orderBy(TablaField::Id)->limit(2)->offset(1);
        $this->assertSame([2, 3], $this->ids($c));
    }

    public function test_in_vacio_no_devuelve_nada(): void
    {
        $this->assertSame([], $this->ids(Criteria::create()->where(Filter::in(TablaField::Id, []))));
    }

    public function test_fechas_y_nulos(): void
    {
        $fecha = new \DateTimeImmutable('2026-03-01');
        $c = Criteria::create()
            ->where(FilterGroup::anyOf(Filter::isNull(TablaField::FIni), Filter::gte(TablaField::FIni, $fecha)))
            ->orderBy(TablaField::FIni, OrderDirection::Asc, NullsPosition::First);
        $this->assertSame([4, 3, 5], $this->ids($c));
    }

    public function test_booleano_y_texto_con_comillas(): void
    {
        $this->pdo->exec("INSERT INTO t_criteria (id, nombre) VALUES (9, 'O''Brien')");
        $this->assertSame([9], $this->ids(Criteria::create()->where(Filter::eq(TablaField::Nombre, "O'Brien"))));
    }

    public function test_count_ignora_orden_y_limite(): void
    {
        $c = Criteria::create()->where(Filter::gt(TablaField::Id, 1))->orderBy(TablaField::Id)->limit(1);
        $this->assertSame(4, $this->repo->cuenta($c));
        $this->assertSame(5, $this->repo->cuenta(Criteria::create()));
    }

    public function test_con_el_adaptador_legacy(): void
    {
        $c = (new LegacyWhereAdapter(TablaField::class))->toCriteria(
            ['nombre' => 'jose', '_ordre' => 'id DESC', '_limit' => 5],
            ['nombre' => 'sin_acentos'],
        );
        $this->assertSame([2, 1], $this->ids($c));
    }

    public function test_error_de_sql_lanza_runtime_exception(): void
    {
        $c = Criteria::create()->where(new LegacyColumnFilter('no_existe', \src\shared\domain\criteria\Operator::Eq, 1));
        // Sin gestor, el trait no escribe en el log de errores (su ruta no existe en el contenedor).
        $gestor = $_SESSION['oGestorErrores'] ?? null;
        unset($_SESSION['oGestorErrores']);
        try {
            $this->expectException(RuntimeException::class);
            $this->repo->filas($c);
        } finally {
            $_SESSION['oGestorErrores'] = $gestor;
        }
    }

    public function test_el_conversor_de_otro_enum_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->repo->filasConConversor(
            Criteria::create()->where(Filter::eq(TablaField::Id, 1)),
            new PgCriteriaConverter(\Tests\unit\shared\domain\criteria\FakeField::class),
        );
    }
}
