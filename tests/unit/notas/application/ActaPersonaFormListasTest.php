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
}
