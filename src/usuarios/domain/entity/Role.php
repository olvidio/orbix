<?php

namespace src\usuarios\domain\entity;

use src\usuarios\application\repositories\RoleRepository;
use function core\is_true;

/**
 * Clase que implementa la entidad aux_roles
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class Role
{

    // pau constants.
    const PAU_CDC = 'cdc'; // Casa.
    const PAU_CTR = 'ctr'; // Centro.
    const PAU_NOM = 'nom'; // Persona.
    const PAU_SACD = 'sacd'; // Sacd.

    const ARRAY_PAU_TXT = [
        self::PAU_CDC => 'cdc',
        self::PAU_CTR => 'ctr',
        self::PAU_NOM => 'nom',
        self::PAU_SACD => 'sacd',
    ];

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_role de Role
     *
     * @var int
     */
    private int $iid_role;
    /**
     * Role de Role
     *
     * @var string|null
     */
    private string|null $srole = null;
    /**
     * Sf de Role
     *
     * @var bool
     */
    private bool $bsf;
    /**
     * Sv de Role
     *
     * @var bool
     */
    private bool $bsv;
    /**
     * Pau de Role
     *
     * @var string|null
     */
    private string|null $spau = null;
    /**
     * Dmz de Role
     *
     * @var bool|null
     */
    private bool|null $bdmz = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function isRole(string $nom_role): bool
    {
        $RoleRepository = new RoleRepository();
        $aPau = $RoleRepository->getArrayRoles();

        $nom_role = strtolower($nom_role ?? '');
        return !empty($aPau[$this->iid_role]) && $aPau[$this->iid_role] === $nom_role;
    }

    public function isRolePau(string $nom_pau): bool
    {
        $RoleRepository = new RoleRepository();
        $aPauRoles = $RoleRepository->getArrayRolesPau();

        $nom_pau = strtolower($nom_pau ?? '');
        return !empty($aPauRoles[$this->iid_role]) && $aPauRoles[$this->iid_role] === $nom_pau;
    }

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Role
     */
    public function setAllAttributes(array $aDatos): Role
    {
        if (array_key_exists('id_role', $aDatos)) {
            $this->setId_role($aDatos['id_role']);
        }
        if (array_key_exists('role', $aDatos)) {
            $this->setRole($aDatos['role']);
        }
        if (array_key_exists('sf', $aDatos)) {
            $this->setSf(is_true($aDatos['sf']));
        }
        if (array_key_exists('sv', $aDatos)) {
            $this->setSv(is_true($aDatos['sv']));
        }
        if (array_key_exists('pau', $aDatos)) {
            $this->setPau($aDatos['pau']);
        }
        if (array_key_exists('dmz', $aDatos)) {
            $this->setDmz(is_true($aDatos['dmz']));
        }
        return $this;
    }

    /**
     *
     * @return int $iid_role
     */
    public function getId_role(): int
    {
        return $this->iid_role;
    }

    /**
     *
     * @param int $iid_role
     */
    public function setId_role(int $iid_role): void
    {
        $this->iid_role = $iid_role;
    }

    /**
     *
     * @return string|null $srole
     */
    public function getRole(): ?string
    {
        return $this->srole;
    }

    /**
     *
     * @param string|null $srole
     */
    public function setRole(?string $srole = null): void
    {
        $this->srole = $srole;
    }

    /**
     *
     * @return bool $bsf
     */
    public function isSf(): bool
    {
        return $this->bsf;
    }

    /**
     *
     * @param bool $bsf
     */
    public function setSf(bool $bsf): void
    {
        $this->bsf = $bsf;
    }

    /**
     *
     * @return bool $bsv
     */
    public function isSv(): bool
    {
        return $this->bsv;
    }

    /**
     *
     * @param bool $bsv
     */
    public function setSv(bool $bsv): void
    {
        $this->bsv = $bsv;
    }

    /**
     *
     * @return string|null $spau
     */
    public function getPau(): ?string
    {
        return $this->spau;
    }

    /**
     *
     * @param string|null $spau
     */
    public function setPau(?string $spau = null): void
    {
        $this->spau = $spau;
    }

    /**
     *
     * @return bool|null $bdmz
     */
    public function isDmz(): ?bool
    {
        return $this->bdmz;
    }

    /**
     *
     * @param bool|null $bdmz
     */
    public function setDmz(?bool $bdmz = null): void
    {
        $this->bdmz = $bdmz;
    }
}