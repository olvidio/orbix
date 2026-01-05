<?php

namespace src\actividadcargos\domain\entity;

class CargoOAsistente
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    private int $id_activ;
    private int $id_nom;
    private bool $propio;
    private int $id_cargo;

    function __construct(int $id_activ)
    {
        $this->id_activ = $id_activ;
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    function getId_activ()
    {
        return $this->id_activ;
    }

    function setId_activ($id_activ)
    {
        $this->id_activ = $id_activ;
    }

    function getId_nom()
    {
        return $this->id_nom;
    }

    function setId_nom($id_nom)
    {
        $this->id_nom = $id_nom;
    }

    function isPropio()
    {
        return $this->propio;
    }

    function setPropio($bpropio = 'f')
    {
        $this->propio = $bpropio;
    }

    function getId_cargo()
    {
        return $this->id_cargo;
    }

    function setId_cargo($id_cargo)
    {
        $this->id_cargo = $id_cargo;
    }

}