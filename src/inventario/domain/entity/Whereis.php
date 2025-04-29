<?php

namespace src\inventario\domain\entity;
/**
 * Clase que implementa la entidad i_whereis_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class Whereis {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_item_whereis de Whereis
	 *
	 * @var int
	 */
	 private int $iid_item_whereis;
	/**
	 * Id_item_egm de Whereis
	 *
	 * @var int|null
	 */
	 private int|null $iid_item_egm = null;
	/**
	 * Id_doc de Whereis
	 *
	 * @var int|null
	 */
	 private int|null $iid_doc = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return Whereis
	 */
	public function setAllAttributes(array $aDatos): Whereis
	{
		if (array_key_exists('id_item_whereis',$aDatos))
		{
			$this->setId_item_whereis($aDatos['id_item_whereis']);
		}
		if (array_key_exists('id_item_egm',$aDatos))
		{
			$this->setId_item_egm($aDatos['id_item_egm']);
		}
		if (array_key_exists('id_doc',$aDatos))
		{
			$this->setId_doc($aDatos['id_doc']);
		}
		return $this;
	}
	/**
	 *
	 * @return int $iid_item_whereis
	 */
	public function getId_item_whereis(): int
	{
		return $this->iid_item_whereis;
	}
	/**
	 *
	 * @param int $iid_item_whereis
	 */
	public function setId_item_whereis(int $iid_item_whereis): void
	{
		$this->iid_item_whereis = $iid_item_whereis;
	}
	/**
	 *
	 * @return int|null $iid_item_egm
	 */
	public function getId_item_egm(): ?int
	{
		return $this->iid_item_egm;
	}
	/**
	 *
	 * @param int|null $iid_item_egm
	 */
	public function setId_item_egm(?int $iid_item_egm = null): void
	{
		$this->iid_item_egm = $iid_item_egm;
	}
	/**
	 *
	 * @return int|null $iid_doc
	 */
	public function getId_doc(): ?int
	{
		return $this->iid_doc;
	}
	/**
	 *
	 * @param int|null $iid_doc
	 */
	public function setId_doc(?int $iid_doc = null): void
	{
		$this->iid_doc = $iid_doc;
	}

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_item_whereis';
    }
}