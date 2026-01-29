<?php

namespace Tests\unit\encargossacd\domain\entity;

use src\encargossacd\domain\entity\EncargoSacdObserv;
use src\encargossacd\domain\value_objects\ObservText;
use Tests\myTest;

class EncargoSacdObservTest extends myTest
{
    private EncargoSacdObserv $EncargoSacdObserv;

    public function setUp(): void
    {
        parent::setUp();
        $this->EncargoSacdObserv = new EncargoSacdObserv();
        $this->EncargoSacdObserv->setId_item(1);
        $this->EncargoSacdObserv->setId_nom(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->EncargoSacdObserv->setId_item(1);
        $this->assertEquals(1, $this->EncargoSacdObserv->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->EncargoSacdObserv->setId_nom(1);
        $this->assertEquals(1, $this->EncargoSacdObserv->getId_nom());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ObservText('Test');
        $this->EncargoSacdObserv->setObservVo($observVo);
        $this->assertInstanceOf(ObservText::class, $this->EncargoSacdObserv->getObservVo());
        $this->assertEquals('Test', $this->EncargoSacdObserv->getObservVo()->value());
    }

    public function test_set_all_attributes()
    {
        $encargoSacdObserv = new EncargoSacdObserv();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'observ' => new ObservText('Test'),
        ];
        $encargoSacdObserv->setAllAttributes($attributes);

        $this->assertEquals(1, $encargoSacdObserv->getId_item());
        $this->assertEquals(1, $encargoSacdObserv->getId_nom());
        $this->assertEquals('Test', $encargoSacdObserv->getObservVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $encargoSacdObserv = new EncargoSacdObserv();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'observ' => 'Test',
        ];
        $encargoSacdObserv->setAllAttributes($attributes);

        $this->assertEquals(1, $encargoSacdObserv->getId_item());
        $this->assertEquals(1, $encargoSacdObserv->getId_nom());
        $this->assertEquals('Test', $encargoSacdObserv->getObservVo()->value());
    }
}
