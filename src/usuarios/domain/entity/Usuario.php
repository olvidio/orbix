<?php

namespace src\usuarios\domain\entity;

use src\usuarios\domain\value_objects\Username;
use src\usuarios\domain\value_objects\Email;
use src\usuarios\domain\value_objects\Password;
use src\usuarios\domain\value_objects\IdPau;
use src\usuarios\domain\value_objects\NombreUsuario;
use src\usuarios\domain\value_objects\Secret2FA;

/**
 * Clase que implementa la entidad aux_usuarios
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class Usuario
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_usuario de usuario
     *
     * @var int
     */
    private int $iid_usuario;
    /**
     * Usuario de usuario
     *
     * @var Username
     */
    private Username $susuario;
    /**
     * Id_role de usuario
     *
     * @var int|null
     */
    private int|null $iid_role = null;
    /**
     * Password de usuario
     *
     * @var Password|null
     */
    private ?Password $spassword = null;
    /**
     * Email de usuario
     *
     * @var Email|null
     */
    private ?Email $semail = null;
    /**
     * Id_pau de usuario
     *
     * @var IdPau|null
     */
    private ?IdPau $sid_pau = null;
    /**
     * Nom_usuario de usuario
     *
     * @var NombreUsuario|null
     */
    private ?NombreUsuario $snom_usuario = null;
    /**
     * Indica si el usuario tiene habilitada la autenticación de dos factores
     *
     * @var bool
     */
    private bool $bhas_2fa = false;
    /**
     * Clave secreta para la autenticación de dos factores
     *
     * @var Secret2FA|null
     */
    private ?Secret2FA $ssecret_2fa = null;
    /**
     * Indica si el usuario debe cambiar el password
     *
     * @var bool
     */
    private bool $bcambio_password = false;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Usuario
     */
    public function setAllAttributes(array $aDatos): Usuario
    {
        if (array_key_exists('id_usuario', $aDatos)) {
            $this->setId_usuario($aDatos['id_usuario']);
        }
        if (array_key_exists('usuario', $aDatos)) {
            // Check if it's already a Username object
            if ($aDatos['usuario'] instanceof Username) {
                $this->setUsuario($aDatos['usuario']);
            } else {
                $this->setUsuario(new Username($aDatos['usuario']));
            }
        }
        if (array_key_exists('id_role', $aDatos)) {
            $this->setId_role($aDatos['id_role']);
        }
        if (array_key_exists('password', $aDatos) && !empty($aDatos['password'])) {
            if ($aDatos['password'] === null) {
                $this->setPassword(null);
            } else {
                // Check if it's already a Password object
                if ($aDatos['password'] instanceof Password) {
                    $this->setPassword($aDatos['password']);
                } else {
                    $this->setPassword(new Password($aDatos['password']));
                }
            }
        }
        if (array_key_exists('email', $aDatos)) {
            if ($aDatos['email'] === null) {
                $this->setEmail(null);
            } else {
                // Check if it's already an Email object
                if ($aDatos['email'] instanceof Email) {
                    $this->setEmail($aDatos['email']);
                } else {
                    $this->setEmail(new Email($aDatos['email']));
                }
            }
        }
        if (array_key_exists('id_pau', $aDatos)) {
            if ($aDatos['id_pau'] === null) {
                $this->setId_pau(null);
            } else {
                // Check if it's already an IdPau object
                if ($aDatos['id_pau'] instanceof IdPau) {
                    $this->setId_pau($aDatos['id_pau']);
                } else {
                    $this->setId_pau(new IdPau($aDatos['id_pau']));
                }
            }
        }
        if (array_key_exists('nom_usuario', $aDatos)) {
            if ($aDatos['nom_usuario'] === null) {
                $this->setNom_usuario(null);
            } else {
                // Check if it's already a NombreUsuario object
                if ($aDatos['nom_usuario'] instanceof NombreUsuario) {
                    $this->setNom_usuario($aDatos['nom_usuario']);
                } else {
                    $this->setNom_usuario(new NombreUsuario($aDatos['nom_usuario']));
                }
            }
        }
        if (array_key_exists('has_2fa', $aDatos)) {
            $this->setHas2fa($aDatos['has_2fa']);
        }
        if (array_key_exists('secret_2fa', $aDatos)) {
            if ($aDatos['secret_2fa'] === null) {
                $this->setSecret2fa(null);
            } else {
                // Check if it's already a Secret2FA object
                if ($aDatos['secret_2fa'] instanceof Secret2FA) {
                    $this->setSecret2fa($aDatos['secret_2fa']);
                } else {
                    $this->setSecret2fa(new Secret2FA($aDatos['secret_2fa']));
                }
            }
        }
        if (array_key_exists('cambio_password', $aDatos)) {
            $this->setCambio_password($aDatos['cambio_password']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_usuario
     */
    public function getId_usuario(): int
    {
        return $this->iid_usuario;
    }

    /**
     *
     * @param int $iid_usuario
     */
    public function setId_usuario(int $iid_usuario): void
    {
        $this->iid_usuario = $iid_usuario;
    }

    /**
     *
     * @return Username
     */
    public function getUsuario(): Username
    {
        return $this->susuario;
    }

    /**
     *
     * @param Username $susuario
     */
    public function setUsuario(Username $susuario): void
    {
        $this->susuario = $susuario;
    }

    /**
     * Get the username as a string
     *
     * @return string
     */
    public function getUsuarioAsString(): string
    {
        return $this->susuario->value();
    }

    /**
     *
     * @return int|null $iid_role
     */
    public function getId_role(): ?int
    {
        return $this->iid_role;
    }

    /**
     *
     * @param int|null $iid_role
     */
    public function setId_role(?int $iid_role = null): void
    {
        $this->iid_role = $iid_role;
    }

    /**
     *
     * @return Password|null
     */
    public function getPassword(): ?Password
    {
        return $this->spassword;
    }

    /**
     *
     * @param Password|null $spassword
     */
    public function setPassword(?Password $spassword = null): void
    {
        $this->spassword = $spassword;
    }

    /**
     * Get the password as a string
     *
     * @return string|null
     */
    public function getPasswordAsString(): ?string
    {
        return $this->spassword ? $this->spassword->value() : null;
    }

    /**
     *
     * @return Email|null
     */
    public function getEmail(): ?Email
    {
        return $this->semail;
    }

    /**
     *
     * @param Email|null $semail
     */
    public function setEmail(?Email $semail = null): void
    {
        $this->semail = $semail;
    }

    /**
     * Get the email as a string
     *
     * @return string|null
     */
    public function getEmailAsString(): ?string
    {
        return $this->semail ? $this->semail->value() : null;
    }

    /**
     *
     * @return IdPau|null
     */
    public function getId_pau(): ?IdPau
    {
        return $this->sid_pau;
    }

    /**
     *
     * @param IdPau|null $sid_pau
     */
    public function setId_pau(?IdPau $sid_pau = null): void
    {
        $this->sid_pau = $sid_pau;
    }

    /**
     * Get the PAU identifier as a string
     *
     * @return string|null
     */
    public function getId_pauAsString(): ?string
    {
        return $this->sid_pau ? $this->sid_pau->value() : null;
    }

    /**
     *
     * @return NombreUsuario|null
     */
    public function getNom_usuario(): ?NombreUsuario
    {
        return $this->snom_usuario;
    }

    /**
     *
     * @param NombreUsuario|null $snom_usuario
     */
    public function setNom_usuario(?NombreUsuario $snom_usuario = null): void
    {
        $this->snom_usuario = $snom_usuario;
    }

    /**
     * Get the user name as a string
     *
     * @return string|null
     */
    public function getNom_usuarioAsString(): ?string
    {
        return $this->snom_usuario ? $this->snom_usuario->value() : null;
    }

    /**
     * Obtiene si el usuario tiene habilitada la autenticación de dos factores
     *
     * @return bool
     */
    public function has2fa(): bool
    {
        return $this->bhas_2fa;
    }

    /**
     * Establece si el usuario tiene habilitada la autenticación de dos factores
     *
     * @param bool $bhas_2fa
     */
    public function setHas2fa(bool $bhas_2fa): void
    {
        $this->bhas_2fa = $bhas_2fa;
    }

    /**
     * Obtiene la clave secreta para la autenticación de dos factores
     *
     * @return Secret2FA|null
     */
    public function getSecret2fa(): ?Secret2FA
    {
        return $this->ssecret_2fa;
    }

    /**
     * Establece la clave secreta para la autenticación de dos factores
     *
     * @param Secret2FA|null $ssecret_2fa
     */
    public function setSecret2fa(?Secret2FA $ssecret_2fa = null): void
    {
        $this->ssecret_2fa = $ssecret_2fa;
    }

    /**
     * Get the 2FA secret as a string
     *
     * @return string|null
     */
    public function getSecret2faAsString(): ?string
    {
        return $this->ssecret_2fa ? $this->ssecret_2fa->value() : null;
    }

    public function isCambio_password(): bool
    {
        return $this->bcambio_password;
    }

    public function setCambio_password(bool $bcambio_password): void
    {
        $this->bcambio_password = $bcambio_password;
    }
}
