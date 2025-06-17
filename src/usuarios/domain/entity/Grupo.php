<?php

namespace src\usuarios\domain\entity;

use src\usuarios\domain\value_objects\Username;

/**
 * Clase que implementa la entidad aux_grupos_y_usuarios
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class Grupo
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_usuario de Grupo
     *
     * @var int
     */
    private int $iid_usuario;
    /**
     * Usuario de Grupo
     *
     * @var UserName
     */
    private UserName $susuario;
    /**
     * Id_role de Grupo
     *
     * @var int|null
     */
    private int|null $iid_role = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Grupo
     */
    public function setAllAttributes(array $aDatos): Grupo
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
}