<?php

namespace src\zonassacd\domain\entity;
/**
 * Clase que implementa la entidad zonas_grupos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/12/2025
 */
class ZonaGrupo {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_grupo de ZonaGrupo
	 *
	 * @var int
	 */
	 private int $iid_grupo;
	/**
	 * Nombre_grupo de ZonaGrupo
	 *
	 * @var string|null
	 */
	 private string|null $snombre_grupo = null;
	/**
	 * Orden de ZonaGrupo
	 *
	 * @var int|null
	 */
	 private int|null $iorden = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return ZonaGrupo
	 */
	public function setAllAttributes(array $aDatos): ZonaGrupo
	{
		if (array_key_exists('id_grupo',$aDatos))
		{
			$this->setId_grupo($aDatos['id_grupo']);
		}
		if (array_key_exists('nombre_grupo',$aDatos))
		{
			$this->setNombre_grupo($aDatos['nombre_grupo']);
		}
		if (array_key_exists('orden',$aDatos))
		{
			$this->setOrden($aDatos['orden']);
		}
		return $this;
	}
	/**
	 *
	 * @return int $iid_grupo
	 */
	public function getId_grupo(): int
	{
		return $this->iid_grupo;
	}
	/**
	 *
	 * @param int $iid_grupo
	 */
	public function setId_grupo(int $iid_grupo): void
	{
		$this->iid_grupo = $iid_grupo;
	}
	/**
	 *
	 * @return string|null $snombre_grupo
	 */
	public function getNombre_grupo(): ?string
	{
		return $this->snombre_grupo;
	}
	/**
	 *
	 * @param string|null $snombre_grupo
	 */
	public function setNombre_grupo(?string $snombre_grupo = null): void
	{
		$this->snombre_grupo = $snombre_grupo;
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
}