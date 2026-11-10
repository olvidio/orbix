<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\notas\application\support\ActaPersonaFormListas;

final class ActaPersonaFormListasTest extends TestCase
{
    public function test_sigla_certificado_quita_prefijo_cr_en_regiones(): void
    {
        $this->assertSame('Galbel', ActaPersonaFormListas::siglaCertificadoSinPrefijoCr('crGalbel'));
        $this->assertSame('Nig', ActaPersonaFormListas::siglaCertificadoSinPrefijoCr('crNig'));
        $this->assertSame('M', ActaPersonaFormListas::siglaCertificadoSinPrefijoCr('crM'));
        $this->assertSame('Arg', ActaPersonaFormListas::siglaCertificadoSinPrefijoCr('crArg'));
    }

    public function test_sigla_certificado_no_toca_dl_ni_siglas_sin_cr(): void
    {
        $this->assertSame('dlb', ActaPersonaFormListas::siglaCertificadoSinPrefijoCr('dlb'));
        $this->assertSame('dlpf', ActaPersonaFormListas::siglaCertificadoSinPrefijoCr('dlpf'));
        $this->assertSame('H', ActaPersonaFormListas::siglaCertificadoSinPrefijoCr('H'));
        $this->assertSame('Galbel', ActaPersonaFormListas::siglaCertificadoSinPrefijoCr('Galbel'));
    }

    public function test_sigla_certificado_cr_solo_o_vacio_se_deja(): void
    {
        $this->assertSame('cr', ActaPersonaFormListas::siglaCertificadoSinPrefijoCr('cr'));
        $this->assertSame('', ActaPersonaFormListas::siglaCertificadoSinPrefijoCr(''));
        $this->assertSame('', ActaPersonaFormListas::siglaCertificadoSinPrefijoCr('  '));
    }

    public function test_con_sigla_region_del_esquema_la_pone_delante(): void
    {
        $opciones = ['Arg' => 'Arg', 'Ita' => 'Ita'];
        $out = ActaPersonaFormListas::conSiglaRegionDelEsquema($opciones, 'H');
        $this->assertSame(['H' => 'H', 'Arg' => 'Arg', 'Ita' => 'Ita'], $out);
    }

    public function test_con_sigla_region_del_esquema_quita_prefijo_cr(): void
    {
        $out = ActaPersonaFormListas::conSiglaRegionDelEsquema(['Arg' => 'Arg'], 'crGalbel');
        $this->assertSame(['Galbel' => 'Galbel', 'Arg' => 'Arg'], $out);
    }

    public function test_con_sigla_region_del_esquema_si_ya_esta_la_mueve_al_frente(): void
    {
        $out = ActaPersonaFormListas::conSiglaRegionDelEsquema(['Arg' => 'Arg', 'H' => 'H'], 'H');
        $this->assertSame(['H' => 'H', 'Arg' => 'Arg'], $out);
    }

    public function test_con_sigla_region_vacia_no_cambia_opciones(): void
    {
        $opciones = ['Arg' => 'Arg'];
        $this->assertSame($opciones, ActaPersonaFormListas::conSiglaRegionDelEsquema($opciones, '  '));
    }

    public function test_esquema_region_stgr_acepta_h_m_y_cr(): void
    {
        $this->assertTrue(ActaPersonaFormListas::esEsquemaRegionStgr('H', 'H'));
        $this->assertTrue(ActaPersonaFormListas::esEsquemaRegionStgr('H', 'Hv'));
        $this->assertTrue(ActaPersonaFormListas::esEsquemaRegionStgr('M', 'M'));
        $this->assertTrue(ActaPersonaFormListas::esEsquemaRegionStgr('Galbel', 'crGalbel'));
        $this->assertTrue(ActaPersonaFormListas::esEsquemaRegionStgr('Galbel', 'crGalbelv'));
        $this->assertTrue(ActaPersonaFormListas::esEsquemaRegionStgr('Cong', 'crCongf'));
    }

    public function test_esquema_region_stgr_rechaza_dl_de_h_y_m(): void
    {
        $this->assertFalse(ActaPersonaFormListas::esEsquemaRegionStgr('H', 'dlb'));
        $this->assertFalse(ActaPersonaFormListas::esEsquemaRegionStgr('H', 'dlbv'));
        $this->assertFalse(ActaPersonaFormListas::esEsquemaRegionStgr('H', 'dlp'));
        $this->assertFalse(ActaPersonaFormListas::esEsquemaRegionStgr('M', 'dlm'));
        $this->assertFalse(ActaPersonaFormListas::esEsquemaRegionStgr('M', 'dlmO'));
        $this->assertFalse(ActaPersonaFormListas::esEsquemaRegionStgr('M', 'dlmOv'));
        $this->assertFalse(ActaPersonaFormListas::esEsquemaRegionStgr('', 'H'));
    }

    public function test_sin_esquema_solo_las_delegaciones_van_a_acta(): void
    {
        $sinEsquema = ['cg', 'crAfom', 'crB', 'crCam', 'crCoe', 'dlu'];
        $this->assertSame(['dlu' => 'dlu'], ActaPersonaFormListas::siglasActaSinEsquema($sinEsquema, false));
        $this->assertSame(['dluf' => 'dluf'], ActaPersonaFormListas::siglasActaSinEsquema($sinEsquema, true));
    }

    public function test_sin_esquema_regiones_y_cg_van_a_certificado_sin_prefijo_cr(): void
    {
        $sinEsquema = ['cg', 'crAfom', 'crB', 'crCam', 'crCoe', 'dlu'];
        $this->assertSame(
            ['cg' => 'cg', 'Afom' => 'Afom', 'B' => 'B', 'Cam' => 'Cam', 'Coe' => 'Coe'],
            ActaPersonaFormListas::siglasCertificadoSinEsquema($sinEsquema)
        );
    }

    public function test_siglas_certificado_inactivas_segun_delegacion(): void
    {
        $estados = [['crIers', false], ['crH', true], ['dlb', true], ['crE', false]];
        $this->assertSame(
            ['Iers' => 'Iers', 'E' => 'E'],
            ActaPersonaFormListas::siglasCertificadoInactivas($estados)
        );
    }

    public function test_sigla_con_alguna_fila_activa_no_es_inactiva(): void
    {
        $estados = [['crH', true], ['H', false], ['', false]];
        $this->assertSame([], ActaPersonaFormListas::siglasCertificadoInactivas($estados));
    }

    public function test_es_sigla_de_delegacion(): void
    {
        $this->assertTrue(ActaPersonaFormListas::esSiglaDeDelegacion('dlu'));
        $this->assertTrue(ActaPersonaFormListas::esSiglaDeDelegacion('dlmO'));
        $this->assertFalse(ActaPersonaFormListas::esSiglaDeDelegacion('crB'));
        $this->assertFalse(ActaPersonaFormListas::esSiglaDeDelegacion('cg'));
    }
}
