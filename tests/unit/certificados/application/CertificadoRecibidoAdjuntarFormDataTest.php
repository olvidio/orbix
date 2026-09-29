<?php

namespace Tests\unit\certificados\application;

use PHPUnit\Framework\TestCase;
use src\certificados\application\CertificadoRecibidoAdjuntarFormData;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaPub;
use src\shared\security\HashB;

final class CertificadoRecibidoAdjuntarFormDataTest extends TestCase
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
        $useCase = new CertificadoRecibidoAdjuntarFormData($this->createMock(PersonaFinderService::class));
        $this->expectException(\RuntimeException::class);
        $useCase->execute(0);
    }

    public function test_persona_no_encontrada(): void
    {
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->with(5)->willReturn(null);
        $useCase = new CertificadoRecibidoAdjuntarFormData($finder);

        $this->expectException(\RuntimeException::class);
        $useCase->execute(5);
    }

    public function test_exito(): void
    {
        $persona = $this->createMock(PersonaPub::class);
        $persona->method('getApellidosNombre')->willReturn('García, Ana');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->with(7)->willReturn($persona);
        $useCase = new CertificadoRecibidoAdjuntarFormData($finder);

        $data = $useCase->execute(7);
        $this->assertSame('García, Ana', $data['nom']);
        $this->assertNotSame('', $data['f_recibido']);
        $ctx = HashB::open($data['ctx_guardar'], 'certificado_recibido_guardar');
        $this->assertSame(1, $ctx['nuevo']);
        $this->assertSame(0, $ctx['id_item']);
        $this->assertSame(7, $ctx['id_nom']);
    }
}
