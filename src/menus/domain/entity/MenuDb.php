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

    private array|null $orden = null;

    private string|null $menu = null;

    private string|null $parametros = null;

    private int|null $id_metamenu = null;

    private int|null $menu_perm = null;

    private int|null $id_grupmenu = null;

    private bool|null $ok = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_menu(): int
    {
        return $this->id_menu;
    }


    public function setId_menu(int $id_menu): void
    {
        $this->id_menu = $id_menu;
    }


    public function getOrden(): array|null
    {
        return $this->orden;
    }


    public function setOrden(array $orden = null): void
    {
        $this->orden = $orden;
    }


    public function getMenu(): ?string
    {
        return $this->menu;
    }


    public function setMenu(string|MenuName|null $menu = null): void
    {
        $this->menu = $menu instanceof MenuName ? $menu->value() : $menu;
    }


    public function getParametros(): ?string
    {
        return $this->parametros;
    }


    public function setParametros(string|MenuParametros|null $parametros = null): void
    {
        $this->parametros = $parametros instanceof MenuParametros ? $parametros->value() : $parametros;
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