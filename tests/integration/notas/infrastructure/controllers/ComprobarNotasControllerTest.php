<?php

declare(strict_types=1);

namespace Tests\integration\notas\infrastructure\controllers;

use src\shared\infrastructure\InProcessSrcDispatch;
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
     * Regresión: `id_asignatura` se leía como texto y se metía tal cual en el DELETE: con «0 OR 1=1» se borraban
     * todas las notas «cursada» de la dl.
     */
    public function test_borrar_una_cursada_solo_admite_ids_numericos(): void
    {
        $this->ejecutar(['id_tabla' => 'n', 'actualizar' => 'borrar_cursada', 'id_nom' => '10011', 'id_asignatura' => '0 OR 1=1']);

        $borrados = $this->consultasCon('DELETE');
        $this->assertCount(1, $borrados);
        $this->assertStringNotContainsString('OR 1=1', $borrados[0]);
        $this->assertMatchesRegularExpression('/id_nom = 10011\s+AND id_asignatura = 0\b/', $borrados[0]);
        $this->assertStringContainsString('e_notas_dl', $borrados[0], 'solo en la tabla de la dl, nunca en la agregada');
    }

    public function test_borrar_una_cursada_borra_esa_asignatura_de_esa_persona(): void
    {
        $this->ejecutar(['id_tabla' => 'a', 'actualizar' => 'borrar_cursada', 'id_nom' => '10011', 'id_asignatura' => '1101']);

        $borrados = $this->consultasCon('DELETE');
        $this->assertCount(1, $borrados);
        $this->assertMatchesRegularExpression('/id_situacion = 2\s+AND id_nom = 10011\s+AND id_asignatura = 1101/', $borrados[0]);
    }
}
