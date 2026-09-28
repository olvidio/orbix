<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\ActaSelectData;
use src\notas\application\support\ActaPrefijosDeEsquema;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaExRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\shared\security\HashB;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

final class ActaSelectDataTest extends TestCase
{
    /** @var mixed */
    private $oConfigBackup = null;

    protected function setUp(): void
    {
        $this->oConfigBackup = $_SESSION['oConfig'] ?? null;
        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], [
            'esquema' => 'H-dlbv',
            'sfsv' => 1,
        ]);
        unset($_SESSION['oConfig']);
    }

    protected function tearDown(): void
    {
        if ($this->oConfigBackup !== null) {
            $_SESSION['oConfig'] = $this->oConfigBackup;
        } else {
            unset($_SESSION['oConfig']);
        }
    }

    public function test_acta_emite_ctx_eliminar_atado_al_numero(): void
    {
        $acta = $this->createMock(Acta::class);
        $acta->method('getActa')->willReturn('dlb 12/26');
        $acta->method('getF_acta')->willReturn(null);
        $acta->method('getId_asignatura')->willReturn(100);
        $acta->method('getPdf')->willReturn(null);

        $actaDl = $this->createMock(ActaDlRepositoryInterface::class);
        $actaDl->method('getActas')->willReturn([$acta]);

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('getArrayAsignaturas')->willReturn([100 => 'Latín I']);

        $useCase = new ActaSelectData(
            $this->createMock(DelegacionRepositoryInterface::class),
            $this->createMock(ActaRepositoryInterface::class),
            $actaDl,
            $this->createMock(ActaExRepositoryInterface::class),
            $asigRepo,
            new ActaPrefijosDeEsquema($this->createMock(MapaPrefijoActaEsquemaRepositoryInterface::class)),
        );

        $result = $useCase->execute([
            'titulo' => 'lista',
            'acta' => 'x',
            'mes_fin_stgr' => 6,
        ]);

        $this->assertCount(1, $result['actas']);
        $this->assertSame('dlb 12/26', $result['actas'][0]['acta']);
        $this->assertSame(
            ['acta' => 'dlb 12/26'],
            HashB::open($result['actas'][0]['ctx_eliminar'], 'acta_eliminar')
        );
    }
}
