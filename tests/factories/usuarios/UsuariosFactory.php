<?php

namespace Tests\factories\usuarios;

use Faker\Factory;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\domain\value_objects\Username;
use src\usuarios\domain\value_objects\Email;
use src\usuarios\domain\value_objects\Password;
use src\usuarios\domain\value_objects\IdPau;
use src\usuarios\domain\value_objects\NombreUsuario;
use src\usuarios\domain\value_objects\Secret2FA;

class UsuariosFactory
{
    private int $count = 1;

    public function __construct()
    {
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function create(int $id_usuario): array
    {
        return $this->crear_Usuarios($id_usuario);
    }

    public function crear_Usuarios(int $id_usuario): array
    {
        $faker = Factory::create();

        $count = $this->getCount() ?? 1;

        $cUsuarios = [];
        for ($i = 0; $i < $count; $i++) {
            $oUsuario = new Usuario();
            $oUsuario->setId_usuario($id_usuario + $i);

            // Generar un username de máximo 20 caracteres
            $username = substr($faker->userName, 0, 20);
            $oUsuario->setUsuarioVo(new Username($username));

            $oUsuario->setId_role($faker->numberBetween(1, 10));
            $oUsuario->setPasswordVo(new Password($faker->password));
            $oUsuario->setEmailVo(new Email($faker->email));
            $oUsuario->setCsvIdPauVo(new IdPau($faker->numerify('pau####')));
            $oUsuario->setNomUsuarioVo(new NombreUsuario($faker->name));
            $oUsuario->setHas_2fa($faker->boolean(30)); // 30% chance of having 2FA

            if ($oUsuario->isHas_2fa()) {
                $oUsuario->setSecret2faVo(new Secret2FA($faker->bothify('????####????####')));
            }

            $oUsuario->setCambio_password($faker->boolean(20)); // 20% chance of needing password change

            $cUsuarios[] = $oUsuario;
        }

        return $cUsuarios;
    }

    public function createSimple(int $id_usuario, string $username = 'testuser'): Usuario
    {
        $oUsuario = new Usuario();
        $oUsuario->setId_usuario($id_usuario);
        $oUsuario->setUsuarioVo(new Username($username));
        $oUsuario->setId_role(1);
        $oUsuario->setPasswordVo(new Password('testpassword'));
        $oUsuario->setEmailVo(new Email('test@example.com'));
        $oUsuario->setNomUsuarioVo(new NombreUsuario('Test User'));
        $oUsuario->setHas_2fa(false);
        $oUsuario->setCambio_password(false);

        return $oUsuario;
    }
}
