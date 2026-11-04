<?php

namespace Tests\unit\usuarios\domain;

use src\usuarios\domain\PasswordHasher;
use src\usuarios\domain\value_objects\Username;
use Tests\myTest;

class PasswordHasherTest extends myTest
{
    private function valida(string $password): bool
    {
        $r = (new PasswordHasher())->is_valid_password(new Username('pepe'), $password);

        return $r['success'] === true;
    }

    public function test_ocho_caracteres_no_bastan(): void
    {
        $this->assertFalse($this->valida('Ab1$cdef'));
    }

    public function test_nueve_caracteres_con_mayuscula_numero_y_especial(): void
    {
        $this->assertTrue($this->valida('Ab1$cdefg'));
    }

    public function test_exige_mayuscula_numero_y_especial(): void
    {
        $this->assertFalse($this->valida('abcdefg1$'), 'sin mayúscula');
        $this->assertFalse($this->valida('Abcdefgh$'), 'sin número');
        $this->assertFalse($this->valida('Abcdefgh1'), 'sin carácter especial');
    }
}
