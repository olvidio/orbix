<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\CtrGetFichaData;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoHorario;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\entity\EncargoTipo;
use src\encargossacd\domain\services\EncargoDominioService;
use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\PermDl;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\shared\security\HashB;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use Tests\unit\planning\support\DescribeCriteria;

final class CtrGetFichaDataTest extends TestCase
{
    private CentroDlRepositoryInterface&MockObject $centros;
    private EncargoDominioService&MockObject $dominio;
    private EncargoHorarioRepositoryInterface&MockObject $horarios;
    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoSacdHorarioRepositoryInterface&MockObject $sacdHorarios;
    private EncargoSacdRepositoryInterface&MockObject $encargosSacd;
    private EncargoTipoRepositoryInterface&MockObject $tipos;
    private PersonaSacdRepositoryInterface&MockObject $sacds;
    private CtrGetFichaData $useCase;
    private mixed $permAnterior;

    protected function setUp(): void
    {
        $this->centros = $this->createMock(CentroDlRepositoryInterface::class);
        $this->dominio = $this->createMock(EncargoDominioService::class);
        $this->horarios = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->sacdHorarios = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $this->encargosSacd = $this->createMock(EncargoSacdRepositoryInterface::class);
        $this->tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $this->sacds = $this->createMock(PersonaSacdRepositoryInterface::class);
        $this->useCase = new CtrGetFichaData(
            $this->centros, $this->dominio, $this->horarios, $this->encargos,
            $this->sacdHorarios, $this->encargosSacd, $this->tipos, $this->sacds
        );
        $this->permAnterior = $_SESSION['oPerm'] ?? null;
        unset($_SESSION['oPerm']);
        $this->sacds->method('getArraySacdyCheckBox')->willReturn(['', '', '', [10011 => 'Perez, A', 10012 => 'Gil, B']]);
    }

    protected function tearDown(): void
    {
        if ($this->permAnterior === null) {
            unset($_SESSION['oPerm']);
        } else {
            $_SESSION['oPerm'] = $this->permAnterior;
        }
    }

    // ---- helpers ------------------------------------------------------------------------------

    private function encargo(int $idEnc, int $idTipo = 2100, string $desc = 'Misa', string $observ = 'obs'): Encargo
    {
        $e = new Encargo();
        $e->setId_enc($idEnc);
        $e->setId_tipo_enc($idTipo);
        $e->setDesc_enc($desc);
        $e->setObserv($observ);

        return $e;
    }

    private function tipo(int $modHorario): EncargoTipo
    {
        $t = $this->createMock(EncargoTipo::class);
        $t->method('getMod_horario')->willReturn($modHorario);

        return $t;
    }

    private function encargoSacd(int $idEnc, int $idNom, int $modo): EncargoSacd
    {
        $o = new EncargoSacd();
        $o->setId_item($idEnc * 100 + $modo);
        $o->setId_enc($idEnc);
        $o->setId_nom($idNom);
        $o->setModoVo($modo);
        $o->setF_ini(new DateTimeLocal('2024-01-01'));

        return $o;
    }

    private function horarioCtr(string $diaRef, ?int $diaInc): EncargoHorario
    {
        $h = new EncargoHorario();
        $h->setId_item_h(1);
        $h->setId_enc(1);
        $h->setF_ini(new DateTimeLocal('2024-01-01'));
        $h->setDia_ref($diaRef);
        $h->setDia_inc($diaInc);

        return $h;
    }

    private function horarioSacd(string $diaRef, ?int $diaInc, string $masMenos = '', ?TimeLocal $ini = null): EncargoSacdHorario
    {
        $h = new EncargoSacdHorario();
        $h->setId_item(1);
        $h->setId_enc(1);
        $h->setId_nom(1);
        $h->setDia_ref($diaRef);
        $h->setDia_inc($diaInc);
        $h->setMas_menos($masMenos === '' ? null : $masMenos);
        $h->setH_ini($ini);

        return $h;
    }

    /** Un encargo con un único tipo de horario ya resuelto. */
    private function conEncargo(Encargo $e, int $modHorario): void
    {
        $this->encargos->method('matching')->willReturn([$e]);
        $this->tipos->method('findById')->willReturn($this->tipo($modHorario));
    }

    /** @param list<EncargoSacd> $vigentes @param list<EncargoSacd> $futuros */
    private function sacdsDelEncargo(array $vigentes, array $futuros = []): void
    {
        $this->encargosSacd->method('matching')->willReturnCallback(
            static fn (Criteria $c) => in_array('f_fin is_null', DescribeCriteria::filtros($c), true) ? $vigentes : $futuros
        );
    }

    /** @param list<EncargoHorario> $vigentes */
    private function horariosCtr(array $vigentes, array $futuros = []): void
    {
        $this->horarios->method('matching')->willReturnCallback(
            static fn (Criteria $c) => in_array('f_fin is_null', DescribeCriteria::filtros($c), true) ? $vigentes : $futuros
        );
    }

    /** @param array<int, list<EncargoSacdHorario>> $porSacd */
    private function horariosSacd(array $porSacd): void
    {
        $this->sacdHorarios->method('matching')->willReturnCallback(function (Criteria $c) use ($porSacd): array {
            foreach (DescribeCriteria::filtros($c) as $f) {
                if (preg_match('/^id_nom eq (\d+)$/', $f, $m)) {
                    return $porSacd[(int) $m[1]] ?? [];
                }
            }

            return [];
        });
    }

    // ---- sin encargos ---------------------------------------------------------------------------

    public function test_sin_encargos_ofrece_un_formulario_vacio_para_crear(): void
    {
        $this->encargos->method('matching')->willReturn([]);

        $out = $this->useCase->execute(1001, 0);

        $this->assertSame('nuevo', $out['mod']);
        $this->assertSame(1, $out['num_enc']);
        $this->assertSame(0, $out['encargos'][0]['id_enc']);
        $this->assertSame('', $out['encargos'][0]['desc_enc']);
        $this->assertSame(1, $out['encargos'][0]['sacd_num']);
    }

    public function test_el_centro_de_la_dl_nace_con_titular_marcado_y_el_de_ellas_sin_marcar(): void
    {
        $this->encargos->method('matching')->willReturn([]);

        $this->assertSame('checked', $this->useCase->execute(1001, 0)['encargos'][0]['cl_checked']);
        $this->assertSame('checked', $this->useCase->execute(3001, 0)['encargos'][0]['cl_checked']);
        $this->assertSame('', $this->useCase->execute(2001, 0)['encargos'][0]['cl_checked']);
    }

    public function test_busca_los_encargos_del_centro_de_tipo_1_2_o_3(): void
    {
        $this->encargos->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_ubi eq 1001', 'id_tipo_enc matches (1|2|3).0'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $this->useCase->execute(1001, 0);
    }

    public function test_tipo_de_centro(): void
    {
        $this->encargos->method('matching')->willReturn([]);
        $c = $this->createMock(CentroDl::class);
        $c->method('getTipo_ctr')->willReturn('ss');
        $this->centros->method('findById')->willReturnMap([[1001, $c], [1002, null]]);

        $this->assertSame('ss', $this->useCase->execute(1001, 0)['tipo_centro']);
        $this->assertSame('', $this->useCase->execute(1002, 0)['tipo_centro']);
    }

    public function test_contrato_de_claves_y_conversion_de_las_opciones_de_sacd(): void
    {
        $this->encargos->method('matching')->willReturn([]);

        $out = $this->useCase->execute(1001, 5);

        $this->assertSame(
            ['mod', 'tipo_centro', 'num_enc', 'chk_prelatura', 'chk_de_paso', 'chk_sssc', 'opciones_sacd', 'opciones_sacd_sssc', 'encargos', 'perm_des', 'ctx_guardar'],
            array_keys($out)
        );
        $this->assertSame(['10011' => 'Perez, A', '10012' => 'Gil, B'], $out['opciones_sacd']);
        $this->assertNull($out['opciones_sacd_sssc'], 'solo se carga si hace falta');
    }

    public function test_el_token_para_guardar_va_firmado_con_el_centro(): void
    {
        $this->encargos->method('matching')->willReturn([]);

        $out = $this->useCase->execute(1001, 0);

        $this->assertSame(['id_ubi' => 1001], HashB::open($out['ctx_guardar'], 'ctr_ficha_update'));
    }

    // ---- encargo existente --------------------------------------------------------------------------

    public function test_datos_basicos_del_encargo_existente(): void
    {
        $this->conEncargo($this->encargo(7, 2100, 'Misa diaria', 'una nota'), 1);
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([]);

        $out = $this->useCase->execute(1001, 0);

        $this->assertSame('editar', $out['mod']);
        $e = $out['encargos'][0];
        $this->assertSame(7, $e['id_enc']);
        $this->assertSame(2100, $e['id_tipo_enc']);
        $this->assertSame(1, $e['mod_horario']);
        $this->assertSame('Misa diaria', $e['desc_enc']);
        $this->assertSame('una nota', $e['observ']);
        $this->assertSame(1, $e['sacd_num'], 'sin colaboradores: solo el titular');
    }

    public function test_un_tipo_de_encargo_inexistente_tiene_modo_de_horario_cero(): void
    {
        $this->encargos->method('matching')->willReturn([$this->encargo(7)]);
        $this->tipos->method('findById')->willReturn(null);
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([]);

        $this->assertSame(0, $this->useCase->execute(1001, 0)['encargos'][0]['mod_horario']);
    }

    public function test_la_dedicacion_del_centro_por_modulo_sale_de_los_horarios_vigentes_y_futuros(): void
    {
        $this->conEncargo($this->encargo(7), 1);
        $this->horariosCtr(
            [$this->horarioCtr('m', 2), $this->horarioCtr('t', 3)],
            [$this->horarioCtr('v', 4)]
        );
        $this->sacdsDelEncargo([]);

        $e = $this->useCase->execute(1001, 0)['encargos'][0];

        $this->assertSame('2', $e['dedic_ctr_m']);
        $this->assertSame('3', $e['dedic_ctr_t']);
        $this->assertSame('4', $e['dedic_ctr_v']);
    }

    public function test_con_modo_de_horario_3_se_ignora_la_dedicacion_por_modulos_del_centro(): void
    {
        $this->conEncargo($this->encargo(7), 3);
        $this->horariosCtr([$this->horarioCtr('m', 2)]);
        $this->sacdsDelEncargo([]);

        $this->assertSame('', $this->useCase->execute(1001, 0)['encargos'][0]['dedic_ctr_m']);
    }

    public function test_un_modulo_desconocido_o_sin_dedicacion_no_rellena_nada(): void
    {
        $this->conEncargo($this->encargo(7), 1);
        $this->horariosCtr([$this->horarioCtr('x', 2), $this->horarioCtr('m', null)]);
        $this->sacdsDelEncargo([]);

        $e = $this->useCase->execute(1001, 0)['encargos'][0];

        $this->assertSame('', $e['dedic_ctr_m'], 'dia_inc nulo se muestra vacío');
        $this->assertSame('', $e['dedic_ctr_t']);
    }

    // ---- sacd del encargo (según el modo) -------------------------------------------------------------

    public function test_modo_3_es_titular_y_trae_su_dedicacion_por_modulos(): void
    {
        $this->conEncargo($this->encargo(7), 1);
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([$this->encargoSacd(7, 10011, 3)]);
        $this->horariosSacd([10011 => [$this->horarioSacd('m', 5), $this->horarioSacd('t', 6), $this->horarioSacd('v', 7)]]);

        $e = $this->useCase->execute(1001, 0)['encargos'][0];

        $this->assertSame(10011, $e['actual_id_sacd_titular']);
        $this->assertSame([0 => '5'], $e['dedic_m']);
        $this->assertSame([0 => '6'], $e['dedic_t']);
        $this->assertSame([0 => '7'], $e['dedic_v']);
        $this->assertSame('', $e['cl_checked'], 'el modo 3 no marca la casilla');
    }

    public function test_modo_2_es_titular_y_ademas_marca_la_casilla(): void
    {
        $this->conEncargo($this->encargo(7), 1);
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([$this->encargoSacd(7, 10012, 2)]);
        $this->horariosSacd([]);

        $e = $this->useCase->execute(1001, 0)['encargos'][0];

        $this->assertSame(10012, $e['actual_id_sacd_titular']);
        $this->assertSame('checked', $e['cl_checked']);
    }

    public function test_modo_4_es_suplente(): void
    {
        $this->conEncargo($this->encargo(7), 1);
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([$this->encargoSacd(7, 10012, 4)]);
        $this->horariosSacd([]);

        $e = $this->useCase->execute(1001, 0)['encargos'][0];

        $this->assertSame(10012, $e['actual_id_sacd_suplente']);
        $this->assertSame(0, $e['actual_id_sacd_titular']);
    }

    public function test_los_modos_1_y_6_no_asignan_a_nadie(): void
    {
        $this->conEncargo($this->encargo(7), 1);
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([$this->encargoSacd(7, 10011, 1), $this->encargoSacd(7, 10012, 6)]);

        $e = $this->useCase->execute(1001, 0)['encargos'][0];

        $this->assertSame(0, $e['actual_id_sacd_titular']);
        $this->assertSame(0, $e['actual_id_sacd_suplente']);
        $this->assertSame([], $e['colaboradores']);
    }

    public function test_los_sacd_futuros_tambien_cuentan(): void
    {
        $this->conEncargo($this->encargo(7), 1);
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([$this->encargoSacd(7, 10011, 3)], [$this->encargoSacd(7, 10012, 4)]);
        $this->horariosSacd([]);

        $e = $this->useCase->execute(1001, 0)['encargos'][0];

        $this->assertSame(10011, $e['actual_id_sacd_titular']);
        $this->assertSame(10012, $e['actual_id_sacd_suplente']);
    }

    public function test_colaboradores_numerados_con_su_dedicacion_en_la_posicion_de_cada_uno(): void
    {
        $this->conEncargo($this->encargo(7), 1);
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([$this->encargoSacd(7, 10011, 5), $this->encargoSacd(7, 10012, 5)]);
        $this->horariosSacd([
            10011 => [$this->horarioSacd('m', 1)],
            10012 => [$this->horarioSacd('t', 2), $this->horarioSacd('v', 3)],
        ]);

        $e = $this->useCase->execute(1001, 0)['encargos'][0];

        $this->assertSame(
            [['s' => 1, 'id_nom' => 10011, 'necesita_sssc' => false], ['s' => 2, 'id_nom' => 10012, 'necesita_sssc' => false]],
            $e['colaboradores']
        );
        $this->assertSame([0 => '', 1 => '1', 2 => ''], $e['dedic_m']);
        $this->assertSame([0 => '', 1 => '', 2 => '2'], $e['dedic_t']);
        $this->assertSame([0 => '', 1 => '', 2 => '3'], $e['dedic_v']);
        $this->assertSame(3, $e['sacd_num'], 'titular + 2 colaboradores');
    }

    public function test_un_colaborador_fuera_de_la_lista_pide_las_opciones_sssc_una_sola_vez(): void
    {
        $this->conEncargo($this->encargo(7), 1);
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([$this->encargoSacd(7, 99001, 5), $this->encargoSacd(7, 99002, 5), $this->encargoSacd(7, 10011, 5)]);
        $this->horariosSacd([]);
        $sacds = $this->createMock(PersonaSacdRepositoryInterface::class);
        $sacds->expects($this->exactly(2))->method('getArraySacdyCheckBox')->willReturnCallback(
            static fn (int $sel) => $sel === 10 ? ['', '', '', [99001 => 'Sssc, X', 99002 => 'Sssc, Y']] : ['', '', '', [10011 => 'Perez, A']]
        );
        $uc = new CtrGetFichaData($this->centros, $this->dominio, $this->horarios, $this->encargos, $this->sacdHorarios, $this->encargosSacd, $this->tipos, $sacds);

        $out = $uc->execute(1001, 0);

        $this->assertSame(['99001' => 'Sssc, X', '99002' => 'Sssc, Y'], $out['opciones_sacd_sssc']);
        $this->assertSame([true, true, false], array_column($out['encargos'][0]['colaboradores'], 'necesita_sssc'));
    }

    // ---- modo de horario 3: texto libre -----------------------------------------------------------------------

    public function test_con_modo_3_el_titular_lleva_el_texto_del_horario_con_sus_horas(): void
    {
        $this->conEncargo($this->encargo(7), 3);
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([$this->encargoSacd(7, 10011, 3)]);
        $this->horariosSacd([10011 => [$this->horarioSacd('v', 3, '+', TimeLocal::fromString('09:30:00'))]]);
        $this->dominio->expects($this->once())->method('texto_horario')
            ->with('+', 'v', '3', '', '09:30:00', '', '')
            ->willReturn('viernes de 09:30:00');

        $e = $this->useCase->execute(1001, 0)['encargos'][0];

        $this->assertSame([0 => 'viernes de 09:30:00'], $e['dedic_sacd']);
    }

    public function test_varios_horarios_de_texto_se_unen_con_y(): void
    {
        $this->conEncargo($this->encargo(7), 3);
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([$this->encargoSacd(7, 10011, 3)]);
        $this->horariosSacd([10011 => [$this->horarioSacd('l', null), $this->horarioSacd('m', null)]]);
        $this->dominio->method('texto_horario')->willReturnOnConsecutiveCalls('lunes', 'martes');

        $this->assertSame([0 => 'lunes y martes'], $this->useCase->execute(1001, 0)['encargos'][0]['dedic_sacd']);
    }

    public function test_con_modo_3_el_colaborador_lleva_su_texto_y_sin_horario_sale_vacio(): void
    {
        $this->conEncargo($this->encargo(7), 3);
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([$this->encargoSacd(7, 10011, 5), $this->encargoSacd(7, 10012, 5)]);
        $this->horariosSacd([10011 => [$this->horarioSacd('v', null)]]);
        $this->dominio->method('texto_horario')->willReturn('los viernes');

        $e = $this->useCase->execute(1001, 0)['encargos'][0];

        $this->assertSame([0 => '', 1 => 'los viernes', 2 => ''], $e['dedic_sacd']);
    }

    public function test_con_modo_3_y_texto_vacio_se_ofrece_crear_el_horario(): void
    {
        $this->conEncargo($this->encargo(7), 3);
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([$this->encargoSacd(7, 10011, 3)]);
        $this->horariosSacd([10011 => [$this->horarioSacd('v', null)]]);
        $this->dominio->method('texto_horario')->willReturn('');

        $this->assertNotSame('', $this->useCase->execute(1001, 0)['encargos'][0]['dedic_sacd'][0]);
    }

    // ---- varios encargos y permisos ---------------------------------------------------------------------------------

    public function test_varios_encargos_conservan_el_orden_y_son_independientes(): void
    {
        $this->encargos->method('matching')->willReturn([$this->encargo(7, 2100, 'A'), $this->encargo(8, 2200, 'B')]);
        $this->tipos->method('findById')->willReturnCallback(fn (int $id) => $this->tipo($id === 2100 ? 1 : 3));
        $this->horariosCtr([]);
        $this->sacdsDelEncargo([]);

        $out = $this->useCase->execute(1001, 0);

        $this->assertSame(2, $out['num_enc']);
        $this->assertSame([7, 8], array_column($out['encargos'], 'id_enc'));
        $this->assertSame([1, 3], array_column($out['encargos'], 'mod_horario'));
    }

    /**
     * @dataProvider oficinasProvider
     */
    public function test_perm_des_segun_las_oficinas_del_usuario(?string $oficina, bool $esperado): void
    {
        $this->encargos->method('matching')->willReturn([]);
        if ($oficina !== null) {
            $perm = new PermDl();
            $perm->setAccion(MenuDlPermissionBits::map()[$oficina]);
            $_SESSION['oPerm'] = $perm;
        }

        $this->assertSame($esperado, $this->useCase->execute(1001, 0)['perm_des']);
    }

    /**
     * @return iterable<string, array{?string, bool}>
     */
    public static function oficinasProvider(): iterable
    {
        yield 'des' => [MenuDlPermissionBits::DES, true];
        yield 'vcsd' => [MenuDlPermissionBits::VCSD, true];
        yield 'agd' => [MenuDlPermissionBits::AGD, false];
        yield 'sin permisos en sesión' => [null, false];
    }
}
