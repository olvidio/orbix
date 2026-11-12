<?php

namespace Tests\integration\personas\infrastructure\persistence\postgresql;

use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\criteria\PersonaField;
use src\personas\domain\entity\PersonaPub;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\myTest;
use Tests\factories\personas\PersonaPubFactory;

class PgPersonaPubRepositoryTest extends myTest
{
    private PersonaPubRepositoryInterface $repository;
    private PersonaPubFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PersonaPubRepositoryInterface::class);
        $this->factory = new PersonaPubFactory();
    }

    /*
     *********** Solamente deben ser test de lectura ************
     */

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oPersonaPub = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oPersonaPub);
    }


    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_personas_para_listado_sigue_el_criterio()
    {
        $criteria = Criteria::create()
            ->where(Filter::eq(PersonaField::IdNom, 99999999));
        $problemas = [];
        $sinRegion = [];

        $this->assertSame([], $this->repository->getPersonasParaListado($criteria, $problemas, $sinRegion));

        $criteria = Criteria::create()
            ->where(Filter::eq(PersonaField::Situacion, 'A'))
            ->orderBy(PersonaField::Dl)
            ->addOrderBy(PersonaField::Apellido1)
            ->limit(3);
        $lista = $this->repository->getPersonasParaListado($criteria, $problemas, $sinRegion);

        $this->assertLessThanOrEqual(3, count($lista));
        foreach ($lista as $persona) {
            $this->assertInstanceOf(PersonaPub::class, $persona);
            $this->assertSame('A', $persona->getSituacion());
        }
    }

    private function idNomDeOtraDlSinPublicar(): int
    {
        $mio = \src\shared\config\ConfigGlobal::mi_id_schema();
        $id = \src\shared\infrastructure\GlobalPdo::get('oDBP')->query(
            "SELECT p.id_nom FROM global.personas p WHERE p.situacion = 'A' AND p.id_schema <> $mio AND p.publicado_para IS NULL
             AND NOT EXISTS (SELECT 1 FROM global.personas o WHERE o.id_nom = p.id_nom AND o.id_schema <> p.id_schema)
             ORDER BY p.id_nom LIMIT 1"
        )->fetchColumn();
        if ($id === false) {
            $this->markTestSkipped('No hay personas activas de otra dl');
        }
        return (int) $id;
    }

    public function test_una_persona_de_otra_dl_publicada_para_mi_dl_sale_en_los_listados()
    {
        $id = $this->idNomDeOtraDlSinPublicar();
        $pdo = \src\shared\infrastructure\GlobalPdo::get('oDBP');
        $schema = (int) $pdo->query("SELECT id_schema FROM global.personas WHERE id_nom = $id")->fetchColumn();
        $porId = Criteria::create()->where(Filter::eq(PersonaField::IdNom, $id));
        $this->assertSame([], $this->repository->matching($porId), 'sin publicar no sale');
        $this->assertNull($this->repository->findById($id));

        $all = $GLOBALS['container']->get(\src\personas\domain\contracts\PersonaAllRepositoryInterface::class);
        try {
            $this->assertTrue($all->marcarPublicadoPara($id, $schema, \src\shared\config\ConfigGlobal::mi_dele()));

            $lista = $this->repository->matching($porId);
            $this->assertCount(1, $lista);
            $this->assertSame([$id, $schema], [$lista[0]->getId_nom(), $lista[0]->getId_schema()]);

            $problemas = [];
            $sinRegion = [];
            $this->assertCount(1, $this->repository->getPersonasParaListado($porId, $problemas, $sinRegion));
            $this->assertSame([], $sinRegion);

            $p = $this->repository->findById($id);
            $this->assertSame($id, $p->getId_nom());
            $marca = true;
            $p = $this->repository->findByIdParaListado($id, $problemas, $marca);
            $this->assertSame($id, $p->getId_nom());
            $this->assertFalse($marca);
            $this->assertNull($this->repository->findByIdParaListado(99999999, $problemas, $marca));
        } finally {
            $pdo->exec("UPDATE global.personas SET publicado_para = NULL WHERE id_nom = $id AND id_schema = $schema");
        }
    }

    public function test_una_publicacion_caducada_no_sale_en_los_listados_pero_se_encuentra_por_id()
    {
        $id = $this->idNomDeOtraDlSinPublicar();
        $pdo = \src\shared\infrastructure\GlobalPdo::get('oDBP');
        $dl = $pdo->quote(\src\shared\config\ConfigGlobal::mi_dele());
        try {
            $pdo->exec("UPDATE global.personas SET publicado_para = jsonb_build_object($dl, '2001-01-01 00:00:00+00') WHERE id_nom = $id");
            $this->assertSame([], $this->repository->matching(Criteria::create()->where(Filter::eq(PersonaField::IdNom, $id))));
            $this->assertSame($id, $this->repository->findById($id)?->getId_nom());
        } finally {
            $pdo->exec("UPDATE global.personas SET publicado_para = NULL WHERE id_nom = $id");
        }
    }
}
