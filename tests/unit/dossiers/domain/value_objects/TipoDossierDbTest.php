<?php

namespace Tests\unit\dossiers\domain\value_objects;

use src\dossiers\domain\value_objects\TipoDossierDb;
use Tests\myTest;

class TipoDossierDbTest extends myTest
{
    public function test_create_valid_tipoDossierDb()
    {
        $tipoDossierDb = new TipoDossierDb(2);
        $this->assertEquals(2, $tipoDossierDb->value());
    }

    public function test_to_string_returns_tipoDossierDb_value()
    {
        $tipoDossierDb = new TipoDossierDb(1);
        $this->assertEquals(1, (string)$tipoDossierDb);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $tipoDossierDb = TipoDossierDb::fromNullableInt(1);
        $this->assertInstanceOf(TipoDossierDb::class, $tipoDossierDb);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $tipoDossierDb = TipoDossierDb::fromNullableInt(null);
        $this->assertNull($tipoDossierDb);
    }

}
