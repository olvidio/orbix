<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\DelegacionQueData;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\DelegacionName;
use src\ubis\domain\value_objects\RegionCode;

/**
 * {@see DelegacionQueData} delega en {@see \src\ubis\application\services\DelegacionDropdown::listaRegDele}.
 */
final class DelegacionQueDataTest extends TestCase
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
        $_SESSION['session_auth']['esquema'] = 'x-dl1v';
        $_SESSION['session_auth']['sfsv'] = 1;
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

    public function test_excluye_delegacion_propia_cuando_listaRegDele_false(): void
    {
        $dlPropia = $this->delegacionMock('R', 'dl1', 'Uno');
        $dlOtra = $this->delegacionMock('R', 'dl2', 'Dos');

        $repo = $this->createMock(DelegacionRepositoryInterface::class);
        $repo->method('getDelegaciones')->with(['active' => true])->willReturn([$dlPropia, $dlOtra]);

        $GLOBALS['container'] = $this->containerFromMap([
            DelegacionRepositoryInterface::class => $repo,
        ]);

        $out = DelegacionQueData::execute();

        $this->assertArrayHasKey('opciones_dl_destino', $out);
        $keys = array_keys($out['opciones_dl_destino']);
        $this->assertContains('R-dl2', $keys);
        $this->assertNotContains('R-dl1', $keys);
    }

    private function delegacionMock(string $region, string $dl, string $nombre): Delegacion
    {
        $m = $this->createMock(Delegacion::class);
        $m->method('getRegionVo')->willReturn(new RegionCode($region));
        $m->method('getDlVo')->willReturn(new DelegacionCode($dl));
        $m->method('getNombreDlVo')->willReturn(new DelegacionName($nombre));

        return $m;
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
