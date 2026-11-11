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

    public function test_sin_esquema_regiones_y_cg_van_a_certificado_sin_prefijo_cr(): void
    {
        $sinEsquema = ['crCoe', 'cg', 'crAfom', 'crB', 'crCam', 'dlu'];
        $this->assertSame(
            ['Afom' => 'Afom', 'B' => 'B', 'Cam' => 'Cam', 'cg' => 'cg', 'Coe' => 'Coe'],
            ActaPersonaFormListas::siglasCertificadoSinEsquema($sinEsquema)
        );
    }

    public function test_es_sigla_de_delegacion(): void
    {
        $this->assertTrue(ActaPersonaFormListas::esSiglaDeDelegacion('dlu'));
        $this->assertTrue(ActaPersonaFormListas::esSiglaDeDelegacion('dlmO'));
        $this->assertFalse(ActaPersonaFormListas::esSiglaDeDelegacion('crB'));
        $this->assertFalse(ActaPersonaFormListas::esSiglaDeDelegacion('cg'));
    }

    public function test_tiene_esquema_delegacion(): void
    {
        $esquemas = ['H-H', 'H-dlb', 'Galbel-crGalbel'];
        $this->assertTrue(ActaPersonaFormListas::tieneEsquemaOrbix('H', 'dlb', $esquemas));
        $this->assertFalse(ActaPersonaFormListas::tieneEsquemaOrbix('H', 'dlal', $esquemas));
        $this->assertFalse(ActaPersonaFormListas::tieneEsquemaOrbix('Pla', 'dlu', $esquemas));
    }

    public function test_tiene_esquema_region_como_region_region_o_region_cr(): void
    {
        $esquemas = ['H-H', 'H-dlb', 'Galbel-crGalbel'];
        $this->assertTrue(ActaPersonaFormListas::tieneEsquemaOrbix('H', 'crH', $esquemas));
        $this->assertTrue(ActaPersonaFormListas::tieneEsquemaOrbix('Galbel', 'crGalbel', $esquemas));
        $this->assertFalse(ActaPersonaFormListas::tieneEsquemaOrbix('M', 'crM', $esquemas));
        $this->assertFalse(ActaPersonaFormListas::tieneEsquemaOrbix('zzz', 'cg', $esquemas));
    }

    public function test_certificado_incluye_las_siglas_fijas_en_orden(): void
    {
        $this->assertSame(
            ['B' => 'B', 'cg' => 'cg', 'crs+' => 'crs+', 'Usca' => 'Usca'],
            ActaPersonaFormListas::conSiglasFijas(['B' => 'B', 'cg' => 'cg', 'Usca' => 'Usca'])
        );
    }
}
