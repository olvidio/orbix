<?php

declare(strict_types=1);

namespace Tests\integration\asistentes\infrastructure;

use PDO;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Migración que pasa a `restov.d_asistentes_ex` las asistencias de personas de paso (`id_nom` negativo)
 * que quedaron en `"<dl>".d_asistentes_dl`: las creaba `SacdAsignar` al asignar un sacd de paso.
 *
 * Ejecuta el SQL del fichero de migración dentro de una transacción que se deshace, sobre filas propias.
 */
final class MigracionAsistenciasDePasoAExTest extends myTest
{
    private const MIGRACION = __DIR__ . '/../../../../db/migrations/202611191000_d_asistentes_de_paso_de_dl_a_ex__sv-e.sql';
    private const ACTIV = 9907770001;
    private const ACTIV_REPETIDA = 9907770002;
    private const PASO = -990777011;
    private const DE_LA_DL = 990777012;

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

    private function enDl(int $idActiv, int $idNom, string $observ): void
    {
        $st = $this->pdo->prepare("INSERT INTO \"{$this->esquema}\".d_asistentes_dl (id_activ, id_nom, propio, falta, observ) VALUES (:a, :n, false, true, :o)");
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

    public function test_pasa_a_ex_las_asistencias_de_personas_de_paso_que_estaban_en_la_dl(): void
    {
        $dl = "\"{$this->esquema}\".d_asistentes_dl";
        $ex = 'restov.d_asistentes_ex';
        $this->enDl(self::ACTIV, self::PASO, 'sacd de paso');
        $this->enDl(self::ACTIV, self::DE_LA_DL, 'sacd de la dl');
        $this->enDl(self::ACTIV_REPETIDA, self::PASO, 'la de la dl');
        $this->pdo->exec("INSERT INTO $ex (id_activ, id_nom, observ) VALUES (" . self::ACTIV_REPETIDA . ', ' . self::PASO . ", 'la de ex')");

        $this->aplicarMigracion();

        $this->assertNull($this->fila($dl, self::ACTIV, self::PASO), 'la de paso ya no está en la dl');
        $movida = $this->fila($ex, self::ACTIV, self::PASO);
        $this->assertNotNull($movida, 'la de paso está en ex');
        $this->assertSame('sacd de paso', $movida['observ']);
        $this->assertFalse((bool) $movida['propio'], 'conserva propio');
        $this->assertTrue((bool) $movida['falta'], 'conserva el resto de campos');
        $this->assertSame('ex', $movida['id_tabla']);
        $this->assertSame((int) $this->pdo->query("SELECT public.idschema('restov')")->fetchColumn(), (int) $movida['id_schema']);

        $this->assertNotNull($this->fila($dl, self::ACTIV, self::DE_LA_DL), 'la de una persona de la dl no se toca');
        $this->assertNull($this->fila($ex, self::ACTIV, self::DE_LA_DL));

        $this->assertSame('la de ex', $this->fila($ex, self::ACTIV_REPETIDA, self::PASO)['observ'] ?? null, 'si ya había una en ex, no se pisa');
        $this->assertNotNull($this->fila($dl, self::ACTIV_REPETIDA, self::PASO), 'y la repetida de la dl se deja para revisarla');

        $this->aplicarMigracion();
        $this->assertNotNull($this->fila($ex, self::ACTIV, self::PASO), 'se puede volver a aplicar sin cambiar nada');
    }
}
