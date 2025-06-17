<?php

namespace src\usuarios\domain\entity;

use src\usuarios\domain\value_objects\TipoPreferencia;
use src\usuarios\domain\value_objects\ValorPreferencia;

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
	 * @var TipoPreferencia
	 */
	 private TipoPreferencia $stipo;
	/**
	 * Preferencia de Preferencia
	 *
	 * @var ValorPreferencia|null
	 */
	 private ?ValorPreferencia $spreferencia = null;
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
			// Check if it's already a TipoPreferencia object
			if ($aDatos['tipo'] instanceof TipoPreferencia) {
				$this->setTipo($aDatos['tipo']);
			} else {
				$this->setTipo(new TipoPreferencia($aDatos['tipo']));
			}
		}
		if (array_key_exists('preferencia',$aDatos))
		{
			if ($aDatos['preferencia'] === null) {
				$this->setPreferencia(null);
			} else {
				// Check if it's already a ValorPreferencia object
				if ($aDatos['preferencia'] instanceof ValorPreferencia) {
					$this->setPreferencia($aDatos['preferencia']);
				} else {
					$this->setPreferencia(new ValorPreferencia($aDatos['preferencia']));
				}
			}
		}
		if (array_key_exists('id_usuario',$aDatos))
		{
			$this->setId_usuario($aDatos['id_usuario']);
		}
		return $this;
	}
	/**
	 *
	 * @return TipoPreferencia
	 */
	public function getTipo(): TipoPreferencia
	{
		return $this->stipo;
	}
	/**
	 *
	 * @param TipoPreferencia $stipo
	 */
	public function setTipo(TipoPreferencia $stipo): void
	{
		$this->stipo = $stipo;
	}

	/**
	 * Get the preference type as a string
	 *
	 * @return string
	 */
	public function getTipoAsString(): string
	{
		return $this->stipo->value();
	}
	/**
	 *
	 * @return ValorPreferencia|null
	 */
	public function getPreferencia(): ?ValorPreferencia
	{
		return $this->spreferencia;
	}
	/**
	 *
	 * @param ValorPreferencia|null $spreferencia
	 */
	public function setPreferencia(?ValorPreferencia $spreferencia = null): void
	{
		$this->spreferencia = $spreferencia;
	}

	/**
	 * Get the preference value as a string
	 *
	 * @return string|null
	 */
	public function getPreferenciaAsString(): ?string
	{
		return $this->spreferencia ? $this->spreferencia->value() : null;
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
