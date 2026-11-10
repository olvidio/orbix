<?php

declare(strict_types=1);

namespace Tests\unit\configuracion\application;

use src\configuracion\application\PeriodoCalendarioEscolarData;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use Tests\myTest;

/** Fechas del curso (STGR / CRT) para inyectar en el selector de periodos del frontend. */
final class PeriodoCalendarioEscolarDataTest extends myTest
{
    private mixed $configAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
    }

    public function tearDown(): void
    {
        if ($this->configAnterior === null) {
            unset($_SESSION['oConfig']);
        } else {
            $_SESSION['oConfig'] = $this->configAnterior;
        }
        parent::tearDown();
    }

    public function test_con_la_configuracion_en_sesion_se_devuelven_sus_dias_y_meses_y_los_anyos_finales(): void
    {
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, 'dl', null, null, null, null, null, null,
            ['ini_dia' => 2, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => 6],
            ['ini_dia' => 5, 'ini_mes' => 10, 'fin_dia' => 20, 'fin_mes' => 7]);
        $uc = new PeriodoCalendarioEscolarData($GLOBALS['container']->get(\src\configuracion\application\ObtenerConfigSnapshot::class));

        $r = $uc->execute();

        $this->assertSame(['mes_fin_stgr' => 6, 'mes_fin_crt' => 7, 'dia_ini_stgr' => 2, 'mes_ini_stgr' => 9, 'dia_fin_stgr' => 30, 'dia_ini_crt' => 5, 'mes_ini_crt' => 10, 'dia_fin_crt' => 20], array_diff_key($r, ['any_final_est' => 1, 'any_final_crt' => 1]));
        $hoy = (int) date('Y');
        $mes = (int) date('m');
        $this->assertSame($mes > 6 ? $hoy + 1 : $hoy, $r['any_final_est']);
        $this->assertSame($mes > 7 ? $hoy + 1 : $hoy, $r['any_final_crt']);
    }

    public function test_sin_configuracion_en_sesion_se_lee_de_la_base_de_datos(): void
    {
        unset($_SESSION['oConfig']);
        $uc = new PeriodoCalendarioEscolarData($GLOBALS['container']->get(\src\configuracion\application\ObtenerConfigSnapshot::class));

        $r = $uc->execute();

        $this->assertCount(10, $r);
        foreach ($r as $clave => $valor) {
            $this->assertIsInt($valor, $clave);
        }
    }
}
