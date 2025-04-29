<?php

namespace src\configuracion\domain\entity;
/**
 * Clase que implementa la entidad m0_modulos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class Modulo {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_mod de Modulo
	 *
	 * @var int
	 */
	 private int $iid_mod;
	/**
	 * Nom de Modulo
	 *
	 * @var string
	 */
	 private string $snom;
	/**
	 * Descripcion de Modulo
	 *
	 * @var string|null
	 */
	 private string|null $sdescripcion = null;
	/**
	 * Mods_req de Modulo
	 *
	 * @var array|null
	 */
	 private array|null $a_mods_req = null;
	/**
	 * Apps_req de Modulo
	 *
	 * @var array|null
	 */
	 private array|null $a_apps_req = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return Modulo
	 */
	public function setAllAttributes(array $aDatos): Modulo
	{
		if (array_key_exists('id_mod',$aDatos))
		{
			$this->setId_mod($aDatos['id_mod']);
		}
		if (array_key_exists('nom',$aDatos))
		{
			$this->setNom($aDatos['nom']);
		}
		if (array_key_exists('descripcion',$aDatos))
		{
			$this->setDescripcion($aDatos['descripcion']);
		}
		if (array_key_exists('mods_req',$aDatos))
		{
			$this->setMods_req($aDatos['mods_req']);
		}
		if (array_key_exists('apps_req',$aDatos))
		{
			$this->setApps_req($aDatos['apps_req']);
		}
		return $this;
	}
	/**
	 *
	 * @return int $iid_mod
	 */
	public function getId_mod(): int
	{
		return $this->iid_mod;
	}
	/**
	 *
	 * @param int $iid_mod
	 */
	public function setId_mod(int $iid_mod): void
	{
		$this->iid_mod = $iid_mod;
	}
	/**
	 *
	 * @return string $snom
	 */
	public function getNom(): string
	{
		return $this->snom;
	}
	/**
	 *
	 * @param string $snom
	 */
	public function setNom(string $snom): void
	{
		$this->snom = $snom;
	}
	/**
	 *
	 * @return string|null $sdescripcion
	 */
	public function getDescripcion(): ?string
	{
		return $this->sdescripcion;
	}
	/**
	 *
	 * @param string|null $sdescripcion
	 */
	public function setDescripcion(?string $sdescripcion = null): void
	{
		$this->sdescripcion = $sdescripcion;
	}
	/**
	 *
	 * @return array|null $a_mods_req
	 */
	public function getMods_req(): array|null
	{
        return $this->a_mods_req;
	}
	/**
	 * 
	 * @param array|null $a_mods_req
	 */
	public function setMods_req(array $a_mods_req= null): void
	{
        $this->a_mods_req = $a_mods_req;
	}
	/**
	 *
	 * @return array|null $a_apps_req
	 */
	public function getApps_req(): array|null
	{
        return $this->a_apps_req;
	}
	/**
	 * 
	 * @param array|null $a_apps_req
	 */
	public function setApps_req(array $a_apps_req= null): void
	{
        $this->a_apps_req = $a_apps_req;
	}
}