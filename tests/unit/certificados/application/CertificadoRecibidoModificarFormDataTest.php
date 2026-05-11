<?php

namespace Tests\unit\certificados\application;

use PHPUnit\Framework\TestCase;
use src\certificados\application\CertificadoRecibidoModificarFormData;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoRecibido;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\usuarios\domain\contracts\LocalRepositoryInterface;

final class CertificadoRecibidoModificarFormDataTest extends TestCase
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

    public function test_mapea_certificado_y_locales(): void
    {
        $oCert = new CertificadoRecibido();
        $oCert->setId_item(10);
        $oCert->setId_nom(55);
        $oCert->setNom('N1');
        $oCert->setIdiomaVo('ca_ES.UTF-8');
        $oCert->setDestino('d1');
        $oCert->setCertificado('tipo-a');
        $oCert->setF_certificado(new DateTimeLocal('2024-03-15'));
        $oCert->setF_recibido(new NullDateTimeLocal());
        $oCert->setFirmado(true);

        $certRepo = $this->createMock(CertificadoRecibidoRepositoryInterface::class);
        $certRepo->method('findById')->with(10)->willReturn($oCert);

        $localRepo = $this->createMock(LocalRepositoryInterface::class);
        $localRepo->method('getArrayLocales')->willReturn(['ca_ES.UTF-8' => 'Català']);

        $GLOBALS['container'] = $this->containerFromMap([
            CertificadoRecibidoRepositoryInterface::class => $certRepo,
            LocalRepositoryInterface::class => $localRepo,
        ]);

        $data = CertificadoRecibidoModificarFormData::execute(10);

        $this->assertSame(55, $data['id_nom']);
        $this->assertSame('N1', $data['nom']);
        $this->assertSame('ca_ES.UTF-8', $data['idioma']);
        $this->assertSame('d1', $data['destino']);
        $this->assertSame('tipo-a', $data['certificado']);
        $this->assertNotSame('', $data['f_certificado']);
        $this->assertNotSame('', $data['f_recibido']);
        $this->assertTrue($data['firmado']);
        $this->assertSame('checked', $data['chk_firmado']);
        $this->assertSame(['ca_ES.UTF-8' => 'Català'], $data['a_locales']);
    }

    public function test_firmado_false_chk_vacio(): void
    {
        $oCert = new CertificadoRecibido();
        $oCert->setId_item(1);
        $oCert->setF_certificado(new NullDateTimeLocal());
        $oCert->setF_recibido(new NullDateTimeLocal());
        $oCert->setFirmado(false);

        $certRepo = $this->createMock(CertificadoRecibidoRepositoryInterface::class);
        $certRepo->method('findById')->willReturn($oCert);

        $localRepo = $this->createMock(LocalRepositoryInterface::class);
        $localRepo->method('getArrayLocales')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap([
            CertificadoRecibidoRepositoryInterface::class => $certRepo,
            LocalRepositoryInterface::class => $localRepo,
        ]);

        $data = CertificadoRecibidoModificarFormData::execute(1);
        $this->assertSame('', $data['chk_firmado']);
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
