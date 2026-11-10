<?php

declare(strict_types=1);

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadtarifas\application\RelacionTarifaFormData;
use src\actividadtarifas\application\services\TipoTarifaDropdown;
use src\actividadtarifas\application\TarifaUbiCopiar;
use src\actividadtarifas\application\TarifaUbiFormData;
use src\actividadtarifas\application\TarifaUbiUpdateInc;
use src\actividadtarifas\application\TipoTarifaFormData;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\shared\security\HashB;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\TarifaUbi;
use Tests\myTest;

/** Formularios y acciones de tarifas: tipos de tarifa, relación tarifa-tipo de actividad y tarifas de casa. */
final class TarifasFormulariosTest extends myTest
{
    private mixed $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1];
    }

    public function tearDown(): void
    {
        if ($this->sesionAnterior === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesionAnterior;
        }
        parent::tearDown();
    }

    // ---- TarifaUbiCopiar ----------------------------------------------------------------------

    public function test_copiar_tarifas_exige_casa_y_anyo_y_hoy_es_un_aviso_de_pendiente(): void
    {
        $uc = new TarifaUbiCopiar();

        $this->assertNotSame('', $uc->execute([]));
        $this->assertNotSame('', $uc->execute(['id_ubi' => '1003']));
        // Hallazgo: la función está sin implementar; con datos válidos solo devuelve un aviso (no copia nada).
        $this->assertNotSame('', $uc->execute(['id_ubi' => '1003', 'year' => '2026']));
        $this->assertNotSame($uc->execute([]), $uc->execute(['id_ubi' => '1003', 'year' => '2026']));
    }

    // ---- TarifaUbiUpdateInc ----------------------------------------------------------------------

    private function tarifaUbi(): TarifaUbi
    {
        return new TarifaUbi();
    }

    public function test_sin_cantidades_no_se_hace_nada(): void
    {
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');
        $uc = new TarifaUbiUpdateInc($repo);

        $this->assertSame('', $uc->execute([]));
        $this->assertSame('', $uc->execute(['inc_cantidad' => []]));
        $this->assertSame('', $uc->execute(['inc_cantidad' => 'x']));
    }

    public function test_cada_cantidad_se_aplica_a_su_tarifa_y_las_sin_id_o_inexistentes_se_saltan(): void
    {
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $t = $this->createMock(TarifaUbi::class);
        $t->expects($this->once())->method('setCantidad')->with(25.0);
        $repo->method('findById')->willReturnMap([[7, $t], [8, null]]);
        $repo->expects($this->once())->method('Guardar')->with($t)->willReturn(true);

        $r = (new TarifaUbiUpdateInc($repo))->execute(['inc_cantidad' => ['a#7' => '25', 'b#0' => '30', 'c#8' => '10']]);

        $this->assertSame('', $r);
    }

    /**
     * Hallazgo: la cantidad se redondea a entero (`(int) round(...)`) antes de guardarla en un campo `float`:
     * una tarifa de 12,50 se guarda como 13 y se pierden los céntimos.
     */
    public function test_la_cantidad_se_redondea_a_entero_y_se_pierden_los_centimos(): void
    {
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $t = $this->tarifaUbi();
        $repo->method('findById')->willReturn($t);
        $repo->method('Guardar')->willReturn(true);

        (new TarifaUbiUpdateInc($repo))->execute(['inc_cantidad' => ['a#7' => '12.5']]);

        $this->assertSame(13.0, $t->getCantidad());
    }

    public function test_los_errores_de_guardado_se_acumulan_por_tarifa(): void
    {
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->method('findById')->willReturn($this->tarifaUbi());
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('sql');

        $r = (new TarifaUbiUpdateInc($repo))->execute(['inc_cantidad' => ['a#7' => '1', 'a#8' => '2']]);

        $this->assertSame(2, substr_count($r, 'sql'));
    }

    // ---- TipoTarifaFormData ----------------------------------------------------------------------

    private function tipoTarifa(): TipoTarifa&MockObject
    {
        $t = $this->createMock(TipoTarifa::class);
        $t->method('getLetra')->willReturn('B');
        $t->method('getModo')->willReturn(2);
        $t->method('getObserv')->willReturn('obs');

        return $t;
    }

    /**
     * @dataProvider idsNuevosProvider
     */
    public function test_un_tipo_de_tarifa_nuevo_trae_valores_vacios_y_solo_firma_el_alta(string $id): void
    {
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');

        $r = (new TipoTarifaFormData($repo))->execute(['id_tarifa' => $id]);

        $this->assertSame(['nuevo', true, '', 0, ''], [$r['id_tarifa'], $r['es_nuevo'], $r['letra'], $r['modo'], $r['observ']]);
        $this->assertSame('', $r['token_eliminar']);
        $this->assertSame([], HashB::open($r['token_update'], 'tipo_tarifa_update'));
        $this->assertNotSame([], $r['opciones_modo']);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function idsNuevosProvider(): iterable
    {
        yield 'vacío' => [''];
        yield 'nuevo' => ['nuevo'];
    }

    public function test_un_tipo_de_tarifa_existente_se_carga_y_firma_modificar_y_eliminar_con_su_id(): void
    {
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn($this->tipoTarifa());

        $r = (new TipoTarifaFormData($repo))->execute(['id_tarifa' => '5']);

        $this->assertSame(['5', false, 'B', 2, 'obs'], [$r['id_tarifa'], $r['es_nuevo'], $r['letra'], $r['modo'], $r['observ']]);
        $this->assertSame(['id_tarifa' => 5], HashB::open($r['token_update'], 'tipo_tarifa_update'));
        $this->assertSame(['id_tarifa' => 5], HashB::open($r['token_eliminar'], 'tipo_tarifa_eliminar'));
    }

    public function test_un_tipo_de_tarifa_inexistente_deja_el_formulario_vacio(): void
    {
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $this->assertSame('', (new TipoTarifaFormData($repo))->execute(['id_tarifa' => '5'])['letra']);
    }

    // ---- RelacionTarifaFormData ----------------------------------------------------------------------

    private function relacionUc(?RelacionTarifaTipoActividad $relacion, ?array &$sfsvPedido = null): RelacionTarifaFormData
    {
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('findById')->willReturn($relacion);
        $tipos = $this->createMock(TipoTarifaRepositoryInterface::class);
        $tipos->method('getArrayTipoTarifas')->willReturnCallback(function ($sfsv) use (&$sfsvPedido): array {
            $sfsvPedido[] = $sfsv;

            return [3 => 'B'];
        });

        return new RelacionTarifaFormData($repo, new TipoTarifaDropdown($tipos));
    }

    public function test_una_relacion_nueva_ofrece_las_tarifas_de_mi_sfsv(): void
    {
        $pedido = [];

        $r = $this->relacionUc(null, $pedido)->execute(['id_item' => 'nuevo']);

        $this->assertSame([true, 'nuevo', 0, 1, [3 => 'B'], ''], [$r['es_nuevo'], $r['id_item'], $r['id_tipo_activ'], $r['isfsv'], $r['opciones_tarifa'], $r['token_eliminar']]);
        $this->assertSame([1], $pedido);
    }

    public function test_una_relacion_existente_se_carga_con_su_tipo_de_actividad_y_tarifa(): void
    {
        $rel = $this->createMock(RelacionTarifaTipoActividad::class);
        $rel->method('getId_tipo_activ')->willReturn(112001);
        $rel->method('getId_tarifa')->willReturn(3);
        $pedido = [];

        $r = $this->relacionUc($rel, $pedido)->execute(['id_item' => '9']);

        $this->assertSame([false, '9', 112001, 3], [$r['es_nuevo'], $r['id_item'], $r['id_tipo_activ'], $r['id_tarifa_sel']]);
        $this->assertNotSame('', $r['nom_tipo_activ']);
        $this->assertSame(['id_item' => 9], HashB::open($r['token_eliminar'], 'relacion_tarifa_eliminar'));
        $this->assertSame([$r['isfsv']], $pedido);
    }

    // ---- TarifaUbiFormData ----------------------------------------------------------------------

    private function tarifaUbiUc(?TarifaUbi $tarifa): TarifaUbiFormData
    {
        $repo = $this->createMock(TarifaUbiRepositoryInterface::class);
        $repo->method('findById')->willReturn($tarifa);
        $tipos = $this->createMock(TipoTarifaRepositoryInterface::class);
        $tipos->method('getArrayTipoTarifas')->willReturn([3 => 'B']);

        return new TarifaUbiFormData($repo, new TipoTarifaDropdown($tipos));
    }

    public function test_una_tarifa_de_casa_nueva_ofrece_las_tarifas_y_firma_solo_el_alta(): void
    {
        $r = $this->tarifaUbiUc(null)->execute(['id_ubi' => '1003', 'year' => '2026']);

        $this->assertSame([true, '', [3 => 'B'], ''], [$r['es_nuevo'], $r['cantidad'], $r['opciones_tarifa'], $r['token_eliminar']]);
        $this->assertSame(['id_ubi' => 1003, 'year' => 2026], HashB::open($r['token_update'], 'tarifa_ubi_update'));
        $this->assertSame(SerieId::GENERAL, $r['id_serie_sel']);
        $this->assertNotSame('', $r['letra'], 'sin letra se muestra «nueva»');
    }

    public function test_una_tarifa_existente_muestra_su_cantidad_y_no_ofrece_cambiar_de_tarifa(): void
    {
        $t = $this->createMock(TarifaUbi::class);
        $t->method('getCantidad')->willReturn(12.5);

        $r = $this->tarifaUbiUc($t)->execute(['id_item' => '7', 'id_ubi' => '1003', 'year' => '2026', 'letra' => 'B']);

        $this->assertSame([false, '12.5', [], 'B'], [$r['es_nuevo'], $r['cantidad'], $r['opciones_tarifa'], $r['letra']]);
        $this->assertSame(['id_item' => 7, 'id_ubi' => 1003, 'year' => 2026], HashB::open($r['token_update'], 'tarifa_ubi_update'));
        $this->assertSame(['id_item' => 7], HashB::open($r['token_eliminar'], 'tarifa_ubi_eliminar'));
        $this->assertSame(SerieId::GENERAL, $r['id_serie_sel'], 'siempre «general», aunque la tarifa sea de otra serie');
    }
}
