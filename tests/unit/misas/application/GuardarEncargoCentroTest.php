<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\misas\application\GuardarEncargoCentro;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\entity\EncargoCtr;

final class GuardarEncargoCentroTest extends TestCase
{
    private const UUID = '550e8400-e29b-41d4-a716-446655440000';

    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_crea_nuevo_cuando_id_item_vacio(): void
    {
        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(function (EncargoCtr $e) {
            $this->assertSame(10, $e->getId_enc());
            $this->assertSame(20, $e->getId_ubi());
            $this->assertNotSame('', $e->getUuid_item());

            return true;
        });

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoCtrRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', GuardarEncargoCentro::execute('', 10, 20));
    }

    public function test_no_encontrado_cuando_id_item_presente(): void
    {
        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->never())->method('Guardar');

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoCtrRepositoryInterface::class => $repo,
        ]);

        $msg = GuardarEncargoCentro::execute(self::UUID, 1, 2);
        $this->assertStringContainsString(self::UUID, $msg);
    }

    public function test_falla_guardar(): void
    {
        $ctr = new EncargoCtr();
        $ctr->setUuid_item(self::UUID);
        $ctr->setId_enc(1);
        $ctr->setId_ubi(1);

        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->method('findById')->willReturn($ctr);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('save-err');

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoCtrRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('save-err', GuardarEncargoCentro::execute(self::UUID, 5, 6));
    }

    public function test_exito_actualiza(): void
    {
        $ctr = new EncargoCtr();
        $ctr->setUuid_item(self::UUID);
        $ctr->setId_enc(1);
        $ctr->setId_ubi(1);

        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->method('findById')->willReturn($ctr);
        $repo->expects($this->once())->method('Guardar')->with($ctr)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoCtrRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', GuardarEncargoCentro::execute(self::UUID, 99, 88));
        $this->assertSame(99, $ctr->getId_enc());
        $this->assertSame(88, $ctr->getId_ubi());
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
