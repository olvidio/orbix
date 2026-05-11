<?php

namespace Tests\unit\cartaspresentacion\application;

use PHPUnit\Framework\TestCase;
use src\cartaspresentacion\application\CartasPresentacionListaData;

final class CartasPresentacionListaDataTest extends TestCase
{
    public function test_que_desconocido_devuelve_html_vacio_sin_tocar_container(): void
    {
        $rta = CartasPresentacionListaData::execute(['que' => '']);
        $this->assertSame('', $rta['html_lista']);
        $this->assertSame('', $rta['html_errores']);
    }
}
