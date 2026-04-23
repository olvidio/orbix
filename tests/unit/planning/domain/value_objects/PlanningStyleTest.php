<?php

namespace Tests\unit\planning\domain\value_objects;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\planning\domain\value_objects\PlanningStyle;

/**
 * Unitarios de {@see PlanningStyle}: cubre el calculo de la clase CSS
 * segun el primer digito del `id_tipo_activ` (sv/sf/otras), las
 * variantes `propio` y `personal`, el prefijo `provisional` cuando la
 * plaza no esta aun asignada y el prefijo `proyecto`/`proyectof`
 * cuando la actividad esta en estado 1 (proyecto).
 */
final class PlanningStyleTest extends TestCase
{
    public function test_id_tipo_activ_que_empieza_por_1_es_actsv(): void
    {
        $this->assertSame('actsv', PlanningStyle::clase(111000, false, '', null));
    }

    public function test_id_tipo_activ_que_empieza_por_2_es_actsf(): void
    {
        $this->assertSame('actsf', PlanningStyle::clase(271000, false, '', null));
    }

    public function test_id_tipo_activ_que_no_empieza_por_1_ni_2_es_actotras(): void
    {
        $this->assertSame('actotras', PlanningStyle::clase(371000, false, '', null));
    }

    public function test_id_tipo_activ_vacio_es_actotras(): void
    {
        $this->assertSame('actotras', PlanningStyle::clase('', false, '', null));
    }

    public function test_propio_true_fuerza_actpropio_independientemente_del_tipo(): void
    {
        $this->assertSame('actpropio', PlanningStyle::clase(111000, true, '', null));
        $this->assertSame('actpropio', PlanningStyle::clase(271000, true, '', null));
        $this->assertSame('actpropio', PlanningStyle::clase(371000, true, '', null));
    }

    public function test_propio_string_p_fuerza_actpersonal(): void
    {
        $this->assertSame('actpersonal', PlanningStyle::clase(111000, 'p', '', null));
    }

    public function test_propio_string_p_tiene_precedencia_sobre_true(): void
    {
        // Si propio es "p" (string), el switch anterior asigna primero "actpropio"
        // cuando propio es TRUE; pero como propio === "p" !== true, se aplica la
        // rama de actpersonal.
        $this->assertSame('actpersonal', PlanningStyle::clase(271000, 'p', '', null));
    }

    public function test_plaza_por_debajo_de_asignada_prefija_provisional(): void
    {
        $this->assertSame('provisional actsv', PlanningStyle::clase(111000, false, PlazaId::PEDIDA, null));
        $this->assertSame('provisional actsv', PlanningStyle::clase(111000, false, PlazaId::EN_ESPERA, null));
        $this->assertSame('provisional actsv', PlanningStyle::clase(111000, false, PlazaId::DENEGADA, null));
    }

    public function test_plaza_asignada_no_prefija_provisional(): void
    {
        $this->assertSame('actsv', PlanningStyle::clase(111000, false, PlazaId::ASIGNADA, null));
    }

    public function test_plaza_confirmada_no_prefija_provisional(): void
    {
        $this->assertSame('actsv', PlanningStyle::clase(111000, false, PlazaId::CONFIRMADA, null));
    }

    public function test_plaza_vacia_no_prefija_provisional(): void
    {
        $this->assertSame('actsv', PlanningStyle::clase(111000, false, '', null));
        $this->assertSame('actsv', PlanningStyle::clase(111000, false, 0, null));
        $this->assertSame('actsv', PlanningStyle::clase(111000, false, null, null));
    }

    public function test_status_1_en_actividad_sv_prefija_solo_proyecto(): void
    {
        $this->assertSame('proyecto actsv', PlanningStyle::clase(111000, false, '', 1));
    }

    public function test_status_1_en_actividad_sf_prefija_proyecto_y_proyectof(): void
    {
        $this->assertSame('proyectof proyecto actsf', PlanningStyle::clase(271000, false, '', 1));
    }

    public function test_status_distinto_de_1_no_prefija_proyecto(): void
    {
        $this->assertSame('actsv', PlanningStyle::clase(111000, false, '', 2));
        $this->assertSame('actsv', PlanningStyle::clase(111000, false, '', 3));
    }

    public function test_status_null_o_vacio_no_prefija_proyecto(): void
    {
        $this->assertSame('actsv', PlanningStyle::clase(111000, false, '', null));
        $this->assertSame('actsv', PlanningStyle::clase(111000, false, '', 0));
        $this->assertSame('actsv', PlanningStyle::clase(111000, false, '', ''));
    }

    public function test_combina_provisional_y_proyecto_en_sv(): void
    {
        $this->assertSame(
            'proyecto provisional actsv',
            PlanningStyle::clase(111000, false, PlazaId::PEDIDA, 1)
        );
    }

    public function test_combina_provisional_y_proyecto_en_sf(): void
    {
        $this->assertSame(
            'proyectof proyecto provisional actsf',
            PlanningStyle::clase(271000, false, PlazaId::PEDIDA, 1)
        );
    }

    public function test_propio_gana_al_tipo_pero_proyecto_sigue_aplicandose(): void
    {
        // svsf deriva de id_tipo_activ, no de la clase final, por lo que
        // propio=true sigue generando "proyecto" (no "proyectof") si el
        // tipo empieza por 1.
        $this->assertSame('proyecto actpropio', PlanningStyle::clase(111000, true, '', 1));
    }

    public function test_propio_p_en_sf_sigue_prefijando_proyecto_y_proyectof(): void
    {
        $this->assertSame(
            'proyectof proyecto actpersonal',
            PlanningStyle::clase(271000, 'p', '', 1)
        );
    }

    public function test_id_tipo_activ_como_string_funciona_igual(): void
    {
        $this->assertSame('actsv', PlanningStyle::clase('111000', false, '', null));
        $this->assertSame('actsf', PlanningStyle::clase('271000', false, '', null));
    }
}
