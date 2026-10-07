<?php

declare(strict_types=1);

namespace Tests\unit\frontend\certificados;

use frontend\certificados\helpers\CertificadosPostInput;
use PHPUnit\Framework\TestCase;

final class CertificadosPostInputTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_POST['sel'], $_POST['id_sel'], $_POST['id_nom']);
        parent::tearDown();
    }

    public function test_parse_acepta_id_nom_negativo_de_persona_de_paso(): void
    {
        $this->assertSame(-4123, CertificadosPostInput::parseIdNomFromSelValue('-4123'));
        $this->assertSame(-4123, CertificadosPostInput::parseIdNomFromSelValue('-4123#pn'));
        $this->assertSame(-4123, CertificadosPostInput::parseIdNomFromSelValue('#-4123#pn'));
        $this->assertSame(-4123, CertificadosPostInput::parseIdNomFromSelValue('checked#-4123'));
    }

    public function test_id_nom_from_sel_post_conserva_persona_de_paso(): void
    {
        $_POST['sel'] = ['-4123'];

        $this->assertSame(-4123, CertificadosPostInput::idNomFromSelPost());
    }

    public function test_id_nom_from_sel_con_tabla_conserva_persona_de_paso(): void
    {
        $_POST['sel'] = ['-4123#pn'];

        $this->assertSame(-4123, CertificadosPostInput::idNomFromSelPost());
    }

    public function test_id_sel_negativo_cuando_sel_no_trae_id(): void
    {
        $_POST['sel'] = ['pn'];
        $_POST['id_sel'] = '-4123#pn';

        $this->assertSame(-4123, CertificadosPostInput::idNomFromSelPost());
    }

    public function test_id_nom_positivo_sigue_valiendo(): void
    {
        $_POST['sel'] = ['88001#n'];

        $this->assertSame(88001, CertificadosPostInput::idNomFromSelPost());
    }

    public function test_cero_sigue_siendo_ausencia_de_persona(): void
    {
        $_POST['sel'] = ['0'];
        $_POST['id_sel'] = '0';
        $_POST['id_nom'] = '0';

        $this->assertSame(0, CertificadosPostInput::idNomFromSelPost());
    }
}
