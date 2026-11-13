<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\helpers;

use PHPUnit\Framework\TestCase;
use src\shared\domain\helpers\OfuscarEmail;

final class OfuscarEmailTest extends TestCase
{
    public function test_oculta_parte_del_nombre_y_del_dominio(): void
    {
        $this->assertSame('pep********@ex*****.org', OfuscarEmail::ofuscarEmailParcial('pepe.garcia@example.org', 3, 2));
        $this->assertSame('pep********@e******.co.uk', OfuscarEmail::ofuscarEmailParcial('pepe.garcia@example.co.uk'));
    }

    public function test_nombres_cortos_solo_dejan_la_primera_letra(): void
    {
        $this->assertSame('a**@x*.es', OfuscarEmail::ofuscarEmailParcial('ana@xy.es', 3, 1));
    }

    public function test_sin_arroba_lo_devuelve_tal_cual(): void
    {
        $this->assertSame('', OfuscarEmail::ofuscarEmailParcial(''));
        $this->assertSame('no-es-un-correo', OfuscarEmail::ofuscarEmailParcial('no-es-un-correo'));
    }
}
