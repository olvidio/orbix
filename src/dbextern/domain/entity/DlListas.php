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

    function getDl(): string {
        return $this->sDl;
    }

    function setDl(string $sDl): void
    {
        $this->sDl = $sDl;
    }

    function getNombre_dl(): string {
        return $this->sNombre_dl;
    }

    function setNombre_dl(string $sNombre_dl): void
    {
        $this->sNombre_dl = $sNombre_dl;
    }

    function getAbr_r(): string {
        return $this->sAbr_r;
    }

    function setAbr_r(string $sAbr_r): void
    {
        $this->sAbr_r = $sAbr_r;
    }

    function getNumero_dl(): int {
        return $this->iNumero_dl;
    }

    function setNumero_dl(int $iNumero_dl): void
    {
        $this->iNumero_dl = $iNumero_dl;
    }

    function getNumero_r(): int {
        return $this->iNumero_r;
    }

    function setNumero_r(int $iNumero_r): void
    {
        $this->iNumero_r = $iNumero_r;
    }

}