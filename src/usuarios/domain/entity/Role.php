<?php

namespace src\usuarios\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\value_objects\RoleName;
use src\usuarios\domain\value_objects\PauType;

class Role
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_role;

    private ?RoleName $role = null;

    private bool $sf;

    private bool $sv;

    private ?PauType $pau = null;

    private bool|null $dmz = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function isRole(string $nom_role): bool
    {
        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $aPau = $RoleRepository->getArrayRoles();

        $nom_role = strtolower($nom_role);
        return !empty($aPau[$this->id_role]) && $aPau[$this->id_role] === $nom_role;
    }

    public function isRolePau(string $nom_pau): bool
    {
        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $aPauRoles = $RoleRepository->getArrayRolesPau();

        $nom_pau = strtolower($nom_pau);
        return !empty($aPauRoles[$this->id_role]) && $aPauRoles[$this->id_role] === $nom_pau;
    }


    public function getId_role(): int
    {
        return $this->id_role;
    }


    public function setId_role(int $id_role): void
    {
        $this->id_role = $id_role;
    }


    public function getRoleVo(): ?RoleName
    {
        return $this->role;
    }


    public function setRoleVo(RoleName|string|null $role = null): void
    {
        if ($role === null) {
            $this->role = null;
            return;
        }

        $this->role = $role instanceof RoleName
            ? $role
            : new RoleName($role);
    }


    public function getRoleAsString(): ?string
    {
        return $this->role?->value();
    }


    public function isSf(): bool
    {
        return $this->sf;
    }


    public function setSf(bool $sf): void
    {
        $this->sf = $sf;
    }


    public function isSv(): bool
    {
        return $this->sv;
    }


    public function setSv(bool $sv): void
    {
        $this->sv = $sv;
    }


    public function getPauVo(): PauType
    {
        // Normalize: never return null; default to PAU_NONE
        if ($this->pau === null) {
            $this->pau = new PauType(PauType::PAU_NONE);
        }
        return $this->pau;
    }

    public function setPauVo(PauType|string|null $pau = null): void
    {
        // Normalize null to PAU_NONE
        if ($pau === null) {
            $this->pau = new PauType(PauType::PAU_NONE);
        } else {
            $this->pau = $pau instanceof PauType
                ? $pau
                : new PauType($pau);
        }
    }


    public function getPauAsString(): string
    {
        return $this->getPauVo()->value();
    }


    public function isDmz(): ?bool
    {
        return $this->dmz;
    }


    public function setDmz(?bool $dmz = null): void
    {
        $this->dmz = $dmz;
    }
}
