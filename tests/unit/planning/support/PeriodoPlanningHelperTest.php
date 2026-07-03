<?php

namespace Tests\unit\planning\support;

use frontend\planning\support\PeriodoPlanningHelper;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use frontend\shared\web\PeriodoQue;

/**
 * Unitarios de {@see PeriodoPlanningHelper}: cubre el catalogo de
 * opciones trimestrales, los textos por defecto segun el `mes_fin_stgr`
 * y la construccion del `PeriodoQue` que usan los `planning_*_que.php`.
 */
final class PeriodoPlanningHelperTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public function test_opciones_trimestrales_contiene_todas_las_claves_esperadas(): void
    {
        $opciones = PeriodoPlanningHelper::opcionesTrimestrales();

        $this->assertArrayHasKey('tot_any', $opciones);
        $this->assertArrayHasKey('trimestre_1', $opciones);
        $this->assertArrayHasKey('trimestre_2', $opciones);
        $this->assertArrayHasKey('trimestre_3', $opciones);
        $this->assertArrayHasKey('trimestre_4', $opciones);
        $this->assertArrayHasKey('separador', $opciones);
        $this->assertArrayHasKey('otro', $opciones);
        $this->assertSame('---------', $opciones['separador']);
    }

    public function test_opciones_trimestrales_mantiene_el_orden_del_desplegable(): void
    {
        $claves = array_keys(PeriodoPlanningHelper::opcionesTrimestrales());

        $this->assertSame([
            'tot_any',
            'trimestre_1',
            'trimestre_2',
            'trimestre_3',
            'trimestre_4',
            'separador',
            'otro',
        ], $claves);
    }

    public function test_texto_periodo_por_defecto_para_mes_actual_menor_o_igual_al_fin_stgr(): void
    {
        $mesFinStgr = 12;

        $texto = PeriodoPlanningHelper::textoPeriodoPorDefecto($mesFinStgr);

        $this->assertStringContainsString('1/6', $texto);
        $this->assertStringContainsString('30/13', $texto);
    }

    public function test_texto_periodo_por_defecto_para_mes_actual_mayor_que_fin_stgr(): void
    {
        $mesFinStgr = 0;

        $texto = PeriodoPlanningHelper::textoPeriodoPorDefecto($mesFinStgr);

        $this->assertStringContainsString('1/1', $texto);
        $this->assertStringContainsString('31/5', $texto);
    }

    public function test_form_periodo_devuelve_una_instancia_de_periodoque(): void
    {
        $oForm = PeriodoPlanningHelper::formPeriodo('trimestre_1', 2030, '1/1/2030', '31/3/2030', 'MI TITULO');

        $this->assertInstanceOf(PeriodoQue::class, $oForm);
    }

    public function test_form_periodo_fija_los_valores_pasados(): void
    {
        $oForm = PeriodoPlanningHelper::formPeriodo(
            'trimestre_2',
            2031,
            '1/4/2031',
            '30/6/2031',
            'MI TITULO'
        );

        $this->assertSame('que', $this->leerPropiedadPrivada($oForm, 'sFormName'));
        $this->assertSame('MI TITULO', $this->leerPropiedadPrivada($oForm, 'sTitulo'));
        $this->assertSame('1/4/2031', $this->leerPropiedadPrivada($oForm, 'sEmpiezaMin'));
        $this->assertSame('30/6/2031', $this->leerPropiedadPrivada($oForm, 'sEmpiezaMax'));
    }

    public function test_form_periodo_titulo_vacio_usa_titulo_por_defecto(): void
    {
        $oForm = PeriodoPlanningHelper::formPeriodo('tot_any', 2032, '', '');

        $titulo = $this->leerPropiedadPrivada($oForm, 'sTitulo');
        $this->assertIsString($titulo);
        $this->assertNotSame('', $titulo);
    }

    public function test_form_periodo_year_vacio_usa_anyo_actual(): void
    {
        $oForm = PeriodoPlanningHelper::formPeriodo('tot_any', '', '', '', 'T');

        $despl = $this->leerPropiedadPrivada($oForm, 'oDesplAnys');
        $this->assertNotNull($despl);
        $opcionSel = $this->leerPropiedadPrivada($despl, 'sOpcion_sel');
        $this->assertSame((int)date('Y'), (int)$opcionSel);
    }

    public function test_form_periodo_inyecta_las_opciones_trimestrales(): void
    {
        $oForm = PeriodoPlanningHelper::formPeriodo('tot_any', 2033, '', '', 'T');

        $despl = $this->leerPropiedadPrivada($oForm, 'oDesplPeriodos');
        $this->assertNotNull($despl);
        $opciones = $despl->getOpciones();

        $this->assertSame(PeriodoPlanningHelper::opcionesTrimestrales(), $opciones);
    }

    private function leerPropiedadPrivada(object $obj, string $prop): mixed
    {
        $ref = new ReflectionObject($obj);
        while ($ref !== false && !$ref->hasProperty($prop)) {
            $ref = $ref->getParentClass();
        }
        if ($ref === false) {
            throw new \RuntimeException("No existe propiedad $prop en " . $obj::class);
        }
        $p = $ref->getProperty($prop);
        return $p->getValue($obj);
    }
}
