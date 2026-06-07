<?php

namespace Tests\unit\certificados\application;

use PHPUnit\Framework\TestCase;
use src\certificados\application\CertificadoEmitidoUploadFirmadoFormData;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoEmitido;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaPub;

final class CertificadoEmitidoUploadFirmadoFormDataTest extends TestCase
{
    public function test_usa_nom_del_certificado_si_no_vacio(): void
    {
        $oCert = new CertificadoEmitido();
        $oCert->setId_item(3);
        $oCert->setId_nom(9);
        $oCert->setNom('Nombre en cert');

        $persona = $this->createMock(PersonaPub::class);
        $persona->method('getApellidosNombre')->willReturn('Apellido, Nombre');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->with(9)->willReturn($persona);

        $certRepo = $this->createMock(CertificadoEmitidoRepositoryInterface::class);
        $certRepo->method('findById')->with(3)->willReturn($oCert);

        $useCase = new CertificadoEmitidoUploadFirmadoFormData($certRepo, $finder);
        $data = $useCase->execute(3);
        $this->assertSame(9, $data['id_nom']);
        $this->assertSame('Nombre en cert', $data['nom']);
        $this->assertSame('Apellido, Nombre', $data['apellidos_nombre']);
    }

    public function test_si_nom_vacio_usa_apellidos_persona(): void
    {
        $oCert = new CertificadoEmitido();
        $oCert->setId_item(1);
        $oCert->setId_nom(12);
        $oCert->setNom('');

        $persona = $this->createMock(PersonaPub::class);
        $persona->method('getApellidosNombre')->willReturn('Solo apellidos');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->with(12)->willReturn($persona);

        $certRepo = $this->createMock(CertificadoEmitidoRepositoryInterface::class);
        $certRepo->method('findById')->willReturn($oCert);

        $useCase = new CertificadoEmitidoUploadFirmadoFormData($certRepo, $finder);
        $data = $useCase->execute(1);
        $this->assertSame('Solo apellidos', $data['nom']);
        $this->assertSame('Solo apellidos', $data['apellidos_nombre']);
    }

    public function test_sin_persona_apellidos_vacios(): void
    {
        $oCert = new CertificadoEmitido();
        $oCert->setId_item(1);
        $oCert->setId_nom(99);
        $oCert->setNom('');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->willReturn(null);

        $certRepo = $this->createMock(CertificadoEmitidoRepositoryInterface::class);
        $certRepo->method('findById')->willReturn($oCert);

        $useCase = new CertificadoEmitidoUploadFirmadoFormData($certRepo, $finder);
        $data = $useCase->execute(1);
        $this->assertSame('', $data['nom']);
        $this->assertSame('', $data['apellidos_nombre']);
    }
}
