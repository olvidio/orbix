<?php

namespace src\menus\domain\entity;
/**
 * Clase que implementa la entidad aux_grupmenu_rol
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class GrupMenuRole {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_item de GrupMenuRole
	 *
	 * @var int
	 */
	 private int $iid_item;
	/**
	 * Id_grupmenu de GrupMenuRole
	 *
	 * @var int|null
	 */
	 private int|null $iid_grupmenu = null;
	/**
	 * Id_role de GrupMenuRole
	 *
	 * @var int|null
	 */
	 private int|null $iid_role = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return GrupMenuRole
	 */
	public function setAllAttributes(array $aDatos): GrupMenuRole
	{
		if (array_key_exists('id_item',$aDatos))
		{
			$this->setId_item($aDatos['id_item']);
		}
		if (array_key_exists('id_grupmenu',$aDatos))
		{
			$this->setId_grupmenu($aDatos['id_grupmenu']);
		}
		if (array_key_exists('id_role',$aDatos))
		{
			$this->setId_role($aDatos['id_role']);
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