<?php

namespace src\usuarios\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\usuarios\domain\value_objects\Username;
use src\usuarios\domain\value_objects\Email;
use src\usuarios\domain\value_objects\Password;
use src\usuarios\domain\value_objects\IdPau;
use src\usuarios\domain\value_objects\NombreUsuario;
use src\usuarios\domain\value_objects\Secret2FA;


class Usuario
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_usuario;

    private Username $usuario;

    private ?int $id_role = null;

    private ?Password $password = null;

    private ?Email $email = null;

    private ?IdPau $csv_id_pau = null;

    private ?NombreUsuario $nom_usuario = null;

    private bool $has_2fa = false;

    private ?Secret2FA $secret_2fa = null;

    private bool $cambio_password = false;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    public function getId_usuario(): int
    {
        return $this->id_usuario;
    }


    public function setId_usuario(int $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }


    public function getUsuarioVo(): Username
    {
        return $this->usuario;
    }


    public function setUsuarioVo(Username|string $usuario): void
    {
        $this->usuario = $usuario instanceof Username ? $usuario : new Username($usuario);
    }


    public function getUsuarioAsString(): string
    {
        return $this->usuario->value();
    }


    public function getId_role(): ?int
    {
        return $this->id_role;
    }


    public function setId_role(?int $id_role = null): void
    {
        $this->id_role = $id_role;
    }


    public function getPasswordVo(): ?Password
    {
        return $this->password;
    }


    public function setPasswordVo(Password|string|null $password = null): void
    {
        if ($password === null) {
            $this->password = null;
            return;
        }

        $this->password = $password instanceof Password
            ? $password
            : new Password($password);
    }


    public function getPasswordAsString(): ?string
    {
        return $this->password?->value();
    }


    public function getEmailVo(): ?Email
    {
        return $this->email;
    }


    public function setEmailVo(Email|string|null $email = null): void
    {
        if ($email === null) {
            $this->email = null;
            return;
        }

        $this->email = $email instanceof Email
            ? $email
            : new Email($email);
    }


    public function getEmailAsString(): ?string
    {
        return $this->email?->value();
    }


    public function getCsvIdPauVo(): ?IdPau
    {
        return $this->csv_id_pau;
    }


    public function setCsvIdPauVo(IdPau|string|null $csv_id_pau = null): void
    {
        if ($csv_id_pau === null) {
            $this->csv_id_pau = null;
            return;
        }

        $this->csv_id_pau = $csv_id_pau instanceof IdPau
            ? $csv_id_pau
            : new IdPau($csv_id_pau);
    }


    public function getCsvIdPauAsString(): ?string
    {
        return $this->csv_id_pau?->value();
    }


    public function getNomUsuarioVo(): ?NombreUsuario
    {
        return $this->nom_usuario;
    }


    public function setNomUsuarioVo(NombreUsuario|string|null $nom_usuario = null): void
    {
        if ($nom_usuario === null) {
            $this->nom_usuario = null;
            return;
        }

        $this->nom_usuario = $nom_usuario instanceof NombreUsuario
            ? $nom_usuario
            : new NombreUsuario($nom_usuario);
    }


    public function getNomUsuarioAsString(): ?string
    {
        return $this->nom_usuario?->value();
    }


    public function isHas_2fa(): bool
    {
        return $this->has_2fa;
    }


    public function setHas_2fa(bool $has_2fa): void
    {
        $this->has_2fa = $has_2fa;
    }


    public function getSecret2faVo(): ?Secret2FA
    {
        return $this->secret_2fa;
    }


    public function setSecret2faVo(Secret2FA|string|null $secret_2fa = null): void
    {
        if ($secret_2fa === null) {
            $this->secret_2fa = null;
            return;
        }

        $this->secret_2fa = $secret_2fa instanceof Secret2FA
            ? $secret_2fa
            : new Secret2FA($secret_2fa);
    }


    public function getSecret2faAsString(): ?string
    {
        return $this->secret_2fa?->value();
    }

    public function isCambio_password(): bool
    {
        return $this->cambio_password;
    }

    public function setCambio_password(bool $cambio_password): void
    {
        $this->cambio_password = $cambio_password;
    }
}
