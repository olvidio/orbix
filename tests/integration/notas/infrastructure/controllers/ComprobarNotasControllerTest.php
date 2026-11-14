<?php

declare(strict_types=1);

namespace Tests\integration\notas\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;

/**
 * Pantalla «comprobar notas» (`comprobar_notas_page_data` + `comprobar_notas_page_body.inc.php`): consultas y
 * acciones de mantenimiento con SQL directo sobre la conexión `oDB`.
 *
 * Para no tocar la BD, la conexión se sustituye por un PDO que apunta cada consulta y devuelve siempre un resultado
 * vacío: así se ve el SQL que se ejecutaría.
 */
final class ComprobarNotasControllerTest extends myTest
{
    private const CONTROLADOR = __DIR__ . '/../../../../../src/notas/infrastructure/ui/http/controllers/comprobar_notas_page_data.php';

    private mixed $oDBAnterior = null;
    /** @var array<string, mixed> */
    private array $postAnterior = [];
    /** @var \ArrayObject<int, string> */
    private \ArrayObject $consultas;

    public function setUp(): void
    {
        parent::setUp();
        $this->oDBAnterior = $GLOBALS['oDB'] ?? null;
        $this->postAnterior = $_POST;
        $this->consultas = new \ArrayObject();
        $GLOBALS['oDB'] = new class($this->consultas) extends \PDO {
            public function __construct(private \ArrayObject $consultas)
            {
                parent::__construct('sqlite::memory:');
            }

            public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): \PDOStatement|false
            {
                $this->consultas[] = $query;

                return parent::query('SELECT 1 WHERE 0');
            }

            public function prepare(string $query, array $options = []): \PDOStatement|false
            {
                $this->consultas[] = $query;

                return parent::prepare('SELECT 1 WHERE 0');
            }
        };
    }

    public function tearDown(): void
    {
        $GLOBALS['oDB'] = $this->oDBAnterior;
        $_POST = $this->postAnterior;
        parent::tearDown();
    }

    /**
     * @param array<string, string> $post
     * @return array<string, mixed>
     */
    private function ejecutar(array $post): array
    {
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::CONTROLADOR;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, substr($salida, 0, 300));

        return $json;
    }

    /** @return list<string> */
    private function consultasCon(string $texto): array
    {
        return array_values(array_filter($this->consultas->getArrayCopy(), static fn (string $q): bool => str_contains($q, $texto)));
    }

    public function test_sin_tipo_de_persona_valido_devuelve_error(): void
    {
        $json = $this->ejecutar(['id_tabla' => 'x']);

        $this->assertFalse($json['success']);
        $this->assertStringContainsString('id_tabla no válido', $json['mensaje']);
    }

    public function test_sin_accion_la_pantalla_solo_consulta(): void
    {
        $json = $this->ejecutar(['id_tabla' => 'n']);

        $this->assertTrue($json['success'], $json['mensaje'] ?? '');
        $this->assertNotSame([], $this->consultas->getArrayCopy());
        $this->assertSame([], $this->consultasCon('DELETE'));
        $this->assertSame([], $this->consultasCon('UPDATE'));
        $this->assertStringContainsString('<table', json_decode($json['data'], true)['html'] ?? '');
    }

    /**
     * Cápsula de una acción, como la firma la pantalla al pintar el enlace.
     *
     * @param array<string, int|string> $ctx
     */
    private function capsula(array $ctx): string
    {
        return HashB::sign('comprobar_notas_actualizar', $ctx + ['id_nom' => 0, 'id_asignatura' => 0]);
    }

    /**
     * Regresión: `id_asignatura` se leía como texto de la petición y se metía tal cual en el DELETE: con
     * «0 OR 1=1» se borraban todas las notas «cursada» de la dl. Ahora la persona y la asignatura salen de la
     * cápsula; lo que venga en la petición no cuenta.
     */
    public function test_borrar_una_cursada_no_usa_los_ids_de_la_peticion(): void
    {
        $this->ejecutar([
            'id_tabla' => 'n',
            'actualizar' => 'borrar_cursada',
            'id_nom' => '99999',
            'id_asignatura' => '0 OR 1=1',
            'ctx_actualizar' => $this->capsula(['actualizar' => 'borrar_cursada', 'id_tabla' => 'n', 'id_nom' => 10011, 'id_asignatura' => 1101]),
        ]);

        $borrados = $this->consultasCon('DELETE');
        $this->assertCount(1, $borrados);
        $this->assertStringNotContainsString('OR 1=1', $borrados[0]);
        $this->assertMatchesRegularExpression('/id_nom = 10011\s+AND id_asignatura = 1101\b/', $borrados[0]);
        $this->assertStringContainsString('e_notas_dl', $borrados[0], 'solo en la tabla de la dl, nunca en la agregada');
    }

    public function test_borrar_una_cursada_borra_esa_asignatura_de_esa_persona(): void
    {
        $this->ejecutar([
            'id_tabla' => 'a',
            'actualizar' => 'borrar_cursada',
            'ctx_actualizar' => $this->capsula(['actualizar' => 'borrar_cursada', 'id_tabla' => 'a', 'id_nom' => 10011, 'id_asignatura' => 1101]),
        ]);

        $borrados = $this->consultasCon('DELETE');
        $this->assertCount(1, $borrados);
        $this->assertMatchesRegularExpression('/id_situacion = 2\s+AND id_nom = 10011\s+AND id_asignatura = 1101/', $borrados[0]);
    }

    /**
     * Las acciones cambian niveles del stgr o borran notas: sin la cápsula que firma la pantalla no se
     * ejecuta ninguna consulta (ni siquiera las de lectura).
     */
    #[DataProvider('accionesProvider')]
    public function test_una_accion_sin_capsula_no_hace_nada(string $accion): void
    {
        $json = $this->ejecutar(['id_tabla' => 'n', 'actualizar' => $accion, 'id_nom' => '10011', 'id_asignatura' => '1101']);

        $this->assertFalse($json['success']);
        $this->assertSame('Operación no autorizada', $json['mensaje']);
        $this->assertSame([], $this->consultas->getArrayCopy());
    }

    /** @return iterable<string, array{string}> */
    public static function accionesProvider(): iterable
    {
        foreach (['c1', 'c2', 'r', 'borrar_cursada', 'caduca_cursada', '9999', '9998'] as $accion) {
            yield $accion => [$accion];
        }
    }

    public function test_una_capsula_de_otra_accion_o_de_otro_tipo_de_persona_no_vale(): void
    {
        $otraAccion = $this->ejecutar([
            'id_tabla' => 'n',
            'actualizar' => 'caduca_cursada',
            'ctx_actualizar' => $this->capsula(['actualizar' => 'c1', 'id_tabla' => 'n']),
        ]);
        $otroTipo = $this->ejecutar([
            'id_tabla' => 'a',
            'actualizar' => 'c1',
            'ctx_actualizar' => $this->capsula(['actualizar' => 'c1', 'id_tabla' => 'n']),
        ]);

        $this->assertSame('Operación no autorizada', $otraAccion['mensaje']);
        $this->assertSame('Operación no autorizada', $otroTipo['mensaje']);
        $this->assertSame([], $this->consultas->getArrayCopy());
    }

    public function test_los_enlaces_de_accion_llevan_su_capsula(): void
    {
        $json = $this->ejecutar(['id_tabla' => 'n']);

        $datos = json_decode(is_string($json['data'] ?? null) ? $json['data'] : '', true);
        $specs = is_array($datos) && is_array($datos['link_specs'] ?? null) ? $datos['link_specs'] : [];
        $acciones = 0;
        foreach ($specs as $spec) {
            $query = is_array($spec) && is_array($spec['query'] ?? null) ? $spec['query'] : [];
            if (!isset($query['actualizar'])) {
                continue;
            }
            $acciones++;
            $ctx = HashB::open(is_string($query['ctx_actualizar'] ?? null) ? $query['ctx_actualizar'] : '', 'comprobar_notas_actualizar');
            $this->assertSame(is_scalar($query['actualizar']) ? (string) $query['actualizar'] : '', $ctx['actualizar']);
            $this->assertSame('n', $ctx['id_tabla']);
        }
        $this->assertGreaterThan(0, $acciones, 'la pantalla pinta enlaces de acción');
    }
}
