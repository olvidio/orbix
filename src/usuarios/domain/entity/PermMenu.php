<?php

namespace src\usuarios\domain\entity;
/**
 * Clase que implementa la entidad aux_grupo_permmenu
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class PermMenu {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_item de PermMenu
	 *
	 * @var int
	 */
	 private int $iid_item;
	/**
	 * Id_usuario de PermMenu
	 *
	 * @var int
	 */
	 private int $iid_usuario;
	/**
	 * Menu_perm de PermMenu
	 *
	 * @var int|null
	 */
	 private int|null $imenu_perm = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return PermMenu
	 */
	public function setAllAttributes(array $aDatos): PermMenu
	{
		if (array_key_exists('id_item',$aDatos))
		{
			$this->setId_item($aDatos['id_item']);
		}
		if (array_key_exists('id_usuario',$aDatos))
		{
			$this->setId_usuario($aDatos['id_usuario']);
		}
		if (array_key_exists('menu_perm',$aDatos))
		{
			$this->setMenu_perm($aDatos['menu_perm']);
		}
		return $this;
	}
	/**
	 *
	 * @return int $iid_item
	 */
	public function getId_item(): int
	{
		return $this->iid_item;
	}
	/**
	 *
	 * @param int $iid_item
	 */
	public function setId_item(int $iid_item): void
	{
		$this->iid_item = $iid_item;
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
}