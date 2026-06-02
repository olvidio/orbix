<?php

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\TestCase;
use src\asistentes\application\AsistentePlazaAsignar;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\entity\Asistente;

final class AsistentePlazaAsignarTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    private function createSut(?AsistenteApplicationService $app = null): AsistentePlazaAsignar
    {
        $app ??= $this->createMock(AsistenteApplicationService::class);
        return new AsistentePlazaAsignar($app);
    }

    public function test_falta_id_activ(): void
    {
        $sut = $this->createSut();

        $this->assertNotSame('', $sut->execute(['id_activ' => 0, 'lista_json' => '[]']));
    }

    public function test_lista_vacia_o_invalida(): void
    {
        $sut = $this->createSut();

        $this->assertNotSame('', $sut->execute([
            'id_activ' => 1,
            'lista_json' => '[]',
        ]));
    }

    public function test_asigna_plaza_y_guarda(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);
        $o->expects($this->once())->method('setPlazaComprobando')->with(3)->willReturn('');

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(9, 5)->willReturn($o);
        $app->expects($this->once())->method('guardar')->with($o)->willReturn(true);

        $sut = $this->createSut($app);

        $json = json_encode([(object)['value' => '5#resto']]);
        $this->assertSame('', $sut->execute([
            'id_activ' => 9,
            'plaza' => 3,
            'lista_json' => $json,
        ]));
    }

    public function test_plaza_vacia_llama_setPlaza_null(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);
        $o->expects($this->once())->method('setPlaza')->with(null);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->method('guardar')->willReturn(true);

        $sut = $this->createSut($app);

        $json = json_encode([(object)['value' => '8#']]);
        $this->assertSame('', $sut->execute([
            'id_activ' => 1,
            'plaza' => '',
            'lista_json' => $json,
        ]));
    }
}
