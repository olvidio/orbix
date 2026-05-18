<?php

namespace Tests\unit\configuracion\domain\value_objects;

use src\configuracion\domain\value_objects\ConfigSnapshot;
use Tests\myTest;

class ConfigSnapshotTest extends myTest
{
    /**
     * Devuelve una instancia mínima de `ConfigSnapshot` con los valores que
     * cada test necesite. Los parámetros no indicados quedan a `null`.
     */
    private function makeSnapshot(array $overrides = []): ConfigSnapshot
    {
        $defaults = [
            'gesCalendario' => null,
            'ceLugar' => null,
            'regionLatin' => null,
            'vstgr' => null,
            'lugarFirma' => null,
            'dirStgr' => null,
            'ambito' => null,
            'notaCorte' => null,
            'notaMax' => null,
            'caducaCursada' => null,
            'idiomaDefault' => null,
            'iniContadorCertificados' => null,
            'jefeCalendario' => null,
            'aCursoStgr' => null,
            'aCursoCrt' => null,
        ];
        $args = array_merge($defaults, $overrides);

        return new ConfigSnapshot(
            gesCalendario:           $args['gesCalendario'],
            ceLugar:                 $args['ceLugar'],
            regionLatin:             $args['regionLatin'],
            vstgr:                   $args['vstgr'],
            lugarFirma:              $args['lugarFirma'],
            dirStgr:                 $args['dirStgr'],
            ambito:                  $args['ambito'],
            notaCorte:               $args['notaCorte'],
            notaMax:                 $args['notaMax'],
            caducaCursada:           $args['caducaCursada'],
            idiomaDefault:           $args['idiomaDefault'],
            iniContadorCertificados: $args['iniContadorCertificados'],
            jefeCalendario:          $args['jefeCalendario'],
            aCursoStgr:              $args['aCursoStgr'],
            aCursoCrt:               $args['aCursoCrt'],
        );
    }

    /* ------------------- Getters simples ------------------- */

    public function test_getGestionCalendario()
    {
        $s = $this->makeSnapshot(['gesCalendario' => 'central']);
        $this->assertSame('central', $s->getGestionCalendario());
    }

    public function test_getAmbito()
    {
        $s = $this->makeSnapshot(['ambito' => 'dl']);
        $this->assertSame('dl', $s->getAmbito());
    }

    public function test_getNomVstgr_is_alias_of_getVstgr()
    {
        $s = $this->makeSnapshot(['vstgr' => 'San Jose']);
        $this->assertSame($s->getVstgr(), $s->getNomVstgr());
        $this->assertSame('San Jose', $s->getNomVstgr());
    }

    public function test_getIdioma_default()
    {
        $s = $this->makeSnapshot(['idiomaDefault' => 'es_ES.UTF-8']);
        $this->assertSame('es_ES.UTF-8', $s->getIdioma_default());
    }

    public function test_getContador_certificados()
    {
        $s = $this->makeSnapshot(['iniContadorCertificados' => '1000']);
        $this->assertSame('1000', $s->getContador_certificados());
    }

    public function test_formatMissingParametersMessage_lists_all_missing()
    {
        $s = $this->makeSnapshot(['vstgr' => 'San Jose']);
        $msg = $s->formatMissingParametersMessage([
            $s->regionLatin => 'region latin',
            $s->vstgr => 'vstgr',
            $s->iniContadorCertificados => 'contador',
        ]);
        $this->assertStringContainsString('region latin', $msg);
        $this->assertStringContainsString('contador', $msg);
        $this->assertStringNotContainsString('vstgr', $msg);
    }

    public function test_formatMissingParametersMessage_empty_when_all_present()
    {
        $s = $this->makeSnapshot([
            'regionLatin' => 'Hispania',
            'iniContadorCertificados' => '1',
        ]);
        $this->assertSame('', $s->formatMissingParametersMessage([
            $s->regionLatin => 'region latin',
            $s->iniContadorCertificados => 'contador',
        ]));
    }

    /* ------------------- getCe / getCe_lugar ------------------- */

    public function test_getCe_parses_csv()
    {
        $s = $this->makeSnapshot(['ceLugar' => 'Madrid,Barcelona,Valencia']);
        $this->assertSame(['Madrid', 'Barcelona', 'Valencia'], $s->getCe());
    }

    public function test_getCe_lugar_returns_raw_string()
    {
        $s = $this->makeSnapshot(['ceLugar' => 'Madrid,Barcelona']);
        $this->assertSame('Madrid,Barcelona', $s->getCe_lugar());
    }

    /* ------------------- is_jefeCalendario ------------------- */

    public function test_is_jefeCalendario_true_for_listed_username()
    {
        $s = $this->makeSnapshot(['jefeCalendario' => 'dani,of2des,mark']);
        $this->assertTrue($s->is_jefeCalendario('dani'));
        $this->assertTrue($s->is_jefeCalendario('mark'));
    }

    public function test_is_jefeCalendario_false_for_unlisted_username()
    {
        $s = $this->makeSnapshot(['jefeCalendario' => 'dani,of2des,mark']);
        $this->assertFalse($s->is_jefeCalendario('pepe'));
    }

    /* ------------------- curso (stgr/crt) ------------------- */

    public function test_getCursoStgr()
    {
        $aCursoStgr = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => 6];
        $s = $this->makeSnapshot(['aCursoStgr' => $aCursoStgr]);
        $this->assertEquals($aCursoStgr, $s->getCursoStgr());
    }

    public function test_getCursoCrt()
    {
        $aCursoCrt = ['ini_dia' => 15, 'ini_mes' => 10, 'fin_dia' => 20, 'fin_mes' => 5];
        $s = $this->makeSnapshot(['aCursoCrt' => $aCursoCrt]);
        $this->assertEquals($aCursoCrt, $s->getCursoCrt());
    }

    public function test_getDiaIniStgr()
    {
        $s = $this->makeSnapshot([
            'aCursoStgr' => ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => 6],
        ]);
        $this->assertSame(1, $s->getDiaIniStgr());
    }

    public function test_getMesIniStgr()
    {
        $s = $this->makeSnapshot([
            'aCursoStgr' => ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => 6],
        ]);
        $this->assertSame(9, $s->getMesIniStgr());
    }

    public function test_getDiaFinStgr()
    {
        $s = $this->makeSnapshot([
            'aCursoStgr' => ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => 6],
        ]);
        $this->assertSame(30, $s->getDiaFinStgr());
    }

    public function test_getMesFinStgr()
    {
        $s = $this->makeSnapshot([
            'aCursoStgr' => ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => 6],
        ]);
        $this->assertSame(6, $s->getMesFinStgr());
    }

    public function test_getDiaIniCrt()
    {
        $s = $this->makeSnapshot([
            'aCursoCrt' => ['ini_dia' => 15, 'ini_mes' => 10, 'fin_dia' => 20, 'fin_mes' => 5],
        ]);
        $this->assertSame(15, $s->getDiaIniCrt());
    }

    public function test_getMesIniCrt()
    {
        $s = $this->makeSnapshot([
            'aCursoCrt' => ['ini_dia' => 15, 'ini_mes' => 10, 'fin_dia' => 20, 'fin_mes' => 5],
        ]);
        $this->assertSame(10, $s->getMesIniCrt());
    }

    public function test_getDiaFinCrt()
    {
        $s = $this->makeSnapshot([
            'aCursoCrt' => ['ini_dia' => 15, 'ini_mes' => 10, 'fin_dia' => 20, 'fin_mes' => 5],
        ]);
        $this->assertSame(20, $s->getDiaFinCrt());
    }

    public function test_getMesFinCrt()
    {
        $s = $this->makeSnapshot([
            'aCursoCrt' => ['ini_dia' => 15, 'ini_mes' => 10, 'fin_dia' => 20, 'fin_mes' => 5],
        ]);
        $this->assertSame(5, $s->getMesFinCrt());
    }

    /* ------------------- any_final_curs ------------------- */

    public function test_any_final_curs_est_with_fin_mes_12_returns_current_year()
    {
        // fin_mes = 12 => el mes actual nunca lo supera => siempre año actual.
        $s = $this->makeSnapshot([
            'aCursoStgr' => ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => 12],
        ]);
        $this->assertSame((int) date('Y'), $s->any_final_curs('est'));
    }

    public function test_any_final_curs_crt_matches_date_logic()
    {
        // fin_mes = 6. El resultado depende del mes actual según la regla:
        //   mes_actual > fin_mes  => año + 1
        //   mes_actual <= fin_mes => año actual
        $finMes = 6;
        $s = $this->makeSnapshot([
            'aCursoCrt' => ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => $finMes],
        ]);
        $expected = (int) date('m') > $finMes ? (int) date('Y') + 1 : (int) date('Y');
        $this->assertSame($expected, $s->any_final_curs('crt'));
    }

    /* ------------------- Serialización ------------------- */

    public function test_snapshot_is_serializable()
    {
        $s = $this->makeSnapshot([
            'gesCalendario' => 'central',
            'ambito' => 'dl',
            'aCursoStgr' => ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 30, 'fin_mes' => 6],
        ]);

        $restored = unserialize(serialize($s));

        $this->assertInstanceOf(ConfigSnapshot::class, $restored);
        $this->assertSame('central', $restored->getGestionCalendario());
        $this->assertSame('dl', $restored->getAmbito());
        $this->assertSame(6, $restored->getMesFinStgr());
    }
}
