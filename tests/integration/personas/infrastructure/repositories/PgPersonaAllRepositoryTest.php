<?php

namespace Tests\integration\personas\infrastructure\persistence\postgresql;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaPub;
use src\personas\domain\PersonaPublicacion;
use src\personas\infrastructure\persistence\postgresql\PgPersonaAllRepository;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Tabla padre global.personas: buscar una persona de mi esquema (activa o no),
 * la búsqueda para listados (cualquier esquema) y marcar «publicado para».
 * Los ids salen de la BD de desarrollo; la única escritura se deshace.
 */
class PgPersonaAllRepositoryTest extends myTest
{
    private PgPersonaAllRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new PgPersonaAllRepository(
            $GLOBALS['container']->get(PersonaDlRepositoryInterface::class),
        );
    }

    /** id_nom de una persona que cumple la condición, o se salta el test. */
    private function idNom(string $where): int
    {
        $id = GlobalPdo::get('oDBP')->query("SELECT p.id_nom FROM global.personas p WHERE $where ORDER BY p.id_nom LIMIT 1")->fetchColumn();
        if ($id === false) {
            $this->markTestSkipped("No hay ninguna persona con: $where");
        }
        return (int) $id;
    }

    private function soloEn(int $schema, string $situacion): string
    {
        return "p.id_schema = $schema AND p.situacion = '$situacion' AND NOT EXISTS (SELECT 1 FROM global.personas o WHERE o.id_nom = p.id_nom AND o.id_schema <> $schema)";
    }

    public function test_get_persona_by_id_nom_cuando_no_hay_coincidencias()
    {
        $this->assertNull($this->repository->getPersonaByIdNom(999999981));
    }

    public function test_get_persona_by_id_nom_de_mi_esquema_activa_y_no_activa()
    {
        $mio = ConfigGlobal::mi_id_schema();
        $activa = $this->idNom($this->soloEn($mio, 'A'));
        $baja = $this->idNom($this->soloEn($mio, 'B'));

        $p = $this->repository->getPersonaByIdNom($activa);
        $this->assertInstanceOf(PersonaDl::class, $p);
        $this->assertSame($activa, $p->getId_nom());

        $p = $this->repository->getPersonaByIdNom($baja);
        $this->assertInstanceOf(PersonaDl::class, $p, 'también si no está en situación A');
        $this->assertSame('B', $p->getSituacion());
    }

    public function test_get_persona_by_id_nom_de_otro_esquema_no_la_encuentra()
    {
        $otra = $this->idNom($this->soloEn(1003, 'A') . ' AND ' . ConfigGlobal::mi_id_schema() . ' <> 1003');
        $this->assertNull($this->repository->getPersonaByIdNom($otra));
    }

    public function test_find_by_id_nom_para_lookup()
    {
        $this->assertNull($this->repository->findByIdNomParaLookup(0));
        $this->assertNull($this->repository->findByIdNomParaLookup(999999981));

        $otra = $this->idNom($this->soloEn(1003, 'A'));
        $p = $this->repository->findByIdNomParaLookup($otra);
        $this->assertInstanceOf(PersonaPub::class, $p);
        $this->assertSame([$otra, 'A'], [$p->getId_nom(), $p->getSituacion()]);
        $this->assertNotNull($this->repository->findByIdNomParaLookup($otra, 1003));
        $this->assertNull($this->repository->findByIdNomParaLookup($otra, 1001), 'filtrando por otro esquema');
    }

    public function test_find_by_id_nom_para_lookup_prefiere_la_fila_activa()
    {
        $id = $this->idNom("p.situacion <> 'A' AND EXISTS (SELECT 1 FROM global.personas o WHERE o.id_nom = p.id_nom AND o.situacion = 'A')");
        $this->assertSame('A', $this->repository->findByIdNomParaLookup($id)->getSituacion());
    }

    public function test_marcar_publicado_para_argumentos_no_validos_o_sin_fila_activa()
    {
        $this->assertFalse($this->repository->marcarPublicadoPara(0, 1001, 'dlz'));
        $this->assertFalse($this->repository->marcarPublicadoPara(100555, 0, 'dlz'));
        $this->assertFalse($this->repository->marcarPublicadoPara(100555, 1001, '  '));
        $baja = $this->idNom($this->soloEn(1001, 'B'));
        $this->assertFalse($this->repository->marcarPublicadoPara($baja, 1001, 'dlz'), 'solo marca filas activas');
        $this->assertFalse($this->repository->marcarPublicadoPara(999999981, 1001, 'dlz'));
    }

    public function test_marcar_publicado_para_anade_la_dl_y_conserva_las_que_habia()
    {
        $id = $this->idNom($this->soloEn(1001, 'A') . ' AND p.publicado_para IS NULL');
        $pdo = GlobalPdo::get('oDBP');
        $leer = function () use ($pdo, $id): array {
            $raw = $pdo->query("SELECT publicado_para FROM global.personas WHERE id_nom = $id AND id_schema = 1001")->fetchColumn();
            return PersonaPublicacion::fromPg($raw === false ? null : $raw);
        };
        try {
            $this->assertTrue($this->repository->marcarPublicadoPara($id, 1001, 'dlz'));
            $this->assertTrue($this->repository->marcarPublicadoPara($id, 1001, PersonaPublicacion::DL_TODAS));
            $mapa = $leer();
            $this->assertArrayHasKey('dlz', $mapa);
            $this->assertArrayHasKey(PersonaPublicacion::DL_TODAS, $mapa);
            $this->assertNotNull($mapa['dlz'], 'una dl concreta lleva fecha hasta por defecto');
        } finally {
            $pdo->exec("UPDATE global.personas SET publicado_para = NULL WHERE id_nom = $id AND id_schema = 1001");
        }
        $this->assertSame([], $leer());
    }
}
