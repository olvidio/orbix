<?php

namespace src\usuarios\domain\entity;
/**
 * Clase que implementa la entidad web_preferencias
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class Preferencia {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Tipo de Preferencia
	 *
	 * @var string
	 */
	 private string $stipo;
	/**
	 * Preferencia de Preferencia
	 *
	 * @var string|null
	 */
	 private string|null $spreferencia = null;
	/**
	 * Id_usuario de Preferencia
	 *
	 * @var int
	 */
	 private int $iid_usuario;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return Preferencia
	 */
	public function setAllAttributes(array $aDatos): Preferencia
	{
		if (array_key_exists('tipo',$aDatos))
		{
			$this->setTipo($aDatos['tipo']);
		}
		if (array_key_exists('preferencia',$aDatos))
		{
			$this->setPreferencia($aDatos['preferencia']);
		}
		if (array_key_exists('id_usuario',$aDatos))
		{
			$this->setId_usuario($aDatos['id_usuario']);
		}
		return $this;
	}
	/**
	 *
	 * @return string $stipo
	 */
	public function getTipo(): string
	{
		return $this->stipo;
	}
	/**
	 *
	 * @param string $stipo
	 */
	public function setTipo(string $stipo): void
	{
		$this->stipo = $stipo;
	}
	/**
	 *
	 * @return string|null $spreferencia
	 */
	public function getPreferencia(): ?string
	{
		return $this->spreferencia;
	}
	/**
	 *
	 * @param string|null $spreferencia
	 */
	public function setPreferencia(?string $spreferencia = null): void
	{
		$this->spreferencia = $spreferencia;
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
}