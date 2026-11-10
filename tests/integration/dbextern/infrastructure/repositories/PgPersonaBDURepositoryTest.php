<?php

namespace Tests\integration\dbextern\infrastructure\repositories;

use PDO;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\entity\PersonaBDU;
use src\shared\infrastructure\GlobalPdo;
use Tests\factories\dbextern\PersonaBDUFactory;
use Tests\myTest;

/**
 * tmp_bdu es una caché de la BDU que rellena CopiarBDU::crearTablaTmp(). El repositorio
 * solo la lee: el test crea la tabla si falta (no todas las copias de BD la traen) e
 * inserta y borra sus propias filas (ids 990000001..990999999).
 */
class PgPersonaBDURepositoryTest extends myTest
{
    private PersonaBDURepositoryInterface $repository;
    private PDO $pdo;

    /** @var list<int> */
    private array $ids = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PersonaBDURepositoryInterface::class);
        $this->pdo = GlobalPdo::get('oDBP');
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS tmp_bdu(
            Identif bigint NOT NULL PRIMARY KEY, Apenom text, dl text, ctr text, Lugar_Naci text,
            Fecha_Naci text, Email text, Tfno_Movil text, Ce text, Prof_Carg text, Titu_Estu text,
            Encargos text, INCORP text, pertenece_r text, camb_fic text, fecha_c_fic text,
            compartida_con_r text)");
        $this->ids = [];
    }

    public function tearDown(): void
    {
        foreach ($this->ids as $id) {
            $this->pdo->exec("DELETE FROM tmp_bdu WHERE identif = $id");
        }
        parent::tearDown();
    }

    /**
     * @param array<string, string|null> $campos
     */
    private function insertar(int $identif, array $campos = []): int
    {
        $datos = array_merge([
            'apenom' => 'García Pérez, Ana',
            'dl' => 'dlb',
            'ctr' => 'Centro Test',
            'lugar_naci' => 'Sevilla',
            'fecha_naci' => '1990-05-17',
            'email' => 'ana@example.org',
            'tfno_movil' => '600000000',
            'ce' => '2Madrid: 05-08',
            'prof_carg' => 'Profesora',
            'titu_estu' => 'Derecho',
            'encargos' => 'Ninguno',
            'incorp' => 'Nu 15-03-2001',
            'pertenece_r' => 'mad',
            'camb_fic' => null,
            'fecha_c_fic' => null,
            'compartida_con_r' => null,
        ], $campos);
        $cols = implode(',', array_keys($datos));
        $params = ':' . implode(',:', array_keys($datos));
        $stmt = $this->pdo->prepare("INSERT INTO tmp_bdu (identif,$cols) VALUES ($identif,$params)");
        $stmt->execute($datos);
        $this->ids[] = $identif;

        return $identif;
    }

    private function idTest(int $n): int
    {
        return PersonaBDUFactory::ID_BASE + 500000 + $n;
    }

    public function test_find_by_id_existente_hidrata_todos_los_campos(): void
    {
        $id = $this->insertar($this->idTest(1));

        $p = $this->repository->findById($id);

        $this->assertInstanceOf(PersonaBDU::class, $p);
        $this->assertSame($id, $p->getIdentif());
        $this->assertSame('García Pérez, Ana', $p->getApenom());
        $this->assertSame('dlb', $p->getDl());
        $this->assertSame('Centro Test', $p->getCtr());
        $this->assertSame('Sevilla', $p->getLugar_Naci());
        $this->assertSame('17/05/1990', $p->getFecha_Naci());
        $this->assertSame('ana@example.org', $p->getEmail());
        $this->assertSame('600000000', $p->getTfno_Movil());
        $this->assertSame('2Madrid: 05-08', $p->getCe());
        $this->assertSame('Profesora', $p->getProfesion_cargo());
        $this->assertSame('Derecho', $p->getTitulo_Estudios());
        $this->assertSame('Ninguno', $p->getEncargos());
        $this->assertSame('Nu 15-03-2001', $p->getIncorporacion());
        $this->assertSame('mad', $p->getPertenece_r());
        $this->assertSame('', $p->getCamb_fic());
        $this->assertNull($p->getFecha_c_fic());
        $this->assertSame('', $p->getCompartida_con_r());
    }

    public function test_find_by_id_hidratada_calcula_campos_derivados(): void
    {
        $p = $this->repository->findById($this->insertar($this->idTest(2)));

        $this->assertSame('Ana', $p->getNombre());
        $this->assertSame('García', $p->getApellido1());
        $this->assertSame('Pérez', $p->getApellido2());
        $this->assertSame('Madrid', $p->getCe_lugar());
        $this->assertSame(2005, $p->getCe_ini());
        $this->assertSame('Nu', $p->getInc());
        $this->assertSame('2001-03-15', $p->getF_inc());
    }

    public function test_find_by_id_con_columnas_nulas(): void
    {
        $id = $this->insertar($this->idTest(3), [
            'apenom' => null, 'dl' => null, 'ctr' => null, 'lugar_naci' => null, 'fecha_naci' => null,
            'email' => null, 'tfno_movil' => null, 'ce' => null, 'prof_carg' => null, 'titu_estu' => null,
            'encargos' => null, 'incorp' => null, 'pertenece_r' => null,
        ]);

        $p = $this->repository->findById($id);

        $this->assertNotNull($p);
        $this->assertSame('', $p->getApenom());
        $this->assertSame('', $p->getFecha_Naci());
        $this->assertSame('', $p->getProfesion_cargo());
        $this->assertSame('', $p->getInc());
        $this->assertSame('', $p->getCe_ini());
    }

    public function test_find_by_id_no_existente(): void
    {
        $this->assertNull($this->repository->findById($this->idTest(99)));
    }

    public function test_datos_by_id(): void
    {
        $id = $this->insertar($this->idTest(4));

        $datos = $this->repository->datosById($id);

        $this->assertIsArray($datos);
        $this->assertSame('García Pérez, Ana', $datos['apenom']);
        $this->assertFalse($this->repository->datosById($this->idTest(98)));
    }

    public function test_get_persona_bdu_query_devuelve_las_filas_pedidas(): void
    {
        $a = $this->insertar($this->idTest(5), ['apenom' => 'Alfa Uno, Ana']);
        $b = $this->insertar($this->idTest(6), ['apenom' => 'Beta Dos, Berta']);
        $this->insertar($this->idTest(7), ['apenom' => 'Gamma Tres, Carla']);

        $res = $this->repository->getPersonaBDUQuery("SELECT * FROM tmp_bdu WHERE identif IN ($a,$b) ORDER BY identif");

        $this->assertCount(2, $res);
        $this->assertSame([$a, $b], array_map(static fn(PersonaBDU $p) => $p->getIdentif(), $res));
        $this->assertSame('Alfa', $res[0]->getApellido1());
        $this->assertSame('Beta', $res[1]->getApellido1());
    }

    public function test_get_persona_bdu_query_filtra_por_dl_y_region(): void
    {
        $this->insertar($this->idTest(8), ['dl' => 'dlb', 'pertenece_r' => 'mad']);
        $this->insertar($this->idTest(9), ['dl' => 'dlp', 'pertenece_r' => 'mad']);
        $this->insertar($this->idTest(10), ['dl' => 'dlb', 'pertenece_r' => 'esp', 'compartida_con_r' => 'mad']);
        $rango = 'identif BETWEEN ' . PersonaBDUFactory::ID_BASE . ' AND ' . (PersonaBDUFactory::ID_BASE + 999999);

        $res = $this->repository->getPersonaBDUQuery(
            "SELECT * FROM tmp_bdu WHERE $rango AND dl='dlb' AND (pertenece_r='mad' OR compartida_con_r='mad') ORDER BY identif"
        );

        $this->assertSame(
            [$this->idTest(8), $this->idTest(10)],
            array_map(static fn(PersonaBDU $p) => $p->getIdentif(), $res)
        );
    }

    public function test_get_persona_bdu_query_sin_resultados(): void
    {
        $this->assertSame([], $this->repository->getPersonaBDUQuery('SELECT * FROM tmp_bdu WHERE identif = ' . $this->idTest(97)));
    }

    public function test_get_nom_tabla(): void
    {
        $this->assertSame('tmp_bdu', $this->repository->getNomTabla());
    }
}
