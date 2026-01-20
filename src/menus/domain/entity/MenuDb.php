<?php

namespace src\menus\domain\entity;

use src\menus\domain\value_objects\MenuName;
use src\menus\domain\value_objects\MenuParametros;
use src\shared\domain\traits\Hydratable;
use function core\is_true;

class MenuDb
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_menu;

    private ?array $orden = null;

    private ?MenuName $menu = null;

    private ?MenuParametros $parametros = null;

    private ?int $id_metamenu = null;

    private ?int $menu_perm = null;

    private ?int $id_grupmenu = null;

    private ?bool $ok = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_menu(): int
    {
        return $this->id_menu;
    }


    public function setId_menu(int $id_menu): void
    {
        $this->id_menu = $id_menu;
    }


    public function getOrden(): ?array
    {
        return $this->orden;
    }


    public function setOrden(?array $orden = null): void
    {
        $this->orden = $orden;
    }


    public function getMenu(): ?string
    {
        return $this->menu?->value();
    }

    public function getMenuVo(): MenuName
    {
        return $this->menu;
    }

    public function setMenu(?string $menu = null): void
    {
        $this->menu = MenuName::fromNullableString($menu);
    }

    public function setMenuVo(MenuName|string|null $texto): void
    {
        $this->menu = $texto instanceof MenuName
            ? $texto
            : MenuName::fromNullableString($texto);
    }

    public function getParametros(): ?string
    {
        return $this->parametros?->value();
    }

    public function getParametrosVo(): MenuParametros
    {
        return $this->parametros;
    }

    public function setParametros(?string $parametros = null): void
    {
        $this->parametros = MenuParametros::fromNullableString($parametros);
    }

    public function setParametrosVo(MenuParametros|string|null $texto): void
    {
        $this->parametros = $texto instanceof MenuParametros
            ? $texto
            : MenuParametros::fromNullableString($texto);
    }

    public function getId_metamenu(): ?int
    {
        return $this->id_metamenu;
    }


    public function setId_metamenu(?int $id_metamenu = null): void
    {
        $this->id_metamenu = $id_metamenu;
    }


    public function getMenu_perm(): ?int
    {
        return $this->menu_perm;
    }


    public function setMenu_perm(?int $menu_perm = null): void
    {
        $this->menu_perm = $menu_perm;
    }


    public function getId_grupmenu(): ?int
    {
        return $this->id_grupmenu;
    }


    public function setId_grupmenu(?int $id_grupmenu = null): void
    {
        $this->id_grupmenu = $id_grupmenu;
    }


    public function isOk(): ?bool
    {
        return $this->ok;
    }


    public function setOk(?bool $ok = null): void
    {
        $this->ok = $ok;
    }
}