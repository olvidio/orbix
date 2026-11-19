<?php

declare(strict_types=1);

namespace Tests\integration\asistentes\infrastructure;

use PDO;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Migración que pasa a `restov.d_asistentes_ex` las asistencias de personas de paso (`id_nom` negativo)
 * que quedaron sueltas en `"<dl>".d_asistentes_out` y en la madre `publicv.d_asistentes_de_paso`, y borra del origen
 * (también de `"<dl>".d_asistentes_dl`) las que ya tenían fila en ex para la misma actividad.
 *
 * Ejecuta el SQL del fichero de migración dentro de una transacción que se deshace, sobre filas propias.
 */
final class MigracionAsistenciasDePasoSueltasAExTest extends myTest
{
    private const MIGRACION = __DIR__ . '/../../../../db/migrations/202611191100_d_asistentes_de_paso_sueltas_a_ex__sv-e.sql';
    private const EXPORT = __DIR__ . '/../../../../db/migrations/202611191050_d_asistentes_de_paso_duplicadas_export__sv-e.sql';
    private const ACTIV = 9907770011;
    private const ACTIV_REPETIDA = 9907770012;
    private const PASO = -990777021;
    private const PASO_MADRE = -990777023;
    private const DE_LA_DL = 990777022;

    private PDO $pdo;
    private string $esquema;

    public function setUp(): void
    {
        parent::setUp();
        $this->pdo = GlobalPdo::get('oDBE');
        $this->esquema = (string) $this->pdo->query('SELECT current_schema()')->fetchColumn();
        $this->pdo->beginTransaction();
    }

    public function tearDown(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
        parent::tearDown();
    }

    private function en(string $tabla, int $idActiv, int $idNom, string $observ): void
    {
        $st = $this->pdo->prepare("INSERT INTO $tabla (id_schema, id_activ, id_nom, propio, falta, observ) VALUES (1001, :a, :n, false, true, :o)");
        $st->execute(['a' => $idActiv, 'n' => $idNom, 'o' => $observ]);
    }

    /** @return array<string, mixed>|null */
    private function fila(string $tabla, int $idActiv, int $idNom): ?array
    {
        $fila = $this->pdo->query("SELECT * FROM ONLY $tabla WHERE id_activ = $idActiv AND id_nom = $idNom")->fetch(PDO::FETCH_ASSOC);

        return $fila === false ? null : $fila;
    }

    private function aplicarMigracion(): void
    {
        $this->assertFileExists(self::MIGRACION);
        $this->pdo->exec((string) file_get_contents(self::MIGRACION));
    }

    /**
     * Filas propias que saldrían en el CSV de la migración de exportación (la consulta entre sus marcas).
     *
     * @return list<array<string, mixed>>
     */
    private function exportadas(): array
    {
        $this->assertFileExists(self::EXPORT);
        $sql = (string) file_get_contents(self::EXPORT);
        $this->assertSame(1, preg_match('/^-- @orbix_export_csv: log\/db\/\S+\.csv$/m', $sql), 'marca del fichero CSV');
        $this->assertSame(1, preg_match('/-- @orbix_export_query_begin\n(.*?)-- @orbix_export_query_end/s', $sql, $m), 'consulta entre marcas');
        $filas = $this->pdo->query(rtrim(trim($m[1]), ';'))->fetchAll(PDO::FETCH_ASSOC);

        return array_values(array_filter($filas, static fn (array $f): bool => in_array((int) $f['id_activ'], [self::ACTIV, self::ACTIV_REPETIDA], true)));
    }

    public function test_pasa_a_ex_las_asistencias_de_paso_sueltas_en_out_y_en_la_madre(): void
    {
        $out = "\"{$this->esquema}\".d_asistentes_out";
        $dl = "\"{$this->esquema}\".d_asistentes_dl";
        $madre = 'publicv.d_asistentes_de_paso';
        $ex = 'restov.d_asistentes_ex';
        $this->en($out, self::ACTIV, self::PASO, 'de paso en out');
        $this->en($madre, self::ACTIV, self::PASO_MADRE, 'de paso en la madre');
        $this->en($out, self::ACTIV, self::DE_LA_DL, 'de la dl');
        $this->en($out, self::ACTIV_REPETIDA, self::PASO, 'la de out');
        $this->pdo->exec("INSERT INTO $ex (id_activ, id_nom, observ) VALUES (" . self::ACTIV_REPETIDA . ', ' . self::PASO . ", 'la de ex')");
        $this->en($dl, self::ACTIV_REPETIDA, self::PASO_MADRE, 'la de la dl');
        $this->pdo->exec("INSERT INTO $ex (id_activ, id_nom, observ) VALUES (" . self::ACTIV_REPETIDA . ', ' . self::PASO_MADRE . ", 'la de ex')");

        $exportadas = $this->exportadas();
        $this->assertCount(2, $exportadas, 'se exportan las dos que se van a borrar, no las que se mueven');
        $this->assertSame(
            ["\"{$this->esquema}\".d_asistentes_dl", "\"{$this->esquema}\".d_asistentes_out"],
            array_column($exportadas, 'origen'),
        );
        $this->assertStringContainsString('"observ":"la de out"', (string) $exportadas[1]['fila']);

        $this->aplicarMigracion();
        $this->assertSame([], $this->exportadas(), 'tras la migración ya no queda ninguna duplicada');

        $idSchemaEx = (int) $this->pdo->query("SELECT public.idschema('restov')")->fetchColumn();
        foreach ([[$out, self::PASO, 'de paso en out'], [$madre, self::PASO_MADRE, 'de paso en la madre']] as [$origen, $idNom, $observ]) {
            $this->assertNull($this->fila($origen, self::ACTIV, $idNom), "$observ: ya no está en el origen");
            $movida = $this->fila($ex, self::ACTIV, $idNom);
            $this->assertNotNull($movida, "$observ: está en ex");
            $this->assertSame($observ, $movida['observ']);
            $this->assertFalse((bool) $movida['propio'], 'conserva propio');
            $this->assertTrue((bool) $movida['falta'], 'conserva el resto de campos');
            $this->assertSame('ex', $movida['id_tabla']);
            $this->assertSame($idSchemaEx, (int) $movida['id_schema']);
        }

        $this->assertNotNull($this->fila($out, self::ACTIV, self::DE_LA_DL), 'la de una persona de la dl no se toca');
        $this->assertNull($this->fila($ex, self::ACTIV, self::DE_LA_DL));

        $this->assertSame('la de ex', $this->fila($ex, self::ACTIV_REPETIDA, self::PASO)['observ'] ?? null, 'si ya había una en ex, no se pisa');
        $this->assertNull($this->fila($out, self::ACTIV_REPETIDA, self::PASO), 'y la repetida de out se borra');
        $this->assertSame('la de ex', $this->fila($ex, self::ACTIV_REPETIDA, self::PASO_MADRE)['observ'] ?? null);
        $this->assertNull($this->fila($dl, self::ACTIV_REPETIDA, self::PASO_MADRE), 'también la repetida que quedó en la dl');

        $this->aplicarMigracion();
        $this->assertNotNull($this->fila($ex, self::ACTIV, self::PASO), 'se puede volver a aplicar sin cambiar nada');
    }
}
