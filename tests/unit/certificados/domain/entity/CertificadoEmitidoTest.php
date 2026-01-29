<?php

namespace Tests\unit\certificados\domain\entity;

use src\certificados\domain\entity\CertificadoEmitido;
use Tests\myTest;

class CertificadoEmitidoTest extends myTest
{
    private CertificadoEmitido $CertificadoEmitido;

    public function setUp(): void
    {
        parent::setUp();
        $this->CertificadoEmitido = new CertificadoEmitido();
        $this->CertificadoEmitido->setId_item(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->CertificadoEmitido->setId_item(1);
        $this->assertEquals(1, $this->CertificadoEmitido->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->CertificadoEmitido->setId_nom(1);
        $this->assertEquals(1, $this->CertificadoEmitido->getId_nom());
    }

    public function test_set_and_get_nom()
    {
        $this->CertificadoEmitido->setNom('test');
        $this->assertEquals('test', $this->CertificadoEmitido->getNom());
    }

    public function test_set_and_get_idioma()
    {
        $this->CertificadoEmitido->setIdioma('test');
        $this->assertEquals('test', $this->CertificadoEmitido->getIdioma());
    }

    public function test_set_and_get_destino()
    {
        $this->CertificadoEmitido->setDestino('test');
        $this->assertEquals('test', $this->CertificadoEmitido->getDestino());
    }

    public function test_set_and_get_certificado()
    {
        $this->CertificadoEmitido->setCertificado('test');
        $this->assertEquals('test', $this->CertificadoEmitido->getCertificado());
    }

    public function test_set_and_get_esquema_emisor()
    {
        $this->CertificadoEmitido->setEsquema_emisor('test');
        $this->assertEquals('test', $this->CertificadoEmitido->getEsquema_emisor());
    }

    public function test_set_and_get_firmado()
    {
        $this->CertificadoEmitido->setFirmado(true);
        $this->assertTrue($this->CertificadoEmitido->isFirmado());
    }

    public function test_set_and_get_documento()
    {
        $this->CertificadoEmitido->setDocumento('test');
        $this->assertEquals('test', $this->CertificadoEmitido->getDocumento());
    }

    public function test_set_all_attributes()
    {
        $certificadoEmitido = new CertificadoEmitido();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'nom' => 'test',
            'idioma' => 'test',
            'destino' => 'test',
            'certificado' => 'test',
            'esquema_emisor' => 'test',
            'firmado' => true,
            'documento' => 'test',
        ];
        $certificadoEmitido->setAllAttributes($attributes);

        $this->assertEquals(1, $certificadoEmitido->getId_item());
        $this->assertEquals(1, $certificadoEmitido->getId_nom());
        $this->assertEquals('test', $certificadoEmitido->getNom());
        $this->assertEquals('test', $certificadoEmitido->getIdioma());
        $this->assertEquals('test', $certificadoEmitido->getDestino());
        $this->assertEquals('test', $certificadoEmitido->getCertificado());
        $this->assertEquals('test', $certificadoEmitido->getEsquema_emisor());
        $this->assertTrue($certificadoEmitido->isFirmado());
        $this->assertEquals('test', $certificadoEmitido->getDocumento());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $certificadoEmitido = new CertificadoEmitido();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'nom' => 'test',
            'idioma' => 'test',
            'destino' => 'test',
            'certificado' => 'test',
            'esquema_emisor' => 'test',
            'firmado' => true,
            'documento' => 'test',
        ];
        $certificadoEmitido->setAllAttributes($attributes);

        $this->assertEquals(1, $certificadoEmitido->getId_item());
        $this->assertEquals(1, $certificadoEmitido->getId_nom());
        $this->assertEquals('test', $certificadoEmitido->getNom());
        $this->assertEquals('test', $certificadoEmitido->getIdioma());
        $this->assertEquals('test', $certificadoEmitido->getDestino());
        $this->assertEquals('test', $certificadoEmitido->getCertificado());
        $this->assertEquals('test', $certificadoEmitido->getEsquema_emisor());
        $this->assertTrue($certificadoEmitido->isFirmado());
        $this->assertEquals('test', $certificadoEmitido->getDocumento());
    }
}
