<?php

namespace Tests\unit\certificados\application;

use PHPUnit\Framework\TestCase;
use src\certificados\application\CertificadoRecibidoAdjuntarFormData;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaPub;

final class CertificadoRecibidoAdjuntarFormDataTest extends TestCase
{
    private mixed $previousContainer;

    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], [
            'idioma' => 'es_ES.UTF-8',
        ]);
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_id_invalido(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            PersonaFinderService::class => $this->createMock(PersonaFinderService::class),
        ]);

        $this->expectException(\RuntimeException::class);
        CertificadoRecibidoAdjuntarFormData::execute(0);
    }

    public function test_persona_no_encontrada(): void
    {
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->with(5)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaFinderService::class => $finder,
        ]);

        $this->expectException(\RuntimeException::class);
        CertificadoRecibidoAdjuntarFormData::execute(5);
    }

    public function test_exito(): void
    {
        $persona = $this->createMock(PersonaPub::class);
        $persona->method('getApellidosNombre')->willReturn('García, Ana');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->with(7)->willReturn($persona);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaFinderService::class => $finder,
        ]);

        $data = CertificadoRecibidoAdjuntarFormData::execute(7);
        $this->assertSame('García, Ana', $data['nom']);
        $this->assertNotSame('', $data['f_recibido']);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
