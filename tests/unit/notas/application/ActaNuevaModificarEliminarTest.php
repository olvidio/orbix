<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\notas\application\ActaEliminar;
use src\notas\application\ActaModificar;
use src\notas\application\ActaNueva;
use src\notas\application\support\ActaDlGuard;
use src\notas\application\support\ActaFirmadaPolicy;
use src\notas\application\support\ActaTribunalSync;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\entity\ActaTribunal;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

/**
 * Alta, modificación y baja de actas de la dl: propiedad del acta, acta firmada, campos y tribunal.
 * Se usan siempre valores con el formato «dl NN/AA» para que Acta::inventarActa no consulte el repositorio.
 */
final class ActaNuevaModificarEliminarTest extends myTest
{
    private ActaDlRepositoryInterface&MockObject $actas;
    private ActaTribunalDlRepositoryInterface&MockObject $tribunales;
    private MapaPrefijoActaEsquemaRepositoryInterface&MockObject $mapa;
    private ActaRepositoryInterface&MockObject $actasLectura;
    private ActaTribunalSync $sync;
    private ActaDlGuard $guard;
    private ActaFirmadaPolicy $firmada;
    private string $miDl = '';

    public function setUp(): void
    {
        parent::setUp();
        $this->actas = $this->createMock(ActaDlRepositoryInterface::class);
        $this->tribunales = $this->createMock(ActaTribunalDlRepositoryInterface::class);
        $this->mapa = $this->createMock(MapaPrefijoActaEsquemaRepositoryInterface::class);
        $this->actasLectura = $this->createMock(ActaRepositoryInterface::class);
        $this->sync = new ActaTribunalSync($this->tribunales);
        $this->guard = new ActaDlGuard($this->mapa);
        $this->firmada = new ActaFirmadaPolicy($this->actasLectura);
        $this->miDl = ConfigGlobal::mi_delef();
    }

    private function nueva(): ActaNueva
    {
        return new ActaNueva($this->actas, $this->sync, $this->guard);
    }

    private function modificar(): ActaModificar
    {
        return new ActaModificar($this->actas, $this->sync, $this->guard, $this->firmada);
    }

    private function eliminar(): ActaEliminar
    {
        return new ActaEliminar($this->actas, $this->sync, $this->guard);
    }

    private function local(string $iso): string
    {
        return (new DateTimeLocal($iso))->getFromLocal();
    }

    /** @return list<array{string, list<string>}> */
    private function capturarTribunal(): array
    {
        $reconstrucciones = [];
        $this->tribunales->method('matching')->willReturn([]);
        $this->tribunales->method('getNewId')->willReturn(1);
        $this->tribunales->method('Guardar')->willReturnCallback(function (ActaTribunal $t) use (&$reconstrucciones): bool {
            $reconstrucciones[] = [$t->getActa(), [$t->getExaminador()]];

            return true;
        });

        return $reconstrucciones;
    }

    // ---- ActaNueva -------------------------------------------------------------------------------------

    public function test_nueva_con_acta_de_otra_dl_se_rechaza(): void
    {
        $this->actas->expects($this->never())->method('Guardar');

        $err = $this->nueva()->execute(['id_asignatura' => '1101', 'acta' => 'zzz 12/24']);

        $this->assertNotSame('', $err);
    }

    public function test_nueva_guarda_todos_los_campos_de_un_acta_de_mi_dl(): void
    {
        $this->tribunales->method('matching')->willReturn([]);
        $guardada = null;
        $this->actas->expects($this->once())->method('Guardar')->willReturnCallback(function (Acta $a) use (&$guardada): bool {
            $guardada = $a;

            return true;
        });

        $err = $this->nueva()->execute([
            'acta' => "{$this->miDl} 12/24", 'f_acta' => $this->local('2024-06-15'), 'id_asignatura' => '1101', 'id_activ' => '3001',
            'libro' => '2', 'pagina' => '15', 'linea' => '3', 'lugar' => 'Aula 1', 'observ' => 'nota',
        ]);

        $this->assertSame('', $err);
        $this->assertSame("{$this->miDl} 12/24", $guardada->getActa());
        $this->assertSame(1101, $guardada->getId_asignatura());
        $this->assertSame(3001, $guardada->getId_activ());
        $this->assertSame('2024-06-15', $guardada->getF_acta()->format('Y-m-d'));
        $this->assertSame('2', $guardada->getLibro());
        $this->assertSame('15', $guardada->getPagina());
        $this->assertSame('3', $guardada->getLinea());
        $this->assertSame('Aula 1', $guardada->getLugar());
        $this->assertSame('nota', $guardada->getObserv());
    }

    public function test_nueva_con_numero_de_acta_construye_el_acta_con_mi_dl(): void
    {
        $this->tribunales->method('matching')->willReturn([]);
        $guardada = null;
        $this->actas->method('Guardar')->willReturnCallback(function (Acta $a) use (&$guardada): bool {
            $guardada = $a;

            return true;
        });

        $this->nueva()->execute(['id_asignatura' => '1101', 'acta' => 'zzz 1/24', 'acta_num' => '12/24']);

        $this->assertSame("{$this->miDl} 12/24", $guardada->getActa(), 'acta_num con formato NN/AA manda sobre acta');
    }

    /**
     * @dataProvider numerosInvalidosProvider
     */
    public function test_nueva_ignora_un_numero_de_acta_con_formato_incorrecto(string $num): void
    {
        $this->actas->expects($this->never())->method('Guardar');

        $err = $this->nueva()->execute(['id_asignatura' => '1101', 'acta' => 'zzz 12/24', 'acta_num' => $num]);

        $this->assertNotSame('', $err, 'sigue valiendo acta (de otra dl)');
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function numerosInvalidosProvider(): iterable
    {
        yield 'sin barra' => ['12'];
        yield 'año de 4 cifras' => ['12/2024'];
        yield 'con letras' => ['ab/24'];
        yield 'con espacio' => ['12 /24'];
    }

    public function test_nueva_toma_el_acta_de_la_seleccion_decodificada(): void
    {
        $this->tribunales->method('matching')->willReturn([]);
        $guardada = null;
        $this->actas->method('Guardar')->willReturnCallback(function (Acta $a) use (&$guardada): bool {
            $guardada = $a;

            return true;
        });

        $this->nueva()->execute(['id_asignatura' => '1101', 'acta' => 'zzz 1/24', 'sel' => [urlencode("{$this->miDl} 33/24") . '#resto']]);

        $this->assertSame("{$this->miDl} 33/24", $guardada->getActa());
    }

    public function test_nueva_si_falla_el_guardado_devuelve_el_error_pero_sigue_con_el_tribunal(): void
    {
        $this->actas->method('Guardar')->willReturn(false);
        $this->actas->method('getErrorTxt')->willReturn('clave duplicada');
        $this->tribunales->method('matching')->willReturn([]);
        $this->tribunales->method('getNewId')->willReturn(1);
        $this->tribunales->expects($this->once())->method('Guardar')->willReturn(true);

        $err = $this->nueva()->execute(['id_asignatura' => '1101', 'acta' => "{$this->miDl} 12/24", 'examinadores' => ['Perez']]);

        $this->assertStringContainsString('clave duplicada', $err);
    }

    public function test_nueva_reconstruye_el_tribunal_con_los_examinadores_validos(): void
    {
        $this->actas->method('Guardar')->willReturn(true);
        $this->tribunales->method('matching')->willReturn([]);
        $this->tribunales->method('getNewId')->willReturn(1);
        $guardados = [];
        $this->tribunales->method('Guardar')->willReturnCallback(function (ActaTribunal $t) use (&$guardados): bool {
            $guardados[] = [$t->getActa(), $t->getExaminador(), $t->getOrden()];

            return true;
        });

        $this->nueva()->execute(['id_asignatura' => '1101', 'acta' => "{$this->miDl} 12/24", 'examinadores' => ['Perez', '', 5, null, 'Gil']]);

        $this->assertSame([["{$this->miDl} 12/24", 'Perez', '1'], ["{$this->miDl} 12/24", 'Gil', '2']], $guardados, 'solo cuentan las cadenas no vacías');
    }

    public function test_nueva_con_fecha_ilegible_guarda_sin_fecha(): void
    {
        $guardada = null;
        $this->actas->method('Guardar')->willReturnCallback(function (Acta $a) use (&$guardada): bool {
            $guardada = $a;

            return true;
        });
        $this->tribunales->method('matching')->willReturn([]);

        $this->nueva()->execute(['id_asignatura' => '1101', 'acta' => "{$this->miDl} 12/24", 'f_acta' => 'no es fecha']);

        $this->assertNull($guardada->getF_acta());
    }

    // ---- ActaModificar --------------------------------------------------------------------------------------------

    private function actaExistente(string $acta): Acta
    {
        $a = new Acta();
        $a->setActa($acta);

        return $a;
    }

    public function test_modificar_un_acta_de_otra_dl_se_rechaza_salvo_que_su_prefijo_pertenezca_a_mi_esquema(): void
    {
        $this->mapa->method('prefijoPerteneceAEsquema')->willReturn(false);
        $this->actas->expects($this->never())->method('findById');

        $this->assertNotSame('', $this->modificar()->execute(['id_asignatura' => '1101', 'acta' => 'zzz 12/24']));
    }

    public function test_modificar_permite_un_acta_historica_de_un_prefijo_absorbido(): void
    {
        $this->mapa->method('prefijoPerteneceAEsquema')->willReturn(true);
        $acta = $this->actaExistente('zzz 12/24');
        $this->actas->method('findById')->with('zzz 12/24')->willReturn($acta);
        $this->actasLectura->method('findById')->willReturn(null);
        $this->actas->expects($this->once())->method('Guardar')->with($acta)->willReturn(true);
        $this->tribunales->method('matching')->willReturn([]);

        $this->assertSame('', $this->modificar()->execute(['id_asignatura' => '1101', 'acta' => 'zzz 12/24', 'libro' => '4']));
        $this->assertSame('4', $acta->getLibro());
    }

    public function test_modificar_un_acta_inexistente_avisa(): void
    {
        $this->actas->method('findById')->willReturn(null);
        $this->actas->expects($this->never())->method('Guardar');

        $this->assertNotSame('', $this->modificar()->execute(['id_asignatura' => '1101', 'acta' => "{$this->miDl} 12/24"]));
    }

    public function test_modificar_un_acta_firmada_no_se_toca(): void
    {
        $this->actas->method('findById')->willReturn($this->actaExistente("{$this->miDl} 12/24"));
        $firmada = $this->createMock(Acta::class);
        $firmada->method('tienePdfFirmado')->willReturn(true);
        $this->actasLectura->method('findById')->willReturn($firmada);
        $this->actas->expects($this->never())->method('Guardar');
        $this->tribunales->expects($this->never())->method('Guardar');

        $err = $this->modificar()->execute(['id_asignatura' => '1101', 'acta' => "{$this->miDl} 12/24", 'libro' => '9']);

        $this->assertNotSame('', $err);
    }

    public function test_modificar_actualiza_los_campos_menos_el_numero_de_acta_y_reconstruye_el_tribunal(): void
    {
        $acta = $this->actaExistente("{$this->miDl} 12/24");
        $this->actas->method('findById')->willReturn($acta);
        $this->actasLectura->method('findById')->willReturn(null);
        $this->actas->expects($this->once())->method('Guardar')->with($acta)->willReturn(true);
        $this->tribunales->method('matching')->willReturn([]);
        $this->tribunales->method('getNewId')->willReturn(1);
        $guardados = [];
        $this->tribunales->method('Guardar')->willReturnCallback(function (ActaTribunal $t) use (&$guardados): bool {
            $guardados[] = [$t->getActa(), $t->getExaminador()];

            return true;
        });

        $err = $this->modificar()->execute([
            'acta' => "{$this->miDl} 12/24", 'f_acta' => $this->local('2024-07-01'), 'id_asignatura' => '1102', 'id_activ' => '3002',
            'libro' => '5', 'pagina' => '6', 'linea' => '7', 'lugar' => 'Aula 2', 'observ' => 'cambiada', 'examinadores' => ['Soler'],
        ]);

        $this->assertSame('', $err);
        $this->assertSame("{$this->miDl} 12/24", $acta->getActa());
        $this->assertSame(1102, $acta->getId_asignatura());
        $this->assertSame(3002, $acta->getId_activ());
        $this->assertSame('2024-07-01', $acta->getF_acta()->format('Y-m-d'));
        $this->assertSame(['5', '6', '7'], [$acta->getLibro(), $acta->getPagina(), $acta->getLinea()]);
        $this->assertSame('Aula 2', $acta->getLugar());
        $this->assertSame('cambiada', $acta->getObserv());
        $this->assertSame([["{$this->miDl} 12/24", 'Soler']], $guardados);
    }

    public function test_modificar_si_falla_el_guardado_devuelve_el_error(): void
    {
        $this->actas->method('findById')->willReturn($this->actaExistente("{$this->miDl} 12/24"));
        $this->actasLectura->method('findById')->willReturn(null);
        $this->actas->method('Guardar')->willReturn(false);
        $this->actas->method('getErrorTxt')->willReturn('boom');
        $this->tribunales->method('matching')->willReturn([]);

        $this->assertStringContainsString('boom', $this->modificar()->execute(['id_asignatura' => '1101', 'acta' => "{$this->miDl} 12/24"]));
    }

    public function test_modificar_toma_el_acta_de_la_seleccion(): void
    {
        $this->actas->expects($this->once())->method('findById')->with("{$this->miDl} 33/24")->willReturn(null);

        $this->modificar()->execute(['id_asignatura' => '1101', 'acta' => 'otra', 'sel' => [urlencode("{$this->miDl} 33/24") . '#x']]);
    }

    // ---- ActaEliminar ----------------------------------------------------------------------------------------------

    public function test_eliminar_un_acta_de_otra_dl_se_rechaza(): void
    {
        $this->mapa->method('prefijoPerteneceAEsquema')->willReturn(false);
        $this->actas->expects($this->never())->method('Eliminar');

        $this->assertNotSame('', $this->eliminar()->execute(['acta' => 'zzz 12/24']));
    }

    public function test_eliminar_un_acta_inexistente_avisa(): void
    {
        $this->actas->method('findById')->willReturn(null);
        $this->actas->expects($this->never())->method('Eliminar');

        $this->assertNotSame('', $this->eliminar()->execute(['acta' => "{$this->miDl} 12/24"]));
    }

    public function test_eliminar_borra_el_acta_y_en_cascada_su_tribunal(): void
    {
        $acta = $this->actaExistente("{$this->miDl} 12/24");
        $this->actas->method('findById')->willReturn($acta);
        $this->actas->expects($this->once())->method('Eliminar')->with($acta)->willReturn(true);
        $tribunal = new ActaTribunal();
        $this->tribunales->method('matching')->willReturn([$tribunal]);
        $this->tribunales->expects($this->once())->method('Eliminar')->with($tribunal)->willReturn(true);
        $this->tribunales->expects($this->never())->method('Guardar');

        $this->assertSame('', $this->eliminar()->execute(['acta' => "{$this->miDl} 12/24"]));
    }

    public function test_eliminar_si_falla_devuelve_el_error_pero_igualmente_limpia_el_tribunal(): void
    {
        $this->actas->method('findById')->willReturn($this->actaExistente("{$this->miDl} 12/24"));
        $this->actas->method('Eliminar')->willReturn(false);
        $this->actas->method('getErrorTxt')->willReturn('no se puede');
        $this->tribunales->expects($this->once())->method('matching')->willReturn([]);

        $this->assertStringContainsString('no se puede', $this->eliminar()->execute(['acta' => "{$this->miDl} 12/24"]));
    }

    /**
     * Comportamiento actual: sin una asignatura válida el value object lanza una excepción que el caso de uso
     * no captura (no devuelve un mensaje de error). El formulario envía siempre la asignatura.
     *
     * @dataProvider asignaturasInvalidasProvider
     */
    public function test_nueva_y_modificar_sin_asignatura_valida_lanzan_excepcion(string $idAsignatura): void
    {
        $this->actas->method('findById')->willReturn($this->actaExistente("{$this->miDl} 12/24"));
        $this->actasLectura->method('findById')->willReturn(null);
        $this->actas->expects($this->never())->method('Guardar');

        $this->expectException(\InvalidArgumentException::class);
        $this->nueva()->execute(['acta' => "{$this->miDl} 12/24", 'id_asignatura' => $idAsignatura]);
    }

    /**
     * @dataProvider asignaturasInvalidasProvider
     */
    public function test_modificar_sin_asignatura_valida_lanza_excepcion(string $idAsignatura): void
    {
        $this->actas->method('findById')->willReturn($this->actaExistente("{$this->miDl} 12/24"));
        $this->actasLectura->method('findById')->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->modificar()->execute(['acta' => "{$this->miDl} 12/24", 'id_asignatura' => $idAsignatura]);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function asignaturasInvalidasProvider(): iterable
    {
        yield 'ausente' => [''];
        yield 'cero' => ['0'];
        yield 'tres cifras' => ['110'];
        yield 'empieza por 4' => ['4101'];
    }
}
