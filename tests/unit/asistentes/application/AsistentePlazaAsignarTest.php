<?php

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\TestCase;
use src\asistentes\application\AsistentePlazaAsignar;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\PlazaPropietarioAsignacionInterface;
use src\asistentes\domain\entity\Asistente;
use src\actividadplazas\domain\value_objects\PlazaId;

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

    private function createSut(
        ?AsistenteApplicationService $app = null,
        ?PlazaPropietarioAsignacionInterface $plazaPropietario = null,
    ): AsistentePlazaAsignar {
        $app ??= $this->createMock(AsistenteApplicationService::class);
        $plazaPropietario ??= $this->createMock(PlazaPropietarioAsignacionInterface::class);
        return new AsistentePlazaAsignar($app, $plazaPropietario);
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
        $o->expects($this->once())->method('setPlazaComprobando')->with(3, $this->anything())->willReturn('');

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

    private function asistente(int $idNom, string $dl = 'dl', ?int $plaza = null): Asistente
    {
        $a = new Asistente();
        $a->setId_activ(9);
        $a->setId_nom($idNom);
        $a->setDl_responsable($dl);
        $a->setPlaza($plaza);

        return $a;
    }

    /** @param list<string> $valores */
    private static function lista(array $valores): string
    {
        return (string) json_encode(array_map(static fn (string $v): object => (object) ['value' => $v], $valores));
    }

    public function test_asigna_la_plaza_a_todo_el_lote_con_la_plaza_actual_de_cada_uno(): void
    {
        $a5 = $this->asistente(5, 'dl', PlazaId::PEDIDA);
        $a6 = $this->asistente(6, 'dl', PlazaId::EN_ESPERA);
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturnMap([[9, 5, $a5], [9, 6, $a6]]);
        $guardados = [];
        $app->method('guardar')->willReturnCallback(function (Asistente $a) use (&$guardados): bool {
            $guardados[] = $a->getId_nom();

            return true;
        });
        $actuales = [];
        $plaza = $this->createMock(PlazaPropietarioAsignacionInterface::class);
        $plaza->method('asegurar')->willReturnCallback(function (Asistente $a, int $actual, int $nueva) use (&$actuales): string {
            $actuales[$a->getId_nom()] = [$actual, $nueva];

            return '';
        });

        $this->assertSame('', $this->createSut($app, $plaza)->execute(['id_activ' => '9', 'plaza' => '4', 'lista_json' => self::lista(['5#a', '6#b'])]));

        $this->assertSame([5, 6], $guardados);
        $this->assertSame([5 => [PlazaId::PEDIDA, PlazaId::ASIGNADA], 6 => [PlazaId::EN_ESPERA, PlazaId::ASIGNADA]], $actuales);
        $this->assertSame(PlazaId::ASIGNADA, $a5->getPlazaVo()?->value());
    }

    public function test_los_errores_de_cada_asistente_se_acumulan_y_el_resto_se_guarda(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturnMap([
            [9, 5, null],
            [9, 6, $this->asistente(6, 'dlp')],
            [9, 7, $this->asistente(7)],
            [9, 8, $this->asistente(8)],
            [9, 11, $this->asistente(11)],
        ]);
        $guardados = [];
        $app->method('guardar')->willReturnCallback(function (Asistente $a) use (&$guardados): bool {
            $guardados[] = $a->getId_nom();

            return $a->getId_nom() !== 8;
        });
        $plaza = $this->createMock(PlazaPropietarioAsignacionInterface::class);
        $plaza->method('asegurar')->willReturnCallback(static fn (Asistente $a): string => $a->getId_nom() === 7 ? 'Ya están todas las plazas ocupadas' : '');

        $msg = $this->createSut($app, $plaza)->execute(['id_activ' => 9, 'plaza' => 4, 'lista_json' => self::lista(['5#', '6#', '7#', '8#', '11#'])]);

        $this->assertSame([8, 11], $guardados);
        $lineas = explode("\n", $msg);
        $this->assertCount(4, $lineas);
        $this->assertStringContainsString('id_nom: 5', $lineas[0]);
        $this->assertSame('Ya están todas las plazas ocupadas', $lineas[2]);
    }

    public function test_elementos_sin_id_o_que_no_son_objetos_se_saltan(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->expects($this->never())->method('findById');

        $this->assertSame('', $this->createSut($app)->execute(['id_activ' => 9, 'plaza' => 4, 'lista_json' => '[1, "x", {"value": "#"}, {"otro": 5}]']));
    }

    /**
     * Hallazgo: una plaza no numérica se convierte en 0 y se guarda como «sin plaza» (`PlazaId::fromNullableInt(0)`)
     * sin avisar; la plaza vacía, en cambio, deja `null` explícitamente.
     */
    public function test_una_plaza_no_numerica_se_guarda_como_cero_sin_avisar(): void
    {
        $a = $this->asistente(5, 'dl', PlazaId::ASIGNADA);
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($a);
        $app->method('guardar')->willReturn(true);
        $plaza = $this->createMock(PlazaPropietarioAsignacionInterface::class);
        $plaza->expects($this->once())->method('asegurar')->with($a, PlazaId::ASIGNADA, 0)->willReturn('');

        $this->assertSame('', $this->createSut($app, $plaza)->execute(['id_activ' => 9, 'plaza' => 'abc', 'lista_json' => self::lista(['5#'])]));
    }

    public function test_un_json_que_no_es_una_lista_se_rechaza(): void
    {
        $this->assertNotSame('', $this->createSut()->execute(['id_activ' => 9, 'lista_json' => '{"value":"5#"}']));
        $this->assertNotSame('', $this->createSut()->execute(['id_activ' => 9, 'lista_json' => 'no es json']));
    }
}
