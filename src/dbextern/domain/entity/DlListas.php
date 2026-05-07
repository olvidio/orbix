<?php

namespace src\dbextern\domain\entity;

use src\shared\domain\traits\Hydratable;

/**
 * Para las dl que están en la DBU
 *
 * @author dani
 *
 */
class DlListas
{
    use Hydratable;
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /*
     Dl vachar(5)
     nombre_dl varchar(30)
     numero_dl tinyinteger
     abr_r varchar(10)
     numero_r tinyinteger
     */

    private int $iNumero_dl;
    private int $iNumero_r;
    private string $sDl;
    private string $sNombre_dl;
    private string $sAbr_r;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    function getDl()
    {
        return $this->sDl;
    }

    function setDl($sDl)
    {
        $this->sDl = $sDl;
    }

    function getNombre_dl()
    {
        return $this->sNombre_dl;
    }

    function setNombre_dl($sNombre_dl)
    {
        $this->sNombre_dl = $sNombre_dl;
    }

    function getAbr_r()
    {
        return $this->sAbr_r;
    }

    function setAbr_r($sAbr_r)
    {
        $this->sAbr_r = $sAbr_r;
    }

    function getNumero_dl()
    {
        return $this->iNumero_dl;
    }

    function setNumero_dl($iNumero_dl)
    {
        $this->iNumero_dl = $iNumero_dl;
    }

    function getNumero_r()
    {
        return $this->iNumero_r;
    }

    function setNumero_r($iNumero_r)
    {
        $this->iNumero_r = $iNumero_r;
    }

}