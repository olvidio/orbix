<?php

namespace Tests\unit\cartaspresentacion\domain\entity;

use src\cartaspresentacion\domain\entity\CartaPresentacion;
use src\cartaspresentacion\domain\value_objects\PresEmailText;
use src\cartaspresentacion\domain\value_objects\PresNombreText;
use src\cartaspresentacion\domain\value_objects\PresObservText;
use src\cartaspresentacion\domain\value_objects\PresTelefonoText;
use src\cartaspresentacion\domain\value_objects\PresZonaText;
use Tests\myTest;

class CartaPresentacionTest extends myTest
{
    private CartaPresentacion $CartaPresentacion;

    public function setUp(): void
    {
        parent::setUp();
        $this->CartaPresentacion = new CartaPresentacion();
        $this->CartaPresentacion->setId_direccion(1);
        $this->CartaPresentacion->setId_ubi(1);
    }

    public function test_set_and_get_id_direccion()
    {
        $this->CartaPresentacion->setId_direccion(1);
        $this->assertEquals(1, $this->CartaPresentacion->getId_direccion());
    }

    public function test_set_and_get_id_ubi()
    {
        $this->CartaPresentacion->setId_ubi(1);
        $this->assertEquals(1, $this->CartaPresentacion->getId_ubi());
    }

    public function test_set_and_get_pres_nom()
    {
        $pres_nomVo = new PresNombreText('Test');
        $this->CartaPresentacion->setPresNomVo($pres_nomVo);
        $this->assertInstanceOf(PresNombreText::class, $this->CartaPresentacion->getPresNomVo());
        $this->assertEquals('Test', $this->CartaPresentacion->getPresNomVo()->value());
    }

    public function test_set_and_get_pres_telf()
    {
        $pres_telfVo = new PresTelefonoText('123456789');
        $this->CartaPresentacion->setPresTelfVo($pres_telfVo);
        $this->assertInstanceOf(PresTelefonoText::class, $this->CartaPresentacion->getPresTelfVo());
        $this->assertEquals('123456789', $this->CartaPresentacion->getPresTelfVo()->value());
    }

    public function test_set_and_get_pres_mail()
    {
        $pres_mailVo = new PresEmailText('test@example.com');
        $this->CartaPresentacion->setPresMailVo($pres_mailVo);
        $this->assertInstanceOf(PresEmailText::class, $this->CartaPresentacion->getPresMailVo());
        $this->assertEquals('test@example.com', $this->CartaPresentacion->getPresMailVo()->value());
    }

    public function test_set_and_get_zona()
    {
        $zonaVo = new PresZonaText('Test');
        $this->CartaPresentacion->setZonaVo($zonaVo);
        $this->assertInstanceOf(PresZonaText::class, $this->CartaPresentacion->getZonaVo());
        $this->assertEquals('Test', $this->CartaPresentacion->getZonaVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new PresObservText('Test');
        $this->CartaPresentacion->setObservVo($observVo);
        $this->assertInstanceOf(PresObservText::class, $this->CartaPresentacion->getObservVo());
        $this->assertEquals('Test', $this->CartaPresentacion->getObservVo()->value());
    }

    public function test_set_all_attributes()
    {
        $cartaPresentacion = new CartaPresentacion();
        $attributes = [
            'id_direccion' => 1,
            'id_ubi' => 1,
            'pres_nom' => new PresNombreText('Test'),
            'pres_telf' => new PresTelefonoText('123456789'),
            'pres_mail' => new PresEmailText('test@example.com'),
            'zona' => new PresZonaText('Test'),
            'observ' => new PresObservText('Test'),
        ];
        $cartaPresentacion->setAllAttributes($attributes);

        $this->assertEquals(1, $cartaPresentacion->getId_direccion());
        $this->assertEquals(1, $cartaPresentacion->getId_ubi());
        $this->assertEquals('Test', $cartaPresentacion->getPresNomVo()->value());
        $this->assertEquals('123456789', $cartaPresentacion->getPresTelfVo()->value());
        $this->assertEquals('test@example.com', $cartaPresentacion->getPresMailVo()->value());
        $this->assertEquals('Test', $cartaPresentacion->getZonaVo()->value());
        $this->assertEquals('Test', $cartaPresentacion->getObservVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $cartaPresentacion = new CartaPresentacion();
        $attributes = [
            'id_direccion' => 1,
            'id_ubi' => 1,
            'pres_nom' => 'Test',
            'pres_telf' => '123456789',
            'pres_mail' => 'test@example.com',
            'zona' => 'Test',
            'observ' => 'Test',
        ];
        $cartaPresentacion->setAllAttributes($attributes);

        $this->assertEquals(1, $cartaPresentacion->getId_direccion());
        $this->assertEquals(1, $cartaPresentacion->getId_ubi());
        $this->assertEquals('Test', $cartaPresentacion->getPresNomVo()->value());
        $this->assertEquals('123456789', $cartaPresentacion->getPresTelfVo()->value());
        $this->assertEquals('test@example.com', $cartaPresentacion->getPresMailVo()->value());
        $this->assertEquals('Test', $cartaPresentacion->getZonaVo()->value());
        $this->assertEquals('Test', $cartaPresentacion->getObservVo()->value());
    }
}
