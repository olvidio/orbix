<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\procesos\application\TipoActivProcesoLstPosibles;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\procesos\domain\entity\ProcesoTipo;

final class TipoActivProcesoLstPosiblesTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], ['sfsv' => 1]);
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_mapea_procesos_tipo(): void
    {
        $p1 = $this->createMock(ProcesoTipo::class);
        $p1->method('getId_tipo_proceso')->willReturn(10);
        $p1->method('getNom_proceso')->willReturn('P1');

        $repo = $this->createMock(ProcesoTipoRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getProcesoTipos')
            ->with(['sfsv' => 1, '_ordre' => 'nom_proceso'])
            ->willReturn([$p1]);

        $out = (new TipoActivProcesoLstPosibles($repo))->execute([
            'id_tipo_activ' => 111000,
            'propio' => 't',
        ]);

        $this->assertSame(111000, $out['id_tipo_activ']);
        $this->assertSame('t', $out['propio']);
        $this->assertSame([
            ['id_tipo_proceso' => 10, 'nom_proceso' => 'P1'],
        ], $out['a_procesos']);
    }
}
