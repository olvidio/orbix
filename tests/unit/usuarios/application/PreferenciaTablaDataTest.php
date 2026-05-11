<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\application;

use PHPUnit\Framework\TestCase;
use src\usuarios\application\PreferenciaTablaData;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\entity\Preferencia;
use src\usuarios\domain\value_objects\ValorPreferencia;

final class PreferenciaTablaDataTest extends TestCase
{
    private mixed $previousContainer;

    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            $_SESSION['session_auth'] = [];
        }
        $_SESSION['session_auth']['id_usuario'] = 100;
        $_SESSION['session_auth']['idioma'] = 'ca_ES';
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

    public function test_sin_id_tabla_solo_formato_presentacion(): void
    {
        $pref = $this->createMock(Preferencia::class);
        $pref->method('getPreferenciaVo')->willReturn(new ValorPreferencia('html'));

        $repo = $this->createMock(PreferenciaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('findById')
            ->with(100, 'tabla_presentacion')
            ->willReturn($pref);

        $GLOBALS['container'] = $this->containerFromMap([
            PreferenciaRepositoryInterface::class => $repo,
        ]);

        $out = PreferenciaTablaData::execute('');

        $this->assertSame('html', $out['formato_tabla']);
        $this->assertNull($out['slickgrid']);
    }

    public function test_slickgrid_toma_fallback_usuario_44(): void
    {
        $gridJson = json_encode(['columns' => [1, 2]], JSON_THROW_ON_ERROR);

        $pref44 = $this->createMock(Preferencia::class);
        $pref44->method('getPreferenciaVo')->willReturn(new ValorPreferencia($gridJson));

        $repo = $this->createMock(PreferenciaRepositoryInterface::class);
        $repo->method('findById')->willReturnMap([
            [100, 'tabla_presentacion', null],
            [100, 'slickGrid_migrid_ca_ES', null],
            [44, 'slickGrid_migrid_ca_ES', $pref44],
        ]);

        $GLOBALS['container'] = $this->containerFromMap([
            PreferenciaRepositoryInterface::class => $repo,
        ]);

        $out = PreferenciaTablaData::execute('migrid');

        $this->assertSame('', $out['formato_tabla']);
        $this->assertSame(['columns' => [1, 2]], $out['slickgrid']);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
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
