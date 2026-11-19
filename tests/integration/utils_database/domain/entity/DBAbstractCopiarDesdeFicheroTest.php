<?php

declare(strict_types=1);

namespace Tests\integration\utils_database\domain\entity;

use PDO;
use src\shared\infrastructure\GlobalPdo;
use src\utils_database\domain\entity\DBAbstract;
use Tests\myTest;

/**
 * `DBAbstract::copiarDesdeFichero()` carga un fichero de texto en una tabla con `Pdo\Pgsql::copyFromFile()`
 * (los `DBEsquema` de cada módulo lo usan para llenar las tablas de datos fijos). Contra una tabla temporal
 * de la BD de pruebas, que desaparece al cerrar la conexión.
 */
final class DBAbstractCopiarDesdeFicheroTest extends myTest
{
    private string $fichero = '';

    public function tearDown(): void
    {
        if ($this->fichero !== '' && is_file($this->fichero)) {
            unlink($this->fichero);
        }
        GlobalPdo::get('oDBC')->exec('DROP TABLE IF EXISTS pg_temp.zz_copia_fichero');
        parent::tearDown();
    }

    public function test_copia_las_filas_del_fichero_con_tabulador_y_nulos(): void
    {
        $pdo = GlobalPdo::get('oDBC');
        $pdo->exec('CREATE TEMP TABLE zz_copia_fichero (id integer, texto text)');
        $this->fichero = tempnam(sys_get_temp_dir(), 'orbix_copia_');
        file_put_contents($this->fichero, "1\tuno\n2\t\\N\n3\ttres con espacios\n");

        (new DBAbstractCopiaDoble($pdo))->copiar('zz_copia_fichero', $this->fichero, "\t", "\\\\N", 'id, texto');

        $filas = $pdo->query('SELECT id, texto FROM zz_copia_fichero ORDER BY id')->fetchAll(PDO::FETCH_NUM);
        $this->assertSame([[1, 'uno'], [2, null], [3, 'tres con espacios']], $filas);
    }
}

final class DBAbstractCopiaDoble extends DBAbstract
{
    public function __construct(PDO $pdo)
    {
        $this->oDbl = $pdo;
    }

    public function copiar(string $tabla, string $fichero, string $delimitador, string $nulo, string $campos): void
    {
        $this->copiarDesdeFichero($this->oDbl, $tabla, $fichero, $delimitador, $nulo, $campos);
    }
}
