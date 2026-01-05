<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace actividades\model;

use core\ConfigGlobal;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use web\Desplegable;

/**
 * Description of actividadlugar
 *
 * @author Daniel Serrabou <dani@moneders.net>
 */
class ActividadLugar
{

    private $isfsv;
    private $ssfsv;
    private $opcion_sel;

    public function getFiltroLugar($id_ubi)
    {

        //dl|dlb,r|Aut
        $oCasa = $GLOBALS['container']->get(CasaRepositoryInterface::class)->findById($id_ubi);
        $dl = $oCasa->getDl();
        $reg = $oCasa->getRegion();

        if (empty($dl)) {
            $filtro_lugar = 'r|' . $reg;
        } else {
            $filtro_lugar = 'dl|' . $dl;
        }
        return $filtro_lugar;
    }

    public function getLugaresPosibles($Qentrada = '')
    {
        $donde_sfsv = '';
        if (empty($Qentrada)) die();

        $dl_r = strtok($Qentrada, "|");
        $reg = strtok("|");
        // las regiones pequeñas, las cr se tratan como dl (p. ej: crBel)
        $cr = substr($reg, 0, 2);
        if ($cr === 'cr') {
            $dl_r = 'r';
            $reg = substr($reg, 2);
        }
        // En el caso de sf, $reg acaba en 'f' (dlbf)
        $reg_no_f = preg_replace('/(\.*)f$/', '\1', $reg);

        if ($this->ssfsv === 'sv') $this->isfsv = 1;
        if ($this->ssfsv === 'sf') $this->isfsv = 2;
        $isfsv = empty($this->isfsv) ? ConfigGlobal::mi_sfsv() : $this->isfsv;
        switch ($isfsv) {
            case 1:
                $donde_sfsv = "AND sv='t' ";
                break;
            case 2:
                $donde_sfsv = "AND sf='t' ";
                break;
        }
        // Casas
        $donde = '';
        switch ($dl_r) {
            case "dl":
                $donde = "WHERE dl='$reg_no_f' ";
                break;
            case "r":
                $donde = "WHERE region='$reg_no_f' ";
                break;
        }
        $donde .= $donde_sfsv;

        if ($dl_r !== "dl" and $dl_r !== "r") {
            $donde = "";
        }
        if (!empty($donde)) {
            $donde .= " AND active='t'";
        } else {
            $donde = "WHERE active='t'";
        }
        $CasaRepository = $GLOBALS['container']->get(CasaRepositoryInterface::class);
        $oOpcionesCasas = $CasaRepository->getArrayCasas($donde);

        // Centros (hay una copia en BD comun)
        $donde_ctr = '';
        switch ($dl_r) {
            case "dl":
                $donde_ctr = "dl='$reg' ";
                break;
            case "r":
                $donde_ctr = "region='$reg_no_f' ";
                break;
        }
        $donde_ctr .= $donde_sfsv;

        $centroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
        $oOpcionesCentros = $centroRepository->getArrayCentrosCdc($donde_ctr);

        $oOpcionesTotal = $oOpcionesCasas + $oOpcionesCentros;

        $oDesplCasas = new Desplegable(array('oOpciones' => $oOpcionesTotal));
        $oDesplCasas->setNombre('id_ubi');
        $oDesplCasas->setBlanco(true);
        if (!empty($this->opcion_sel)) {
            $oDesplCasas->setOpcion_sel($this->opcion_sel);
        }
        return $oDesplCasas;

    }

    public function setIsfsv($isfsv)
    {
        $this->isfsv = $isfsv;
    }

    public function setSsfsv($ssfsv)
    {
        $this->ssfsv = $ssfsv;
    }

    public function setOpcion_sel($opcion_sel)
    {
        $this->opcion_sel = $opcion_sel;
    }
}