<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\SacdFichaData;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdObservRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoHorario;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\entity\EncargoSacdObserv;
use src\encargossacd\domain\entity\EncargoTipo;
use src\encargossacd\domain\services\EncargoDominioService;
use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\PermDl;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\shared\security\HashB;
use Tests\unit\planning\support\DescribeCriteria;

final class SacdFichaDataTest extends TestCase
{
    private EncargoDominioService&MockObject $dominio;
    private EncargoHorarioRepositoryInterface&MockObject $horariosCtr;
    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoSacdHorarioRepositoryInterface&MockObject $horariosSacd;
    private EncargoSacdObservRepositoryInterface&MockObject $observ;
    private EncargoSacdRepositoryInterface&MockObject $encargosSacd;
    private EncargoTipoRepositoryInterface&MockObject $tipos;
    private SacdFichaData $useCase;
    private mixed $permAnterior;

    protected function setUp(): void
    {
        $this->dominio = $this->createMock(EncargoDominioService::class);
        $this->horariosCtr = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->horariosSacd = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $this->observ = $this->createMock(EncargoSacdObservRepositoryInterface::class);
        $this->encargosSacd = $this->createMock(EncargoSacdRepositoryInterface::class);
        $this->tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $this->useCase = new SacdFichaData(
            $this->dominio, $this->horariosCtr, $this->encargos, $this->horariosSacd,
            $this->observ, $this->encargosSacd, $this->tipos
        );
        $this->permAnterior = $_SESSION['oPerm'] ?? null;
        unset($_SESSION['oPerm']);
        $this->observ->method('matching')->willReturn([]);
        $this->horariosCtr->method('matching')->willReturn([]);
        $this->horariosSacd->method('matching')->willReturn([]);
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

    private function conPermiso(string $oficina): void
    {
        $perm = new PermDl();
        $perm->setAccion(MenuDlPermissionBits::map()[$oficina]);
        $_SESSION['oPerm'] = $perm;
    }

    private function encargoSacd(int $idEnc, int $modo = 3): EncargoSacd
    {
        $o = new EncargoSacd();
        $o->setId_item($idEnc * 10 + $modo);
        $o->setId_enc($idEnc);
        $o->setId_nom(10011);
        $o->setModoVo($modo);
        $o->setF_ini(new DateTimeLocal('2024-01-01'));

        return $o;
    }

    private function encargo(int $idEnc, int $idTipo = 2100, int $grupo = 1, string $desc = 'Misa (capilla)', ?int $idUbi = 1001): Encargo
    {
        $e = new Encargo();
        $e->setId_enc($idEnc);
        $e->setId_tipo_enc($idTipo);
        $e->setGrupo_encargo($grupo);
        $e->setDesc_enc($desc);
        $e->setId_ubi($idUbi);

        return $e;
    }

    private function tipo(int $modHorario): EncargoTipo
    {
        $t = $this->createMock(EncargoTipo::class);
        $t->method('getMod_horario')->willReturn($modHorario);

        return $t;
    }

    /** Sacd con un único encargo ya resuelto. */
    private function unEncargo(Encargo $e, int $modHorario = 1, int $modo = 3): void
    {
        $this->encargosSacd->method('matching')->willReturnCallback(
            fn (Criteria $c) => in_array('f_fin is_null', DescribeCriteria::filtros($c), true) ? [$this->encargoSacd($e->getId_enc(), $modo)] : []
        );
        $this->encargos->method('findById')->willReturn($e);
        $this->tipos->method('findById')->willReturn($this->tipo($modHorario));
    }

    private function horario(?string $diaRef, ?int $diaInc, ?TimeLocal $ini = null): EncargoHorario
    {
        $h = new EncargoHorario();
        $h->setId_item_h(1);
        $h->setId_enc(1);
        $h->setF_ini(new DateTimeLocal('2024-01-01'));
        $h->setDia_ref($diaRef);
        $h->setDia_inc($diaInc);
        $h->setH_ini($ini);

        return $h;
    }

    private function horarioSacd(?string $diaRef, ?int $diaInc): EncargoSacdHorario
    {
        $h = new EncargoSacdHorario();
        $h->setId_item(1);
        $h->setId_enc(1);
        $h->setId_nom(10011);
        $h->setDia_ref($diaRef);
        $h->setDia_inc($diaInc);

        return $h;
    }

    // ---- cabecera --------------------------------------------------------------------------------------

    public function test_sin_encargos_devuelve_la_estructura_vacia(): void
    {
        $this->encargosSacd->method('matching')->willReturn([]);

        $out = $this->useCase->execute(10011);

        $this->assertSame(['permiso', 'observ_sacd', 'encargos', 'opciones_mas', 'avisos', 'ctx_guardar'], array_keys($out));
        $this->assertSame(0, $out['permiso']);
        $this->assertSame('', $out['observ_sacd']);
        $this->assertSame([], $out['encargos']);
        $this->assertSame([], $out['avisos']);
        $this->assertNotEmpty($out['opciones_mas']);
        $this->assertContainsOnly('string', $out['opciones_mas']);
    }

    public function test_el_token_para_guardar_va_firmado_con_el_sacd(): void
    {
        $this->encargosSacd->method('matching')->willReturn([]);

        $out = $this->useCase->execute(10011);

        $this->assertSame(['id_nom' => 10011], HashB::open($out['ctx_guardar'], 'sacd_ficha_update'));
    }

    /**
     * @dataProvider oficinasProvider
     */
    public function test_permiso_segun_las_oficinas(?string $oficina, int $esperado): void
    {
        $this->encargosSacd->method('matching')->willReturn([]);
        if ($oficina !== null) {
            $this->conPermiso($oficina);
        }

        $this->assertSame($esperado, $this->useCase->execute(10011)['permiso']);
    }

    /**
     * @return iterable<string, array{?string, int}>
     */
    public static function oficinasProvider(): iterable
    {
        yield 'des' => [MenuDlPermissionBits::DES, 1];
        yield 'vcsd' => [MenuDlPermissionBits::VCSD, 1];
        yield 'agd' => [MenuDlPermissionBits::AGD, 0];
        yield 'sin permisos en sesión' => [null, 0];
    }

    public function test_la_observacion_es_la_ultima_del_sacd(): void
    {
        $observ = $this->createMock(EncargoSacdObservRepositoryInterface::class);
        $a = $this->createMock(EncargoSacdObserv::class);
        $a->method('getObserv')->willReturn('primera');
        $b = $this->createMock(EncargoSacdObserv::class);
        $b->method('getObserv')->willReturn('segunda');
        $observ->method('matching')->willReturn([$a, $b]);
        $this->encargosSacd->method('matching')->willReturn([]);
        $uc = new SacdFichaData($this->dominio, $this->horariosCtr, $this->encargos, $this->horariosSacd, $observ, $this->encargosSacd, $this->tipos);

        $this->assertSame('segunda', $uc->execute(10011)['observ_sacd']);
    }

    // ---- selección de encargos ----------------------------------------------------------------------------

    public function test_consulta_los_encargos_vigentes_y_futuros_del_sacd_ordenados_por_modo(): void
    {
        $vistos = [];
        $this->encargosSacd->method('matching')->willReturnCallback(function (Criteria $c) use (&$vistos): array {
            $vistos[] = [DescribeCriteria::filtros($c), DescribeCriteria::orden($c)];

            return [];
        });

        $this->useCase->execute(10011);

        $this->assertCount(2, $vistos);
        $this->assertSame(['id_nom eq 10011', 'f_fin is_null'], $vistos[0][0]);
        $this->assertSame('id_nom eq 10011', $vistos[1][0][0]);
        $this->assertStringStartsWith('f_fin gt ', $vistos[1][0][1]);
        $this->assertSame(['modo asc', 'f_ini desc'], $vistos[0][1]);
    }

    public function test_un_encargo_que_ya_no_existe_se_omite(): void
    {
        $this->encargosSacd->method('matching')->willReturnCallback(
            fn (Criteria $c) => in_array('f_fin is_null', DescribeCriteria::filtros($c), true) ? [$this->encargoSacd(1), $this->encargoSacd(2)] : []
        );
        $this->encargos->method('findById')->willReturnCallback(fn (int $id) => $id === 2 ? $this->encargo(2) : null);
        $this->tipos->method('findById')->willReturn($this->tipo(1));

        $out = $this->useCase->execute(10011);

        $this->assertSame([2], array_column($out['encargos'], 'id_enc'));
    }

    /**
     * Los encargos personales (7xxx) y los de tipo 4xxx no se muestran en la ficha.
     *
     * @dataProvider tiposOcultosProvider
     */
    public function test_se_omiten_los_tipos_7_y_4(int $idTipo, bool $visible): void
    {
        $this->unEncargo($this->encargo(1, $idTipo));

        $out = $this->useCase->execute(10011);

        $this->assertCount($visible ? 1 : 0, $out['encargos']);
    }

    /**
     * @return iterable<string, array{int, bool}>
     */
    public static function tiposOcultosProvider(): iterable
    {
        yield '7xxx' => [7100, false];
        yield '4xxx' => [4100, false];
        yield '2xxx' => [2100, true];
        yield '1xxx' => [1100, true];
        yield '3xxx' => [3100, true];
        yield '47xx (empieza por 4)' => [4700, false];
    }

    public function test_datos_de_cada_encargo(): void
    {
        $this->unEncargo($this->encargo(7, 2100, 1, 'Misa diaria', 1001), 2, 5);

        $e = $this->useCase->execute(10011)['encargos'][0];

        $this->assertSame(7, $e['id_enc']);
        $this->assertSame(2100, $e['id_tipo_enc']);
        $this->assertSame(2, $e['mod_horario']);
        $this->assertSame(5, $e['modo']);
        $this->assertSame(1, $e['sf_sv']);
        $this->assertSame(1001, $e['id_ubi']);
        $this->assertSame('Misa diaria', $e['desc_enc']);
    }

    public function test_un_encargo_sin_centro_tiene_id_ubi_cero(): void
    {
        $this->unEncargo($this->encargo(7, 2100, 1, 'Personal', null));

        $this->assertSame(0, $this->useCase->execute(10011)['encargos'][0]['id_ubi']);
    }

    // ---- privacidad de la descripción ----------------------------------------------------------------------------

    public function test_sin_permiso_se_quita_el_parentesis_de_la_descripcion_de_los_encargos_sf(): void
    {
        $this->unEncargo($this->encargo(7, 2100, 2, 'Misa (Centro Norte) diaria'));

        $this->assertSame('Misa  diaria', $this->useCase->execute(10011)['encargos'][0]['desc_enc']);
    }

    public function test_con_permiso_se_ve_la_descripcion_completa(): void
    {
        $this->conPermiso(MenuDlPermissionBits::DES);
        $this->unEncargo($this->encargo(7, 2100, 2, 'Misa (Centro Norte) diaria'));

        $this->assertSame('Misa (Centro Norte) diaria', $this->useCase->execute(10011)['encargos'][0]['desc_enc']);
    }

    public function test_los_encargos_sv_conservan_siempre_el_parentesis(): void
    {
        $this->unEncargo($this->encargo(7, 2100, 1, 'Misa (Centro Norte)'));

        $this->assertSame('Misa (Centro Norte)', $this->useCase->execute(10011)['encargos'][0]['desc_enc']);
    }

    // ---- horarios por módulos (mod_horario distinto de 3) -------------------------------------------------------------

    public function test_dedicacion_del_centro_y_del_sacd_por_modulo(): void
    {
        $horariosCtr = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $horariosCtr->method('matching')->willReturnCallback(
            fn (Criteria $c) => in_array('f_fin is_null', DescribeCriteria::filtros($c), true)
                ? [$this->horario('m', 2), $this->horario('t', 3)] : [$this->horario('v', 4)]
        );
        $horariosSacd = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $horariosSacd->method('matching')->willReturnCallback(
            fn (Criteria $c) => in_array('f_fin is_null', DescribeCriteria::filtros($c), true)
                ? [$this->horarioSacd('m', 5)] : [$this->horarioSacd('t', 6), $this->horarioSacd('v', 7)]
        );
        $uc = new SacdFichaData($this->dominio, $horariosCtr, $this->encargos, $horariosSacd, $this->observ, $this->encargosSacd, $this->tipos);
        $this->unEncargo($this->encargo(7), 1);

        $e = $uc->execute(10011)['encargos'][0];

        $this->assertSame(['2', '3', '4'], [$e['dedic_ctr_m'], $e['dedic_ctr_t'], $e['dedic_ctr_v']]);
        $this->assertSame(['5', '6', '7'], [$e['dedic_m'], $e['dedic_t'], $e['dedic_v']]);
        $this->assertSame('', $e['dedic_ctr']);
        $this->assertSame('', $e['dedic_sacd']);
    }

    public function test_un_horario_del_sacd_sin_dia_de_referencia_conocido_genera_aviso_con_la_descripcion_completa(): void
    {
        $horariosSacd = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $horariosSacd->method('matching')->willReturnCallback(
            fn (Criteria $c) => in_array('f_fin is_null', DescribeCriteria::filtros($c), true) ? [$this->horarioSacd(null, 3), $this->horarioSacd('x', 3)] : []
        );
        $uc = new SacdFichaData($this->dominio, $this->horariosCtr, $this->encargos, $horariosSacd, $this->observ, $this->encargosSacd, $this->tipos);
        $this->unEncargo($this->encargo(7, 2100, 2, 'Misa (privada)'), 1);

        $out = $uc->execute(10011);

        $this->assertCount(2, $out['avisos']);
        $this->assertStringContainsString('Misa (privada)', $out['avisos'][0], 'el aviso usa la descripción sin recortar');
        $this->assertSame('Misa ', $out['encargos'][0]['desc_enc']);
    }

    public function test_si_hay_varios_horarios_del_mismo_modulo_gana_el_ultimo(): void
    {
        $horariosSacd = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $horariosSacd->method('matching')->willReturnCallback(
            fn (Criteria $c) => in_array('f_fin is_null', DescribeCriteria::filtros($c), true) ? [$this->horarioSacd('m', 1), $this->horarioSacd('m', 9)] : []
        );
        $uc = new SacdFichaData($this->dominio, $this->horariosCtr, $this->encargos, $horariosSacd, $this->observ, $this->encargosSacd, $this->tipos);
        $this->unEncargo($this->encargo(7), 1);

        $this->assertSame('9', $uc->execute(10011)['encargos'][0]['dedic_m']);
    }

    // ---- horarios de texto libre (mod_horario 3) --------------------------------------------------------------------------

    public function test_con_modo_3_el_texto_del_centro_incluye_las_horas_y_se_une_con_y(): void
    {
        $horariosCtr = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $horariosCtr->method('matching')->willReturnCallback(
            fn (Criteria $c) => in_array('f_fin is_null', DescribeCriteria::filtros($c), true)
                ? [$this->horario('l', 1, TimeLocal::fromString('09:30:00')), $this->horario('m', 1)] : []
        );
        $uc = new SacdFichaData($this->dominio, $horariosCtr, $this->encargos, $this->horariosSacd, $this->observ, $this->encargosSacd, $this->tipos);
        $this->unEncargo($this->encargo(7), 3);
        $llamadas = [];
        $this->dominio->method('texto_horario')->willReturnCallback(function (...$args) use (&$llamadas): string {
            $llamadas[] = $args;

            return count($llamadas) === 1 ? 'lunes' : 'martes';
        });

        $e = $uc->execute(10011)['encargos'][0];

        $this->assertSame('lunes y martes', $e['dedic_ctr']);
        $this->assertSame('09:30:00', $llamadas[0][4], 'regresión: las horas llegaban vacías');
        $this->assertSame('', $llamadas[1][4]);
    }

    public function test_con_modo_3_el_texto_del_sacd_va_en_dedic_sacd(): void
    {
        $horariosSacd = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $horariosSacd->method('matching')->willReturnCallback(
            fn (Criteria $c) => in_array('f_fin is_null', DescribeCriteria::filtros($c), true) ? [$this->horarioSacd('v', 1)] : []
        );
        $uc = new SacdFichaData($this->dominio, $this->horariosCtr, $this->encargos, $horariosSacd, $this->observ, $this->encargosSacd, $this->tipos);
        $this->unEncargo($this->encargo(7), 3);
        $this->dominio->method('texto_horario')->willReturn('los viernes');

        $this->assertSame('los viernes', $uc->execute(10011)['encargos'][0]['dedic_sacd']);
    }

    public function test_con_modo_3_y_sin_horario_propio_se_muestra_el_horario_del_centro(): void
    {
        $horariosCtr = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $horariosCtr->method('matching')->willReturnCallback(
            fn (Criteria $c) => in_array('f_fin is_null', DescribeCriteria::filtros($c), true) ? [$this->horario('l', 1)] : []
        );
        $uc = new SacdFichaData($this->dominio, $horariosCtr, $this->encargos, $this->horariosSacd, $this->observ, $this->encargosSacd, $this->tipos);
        $this->unEncargo($this->encargo(7), 3);
        $this->dominio->method('texto_horario')->willReturn('lunes');

        $e = $uc->execute(10011)['encargos'][0];

        $this->assertSame('lunes', $e['dedic_ctr']);
        $this->assertStringEndsWith(': lunes', $e['dedic_sacd']);
    }
}
