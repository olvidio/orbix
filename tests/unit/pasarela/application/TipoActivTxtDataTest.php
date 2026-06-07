<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\TestCase;
use src\pasarela\application\TipoActivTxtData;

final class TipoActivTxtDataTest extends TestCase
{
    public function test_id_vacio_devuelve_tipo_txt_vacio(): void
    {
        $this->assertSame(['tipo_txt' => ''], (new TipoActivTxtData())->execute(''));
    }

    public function test_id_conocido_devuelve_texto_compuesto(): void
    {
        $out = (new TipoActivTxtData())->execute('111000');
        $this->assertArrayHasKey('tipo_txt', $out);
        $this->assertNotSame('', trim($out['tipo_txt']));
    }
}
