<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\cambios\application\CambioObjetoDatosCampos;
use src\cambios\application\CambioUsuarioPropiedadPrefGuardarTodas;
use src\cambios\application\CambioUsuarioPropiedadPrefPreview;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\domain\entity\CambioUsuarioPropiedadPref;
use src\shared\domain\DatosCampo;
use Tests\myTest;

/** Preferencias de avisos de cambios: campos configurables, vista previa de la condición y guardado de propiedades. */
final class PreferenciasDeAvisosTest extends myTest
{
    // ---- CambioObjetoDatosCampos ----------------------------------------------------------------------

    public function test_un_objeto_vacio_o_desconocido_no_tiene_campos(): void
    {
        $this->assertSame([], CambioObjetoDatosCampos::forObjeto(''));
        $this->assertSame([], CambioObjetoDatosCampos::forObjeto('ObjetoQueNoExiste'));
    }

    public function test_un_objeto_conocido_devuelve_solo_datos_de_campo(): void
    {
        $campos = CambioObjetoDatosCampos::forObjeto('Actividad');

        $this->assertNotSame([], $campos);
        $this->assertContainsOnlyInstancesOf(DatosCampo::class, $campos);
    }

    // ---- CambioUsuarioPropiedadPrefPreview ----------------------------------------------------------------------

    public function test_la_vista_previa_serializa_la_condicion_con_t_y_f(): void
    {
        $r = (new CambioUsuarioPropiedadPrefPreview())->execute([
            'id_item' => '12', 'objeto' => 'Actividad', 'propiedad' => 'nom_activ', 'operador' => '=', 'valor' => 'ca',
            'valor_new' => 'on',
        ]);

        $this->assertSame('', $r['error']);
        $this->assertSame([12, 'Actividad', 'nom_activ'], [$r['id_item'], $r['objeto'], $r['propiedad']]);
        $this->assertSame(['iid_item' => 12, 'spropiedad' => 'nom_activ', 'soperador' => '=', 'svalor' => 'ca', 'bvalor_old' => 'f', 'bvalor_new' => 't'], json_decode($r['cambio_prop'], true));
        $this->assertStringContainsString('=', $r['condicion']);
    }

    public function test_sin_marcar_valor_nuevo_ni_actual_no_hay_texto_de_condicion(): void
    {
        $r = (new CambioUsuarioPropiedadPrefPreview())->execute(['propiedad' => 'nom_activ']);

        $this->assertSame('', $r['condicion'], '`getTextCambio()` devuelve false y se convierte en cadena vacía');
        $this->assertSame(0, $r['id_item']);
    }

    public function test_los_centros_elegidos_se_unen_con_comas_en_el_valor(): void
    {
        $r = (new CambioUsuarioPropiedadPrefPreview())->execute(['propiedad' => 'id_ubi', 'id_ubi' => [1003, '1004', [9]], 'valor_old' => 'x']);

        $this->assertSame('1003,1004,', json_decode($r['cambio_prop'], true)['svalor']);
    }

    // ---- CambioUsuarioPropiedadPrefGuardarTodas ----------------------------------------------------------------------

    private CambioUsuarioPropiedadPrefRepositoryInterface&MockObject $repo;

    private function guardar(): CambioUsuarioPropiedadPrefGuardarTodas
    {
        $this->repo = $this->createMock(CambioUsuarioPropiedadPrefRepositoryInterface::class);

        return new CambioUsuarioPropiedadPrefGuardarTodas($this->repo);
    }

    /**
     * @dataProvider entradasSinClaveProvider
     * @param array<string, mixed> $input
     */
    public function test_sin_objeto_o_sin_id_de_preferencia_no_se_hace_nada(array $input): void
    {
        $uc = $this->guardar();
        $this->repo->expects($this->never())->method('Guardar');
        $this->repo->expects($this->never())->method('Eliminar');

        $this->assertNotSame('', $uc->execute($input)['error']);
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function entradasSinClaveProvider(): iterable
    {
        yield 'vacío' => [[]];
        yield 'sin objeto' => [['id_item_usuario_objeto_prop' => '5']];
        yield 'sin id' => [['objeto_prop' => 'Actividad']];
        yield 'id cero' => [['id_item_usuario_objeto_prop' => '0', 'objeto_prop' => 'Actividad']];
    }

    public function test_una_condicion_con_payload_crea_la_propiedad_con_operador_y_valor(): void
    {
        $uc = $this->guardar();
        $this->repo->method('getNewId')->willReturn(900);
        $guardada = null;
        $this->repo->expects($this->once())->method('Guardar')->willReturnCallback(function (CambioUsuarioPropiedadPref $p) use (&$guardada): bool {
            $guardada = $p;

            return true;
        });
        $this->repo->method('matching')->willReturn([]);

        $r = $uc->execute([
            'id_item_usuario_objeto_prop' => '5', 'objeto_prop' => 'Actividad',
            'Actividad' => ['Actividad_nom_activ_cond'],
            'Actividad_nom_activ_cond' => json_encode(['iid_item' => 0, 'spropiedad' => 'nom_activ', 'soperador' => '=', 'svalor' => 'ca', 'bvalor_old' => 't', 'bvalor_new' => 'f']),
        ]);

        $this->assertSame(['error' => ''], $r);
        $this->assertSame([900, 5, 'nom_activ', '=', 'ca', true], [
            $guardada->getId_item(), $guardada->getId_item_usuario_objeto(), $guardada->getPropiedad(), $guardada->getOperador(),
            $guardada->getValor(), $guardada->isValor_old(),
        ]);
        $this->assertEmpty($guardada->isValor_new());
    }

    public function test_sin_payload_se_reutiliza_la_propiedad_existente_o_se_crea_una_nueva(): void
    {
        $uc = $this->guardar();
        $this->repo->method('getNewId')->willReturn(901);
        $this->repo->method('findById')->willReturnMap([[7, null]]);
        $ids = [];
        $this->repo->method('Guardar')->willReturnCallback(function (CambioUsuarioPropiedadPref $p) use (&$ids): bool {
            $ids[] = $p->getId_item();

            return true;
        });
        $this->repo->method('matching')->willReturn([]);

        $uc->execute([
            'id_item_usuario_objeto_prop' => '5', 'objeto_prop' => 'Actividad',
            'Actividad' => ['Actividad_a_cond', 'Actividad_b_cond'],
            'Actividad_a_item' => '7',
        ]);

        $this->assertSame([7, 901], $ids, 'una con id conocido pero no encontrada conserva ese id; la otra toma uno nuevo');
    }

    public function test_un_fallo_de_guardado_corta_con_error(): void
    {
        $uc = $this->guardar();
        $this->repo->method('getNewId')->willReturn(1);
        $this->repo->method('Guardar')->willReturn(false);

        $r = $uc->execute(['id_item_usuario_objeto_prop' => '5', 'objeto_prop' => 'Actividad', 'Actividad' => ['Actividad_a_cond']]);

        $this->assertNotSame('', $r['error']);
    }

    private function propiedad(int $id, string $nombre): CambioUsuarioPropiedadPref
    {
        $p = new CambioUsuarioPropiedadPref();
        $p->setId_item($id);
        $p->setPropiedad($nombre);

        return $p;
    }

    public function test_las_propiedades_guardadas_que_ya_no_estan_marcadas_se_eliminan(): void
    {
        $uc = $this->guardar();
        $this->repo->method('getNewId')->willReturn(1);
        $this->repo->method('Guardar')->willReturn(true);
        $a = $this->propiedad(10, 'a');
        $b = $this->propiedad(11, 'b');
        $c = $this->propiedad(12, 'c');
        $this->repo->method('matching')->willReturn([$a, $b, $c]);
        $this->repo->method('findById')->willReturnMap([[10, $a], [11, $b], [12, $c]]);
        $eliminadas = [];
        $this->repo->method('Eliminar')->willReturnCallback(function (CambioUsuarioPropiedadPref $p) use (&$eliminadas): bool {
            $eliminadas[] = $p->getId_item();

            return true;
        });

        $r = $uc->execute(['id_item_usuario_objeto_prop' => '5', 'objeto_prop' => 'Actividad', 'Actividad' => ['Actividad_a_cond']]);

        $this->assertSame(['error' => ''], $r);
        $this->assertSame([11, 12], $eliminadas);
    }

    public function test_un_fallo_al_eliminar_se_devuelve(): void
    {
        $uc = $this->guardar();
        $b = $this->propiedad(11, 'b');
        $this->repo->method('matching')->willReturn([$b]);
        $this->repo->method('findById')->willReturn($b);
        $this->repo->method('Eliminar')->willReturn(false);

        $this->assertNotSame('', $uc->execute(['id_item_usuario_objeto_prop' => '5', 'objeto_prop' => 'Actividad'])['error']);
    }
}
