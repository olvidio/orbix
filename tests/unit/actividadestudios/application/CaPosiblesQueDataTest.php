<?php

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\CaPosiblesQueData;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\Delegacion;

final class CaPosiblesQueDataTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_sin_delegacion_mi_dl_centros_vacios(): void
    {
        $delegRepo = $this->createMock(DelegacionRepositoryInterface::class);
        $delegRepo->method('getDelegaciones')->willReturn([]);

        $personaN = $this->createMock(PersonaNRepositoryInterface::class);
        $personaN->method('getArrayIdCentros')->willReturn([]);

        $personaAgd = $this->createMock(PersonaAgdRepositoryInterface::class);
        $personaAgd->method('getArrayIdCentros')->willReturn([]);

        $centroRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroRepo->expects($this->never())->method('findById');

        $GLOBALS['container'] = $this->containerFromMap([
            DelegacionRepositoryInterface::class => $delegRepo,
            PersonaNRepositoryInterface::class => $personaN,
            PersonaAgdRepositoryInterface::class => $personaAgd,
            CentroDlRepositoryInterface::class => $centroRepo,
        ]);

        $out = CaPosiblesQueData::execute();
        $this->assertNull($out['grupo_estudios']);
        $this->assertNotSame('', $out['mi_grupo']);
        $this->assertArrayHasKey(1, $out['aCentrosNExt']);
        $this->assertArrayHasKey(2, $out['aCentrosNExt']);
        $this->assertArrayHasKey(1, $out['aCentrosAgdExt']);
    }

    public function test_grupo_y_centros_ordenados(): void
    {
        $d1 = new Delegacion();
        $d1->setDl('dl1');
        $d1->setGrupoEstudiosVo('G1');

        $d2 = new Delegacion();
        $d2->setDl('dl2');
        $d2->setGrupoEstudiosVo('G1');

        $delegRepo = $this->createMock(DelegacionRepositoryInterface::class);
        $delegRepo->method('getDelegaciones')->willReturnMap([
            [['dl' => 'dl'], [], [$d1]],
            [['grupo_estudios' => 'G1'], [], [$d1, $d2]],
        ]);

        $personaN = $this->createMock(PersonaNRepositoryInterface::class);
        $personaN->method('getArrayIdCentros')->willReturn([10]);

        $personaAgd = $this->createMock(PersonaAgdRepositoryInterface::class);
        $personaAgd->method('getArrayIdCentros')->willReturn([20]);

        $c10 = $this->createMock(CentroDl::class);
        $c10->method('getNombre_ubi')->willReturn('Beta');
        $c20 = $this->createMock(CentroDl::class);
        $c20->method('getNombre_ubi')->willReturn('Alpha');

        $centroRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroRepo->method('findById')->willReturnMap([
            [10, $c10],
            [20, $c20],
        ]);

        $GLOBALS['container'] = $this->containerFromMap([
            DelegacionRepositoryInterface::class => $delegRepo,
            PersonaNRepositoryInterface::class => $personaN,
            PersonaAgdRepositoryInterface::class => $personaAgd,
            CentroDlRepositoryInterface::class => $centroRepo,
        ]);

        $out = CaPosiblesQueData::execute();
        $this->assertSame('G1', $out['grupo_estudios']);
        $this->assertSame('dl1,dl2', $out['mi_grupo']);
        $this->assertSame('Alpha', $out['aCentrosAgdExt'][20]);
        $this->assertContains('Beta', $out['aCentrosNExt']);
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
