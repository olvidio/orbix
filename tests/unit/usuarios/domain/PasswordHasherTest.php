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

    public function test_una_contrasena_nueva_se_guarda_con_un_algoritmo_lento_y_se_verifica(): void
    {
        $h = new PasswordHasher();
        $hash = $h->encode('Clave-Larga.2026!');

        $this->assertStringStartsWith('$argon2id$', $hash);
        $this->assertNotSame($hash, $h->encode('Clave-Larga.2026!'), 'sal distinta cada vez');
        $this->assertTrue($h->verify('Clave-Larga.2026!', $hash));
        $this->assertFalse($h->verify('otra', $hash));
        $this->assertFalse($h->verify('', $hash));
        $this->assertFalse($h->needsRehash($hash));
    }

    public function test_los_hashes_antiguos_siguen_valiendo_y_piden_migracion(): void
    {
        $h = new PasswordHasher();
        // Formato anterior: 200 caracteres de sal + whirlpool(sal . clave).
        $sal = str_repeat('ab', 100);
        $antiguo = $sal . hash('whirlpool', $sal . 'Clave-Larga.2026!');

        $this->assertTrue($h->verify('Clave-Larga.2026!', $antiguo));
        $this->assertFalse($h->verify('otra', $antiguo));
        $this->assertTrue($h->needsRehash($antiguo));
        $this->assertFalse($h->verify('x', ''));
    }
}
