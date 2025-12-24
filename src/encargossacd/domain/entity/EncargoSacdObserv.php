<?php

namespace src\encargossacd\domain\entity;

use src\encargossacd\domain\value_objects\ObservText;

/**
 * Clase que implementa la entidad encargo_sacd_observ
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
class EncargoSacdObserv
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de EncargoSacdObserv
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_nom de EncargoSacdObserv
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * Observ de EncargoSacdObserv
     *
     * @var ObservText|null
     */
    private ObservText|null $sobserv = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return EncargoSacdObserv
     */
    public function setAllAttributes(array $aDatos): EncargoSacdObserv
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
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
     * @return int $iid_nom
     */
    public function getId_nom(): int
    {
        return $this->iid_nom;
    }

    /**
     *
     * @param int $iid_nom
     */
    public function setId_nom(int $iid_nom): void
    {
        $this->iid_nom = $iid_nom;
    }
    /**
     *
     * @return string|null $sobserv
     */
    /**
     * @deprecated Usar `getObservVo(): ?ObservText` en su lugar.
     */
    public function getObserv(): ?string
    {
        return $this->sobserv?->value();
    }
    /**
     *
     * @param string|null $sobserv
     */
    /**
     * @deprecated Usar `setObservVo(?ObservText $vo): void` en su lugar.
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = $sobserv !== null ? new ObservText($sobserv) : null;
    }

    public function getObservVo(): ?ObservText
    {
        return $this->sobserv;
    }

    public function setObservVo(?ObservText $vo): void
    {
        $this->sobserv = $vo;
    }
}