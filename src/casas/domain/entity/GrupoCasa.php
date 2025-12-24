<?php

namespace src\casas\domain\entity;


/**
 * Clase que implementa la entidad du_grupos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 22/12/2025
 */
class GrupoCasa
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de GrupoCasa
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_ubi_padre de GrupoCasa
     *
     */
    private int $iid_ubi_padre;
    /**
     * Id_ubi_hijo de GrupoCasa
     *
     */
    private int $iid_ubi_hijo;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return GrupoCasa
     */
    public function setAllAttributes(array $aDatos): GrupoCasa
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_ubi_padre', $aDatos)) {
            $this->setId_ubi_padre($aDatos['id_ubi_padre']);
        }
        if (array_key_exists('id_ubi_hijo', $aDatos)) {
            $this->setId_ubi_hijo($aDatos['id_ubi_hijo']);
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
     * @return int $iid_ubi_padre
     */
    public function getId_ubi_padre(): int
    {
        return $this->iid_ubi_padre;
    }

    /**
     *
     * @param int $iid_ubi_padre
     */
    public function setId_ubi_padre(int $iid_ubi_padre): void
    {
        $this->iid_ubi_padre = $iid_ubi_padre;
    }

    /**
     *
     * @return int $iid_ubi_hijo
     */
    public function getId_ubi_hijo(): int
    {
        return $this->iid_ubi_hijo;
    }

    /**
     *
     * @param int $iid_ubi_hijo
     */
    public function setId_ubi_hijo(int $iid_ubi_hijo): void
    {
        $this->iid_ubi_hijo = $iid_ubi_hijo;
    }
}