<?php

namespace src\inventario\domain\entity;
use src\inventario\domain\value_objects\{WhereisItemId, WhereisItemEgmId, WhereisDocId};
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

    // Value Object API (duplicada con legacy)
    public function getIdItemWhereisVo(): WhereisItemId
    {
        return new WhereisItemId($this->iid_item_whereis);
    }

    public function setIdItemWhereisVo(?WhereisItemId $id = null): void
    {
        if ($id === null) { return; }
        $this->iid_item_whereis = $id->value();
    }

    public function getIdItemEgmVo(): ?WhereisItemEgmId
    {
        return $this->iid_item_egm !== null ? new WhereisItemEgmId($this->iid_item_egm) : null;
    }

    public function setIdItemEgmVo(?WhereisItemEgmId $id = null): void
    {
        $this->iid_item_egm = $id?->value();
    }

    public function getIdDocVo(): ?WhereisDocId
    {
        return $this->iid_doc !== null ? new WhereisDocId($this->iid_doc) : null;
    }

    public function setIdDocVo(?WhereisDocId $id = null): void
    {
        $this->iid_doc = $id?->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_item_whereis';
    }
}