<?php

namespace src\usuarios\domain\entity;

use src\usuarios\application\repositories\RoleRepository;
use src\usuarios\domain\value_objects\RoleName;
use src\usuarios\domain\value_objects\PauType;
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
     * @var RoleName|null
     */
    private ?RoleName $srole = null;
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
     * @var PauType|null
     */
    private ?PauType $spau = null;
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
            if ($aDatos['role'] === null) {
                $this->setRole(null);
            } else {
                // Check if it's already a RoleName object
                if ($aDatos['role'] instanceof RoleName) {
                    $this->setRole($aDatos['role']);
                } else {
                    $this->setRole(new RoleName($aDatos['role']));
                }
            }
        }
        if (array_key_exists('sf', $aDatos)) {
            $this->setSf(is_true($aDatos['sf']));
        }
        if (array_key_exists('sv', $aDatos)) {
            $this->setSv(is_true($aDatos['sv']));
        }
        if (array_key_exists('pau', $aDatos)) {
            if ($aDatos['pau'] === null) {
                $this->setPau(null);
            } else {
                // Check if it's already a PauType object
                if ($aDatos['pau'] instanceof PauType) {
                    $this->setPau($aDatos['pau']);
                } else {
                    $this->setPau(new PauType($aDatos['pau']));
                }
            }
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
     * @return RoleName|null
     */
    public function getRole(): ?RoleName
    {
        return $this->srole;
    }

    /**
     *
     * @param RoleName|null $srole
     */
    public function setRole(?RoleName $srole = null): void
    {
        $this->srole = $srole;
    }

    /**
     * Get the role name as a string
     *
     * @return string|null
     */
    public function getRoleAsString(): ?string
    {
        return $this->srole ? $this->srole->value() : null;
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
     * @return PauType|null
     */
    public function getPau(): ?PauType
    {
        return $this->spau;
    }

    /**
     *
     * @param PauType|null $spau
     */
    public function setPau(?PauType $spau = null): void
    {
        $this->spau = $spau;
    }

    /**
     * Get the PAU type as a string
     *
     * @return string|null
     */
    public function getPauAsString(): ?string
    {
        return $this->spau ? $this->spau->value() : null;
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
