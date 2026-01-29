<?php

namespace Tests\unit\notas\domain\entity;

use src\notas\domain\entity\ActaTribunal;
use src\notas\domain\value_objects\ActaNumero;
use src\notas\domain\value_objects\Examinador;
use src\notas\domain\value_objects\Orden;
use Tests\myTest;

class ActaTribunalTest extends myTest
{
    private ActaTribunal $ActaTribunal;

    public function setUp(): void
    {
        parent::setUp();
        $this->ActaTribunal = new ActaTribunal();
        $this->ActaTribunal->setActaVo(new ActaNumero('dlb 23/24'));
        $this->ActaTribunal->setId_item(1);
    }

    public function test_set_and_get_acta()
    {
        $actaVo = new ActaNumero('dlb 23/24');
        $this->ActaTribunal->setActaVo($actaVo);
        $this->assertInstanceOf(ActaNumero::class, $this->ActaTribunal->getActaVo());
        $this->assertEquals('dlb 23/24', $this->ActaTribunal->getActaVo()->value());
    }

    public function test_set_and_get_examinador()
    {
        $examinadorVo = new Examinador('test');
        $this->ActaTribunal->setExaminadorVo($examinadorVo);
        $this->assertInstanceOf(Examinador::class, $this->ActaTribunal->getExaminadorVo());
        $this->assertEquals('test', $this->ActaTribunal->getExaminadorVo()->value());
    }

    public function test_set_and_get_orden()
    {
        $ordenVo = new Orden(10);
        $this->ActaTribunal->setOrdenVo($ordenVo);
        $this->assertInstanceOf(Orden::class, $this->ActaTribunal->getOrdenVo());
        $this->assertEquals(10, $this->ActaTribunal->getOrdenVo()->value());
    }

    public function test_set_and_get_id_item()
    {
        $this->ActaTribunal->setId_item(1);
        $this->assertEquals(1, $this->ActaTribunal->getId_item());
    }

    public function test_set_all_attributes()
    {
        $actaTribunal = new ActaTribunal();
        $attributes = [
            'acta' => new ActaNumero('dlb 23/24'),
            'examinador' => new Examinador('test'),
            'orden' => new Orden(10),
            'id_item' => 1,
        ];
        $actaTribunal->setAllAttributes($attributes);

        $this->assertEquals('dlb 23/24', $actaTribunal->getActaVo()->value());
        $this->assertEquals('test', $actaTribunal->getExaminadorVo()->value());
        $this->assertEquals(10, $actaTribunal->getOrdenVo()->value());
        $this->assertEquals(1, $actaTribunal->getId_item());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $actaTribunal = new ActaTribunal();
        $attributes = [
            'acta' => 'dlb 23/24',
            'examinador' => 'test',
            'orden' => 10,
            'id_item' => 1,
        ];
        $actaTribunal->setAllAttributes($attributes);

        $this->assertEquals('dlb 23/24', $actaTribunal->getActaVo()->value());
        $this->assertEquals('test', $actaTribunal->getExaminadorVo()->value());
        $this->assertEquals(10, $actaTribunal->getOrdenVo()->value());
        $this->assertEquals(1, $actaTribunal->getId_item());
    }
}
