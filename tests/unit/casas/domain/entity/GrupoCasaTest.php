<?php

namespace Tests\unit\casas\domain\entity;

use src\casas\domain\entity\GrupoCasa;
use Tests\myTest;

class GrupoCasaTest extends myTest
{
    private GrupoCasa $GrupoCasa;

    public function setUp(): void
    {
        parent::setUp();
        $this->GrupoCasa = new GrupoCasa();
        $this->GrupoCasa->setId_item(1);
        $this->GrupoCasa->setId_ubi_padre(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->GrupoCasa->setId_item(1);
        $this->assertEquals(1, $this->GrupoCasa->getId_item());
    }

    public function test_set_and_get_id_ubi_padre()
    {
        $this->GrupoCasa->setId_ubi_padre(1);
        $this->assertEquals(1, $this->GrupoCasa->getId_ubi_padre());
    }

    public function test_set_and_get_id_ubi_hijo()
    {
        $this->GrupoCasa->setId_ubi_hijo(1);
        $this->assertEquals(1, $this->GrupoCasa->getId_ubi_hijo());
    }

    public function test_set_all_attributes()
    {
        $grupoCasa = new GrupoCasa();
        $attributes = [
            'id_item' => 1,
            'id_ubi_padre' => 1,
            'id_ubi_hijo' => 1,
        ];
        $grupoCasa->setAllAttributes($attributes);

        $this->assertEquals(1, $grupoCasa->getId_item());
        $this->assertEquals(1, $grupoCasa->getId_ubi_padre());
        $this->assertEquals(1, $grupoCasa->getId_ubi_hijo());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $grupoCasa = new GrupoCasa();
        $attributes = [
            'id_item' => 1,
            'id_ubi_padre' => 1,
            'id_ubi_hijo' => 1,
        ];
        $grupoCasa->setAllAttributes($attributes);

        $this->assertEquals(1, $grupoCasa->getId_item());
        $this->assertEquals(1, $grupoCasa->getId_ubi_padre());
        $this->assertEquals(1, $grupoCasa->getId_ubi_hijo());
    }
}
