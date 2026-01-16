<?php

namespace Tests\unit\dossiers\domain\value_objects;

use src\dossiers\domain\value_objects\DossierTabla;
use Tests\myTest;

class DossierTablaTest extends myTest
{
    public function test_create_valid_dossierTabla()
    {
        $dossierTabla = new DossierTabla('a');
        $this->assertEquals('a', $dossierTabla->value());
    }

    public function test_to_string_returns_dossierTabla_value()
    {
        $dossierTabla = new DossierTabla('p');
        $this->assertEquals('p', (string)$dossierTabla);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $dossierTabla = DossierTabla::fromNullableString('p');
        $this->assertInstanceOf(DossierTabla::class, $dossierTabla);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $dossierTabla = DossierTabla::fromNullableString(null);
        $this->assertNull($dossierTabla);
    }

}
