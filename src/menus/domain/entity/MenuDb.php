<?php

namespace src\menus\domain\entity;
	use function core\is_true;
/**
 * Clase que implementa la entidad aux_menus
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class MenuDb {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_menu de MenuDb
	 *
	 * @var int
	 */
	 private int $iid_menu;
	/**
	 * Orden de MenuDb
	 *
	 * @var array|null
	 */
	 private array|null $a_orden = null;
	/**
	 * Menu de MenuDb
	 *
	 * @var string|null
	 */
	 private string|null $smenu = null;
	/**
	 * Parametros de MenuDb
	 *
	 * @var string|null
	 */
	 private string|null $sparametros = null;
	/**
	 * Id_metamenu de MenuDb
	 *
	 * @var int|null
	 */
	 private int|null $iid_metamenu = null;
	/**
	 * Menu_perm de MenuDb
	 *
	 * @var int|null
	 */
	 private int|null $imenu_perm = null;
	/**
	 * Id_grupmenu de MenuDb
	 *
	 * @var int|null
	 */
	 private int|null $iid_grupmenu = null;
	/**
	 * Ok de MenuDb
	 *
	 * @var bool|null
	 */
	 private bool|null $bok = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return MenuDb
	 */
	public function setAllAttributes(array $aDatos): MenuDb
	{
		if (array_key_exists('id_menu',$aDatos))
		{
			$this->setId_menu($aDatos['id_menu']);
		}
		if (array_key_exists('orden',$aDatos))
		{
			$this->setOrden($aDatos['orden']);
		}
		if (array_key_exists('menu',$aDatos))
		{
			$this->setMenu($aDatos['menu']);
		}
		if (array_key_exists('parametros',$aDatos))
		{
			$this->setParametros($aDatos['parametros']);
		}
		if (array_key_exists('id_metamenu',$aDatos))
		{
			$this->setId_metamenu($aDatos['id_metamenu']);
		}
		if (array_key_exists('menu_perm',$aDatos))
		{
			$this->setMenu_perm($aDatos['menu_perm']);
		}
		if (array_key_exists('id_grupmenu',$aDatos))
		{
			$this->setId_grupmenu($aDatos['id_grupmenu']);
		}
		if (array_key_exists('ok',$aDatos))
		{
			$this->setOk(is_true($aDatos['ok']));
		}
		return $this;
	}
	/**
	 *
	 * @return int $iid_menu
	 */
	public function getId_menu(): int
	{
		return $this->iid_menu;
	}
	/**
	 *
	 * @param int $iid_menu
	 */
	public function setId_menu(int $iid_menu): void
	{
		$this->iid_menu = $iid_menu;
	}
	/**
	 *
	 * @return array|null $a_orden
	 */
	public function getOrden(): array|null
	{
        return $this->a_orden;
	}
	/**
	 * 
	 * @param array|null $a_orden
	 */
	public function setOrden(array $a_orden= null): void
	{
        $this->a_orden = $a_orden;
	}
	/**
	 *
	 * @return string|null $smenu
	 */
	public function getMenu(): ?string
	{
		return $this->smenu;
	}
	/**
	 *
	 * @param string|null $smenu
	 */
	public function setMenu(?string $smenu = null): void
	{
		$this->smenu = $smenu;
	}
	/**
	 *
	 * @return string|null $sparametros
	 */
	public function getParametros(): ?string
	{
		return $this->sparametros;
	}
	/**
	 *
	 * @param string|null $sparametros
	 */
	public function setParametros(?string $sparametros = null): void
	{
		$this->sparametros = $sparametros;
	}
	/**
	 *
	 * @return int|null $iid_metamenu
	 */
	public function getId_metamenu(): ?int
	{
		return $this->iid_metamenu;
	}
	/**
	 *
	 * @param int|null $iid_metamenu
	 */
	public function setId_metamenu(?int $iid_metamenu = null): void
	{
		$this->iid_metamenu = $iid_metamenu;
	}
	/**
	 *
	 * @return int|null $imenu_perm
	 */
	public function getMenu_perm(): ?int
	{
		return $this->imenu_perm;
	}
	/**
	 *
	 * @param int|null $imenu_perm
	 */
	public function setMenu_perm(?int $imenu_perm = null): void
	{
		$this->imenu_perm = $imenu_perm;
	}
	/**
	 *
	 * @return int|null $iid_grupmenu
	 */
	public function getId_grupmenu(): ?int
	{
		return $this->iid_grupmenu;
	}
	/**
	 *
	 * @param int|null $iid_grupmenu
	 */
	public function setId_grupmenu(?int $iid_grupmenu = null): void
	{
		$this->iid_grupmenu = $iid_grupmenu;
	}
	/**
	 *
	 * @return bool|null $bok
	 */
	public function isOk(): ?bool
	{
		return $this->bok;
	}
	/**
	 *
	 * @param bool|null $bok
	 */
	public function setOk(?bool $bok = null): void
	{
		$this->bok = $bok;
	}
}