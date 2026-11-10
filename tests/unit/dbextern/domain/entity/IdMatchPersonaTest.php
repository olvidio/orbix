<?php

declare(strict_types=1);

namespace Tests\unit\dbextern\domain\entity;

use src\dbextern\domain\entity\IdMatchPersona;
use Tests\factories\dbextern\IdMatchPersonaFactory;
use Tests\myTest;

final class IdMatchPersonaTest extends myTest
{
    public function test_set_and_get_campos(): void
    {
        $o = new IdMatchPersona();
        $o->setId_listas(990000001);
        $o->setId_orbix(123);
        $o->setId_tabla('agd');

        $this->assertSame(990000001, $o->getId_listas());
        $this->assertSame(123, $o->getId_orbix());
        $this->assertSame('agd', $o->getId_tabla());
    }

    public function test_id_orbix_admite_null(): void
    {
        $o = (new IdMatchPersonaFactory())->createSimple(990000002, 5);
        $o->setId_orbix(null);
        $this->assertNull($o->getId_orbix());
        $o->setId_orbix();
        $this->assertNull($o->getId_orbix());
    }

    public function test_id_tabla_null_pasa_a_cadena_vacia(): void
    {
        $o = new IdMatchPersona();
        $o->setId_tabla(null);
        $this->assertSame('', $o->getId_tabla());
    }

    public function test_from_array_hidrata_como_filas_de_bd(): void
    {
        $o = IdMatchPersona::fromArray(['id_listas' => 7, 'id_orbix' => null, 'id_tabla' => 'n']);
        $this->assertSame(7, $o->getId_listas());
        $this->assertNull($o->getId_orbix());
        $this->assertSame('n', $o->getId_tabla());
    }

    public function test_to_array_for_database(): void
    {
        $o = (new IdMatchPersonaFactory())->createSimple(990000003, 9, 'sup');
        $a = $o->toArrayForDatabase();
        $this->assertSame(990000003, $a['id_listas']);
        $this->assertSame(9, $a['id_orbix']);
        $this->assertSame('sup', $a['id_tabla']);
    }
}
