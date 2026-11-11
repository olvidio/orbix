<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\CtrFichaUpdate;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\application\traits\EncargoFunciones;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\EncargoTipo;
use src\encargossacd\domain\services\EncargoDominioService;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use Tests\support\DescribeCriteria;

final class CtrFichaUpdateTest extends TestCase
{
    private CentroDlRepositoryInterface&MockObject $centrosDl;
    private CentroEllasRepositoryInterface&MockObject $centrosEllas;
    private EncargoAplicacionService&MockObject $aplicacion;
    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoSacdRepositoryInterface&MockObject $encargosSacd;
    private EncargoTipoRepositoryInterface&MockObject $tipos;
    private CtrFichaUpdate $useCase;

    /** @var list<array<int, mixed>> llamadas a EncargoAplicacionService, en orden */
    private array $log = [];

    protected function setUp(): void
    {
        $this->centrosDl = $this->createMock(CentroDlRepositoryInterface::class);
        $this->centrosEllas = $this->createMock(CentroEllasRepositoryInterface::class);
        $this->aplicacion = $this->createMock(EncargoAplicacionService::class);
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->encargosSacd = $this->createMock(EncargoSacdRepositoryInterface::class);
        $this->tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $this->log = [];
        foreach (['insert_horario_ctr', 'modificar_horario_ctr', 'insert_horario_sacd', 'modificar_horario_sacd', 'finalizar_horario_sacd', 'finalizar_sacd', 'grabar_alumnos'] as $m) {
            $this->aplicacion->method($m)->willReturnCallback(function (...$args) use ($m): void {
                $this->log[] = [$m, ...array_map(static fn ($a) => $a instanceof DateTimeLocal ? 'fecha' : $a, $args)];
            });
        }
        $this->aplicacion->method('insert_sacd')->willReturnCallback(function (int $enc, int $sacd, int $modo): EncargoSacd {
            $this->log[] = ['insert_sacd', $enc, $sacd, $modo];
            $o = new EncargoSacd();
            $o->setId_item(900 + $modo);

            return $o;
        });
        $this->useCase = new CtrFichaUpdate(
            $this->centrosDl, $this->centrosEllas,
            new EncargoFunciones($this->createMock(EncargoDominioService::class), $this->aplicacion),
            $this->encargos, $this->encargosSacd, $this->tipos
        );
    }

    /** @return list<string> nombres de las operaciones registradas */
    private function metodos(): array
    {
        return array_column($this->log, 0);
    }

    /** @return list<array<int, mixed>> */
    private function llamadas(string $metodo): array
    {
        return array_values(array_filter($this->log, static fn (array $l) => $l[0] === $metodo));
    }

    // ---- entrada --------------------------------------------------------------------------------

    /** @return array<string, mixed> */
    private function post(string $mod, array $extra = []): array
    {
        return array_merge([
            'e' => '0', 'mod_0' => $mod, 'id_enc_0' => '77', 'id_ubi_0' => '1001', 'tipo_centro_0' => 'n',
            'sacd_num' => '1', 'n_sacd' => '2', 'id_sacd_titular' => '10011', 'id_sacd_suplente' => '0',
            'observ' => 'nota', 'dedic_ctr_m' => '', 'dedic_ctr_t' => '', 'dedic_ctr_v' => '',
        ], $extra);
    }

    private function centroDl(string $nombre, string $tipoCtr): void
    {
        $c = $this->createMock(CentroDl::class);
        $c->method('getNombre_ubi')->willReturn($nombre);
        $c->method('getTipo_ctr')->willReturn($tipoCtr);
        $this->centrosDl->method('findById')->willReturn($c);
    }

    private function capturarEncargoNuevo(?Encargo &$guardado): void
    {
        $this->encargos->method('getNewId')->willReturn(500);
        $this->encargos->method('Guardar')->willReturnCallback(function (Encargo $e) use (&$guardado): bool {
            $guardado = $e;

            return true;
        });
    }

    private function tipoEncargo(string $texto): void
    {
        $t = $this->createMock(EncargoTipo::class);
        $t->method('getTipo_enc')->willReturn($texto);
        $this->tipos->method('findById')->willReturn($t);
    }

    // ---- selección de operación ---------------------------------------------------------------------

    public function test_un_modo_desconocido_no_hace_nada(): void
    {
        $this->encargos->expects($this->never())->method('Guardar');

        $this->assertSame(['error' => ''], $this->useCase->execute($this->post('borrar')));
        $this->assertSame([], $this->log);
    }

    public function test_el_numero_de_fila_e_elige_los_campos_de_esa_fila(): void
    {
        $this->capturarEncargoNuevo($g);
        $this->centroDl('Norte', 'n');
        $this->tipoEncargo('Misa');

        $post = $this->post('nuevo', ['e' => '3', 'mod_3' => 'nuevo', 'id_ubi_3' => '1002', 'tipo_centro_3' => 'n']);
        unset($post['mod_0']);
        $this->useCase->execute($post);

        $this->assertSame(1002, $g->getId_ubi(), 'usa id_ubi_3, no id_ubi_0');
    }

    // ---- nuevo ------------------------------------------------------------------------------------------------

    public function test_nuevo_exige_titular_salvo_en_las_oficinas(): void
    {
        $this->encargos->expects($this->never())->method('Guardar');

        $out = $this->useCase->execute($this->post('nuevo', ['id_sacd_titular' => '0']));

        $this->assertStringContainsString('<br>', $out['error']);
        $this->assertSame([], $this->log);
    }

    public function test_nuevo_en_una_oficina_no_exige_titular(): void
    {
        $this->capturarEncargoNuevo($g);
        $this->centroDl('Oficina', 'of');
        $this->tipoEncargo('Misa');

        $out = $this->useCase->execute($this->post('nuevo', ['tipo_centro_0' => 'of', 'id_sacd_titular' => '0']));

        $this->assertSame(['error' => ''], $out);
        $this->assertNotNull($g);
    }

    /**
     * @dataProvider tiposDeCentroDlProvider
     */
    public function test_el_tipo_de_encargo_de_un_centro_sv_depende_del_tipo_de_centro(string $tipoCtr, int $esperado): void
    {
        $this->capturarEncargoNuevo($g);
        $this->centroDl('Norte', $tipoCtr);
        $this->tipoEncargo('Misa');

        $this->useCase->execute($this->post('nuevo'));

        $this->assertSame($esperado, $g->getId_tipo_enc());
        $this->assertSame(1, $g->getGrupo_encargo());
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function tiposDeCentroDlProvider(): iterable
    {
        yield 'cgioc' => ['cgioc', 2100];
        yield 'oc' => ['oc', 2100];
        yield 'iglesia' => ['igloc', 3000];
        yield 'sssc' => ['ss', 1300];
        yield 'numerario' => ['n', 1100];
        yield 'otro' => ['xx', 1100];
    }

    /**
     * @dataProvider tiposDeCentroEllasProvider
     */
    public function test_el_tipo_de_encargo_de_un_centro_sf(string $tipoCtr, int $esperado): void
    {
        $this->capturarEncargoNuevo($g);
        $c = $this->createMock(CentroEllas::class);
        $c->method('getNombre_ubi')->willReturn('Sur');
        $c->method('getTipo_ctr')->willReturn($tipoCtr);
        $this->centrosEllas->method('findById')->with(2001)->willReturn($c);
        $this->tipoEncargo('Misa');

        $this->useCase->execute($this->post('nuevo', ['id_ubi_0' => '2001']));

        $this->assertSame($esperado, $g->getId_tipo_enc());
        $this->assertSame(2, $g->getGrupo_encargo());
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function tiposDeCentroEllasProvider(): iterable
    {
        yield 'cgioc' => ['cgioc', 2200];
        yield 'oc' => ['oc', 2200];
        yield 'otro' => ['n', 1200];
    }

    public function test_la_descripcion_es_el_tipo_de_encargo_y_el_nombre_del_centro(): void
    {
        $this->capturarEncargoNuevo($g);
        $this->centroDl('Centro Norte', 'n');
        $this->tipoEncargo('Misa y confesiones');

        $this->useCase->execute($this->post('nuevo', ['observ' => 'una nota']));

        $this->assertSame('Misa y confesiones (Centro Norte)', $g->getDesc_enc());
        $this->assertSame('una nota', $g->getObserv());
        $this->assertSame(500, $g->getId_enc());
        $this->assertSame(1001, $g->getId_ubi());
    }

    public function test_un_centro_inexistente_deja_el_nombre_vacio(): void
    {
        $this->capturarEncargoNuevo($g);
        $this->centrosDl->method('findById')->willReturn(null);
        $this->tipos->method('findById')->willReturn(null);

        $this->useCase->execute($this->post('nuevo'));

        $this->assertSame('()', $g->getDesc_enc(), 'la entidad recorta los espacios');
        $this->assertSame(1100, $g->getId_tipo_enc());
    }

    public function test_si_falla_el_guardado_del_encargo_no_se_crea_nada_mas(): void
    {
        $this->encargos->method('getNewId')->willReturn(500);
        $this->encargos->method('Guardar')->willReturn(false);
        $this->encargos->method('getErrorTxt')->willReturn('clave duplicada');
        $this->centroDl('Norte', 'n');
        $this->tipoEncargo('Misa');

        $out = $this->useCase->execute($this->post('nuevo', ['dedic_ctr_m' => '3']));

        $this->assertStringContainsString('clave duplicada', $out['error']);
        $this->assertSame([], $this->log);
    }

    public function test_nuevo_crea_el_horario_del_centro_solo_para_los_modulos_informados(): void
    {
        $this->capturarEncargoNuevo($g);
        $this->centroDl('Norte', 'n');
        $this->tipoEncargo('Misa');

        $this->useCase->execute($this->post('nuevo', ['dedic_ctr_m' => '3', 'dedic_ctr_v' => '5', 'n_sacd' => '4']));

        $this->assertSame(
            [['insert_horario_ctr', 500, 'm', '3', 4], ['insert_horario_ctr', 500, 'v', '5', 4]],
            $this->llamadas('insert_horario_ctr')
        );
    }

    public function test_n_sacd_cero_se_trata_como_uno(): void
    {
        $this->capturarEncargoNuevo($g);
        $this->centroDl('Norte', 'n');
        $this->tipoEncargo('Misa');

        $this->useCase->execute($this->post('nuevo', ['dedic_ctr_t' => '2', 'n_sacd' => '0']));

        $this->assertSame(1, $this->llamadas('insert_horario_ctr')[0][4]);
    }

    /**
     * @dataProvider modosDelTitularProvider
     */
    public function test_el_titular_es_modo_2_si_esta_marcada_la_casilla_y_3_si_no(bool $cl, int $modo): void
    {
        $this->capturarEncargoNuevo($g);
        $this->centroDl('Norte', 'n');
        $this->tipoEncargo('Misa');

        $this->useCase->execute($this->post('nuevo', $cl ? ['cl' => '1'] : []));

        $this->assertSame([['insert_sacd', 500, 10011, $modo]], $this->llamadas('insert_sacd'));
    }

    /**
     * @return iterable<string, array{bool, int}>
     */
    public static function modosDelTitularProvider(): iterable
    {
        yield 'con casilla' => [true, 2];
        yield 'sin casilla' => [false, 3];
    }

    public function test_los_colaboradores_son_modo_5_con_su_horario_y_el_titular_con_el_suyo(): void
    {
        $this->capturarEncargoNuevo($g);
        $this->centroDl('Norte', 'n');
        $this->tipoEncargo('Misa');

        $this->useCase->execute($this->post('nuevo', [
            'sacd_num' => '3',
            'id_sacd' => [0 => '999', 1 => '10012', 2 => '10013'],
            'dedic_m' => [0 => '1', 1 => '', 2 => '7'],
            'dedic_t' => [0 => '', 1 => '2', 2 => ''],
            'dedic_v' => [0 => '', 1 => '', 2 => '9'],
        ]));

        $this->assertSame(
            [['insert_sacd', 500, 10011, 3], ['insert_sacd', 500, 10012, 5], ['insert_sacd', 500, 10013, 5]],
            $this->llamadas('insert_sacd'),
            'la posición 0 es siempre el titular, aunque id_sacd[0] traiga otro valor'
        );
        $this->assertSame(
            [
                ['insert_horario_sacd', 903, 500, 10011, 'm', '1'],
                ['insert_horario_sacd', 905, 500, 10012, 't', '2'],
                ['insert_horario_sacd', 905, 500, 10013, 'm', '7'],
                ['insert_horario_sacd', 905, 500, 10013, 'v', '9'],
            ],
            $this->llamadas('insert_horario_sacd')
        );
    }

    public function test_el_suplente_es_modo_4_y_solo_si_se_indica(): void
    {
        $this->capturarEncargoNuevo($g);
        $this->centroDl('Norte', 'n');
        $this->tipoEncargo('Misa');

        $this->useCase->execute($this->post('nuevo', ['id_sacd_suplente' => '10020']));
        $this->assertContains(['insert_sacd', 500, 10020, 4], $this->log);

        $this->log = [];
        $this->useCase->execute($this->post('nuevo', ['id_sacd_suplente' => '0']));
        $this->assertNotContains(4, array_column($this->llamadas('insert_sacd'), 3));
    }

    public function test_los_centros_cgi_graban_el_numero_de_alumnos(): void
    {
        $this->capturarEncargoNuevo($g);
        $this->centroDl('Norte', 'cgioc');
        $this->tipoEncargo('Misa');

        $this->useCase->execute($this->post('nuevo', ['tipo_centro_0' => 'cgioc', 'num_alum' => '12']));
        $this->assertSame([['grabar_alumnos', 1001, 12]], $this->llamadas('grabar_alumnos'));

        $this->log = [];
        $this->useCase->execute($this->post('nuevo', ['tipo_centro_0' => 'n', 'num_alum' => '12']));
        $this->assertSame([], $this->llamadas('grabar_alumnos'));
    }

    public function test_un_centro_de_id_que_no_empieza_por_1_ni_2_crea_un_encargo_de_tipo_0_personal(): void
    {
        // Comportamiento actual (sin validar): sf_sv = 0 y id_tipo_enc = 0 → encargo personal sin tipo.
        $this->capturarEncargoNuevo($g);
        $this->tipoEncargo('');

        $this->useCase->execute($this->post('nuevo', ['id_ubi_0' => '3001']));

        $this->assertSame(0, $g->getId_tipo_enc());
        $this->assertSame(0, $g->getGrupo_encargo());
    }

    // ---- editar ---------------------------------------------------------------------------------------------------

    private function sacdActivo(int $idNom, int $modo, int $idItem = 1): EncargoSacd
    {
        $o = new EncargoSacd();
        $o->setId_item($idItem);
        $o->setId_enc(77);
        $o->setId_nom($idNom);
        $o->setModoVo($modo);
        $o->setF_ini(new DateTimeLocal('2024-01-01'));

        return $o;
    }

    /**
     * El repositorio responde según el modo filtrado en el criterio.
     *
     * @param array<string, list<EncargoSacd>> $porModo clave: texto del filtro de modo (p. ej. 'modo eq 5', 'modo matches (2|3)')
     */
    private function sacdsSegunModo(array $porModo): void
    {
        $this->encargosSacd->method('matching')->willReturnCallback(function (Criteria $c) use ($porModo): array {
            foreach (DescribeCriteria::filtros($c) as $f) {
                if (isset($porModo[$f])) {
                    return $porModo[$f];
                }
            }

            return [];
        });
    }

    public function test_editar_sin_titular_sobre_un_encargo_existente_lo_elimina(): void
    {
        $e = new Encargo();
        $this->encargos->method('findById')->with(77)->willReturn($e);
        $this->encargos->expects($this->once())->method('Eliminar')->with($e);

        $out = $this->useCase->execute($this->post('editar', ['id_sacd_titular' => '0']));

        $this->assertSame(['error' => ''], $out);
        $this->assertSame([], $this->log);
    }

    public function test_editar_sin_titular_y_sin_encargo_pide_titular(): void
    {
        $this->encargos->expects($this->never())->method('Eliminar');

        $out = $this->useCase->execute($this->post('editar', ['id_sacd_titular' => '0', 'id_enc_0' => '0']));

        $this->assertNotSame('', $out['error']);
    }

    public function test_editar_titular_y_suplente_deben_ser_distintos(): void
    {
        $this->encargos->expects($this->never())->method('Guardar');

        $out = $this->useCase->execute($this->post('editar', ['id_sacd_titular' => '10011', 'id_sacd_suplente' => '10011']));

        $this->assertNotSame('', $out['error']);
        $this->assertSame([], $this->log);
    }

    public function test_editar_actualiza_las_observaciones_y_los_horarios_del_centro(): void
    {
        $e = new Encargo();
        $this->encargos->method('findById')->willReturn($e);
        $this->encargos->expects($this->once())->method('Guardar')->with($e)->willReturn(true);
        $this->sacdsSegunModo([]);

        $this->useCase->execute($this->post('editar', ['observ' => 'nueva nota', 'dedic_ctr_m' => '3', 'dedic_ctr_t' => '', 'dedic_ctr_v' => '5', 'n_sacd' => '2']));

        $this->assertSame('nueva nota', $e->getObserv());
        $this->assertSame(
            [['modificar_horario_ctr', 77, 'm', '3', 2], ['modificar_horario_ctr', 77, 't', '', 2], ['modificar_horario_ctr', 77, 'v', '5', 2]],
            $this->llamadas('modificar_horario_ctr'),
            'se llama también con vacío: así se borra la dedicación'
        );
    }

    public function test_editar_si_falla_el_guardado_devuelve_el_error_y_no_toca_horarios(): void
    {
        $this->encargos->method('findById')->willReturn(new Encargo());
        $this->encargos->method('Guardar')->willReturn(false);
        $this->encargos->method('getErrorTxt')->willReturn('boom');

        $out = $this->useCase->execute($this->post('editar'));

        $this->assertStringContainsString('boom', $out['error']);
        $this->assertSame([], $this->log);
    }

    public function test_editar_en_una_oficina_no_toca_el_horario_del_centro_ni_el_titular(): void
    {
        $this->encargos->expects($this->never())->method('Guardar');
        $this->sacdsSegunModo([]);

        $this->useCase->execute($this->post('editar', ['tipo_centro_0' => 'of', 'id_sacd_titular' => '0', 'sacd_num' => '2', 'id_sacd' => [0 => '10012', 1 => '10013']]));

        $this->assertSame([], $this->llamadas('modificar_horario_ctr'));
        $this->assertSame([['insert_sacd', 77, 10012, 5], ['insert_sacd', 77, 10013, 5]], $this->llamadas('insert_sacd'));
    }

    public function test_editar_cierra_los_colaboradores_vigentes_y_vuelve_a_crear_los_de_la_lista(): void
    {
        $this->encargos->method('findById')->willReturn(new Encargo());
        $this->encargos->method('Guardar')->willReturn(true);
        $viejo = $this->sacdActivo(10050, 5);
        $this->sacdsSegunModo(['modo eq 5' => [$viejo], 'modo matches (2|3)' => [$this->sacdActivo(10011, 3)]]);
        $this->encargosSacd->method('Guardar')->willReturn(true);

        $this->useCase->execute($this->post('editar', ['sacd_num' => '2', 'id_sacd' => [0 => '10011', 1 => '10012']]));

        $this->assertNotNull($viejo->getF_fin(), 'el colaborador anterior queda con fecha de fin');
        $this->assertSame([['finalizar_horario_sacd', 77, 10050, 'fecha']], $this->llamadas('finalizar_horario_sacd'));
        $this->assertContains(['insert_sacd', 77, 10012, 5], $this->log);
    }

    public function test_editar_si_falla_cerrar_un_colaborador_devuelve_error(): void
    {
        $this->encargos->method('findById')->willReturn(new Encargo());
        $this->encargos->method('Guardar')->willReturn(true);
        $this->sacdsSegunModo(['modo eq 5' => [$this->sacdActivo(10050, 5)]]);
        $this->encargosSacd->method('Guardar')->willReturn(false);
        $this->encargosSacd->method('getErrorTxt')->willReturn('fallo al cerrar');

        $out = $this->useCase->execute($this->post('editar'));

        $this->assertStringContainsString('fallo al cerrar', $out['error']);
        $this->assertSame([], $this->llamadas('insert_sacd'));
    }

    public function test_editar_cambio_de_titular_crea_el_nuevo_y_cierra_el_anterior(): void
    {
        $this->encargos->method('findById')->willReturn(new Encargo());
        $this->encargos->method('Guardar')->willReturn(true);
        $this->sacdsSegunModo(['modo matches (2|3)' => [$this->sacdActivo(10099, 3)]]);

        $this->useCase->execute($this->post('editar', ['id_sacd_titular' => '10011']));

        $this->assertContains(['insert_sacd', 77, 10011, 3], $this->log);
        $this->assertContains(['finalizar_horario_sacd', 77, 10099, 'fecha'], $this->log);
        $this->assertContains(['finalizar_sacd', 77, 10099, 3, 'fecha'], $this->log);
    }

    public function test_editar_primer_titular_no_finaliza_un_sacd_anterior_inexistente(): void
    {
        $this->encargos->method('findById')->willReturn(new Encargo());
        $this->encargos->method('Guardar')->willReturn(true);
        $this->sacdsSegunModo([]);

        $this->useCase->execute($this->post('editar', ['id_sacd_titular' => '10011']));

        $this->assertContains(['insert_sacd', 77, 10011, 3], $this->log);
        $this->assertSame([], $this->llamadas('finalizar_sacd'), 'no hay titular anterior que cerrar');
    }

    public function test_editar_mismo_titular_con_cambio_de_modo_actualiza_el_registro_existente(): void
    {
        $this->encargos->method('findById')->willReturn(new Encargo());
        $this->encargos->method('Guardar')->willReturn(true);
        $actual = $this->sacdActivo(10011, 3);
        $this->sacdsSegunModo(['modo matches (2|3)' => [$actual]]);
        $this->encargosSacd->expects($this->once())->method('Guardar')->with($actual)->willReturn(true);

        $this->useCase->execute($this->post('editar', ['id_sacd_titular' => '10011', 'cl' => '1']));

        $this->assertSame(2, $actual->getModo(), 'de 3 a 2 al marcar la casilla');
        $this->assertSame([], $this->llamadas('insert_sacd'));
    }

    public function test_editar_mismo_titular_y_mismo_modo_no_cambia_nada_salvo_horarios(): void
    {
        $this->encargos->method('findById')->willReturn(new Encargo());
        $this->encargos->method('Guardar')->willReturn(true);
        $this->sacdsSegunModo(['modo matches (2|3)' => [$this->sacdActivo(10011, 3, 41)], 'modo matches (2|3|5)' => [$this->sacdActivo(10011, 3, 41)]]);
        $this->encargosSacd->expects($this->never())->method('Guardar');

        $this->useCase->execute($this->post('editar', ['id_sacd_titular' => '10011', 'dedic_m' => [0 => '4'], 'dedic_t' => [0 => ''], 'dedic_v' => [0 => '']]));

        $this->assertSame([], $this->llamadas('insert_sacd'));
        $this->assertSame(
            [['modificar_horario_sacd', 41, 77, 10011, 'm', '4'], ['modificar_horario_sacd', 41, 77, 10011, 't', ''], ['modificar_horario_sacd', 41, 77, 10011, 'v', '']],
            $this->llamadas('modificar_horario_sacd')
        );
    }

    public function test_editar_sin_suplente_cierra_los_suplentes_existentes(): void
    {
        $this->encargos->method('findById')->willReturn(new Encargo());
        $this->encargos->method('Guardar')->willReturn(true);
        $suplente = $this->sacdActivo(10020, 4);
        $this->sacdsSegunModo(['modo eq 4' => [$suplente]]);
        $this->encargosSacd->method('Guardar')->willReturn(true);

        $this->useCase->execute($this->post('editar', ['id_sacd_suplente' => '0']));

        $this->assertNotNull($suplente->getF_fin());
    }

    public function test_editar_con_suplente_nuevo_lo_crea_si_no_habia(): void
    {
        $this->encargos->method('findById')->willReturn(new Encargo());
        $this->encargos->method('Guardar')->willReturn(true);
        $this->sacdsSegunModo([]);

        $this->useCase->execute($this->post('editar', ['id_sacd_suplente' => '10020']));

        $this->assertContains(['insert_sacd', 77, 10020, 4], $this->log);
    }

    public function test_editar_con_otro_suplente_cierra_el_anterior_y_crea_el_nuevo(): void
    {
        $this->encargos->method('findById')->willReturn(new Encargo());
        $this->encargos->method('Guardar')->willReturn(true);
        $anterior = $this->sacdActivo(10030, 4);
        $this->sacdsSegunModo(['modo eq 4' => [$anterior]]);
        $this->encargosSacd->method('Guardar')->willReturn(true);

        $this->useCase->execute($this->post('editar', ['id_sacd_suplente' => '10020']));

        $this->assertNotNull($anterior->getF_fin());
        $this->assertContains(['insert_sacd', 77, 10020, 4], $this->log);
    }

    public function test_editar_con_el_mismo_suplente_no_hace_nada(): void
    {
        $this->encargos->method('findById')->willReturn(new Encargo());
        $this->encargos->method('Guardar')->willReturn(true);
        $suplente = $this->sacdActivo(10020, 4);
        $this->sacdsSegunModo(['modo eq 4' => [$suplente]]);

        $this->useCase->execute($this->post('editar', ['id_sacd_suplente' => '10020']));

        $this->assertNull($suplente->getF_fin());
        $this->assertNotContains(4, array_column($this->llamadas('insert_sacd'), 3));
    }
}
