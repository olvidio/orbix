<?php

namespace Tests\unit\usuarios\domain\entity;

use src\usuarios\domain\entity\Usuario;
use src\usuarios\domain\value_objects\Username;
use src\usuarios\domain\value_objects\Email;
use src\usuarios\domain\value_objects\Password;
use src\usuarios\domain\value_objects\IdPau;
use src\usuarios\domain\value_objects\NombreUsuario;
use src\usuarios\domain\value_objects\Secret2FA;
use Tests\myTest;

class UsuarioTest extends myTest
{
    private Usuario $usuario;

    public function setUp(): void
    {
        parent::setUp();
        $this->usuario = new Usuario();
        $this->usuario->setId_usuario(1);
        $this->usuario->setUsuario(new Username('testuser'));
        $this->usuario->setId_role(2);
    }

    public function test_get_id_usuario()
    {
        $this->assertEquals(1, $this->usuario->getId_usuario());
    }

    public function test_get_usuario()
    {
        $this->assertInstanceOf(Username::class, $this->usuario->getUsuario());
        $this->assertEquals('testuser', $this->usuario->getUsuarioAsString());
    }

    public function test_get_id_role()
    {
        $this->assertEquals(2, $this->usuario->getId_role());
    }

    public function test_set_and_get_password()
    {
        $password = new Password('securepassword');
        $this->usuario->setPassword($password);
        $this->assertInstanceOf(Password::class, $this->usuario->getPassword());
        $this->assertEquals('securepassword', $this->usuario->getPasswordAsString());
    }

    public function test_set_and_get_email()
    {
        $email = new Email('test@example.com');
        $this->usuario->setEmail($email);
        $this->assertInstanceOf(Email::class, $this->usuario->getEmail());
        $this->assertEquals('test@example.com', $this->usuario->getEmailAsString());
    }

    public function test_set_and_get_id_pau()
    {
        $idPau = new IdPau('12345');
        $this->usuario->setId_pau($idPau);
        $this->assertInstanceOf(IdPau::class, $this->usuario->getId_pau());
        $this->assertEquals('12345', $this->usuario->getId_pauAsString());
    }

    public function test_set_and_get_nom_usuario()
    {
        $nombreUsuario = new NombreUsuario('John Doe');
        $this->usuario->setNom_usuario($nombreUsuario);
        $this->assertInstanceOf(NombreUsuario::class, $this->usuario->getNom_usuario());
        $this->assertEquals('John Doe', $this->usuario->getNom_usuarioAsString());
    }

    public function test_set_and_get_has_2fa()
    {
        $this->usuario->setHas2fa(true);
        $this->assertTrue($this->usuario->has2fa());
    }

    public function test_set_and_get_secret_2fa()
    {
        $secret2fa = new Secret2FA('ABCDEFGHIJKLMNOP');
        $this->usuario->setSecret2fa($secret2fa);
        $this->assertInstanceOf(Secret2FA::class, $this->usuario->getSecret2fa());
        $this->assertEquals('ABCDEFGHIJKLMNOP', $this->usuario->getSecret2faAsString());
    }

    public function test_set_and_get_cambio_password()
    {
        $this->usuario->setCambio_password(true);
        $this->assertTrue($this->usuario->isCambio_password());
    }

    public function test_set_all_attributes()
    {
        $usuario = new Usuario();
        $attributes = [
            'id_usuario' => 1,
            'usuario' => new Username('testuser'),
            'id_role' => 2,
            'password' => new Password('securepassword'),
            'email' => new Email('test@example.com'),
            'id_pau' => new IdPau('12345'),
            'nom_usuario' => new NombreUsuario('John Doe'),
            'has_2fa' => true,
            'secret_2fa' => new Secret2FA('ABCDEFGHIJKLMNOP'),
            'cambio_password' => true
        ];
        $usuario->setAllAttributes($attributes);

        $this->assertEquals(1, $usuario->getId_usuario());
        $this->assertEquals('testuser', $usuario->getUsuarioAsString());
        $this->assertEquals(2, $usuario->getId_role());
        $this->assertEquals('securepassword', $usuario->getPasswordAsString());
        $this->assertEquals('test@example.com', $usuario->getEmailAsString());
        $this->assertEquals('12345', $usuario->getId_pauAsString());
        $this->assertEquals('John Doe', $usuario->getNom_usuarioAsString());
        $this->assertTrue($usuario->has2fa());
        $this->assertEquals('ABCDEFGHIJKLMNOP', $usuario->getSecret2faAsString());
        $this->assertTrue($usuario->isCambio_password());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $usuario = new Usuario();
        $attributes = [
            'id_usuario' => 1,
            'usuario' => 'testuser',
            'id_role' => 2,
            'password' => 'securepassword',
            'email' => 'test@example.com',
            'id_pau' => '12345',
            'nom_usuario' => 'John Doe',
            'has_2fa' => true,
            'secret_2fa' => 'ABCDEFGHIJKLMNOP',
            'cambio_password' => true
        ];
        $usuario->setAllAttributes($attributes);

        $this->assertEquals(1, $usuario->getId_usuario());
        $this->assertEquals('testuser', $usuario->getUsuarioAsString());
        $this->assertEquals(2, $usuario->getId_role());
        $this->assertEquals('securepassword', $usuario->getPasswordAsString());
        $this->assertEquals('test@example.com', $usuario->getEmailAsString());
        $this->assertEquals('12345', $usuario->getId_pauAsString());
        $this->assertEquals('John Doe', $usuario->getNom_usuarioAsString());
        $this->assertTrue($usuario->has2fa());
        $this->assertEquals('ABCDEFGHIJKLMNOP', $usuario->getSecret2faAsString());
        $this->assertTrue($usuario->isCambio_password());
    }
}