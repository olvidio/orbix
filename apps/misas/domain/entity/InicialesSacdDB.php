<?php

namespace misas\domain\entity;

class InicialesSacdDB
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int|null $iid_nom = null;
    private string|null $siniciales = null;
    private string|null $scolor = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return InicialesSacdDB|InicialesSacd
     */
    public function setAllAttributes(array $aDatos): InicialesSacdDB|InicialesSacd
    {
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('iniciales', $aDatos)) {
            $this->setIniciales($aDatos['iniciales']);
        }
        if (array_key_exists('color', $aDatos)) {
            $this->setColor($aDatos['color']);
        }
        return $this;
    }


    /**
     *
     * @return int|null $iid_nom
     */
    public function getId_nom(): ?int
    {
        return $this->iid_nom;
    }

    /**
     *
     * @param int|null $iid_nom
     */
    public function setId_nom(?int $iid_nom = null): void
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     *
     * @return string|null $siniciales
     */
    public function getIniciales(): ?string
    {
        return $this->siniciales;
    }

    /**
     *
     * @param string|null $siniciales
     */
    public function setIniciales(?string $siniciales = null): void
    {
        $this->siniciales = $siniciales;
    }

    /**
     *
     * @return string|null $scolor
     */
    public function getColor(): ?string
    {
        return $this->scolor;
    }

    /**
     *
     * @param string|null $scolor
     */
    public function setColor(?string $scolor = null): void
    {
        $this->scolor = $scolor;
    }
}