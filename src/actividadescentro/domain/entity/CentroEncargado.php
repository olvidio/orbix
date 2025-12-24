<?php

namespace src\actividadescentro\domain\entity;
/**
 * Clase que implementa la entidad da_ctr_encargados
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/12/2025
 */
class CentroEncargado {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_activ de CentroEncargado
	 *
	 * @var int
	 */
	 private int $iid_activ;
	/**
	 * Id_ubi de CentroEncargado
	 *
	 * @var int
	 */
	 private int $iid_ubi;
	/**
	 * Num_orden de CentroEncargado
	 *
	 * @var int|null
	 */
	 private int|null $inum_orden = null;
	/**
	 * Encargo de CentroEncargado
	 *
	 * @var string|null
	 */
	 private string|null $sencargo = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return CentroEncargado
	 */
	public function setAllAttributes(array $aDatos): CentroEncargado
	{
		if (array_key_exists('id_activ',$aDatos))
		{
			$this->setId_activ($aDatos['id_activ']);
		}
		if (array_key_exists('id_ubi',$aDatos))
		{
			$this->setId_ubi($aDatos['id_ubi']);
		}
		if (array_key_exists('num_orden',$aDatos))
		{
			$this->setNum_orden($aDatos['num_orden']);
		}
		if (array_key_exists('encargo',$aDatos))
		{
			$this->setEncargo($aDatos['encargo']);
		}
		return $this;
	}
	/**
	 *
	 * @return int $iid_activ
	 */
	public function getId_activ(): int
	{
		return $this->iid_activ;
	}
	/**
	 *
	 * @param int $iid_activ
	 */
	public function setId_activ(int $iid_activ): void
	{
		$this->iid_activ = $iid_activ;
	}
	/**
	 *
	 * @return int $iid_ubi
	 */
	public function getId_ubi(): int
	{
		return $this->iid_ubi;
	}
	/**
	 *
	 * @param int $iid_ubi
	 */
	public function setId_ubi(int $iid_ubi): void
	{
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 *
	 * @return int|null $inum_orden
	 */
	public function getNum_orden(): ?int
	{
		return $this->inum_orden;
	}
	/**
	 *
	 * @param int|null $inum_orden
	 */
	public function setNum_orden(?int $inum_orden = null): void
	{
		$this->inum_orden = $inum_orden;
	}
	/**
	 *
	 * @return string|null $sencargo
	 */
	public function getEncargo(): ?string
	{
		return $this->sencargo;
	}
	/**
	 *
	 * @param string|null $sencargo
	 */
	public function setEncargo(?string $sencargo = null): void
	{
		$this->sencargo = $sencargo;
	}
}