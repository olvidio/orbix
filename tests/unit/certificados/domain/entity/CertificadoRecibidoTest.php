<?php

namespace Tests\unit\certificados\domain\entity;

use src\certificados\domain\entity\CertificadoRecibido;
use Tests\myTest;

class CertificadoRecibidoTest extends myTest
{
    private CertificadoRecibido $CertificadoRecibido;

    public function setUp(): void
    {
        parent::setUp();
        $this->CertificadoRecibido = new CertificadoRecibido();
        $this->CertificadoRecibido->setId_item(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->CertificadoRecibido->setId_item(1);
        $this->assertEquals(1, $this->CertificadoRecibido->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->CertificadoRecibido->setId_nom(1);
        $this->assertEquals(1, $this->CertificadoRecibido->getId_nom());
    }

    public function test_set_and_get_nom()
    {
        $this->CertificadoRecibido->setNom('test');
        $this->assertEquals('test', $this->CertificadoRecibido->getNom());
    }

    public function test_set_and_get_idioma()
    {
        $this->CertificadoRecibido->setIdioma('test');
        $this->assertEquals('test', $this->CertificadoRecibido->getIdioma());
    }

    public function test_set_and_get_destino()
    {
        $this->CertificadoRecibido->setDestino('test');
        $this->assertEquals('test', $this->CertificadoRecibido->getDestino());
    }

    public function test_set_and_get_certificado()
    {
        $this->CertificadoRecibido->setCertificado('test');
        $this->assertEquals('test', $this->CertificadoRecibido->getCertificado());
    }

    public function test_set_and_get_esquema_emisor()
    {
        $this->CertificadoRecibido->setEsquema_emisor('test');
        $this->assertEquals('test', $this->CertificadoRecibido->getEsquema_emisor());
    }

    public function test_set_and_get_firmado()
    {
        $this->CertificadoRecibido->setFirmado(true);
        $this->assertTrue($this->CertificadoRecibido->isFirmado());
    }

    public function test_set_and_get_documento()
    {
        $this->CertificadoRecibido->setDocumento('test');
        $this->assertEquals('test', $this->CertificadoRecibido->getDocumento());
    }

    public function test_set_all_attributes()
    {
        $certificadoRecibido = new CertificadoRecibido();
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
        $certificadoRecibido->setAllAttributes($attributes);

        $this->assertEquals(1, $certificadoRecibido->getId_item());
        $this->assertEquals(1, $certificadoRecibido->getId_nom());
        $this->assertEquals('test', $certificadoRecibido->getNom());
        $this->assertEquals('test', $certificadoRecibido->getIdioma());
        $this->assertEquals('test', $certificadoRecibido->getDestino());
        $this->assertEquals('test', $certificadoRecibido->getCertificado());
        $this->assertEquals('test', $certificadoRecibido->getEsquema_emisor());
        $this->assertTrue($certificadoRecibido->isFirmado());
        $this->assertEquals('test', $certificadoRecibido->getDocumento());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $certificadoRecibido = new CertificadoRecibido();
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
        $certificadoRecibido->setAllAttributes($attributes);

        $this->assertEquals(1, $certificadoRecibido->getId_item());
        $this->assertEquals(1, $certificadoRecibido->getId_nom());
        $this->assertEquals('test', $certificadoRecibido->getNom());
        $this->assertEquals('test', $certificadoRecibido->getIdioma());
        $this->assertEquals('test', $certificadoRecibido->getDestino());
        $this->assertEquals('test', $certificadoRecibido->getCertificado());
        $this->assertEquals('test', $certificadoRecibido->getEsquema_emisor());
        $this->assertTrue($certificadoRecibido->isFirmado());
        $this->assertEquals('test', $certificadoRecibido->getDocumento());
    }
}
