<?php

namespace Tests\integration\asistentes\infrastructure\persistence\postgresql;

use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use Tests\factories\asistentes\AsistenteFactory;
use Tests\myTest;

class PgAsistentePubRepositoryTest extends myTest
{
    private AsistentePubRepositoryInterface $repository;
    private AsistenteFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(AsistentePubRepositoryInterface::class);
        $this->factory = new AsistenteFactory();
    }

    public function test_guardar_eliminar_asistente()
    {
        $o = $this->factory->createSimple();
        $ida = $o->getId_activ();
        $idb = $o->getId_nom();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($ida, $idb);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(Asistente::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($ida, $idb));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999991, 999999992));
    }

    /**
     * Personas distintas de paso apuntadas a unas actividades (lo usa el desplegable de profesores de un ca).
     */
    public function test_lista_las_personas_distintas_de_las_actividades_pedidas(): void
    {
        $idActiv1 = 10018901;
        $idActiv2 = 10018902;
        $creados = [];
        try {
            foreach ([[$idActiv1, 10019901], [$idActiv1, 10019902], [$idActiv2, 10019902], [$idActiv2, 10019903]] as [$a, $n]) {
                $o = $this->factory->createSimple(new \src\asistentes\domain\value_objects\AsistentePk($a, $n));
                $this->assertTrue($this->repository->Guardar($o));
                $creados[] = $o;
            }

            $una = $this->repository->getListaAsistentesDistintos([$idActiv1]);
            sort($una);
            $this->assertSame([10019901, 10019902], $una);

            $dos = $this->repository->getListaAsistentesDistintos([$idActiv1, $idActiv2]);
            sort($dos);
            $this->assertSame([10019901, 10019902, 10019903], $dos, 'sin repetir a quien está en las dos');

            $this->assertSame([], $this->repository->getListaAsistentesDistintos([999999991]));

            $todas = $this->repository->getListaAsistentesDistintos();
            $this->assertIsArray($todas);
            $this->assertContains(10019903, $todas);
        } finally {
            foreach ($creados as $o) {
                $this->repository->Eliminar($o);
            }
        }
    }
}
