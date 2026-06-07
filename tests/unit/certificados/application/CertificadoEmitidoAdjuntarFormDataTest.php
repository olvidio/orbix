<?php

namespace Tests\unit\certificados\application;

use PHPUnit\Framework\TestCase;
use src\certificados\application\CertificadoEmitidoAdjuntarFormData;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaPub;

final class CertificadoEmitidoAdjuntarFormDataTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], [
            'idioma' => 'es_ES.UTF-8',
        ]);
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_id_invalido(): void
    {
        $useCase = new CertificadoEmitidoAdjuntarFormData($this->createMock(PersonaFinderService::class));
        $this->expectException(\RuntimeException::class);
        $useCase->execute(-1);
    }

    public function test_persona_no_encontrada(): void
    {
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->willReturn(null);
        $useCase = new CertificadoEmitidoAdjuntarFormData($finder);

        $this->expectException(\RuntimeException::class);
        $useCase->execute(3);
    }

    public function test_exito(): void
    {
        $persona = $this->createMock(PersonaPub::class);
        $persona->method('getApellidosNombre')->willReturn('López, Luis');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->with(2)->willReturn($persona);
        $useCase = new CertificadoEmitidoAdjuntarFormData($finder);

        $data = $useCase->execute(2);
        $this->assertSame('López, Luis', $data['nom']);
        $this->assertNotSame('', $data['f_enviado']);
    }
}
