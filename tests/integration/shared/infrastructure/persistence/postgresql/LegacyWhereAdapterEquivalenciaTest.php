<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure\persistence\postgresql;

use PDO;
use src\shared\domain\criteria\CriteriaField;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use Tests\myTest;

enum TablaField: string implements CriteriaField
{
    case Id = 'id';
    case Nombre = 'nombre';
    case FIni = 'f_ini';
    case Bits = 'bits';
    case Tags = 'tags';
    case Cod = 'cod';
}

/**
 * Para cada operador de Condicion: las filas que devuelve el SQL antiguo (bucle de los
 * repositorios + Condicion) y las del nuevo (LegacyWhereAdapter + PgCriteriaConverter)
 * son las mismas, y en el mismo orden cuando hay _ordre.
 */
class LegacyWhereAdapterEquivalenciaTest extends myTest
{
    private PDO $pdo;

    public function setUp(): void
    {
        parent::setUp();
        $this->pdo = $GLOBALS['oDBC'];
        $this->pdo->exec('DROP TABLE IF EXISTS pg_temp.t_criteria');
        $this->pdo->exec(
            'CREATE TEMP TABLE t_criteria (id int PRIMARY KEY, nombre text, f_ini date, bits int, tags int[], cod varchar)'
        );
        $this->pdo->exec(<<<'SQL'
INSERT INTO t_criteria (id, nombre, f_ini, bits, tags, cod) VALUES
 (1, 'José García', '2026-01-10', 1, '{1,2}', 'C12'),
 (2, 'jose perez', '2026-02-15', 2, '{3}',   'x'),
 (3, 'Ana López',   '2026-03-20', 3, '{}',    NULL),
 (4, 'Álvaro Gil',  NULL,         6, '{2,7}', 'c05'),
 (5, 'Luis',        '2026-05-01', 0, NULL,    'C99'),
 (6, 'marta',       '2026-06-30', 4, '{1}',   'D1')
SQL);
    }

    public function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS pg_temp.t_criteria');
        parent::tearDown();
    }

    /** SQL antiguo: copia del bucle de los repositorios `getXxx()`. @return list<int> */
    private function legacy(array $aWhere, array $aOperators): array
    {
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre' || $camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            if (in_array($sOperador, ['BETWEEN', 'IS NULL', 'IS NOT NULL', 'OR', 'IN', 'NOT IN', 'TXT'], true)) {
                unset($aWhere[$camp]);
            }
        }
        $sql = 'SELECT id FROM t_criteria';
        if ($aCondicion !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $aCondicion);
        }
        $sql .= isset($aWhere['_ordre']) ? ' ORDER BY ' . $aWhere['_ordre'] : ' ORDER BY id';
        if (isset($aWhere['_limit'])) {
            $sql .= ' LIMIT ' . $aWhere['_limit'];
        }
        unset($aWhere['_ordre'], $aWhere['_limit']);
        $st = $this->pdo->prepare($sql);
        $st->execute($aWhere);
        return array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
    }

    /** @return list<int> */
    private function nuevo(array $aWhere, array $aOperators): array
    {
        $criteria = (new LegacyWhereAdapter(TablaField::class))->toCriteria($aWhere, $aOperators);
        if (!isset($aWhere['_ordre'])) {
            $criteria = $criteria->orderBy(TablaField::Id);
        }
        $r = (new PgCriteriaConverter(TablaField::class))->convert($criteria);
        $st = $this->pdo->prepare('SELECT id FROM t_criteria' . $r['sql']);
        foreach ($r['params'] as $nombre => $valor) {
            $st->bindValue($nombre, $valor, is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $st->execute();
        return array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
    }

    /** @return array<string, array{0: array, 1: array}> */
    public static function casos(): array
    {
        return [
            'igualdad' => [['id' => 3], []],
            'igualdad explícita' => [['id' => 3], ['id' => '=']],
            'varios campos' => [['id' => 2, 'nombre' => 'jose perez'], []],
            'distinto' => [['id' => 3], ['id' => '!=']],
            'menor' => [['id' => 3], ['id' => '<']],
            'mayor' => [['id' => 3], ['id' => '>']],
            'menor o igual' => [['id' => 3], ['id' => '<=']],
            'mayor o igual' => [['id' => 3], ['id' => '>=']],
            'is null' => [['f_ini' => ''], ['f_ini' => 'IS NULL']],
            'is not null' => [['f_ini' => ''], ['f_ini' => 'IS NOT NULL']],
            'or' => [['id' => '1,3,5'], ['id' => 'OR']],
            'between numérico' => [['id' => '2,4'], ['id' => 'BETWEEN']],
            'between fechas' => [['f_ini' => "'2026-02-01','2026-05-01'"], ['f_ini' => 'BETWEEN']],
            'in lista' => [['id' => [1, 4, 6]], ['id' => 'IN']],
            'in mapa id=>etiqueta' => [['id' => [2 => 'a', 5 => 'b']], ['id' => 'IN']],
            'in cadenas' => [['cod' => ['C12', 'x']], ['cod' => 'IN']],
            'in cadena formateada' => [['id' => '1,2'], ['id' => 'IN']],
            'not in' => [['id' => [1, 4, 6]], ['id' => 'NOT IN']],
            'any' => [['id' => '{1, 3, 7}'], ['id' => 'ANY']],
            'in_array' => [['tags' => 2], ['tags' => 'IN_ARRAY']],
            'regex' => [['nombre' => '^J'], ['nombre' => '~']],
            'regex insensible' => [['nombre' => '^j'], ['nombre' => '~*']],
            'no regex' => [['nombre' => '^J'], ['nombre' => '!~']],
            'no regex insensible' => [['nombre' => '^j'], ['nombre' => '!~*']],
            'sin acentos' => [['nombre' => 'jose'], ['nombre' => 'sin_acentos']],
            'máscara de bits' => [['bits' => 2], ['bits' => '&']],
            'txt' => [['cod' => "COALESCE(cod,'x') ~* 'C\\d\\d'"], ['cod' => 'TXT']],
            'txt negado' => [['cod' => "COALESCE(cod,'x') !~* 'C\\d\\d'"], ['cod' => 'TXT']],
            '~INV' => [['cod' => 'C12 y más'], ['cod' => '~INV']],
            'ilike' => [['nombre' => 'JOSE%'], ['nombre' => 'ILIKE']],
            'like' => [['nombre' => 'jose%'], ['nombre' => 'LIKE']],
            'combinado' => [['id' => 1, 'f_ini' => '2026-06-01', 'cod' => 'x'], ['id' => '>=', 'f_ini' => '<', 'cod' => '!=']],
            'orden' => [['id' => 0, '_ordre' => 'nombre DESC'], ['id' => '>']],
            'orden compuesto' => [['id' => 0, '_ordre' => 'bits DESC, id'], ['id' => '>']],
            'orden con nulls last' => [['id' => 0, '_ordre' => 'f_ini NULLS LAST, id'], ['id' => '>']],
            'orden expresión' => [['id' => 0, '_ordre' => 'substring(nombre::text,1,2), id'], ['id' => '>']],
            'límite' => [['id' => 0, '_ordre' => 'id', '_limit' => 3], ['id' => '>']],
            'sin condiciones' => [['_ordre' => 'id DESC'], []],
        ];
    }

    /** @dataProvider casos */
    #[\PHPUnit\Framework\Attributes\DataProvider('casos')]
    public function test_mismas_filas(array $aWhere, array $aOperators): void
    {
        $esperado = $this->legacy($aWhere, $aOperators);
        $this->assertSame($esperado, $this->nuevo($aWhere, $aOperators));
    }

    public function test_los_casos_no_son_triviales(): void
    {
        $vacios = 0;
        foreach (self::casos() as [$w, $o]) {
            if ($this->legacy($w, $o) === []) {
                $vacios++;
            }
        }
        // Un caso que devuelve siempre [] no prueba nada.
        $this->assertLessThanOrEqual(2, $vacios);
    }
}
