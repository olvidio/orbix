<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application\support;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\support\MatriculaNotaEstado;

final class MatriculaNotaEstadoTest extends TestCase
{
    public function test_sin_nota_siempre_editable(): void
    {
        $firmadas = ['dlb 1/26' => true];
        $this->assertTrue(MatriculaNotaEstado::editable('dlb 1/26', null, $firmadas));
        $this->assertTrue(MatriculaNotaEstado::editable('dlb 1/26', '', $firmadas));
        $this->assertTrue(MatriculaNotaEstado::editable('', null, $firmadas));
    }

    public function test_con_nota_en_acta_firmada_no_editable(): void
    {
        $firmadas = ['dlb 1/26' => true];
        $this->assertFalse(MatriculaNotaEstado::editable('dlb 1/26', '8', $firmadas));
    }

    public function test_con_nota_en_acta_no_firmada_editable(): void
    {
        $this->assertTrue(MatriculaNotaEstado::editable('dlb 2/26', '8', ['dlb 1/26' => true]));
        $this->assertTrue(MatriculaNotaEstado::editable('', '8', ['dlb 1/26' => true]));
    }

    public function test_tiene_nota(): void
    {
        $this->assertFalse(MatriculaNotaEstado::tieneNota(null));
        $this->assertFalse(MatriculaNotaEstado::tieneNota(''));
        $this->assertFalse(MatriculaNotaEstado::tieneNota('  '));
        $this->assertTrue(MatriculaNotaEstado::tieneNota('0'));
        $this->assertTrue(MatriculaNotaEstado::tieneNota('7.5'));
    }
}
