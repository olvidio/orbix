<?php

namespace Tests\integration\inventario\infrastructure\persistence\postgresql;

use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\entity\Lugar;
use Tests\myTest;

class PgLugarRepositoryTest extends myTest
{
    private LugarRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(LugarRepositoryInterface::class);
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }

    public function test_datos_by_id_no_existente()
    {
        $this->assertFalse($this->repository->datosById(999999981));
    }

    public function test_get_new_id()
    {
        $nid = $this->repository->getNewId();
        $this->assertTrue(is_numeric($nid));
    }

    public function test_ciclo_guardar_actualizar_y_eliminar_y_lista_de_lugares_del_centro(): void
    {
        $idUbi = 990000000 + random_int(1000, 9999);
        $lugares = [];
        foreach (['Zeta', 'Alfa'] as $nombre) {
            $o = new Lugar();
            $o->setId_lugar($this->repository->getNewId());
            $o->setId_ubi($idUbi);
            $o->setNom_lugar($nombre);
            $this->assertTrue($this->repository->Guardar($o));
            $lugares[] = $o;
        }
        [$zeta, $alfa] = $lugares;
        try {
            $this->assertSame([$alfa->getId_lugar() => 'Alfa', $zeta->getId_lugar() => 'Zeta'], $this->repository->getArrayLugares($idUbi));
            $this->assertSame([], $this->repository->getArrayLugares(-1));

            $leido = $this->repository->findById($zeta->getId_lugar());
            $this->assertNotNull($leido);
            $this->assertSame([$idUbi, 'Zeta'], [$leido->getId_ubi(), $leido->getNom_lugar()]);
            $leido->setNom_lugar('Beta');
            $this->assertTrue($this->repository->Guardar($leido));
            $this->assertSame('Beta', $this->repository->findById($zeta->getId_lugar())?->getNom_lugar());
            $this->assertSame('Alfa', $this->repository->findById($alfa->getId_lugar())?->getNom_lugar(), 'el otro lugar no cambia');

            $this->assertTrue($this->repository->Eliminar($leido));
            $this->assertNull($this->repository->findById($zeta->getId_lugar()));
            $this->assertNotNull($this->repository->findById($alfa->getId_lugar()));
        } finally {
            foreach ($lugares as $o) {
                $this->repository->Eliminar($o);
            }
        }
    }
}
