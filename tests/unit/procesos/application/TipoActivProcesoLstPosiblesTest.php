<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\procesos\application\TipoActivProcesoLstPosibles;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\procesos\domain\entity\ProcesoTipo;

final class TipoActivProcesoLstPosiblesTest extends TestCase
{
    private mixed $previousContainer;

    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], ['sfsv' => 1]);
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

        $GLOBALS['container'] = $this->containerFromMap([
            ProcesoTipoRepositoryInterface::class => $repo,
        ]);

        $out = (new TipoActivProcesoLstPosibles())->execute([
            'id_tipo_activ' => 111000,
            'propio' => 't',
        ]);

        $this->assertSame(111000, $out['id_tipo_activ']);
        $this->assertSame('t', $out['propio']);
        $this->assertSame([
            ['id_tipo_proceso' => 10, 'nom_proceso' => 'P1'],
        ], $out['a_procesos']);
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
