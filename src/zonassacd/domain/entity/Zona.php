<?php

namespace src\zonassacd\domain\entity;
/**
 * Clase que implementa la entidad zonas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/12/2025
 */
class Zona {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_zona de Zona
	 *
	 * @var int
	 */
	 private int $iid_zona;
	/**
	 * Nombre_zona de Zona
	 *
	 * @var string
	 */
	 private string $snombre_zona;
	/**
	 * Orden de Zona
	 *
	 * @var int|null
	 */
	 private int|null $iorden = null;
	/**
	 * Id_grupo de Zona
	 *
	 * @var int|null
	 */
	 private int|null $iid_grupo = null;
	/**
	 * Id_nom de Zona
	 *
	 * @var int|null
	 */
	 private int|null $iid_nom = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return Zona
	 */
	public function setAllAttributes(array $aDatos): Zona
	{
		if (array_key_exists('id_zona',$aDatos))
		{
			$this->setId_zona($aDatos['id_zona']);
		}
		if (array_key_exists('nombre_zona',$aDatos))
		{
			$this->setNombre_zona($aDatos['nombre_zona']);
		}
		if (array_key_exists('orden',$aDatos))
		{
			$this->setOrden($aDatos['orden']);
		}
		if (array_key_exists('id_grupo',$aDatos))
		{
			$this->setId_grupo($aDatos['id_grupo']);
		}
		if (array_key_exists('id_nom',$aDatos))
		{
			$this->setId_nom($aDatos['id_nom']);
		}
		return $this;
	}
	/**
	 *
	 * @return int $iid_zona
	 */
	public function getId_zona(): int
	{
		return $this->iid_zona;
	}
	/**
	 *
	 * @param int $iid_zona
	 */
	public function setId_zona(int $iid_zona): void
	{
		$this->iid_zona = $iid_zona;
	}
	/**
	 *
	 * @return string $snombre_zona
	 */
	public function getNombre_zona(): string
	{
		return $this->snombre_zona;
	}
	/**
	 *
	 * @param string $snombre_zona
	 */
	public function setNombre_zona(string $snombre_zona): void
	{
		$this->snombre_zona = $snombre_zona;
	}
	/**
	 *
	 * @return int|null $iorden
	 */
	public function getOrden(): ?int
	{
		return $this->iorden;
	}
	/**
	 *
	 * @param int|null $iorden
	 */
	public function setOrden(?int $iorden = null): void
	{
		$this->iorden = $iorden;
	}
	/**
	 *
	 * @return int|null $iid_grupo
	 */
	public function getId_grupo(): ?int
	{
		return $this->iid_grupo;
	}
	/**
	 *
	 * @param int|null $iid_grupo
	 */
	public function setId_grupo(?int $iid_grupo = null): void
	{
		$this->iid_grupo = $iid_grupo;
	}
	/**
	 *
	 * @return int|null $iid_nom
	 */
	public function getId_nom(): ?int
	{
		return $this->iid_nom;
	}
	/**
	 *
	 * @param int|null $iid_nom
	 */
	public function setId_nom(?int $iid_nom = null): void
	{
		$this->iid_nom = $iid_nom;
	}
}