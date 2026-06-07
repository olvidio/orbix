<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\DelegacionQueData;
use src\ubis\application\services\DelegacionDropdown;
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
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
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
        parent::tearDown();
    }

    public function test_excluye_delegacion_propia_cuando_listaRegDele_false(): void
    {
        $dlPropia = $this->delegacionMock('R', 'dl1', 'Uno');
        $dlOtra = $this->delegacionMock('R', 'dl2', 'Dos');

        $repo = $this->createMock(DelegacionRepositoryInterface::class);
        $repo->method('getDelegaciones')->with(['active' => true])->willReturn([$dlPropia, $dlOtra]);

        $useCase = new DelegacionQueData(new DelegacionDropdown($repo));
        $out = $useCase->execute();

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
}
