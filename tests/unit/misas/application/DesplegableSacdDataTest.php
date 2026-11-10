<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\misas\application\DesplegableSacdData;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\entity\EncargoDia;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;
use src\shared\domain\criteria\Criteria;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\ZonaSacd;
use Tests\unit\planning\support\DescribeCriteria;

/** Desplegable de SACD del modal de la cuadrícula: libres del día (1), de la zona (2), de la dl n/a (4) y todos (8). */
final class DesplegableSacdDataTest extends TestCase
{
    private InicialesSacdService&MockObject $iniciales;
    private ZonaSacdRepositoryInterface&MockObject $zonaSacd;
    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoDiaRepositoryInterface&MockObject $encargosDia;
    private PersonaSacdRepositoryInterface&MockObject $personas;

    /** @var array<int, array{0: string, 1: string}> id_nom => [iniciales, nombre] */
    private array $sacds = [10 => ['AB', 'Ana B'], 20 => ['CD', 'Carlos D'], 30 => ['EF', 'Eva F']];

    public function setUp(): void
    {
        $this->iniciales = $this->createMock(InicialesSacdService::class);
        $this->iniciales->method('obtenerIniciales')->willReturnCallback(fn (int $id): string => $this->sacds[$id][0] ?? '');
        $this->iniciales->method('obtenerNombreConIniciales')->willReturnCallback(fn (int $id): string => $this->sacds[$id][1] ?? '');
        $this->zonaSacd = $this->createMock(ZonaSacdRepositoryInterface::class);
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->encargosDia = $this->createMock(EncargoDiaRepositoryInterface::class);
        $this->personas = $this->createMock(PersonaSacdRepositoryInterface::class);
    }

    private function uc(): DesplegableSacdData
    {
        return new DesplegableSacdData($this->iniciales, $this->zonaSacd, $this->encargos, $this->encargosDia, $this->personas);
    }

    private function zonaDeSacd(bool $lunes, bool $domingo = true): ZonaSacd
    {
        $z = $this->createMock(ZonaSacd::class);
        foreach ([2, 3, 4, 5, 6] as $n) {
            $z->method("isDw$n")->willReturn(true);
        }
        $z->method('isDw1')->willReturn($lunes);
        $z->method('isDw7')->willReturn($domingo);

        return $z;
    }

    private function persona(int $id): PersonaSacd&MockObject
    {
        $p = $this->createMock(PersonaSacd::class);
        $p->method('getId_nom')->willReturn($id);

        return $p;
    }

    public function test_sin_seleccion_solo_va_el_actual_con_su_blanco(): void
    {
        $r = $this->uc()->getData(1, 10, 0, '2026-03-02');

        $this->assertSame([['value' => 'AB#10', 'label' => 'Ana B'], ['value' => '', 'label' => '']], $r['rows']);
        $this->assertSame('AB#10', $r['selected']);
        $this->assertSame('id_sacd', $r['id']);
    }

    public function test_sin_sacd_actual_no_hay_opcion_en_blanco_extra(): void
    {
        $r = $this->uc()->getData(1, 0, 0, '2026-03-02');

        $this->assertCount(1, $r['rows']);
        $this->assertSame('#0', $r['rows'][0]['value']);
    }

    public function test_la_seleccion_2_ofrece_todos_los_de_la_zona_ordenados_por_clave(): void
    {
        $this->zonaSacd->method('getIdSacdsDeZona')->with(7)->willReturn([30, 10]);

        $r = $this->uc()->getData(7, 20, 2, '2026-03-02');

        $this->assertSame(['CD#20', '', 'AB#10', 'EF#30'], array_column($r['rows'], 'value'));
        $this->assertSame([['CD#20', 'Carlos D'], ['', ''], ['AB#10', 'Ana B'], ['EF#30', 'Eva F']], $r['opciones']);
    }

    public function test_la_seleccion_1_excluye_a_quien_tiene_misa_ese_dia_y_a_quien_no_trabaja_ese_dia_de_la_semana(): void
    {
        $this->zonaSacd->method('getIdSacdsDeZona')->willReturn([10, 20, 30]);
        // 10 tiene un encargo del día de tipo X1XX (misa); 20 no trabaja los lunes; 30 libre.
        $this->encargosDia->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = DescribeCriteria::filtros($c);
            $this->assertNotSame([], preg_grep('/^tstart between/', $f) ?: preg_grep('/between/', $f));
            if (in_array('id_nom eq 10', $f, true)) {
                $d = $this->createMock(EncargoDia::class);
                $d->method('getId_enc')->willReturn(555);

                return [$d];
            }

            return [];
        });
        $enc = $this->createMock(Encargo::class);
        $enc->method('getId_tipo_enc')->willReturn(8110);
        $this->encargos->method('matching')->willReturn([$enc]);
        $this->zonaSacd->method('matching')->willReturnCallback(function (Criteria $c): array {
            return in_array('id_nom eq 20', DescribeCriteria::filtros($c), true)
                ? [$this->zonaDeSacd(false)]
                : [$this->zonaDeSacd(true)];
        });

        $r = $this->uc()->getData(7, 0, 1, '2026-03-02');

        $this->assertSame(['#0', 'EF#30'], array_column($r['rows'], 'value'));
    }

    public function test_un_encargo_del_dia_que_no_es_misa_no_ocupa_al_sacd(): void
    {
        $this->zonaSacd->method('getIdSacdsDeZona')->willReturn([10]);
        $d = $this->createMock(EncargoDia::class);
        $d->method('getId_enc')->willReturn(555);
        $this->encargosDia->method('matching')->willReturn([$d]);
        $enc = $this->createMock(Encargo::class);
        $enc->method('getId_tipo_enc')->willReturn(8210);
        $this->encargos->method('matching')->willReturn([$enc]);
        $this->zonaSacd->method('matching')->willReturn([$this->zonaDeSacd(true)]);

        $r = $this->uc()->getData(7, 0, 1, '2026-03-02');

        $this->assertContains('AB#10', array_column($r['rows'], 'value'));
    }

    public function test_el_domingo_se_consulta_dw7(): void
    {
        $this->zonaSacd->method('getIdSacdsDeZona')->willReturn([10]);
        $this->encargosDia->method('matching')->willReturn([]);
        $this->zonaSacd->method('matching')->willReturn([$this->zonaDeSacd(true, false)]);

        $r = $this->uc()->getData(7, 0, 1, '2026-03-08');

        $this->assertSame(['#0'], array_column($r['rows'], 'value'));
    }

    public function test_las_selecciones_4_y_8_buscan_sacd_activos_en_la_dl(): void
    {
        $criterios = [];
        $this->personas->method('matching')->willReturnCallback(function (Criteria $c) use (&$criterios): array {
            $criterios[] = DescribeCriteria::filtros($c);

            return [$this->persona(30), $this->persona(20)];
        });

        $this->uc()->getData(7, 0, 4, '2026-03-02');
        $r = $this->uc()->getData(7, 0, 8, '2026-03-02');

        $this->assertSame(['sacd eq true', 'situacion eq A', 'id_tabla in [n,a]'], $criterios[0]);
        $this->assertSame(['sacd eq true', 'situacion eq A'], $criterios[1]);
        $this->assertSame(['#0', 'CD#20', 'EF#30'], array_column($r['rows'], 'value'));
    }

    public function test_los_bits_se_combinan_sin_duplicar_sacd(): void
    {
        $this->zonaSacd->method('getIdSacdsDeZona')->willReturn([10]);
        $this->personas->method('matching')->willReturn([$this->persona(10), $this->persona(20)]);

        $r = $this->uc()->getData(7, 0, 2 | 8, '2026-03-02');

        $this->assertSame(['#0', 'AB#10', 'CD#20'], array_column($r['rows'], 'value'));
    }

    public function test_el_html_heredado_escapa_valores_y_etiquetas_e_ignora_filas_invalidas(): void
    {
        $html = $this->uc()->legacySelectHtml(['rows' => [
            ['value' => 'A"B#1', 'label' => '<b>Ana</b>'],
            'no es fila',
            ['value' => 7, 'label' => 8],
            ['value' => [], 'label' => null],
        ]]);

        $this->assertSame(
            '<SELECT ID="id_sacd"><OPTION VALUE="A&quot;B#1">&lt;b&gt;Ana&lt;/b&gt;</OPTION>'
            . '<OPTION VALUE="7">8</OPTION><OPTION VALUE=""></OPTION></SELECT>',
            $html
        );
        $this->assertSame('<SELECT ID="id_sacd"></SELECT>', $this->uc()->legacySelectHtml([]));
        $this->assertSame('<SELECT ID="id_sacd"></SELECT>', $this->uc()->legacySelectHtml(['rows' => 'x']));
    }
}
