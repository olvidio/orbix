<?php

namespace src\usuarios\domain\entity;
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
     * @var string
     */
    private string $susuario;
    /**
     * Id_role de usuario
     *
     * @var int|null
     */
    private int|null $iid_role = null;
    /**
     * Password de usuario
     *
     * @var string|null
     */
    private $spassword = null;
    /**
     * Email de usuario
     *
     * @var string|null
     */
    private string|null $semail = null;
    /**
     * Id_pau de usuario
     *
     * @var string|null
     */
    private string|null $sid_pau = null;
    /**
     * Nom_usuario de usuario
     *
     * @var string|null
     */
    private string|null $snom_usuario = null;

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
            $this->setUsuario($aDatos['usuario']);
        }
        if (array_key_exists('id_role', $aDatos)) {
            $this->setId_role($aDatos['id_role']);
        }
        if (array_key_exists('password', $aDatos)) {
            $this->setPassword($aDatos['password']);
        }
        if (array_key_exists('email', $aDatos)) {
            $this->setEmail($aDatos['email']);
        }
        if (array_key_exists('id_pau', $aDatos)) {
            $this->setId_pau($aDatos['id_pau']);
        }
        if (array_key_exists('nom_usuario', $aDatos)) {
            $this->setNom_usuario($aDatos['nom_usuario']);
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
     * @return string $susuario
     */
    public function getUsuario(): string
    {
        return $this->susuario;
    }

    /**
     *
     * @param string $susuario
     */
    public function setUsuario(string $susuario): void
    {
        $this->susuario = $susuario;
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
     * @return string|null $spassword
     */
    public function getPassword(): ?string
    {
        return $this->spassword;
    }

    /**
     *
     * @param string|null $spassword
     */
    public function setPassword($spassword = null): void
    {
        $this->spassword = $spassword;
    }

    /**
     *
     * @return string|null $semail
     */
    public function getEmail(): ?string
    {
        return $this->semail;
    }

    /**
     *
     * @param string|null $semail
     */
    public function setEmail(?string $semail = null): void
    {
        $this->semail = $semail;
    }

    /**
     *
     * @return string|null $sid_pau
     */
    public function getId_pau(): ?string
    {
        return $this->sid_pau;
    }

    /**
     *
     * @param string|null $sid_pau
     */
    public function setId_pau(?string $sid_pau = null): void
    {
        $this->sid_pau = $sid_pau;
    }

    /**
     *
     * @return string|null $snom_usuario
     */
    public function getNom_usuario(): ?string
    {
        return $this->snom_usuario;
    }

    /**
     *
     * @param string|null $snom_usuario
     */
    public function setNom_usuario(?string $snom_usuario = null): void
    {
        $this->snom_usuario = $snom_usuario;
    }
}