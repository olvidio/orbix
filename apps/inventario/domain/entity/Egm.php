<?php

namespace inventario\domain\entity;
/**
 * Clase que implementa la entidad i_egm_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class Egm {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_item de Egm
	 *
	 * @var int
	 */
	 private int $iid_item;
	/**
	 * Id_equipaje de Egm
	 *
	 * @var int|null
	 */
	 private int|null $iid_equipaje = null;
	/**
	 * Id_grupo de Egm
	 *
	 * @var int|null
	 */
	 private int|null $iid_grupo = null;
	/**
	 * Id_lugar de Egm
	 *
	 * @var int|null
	 */
	 private int|null $iid_lugar = null;
	/**
	 * Texto de Egm
	 *
	 * @var string|null
	 */
	 private string|null $stexto = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return Egm
	 */
	public function setAllAttributes(array $aDatos): Egm
	{
		if (array_key_exists('id_item',$aDatos))
		{
			$this->setId_item($aDatos['id_item']);
		}
		if (array_key_exists('id_equipaje',$aDatos))
		{
			$this->setId_equipaje($aDatos['id_equipaje']);
		}
		if (array_key_exists('id_grupo',$aDatos))
		{
			$this->setId_grupo($aDatos['id_grupo']);
		}
		if (array_key_exists('id_lugar',$aDatos))
		{
			$this->setId_lugar($aDatos['id_lugar']);
		}
		if (array_key_exists('texto',$aDatos))
		{
			$this->setTexto($aDatos['texto']);
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
	 * @return int|null $iid_equipaje
	 */
	public function getId_equipaje(): ?int
	{
		return $this->iid_equipaje;
	}
	/**
	 *
	 * @param int|null $iid_equipaje
	 */
	public function setId_equipaje(?int $iid_equipaje = null): void
	{
		$this->iid_equipaje = $iid_equipaje;
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
	 * @return int|null $iid_lugar
	 */
	public function getId_lugar(): ?int
	{
		return $this->iid_lugar;
	}
	/**
	 *
	 * @param int|null $iid_lugar
	 */
	public function setId_lugar(?int $iid_lugar = null): void
	{
		$this->iid_lugar = $iid_lugar;
	}
	/**
	 *
	 * @return string|null $stexto
	 */
	public function getTexto(): ?string
	{
		return $this->stexto;
	}
	/**
	 *
	 * @param string|null $stexto
	 */
	public function setTexto(?string $stexto = null): void
	{
		$this->stexto = $stexto;
	}

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_item';
    }
}