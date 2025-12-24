<?php

namespace src\encargossacd\domain\entity;
/**
 * Clase que implementa la entidad encargo_textos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
class EncargoTexto
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de EncargoTexto
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Idioma de EncargoTexto
     *
     * @var string
     */
    private string $sidioma;
    /**
     * Clave de EncargoTexto
     *
     * @var string
     */
    private string $sclave;
    /**
     * Texto de EncargoTexto
     *
     * @var string|null
     */
    private string|null $stexto = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return EncargoTexto
     */
    public function setAllAttributes(array $aDatos): EncargoTexto
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('idioma', $aDatos)) {
            $this->setIdioma($aDatos['idioma']);
        }
        if (array_key_exists('clave', $aDatos)) {
            $this->setClave($aDatos['clave']);
        }
        if (array_key_exists('texto', $aDatos)) {
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
     * @return string $sidioma
     */
    public function getIdioma(): string
    {
        return $this->sidioma;
    }

    /**
     *
     * @param string $sidioma
     */
    public function setIdioma(string $sidioma): void
    {
        $this->sidioma = $sidioma;
    }

    /**
     *
     * @return string $sclave
     */
    public function getClave(): string
    {
        return $this->sclave;
    }

    /**
     *
     * @param string $sclave
     */
    public function setClave(string $sclave): void
    {
        $this->sclave = $sclave;
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
}