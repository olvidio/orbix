<?php

namespace ubis\model;

use core\DatosInfo;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrRepositoryInterface;
use web\Desplegable;

// necesario para los desplegables de 'depende'


/* No vale el underscore en el nombre */

class Info2001 extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("telecomunicaciones de un centro o casa"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta teleco?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        // No hace falta definir, porque ya se sobreescribe el metodo setObj_pau().
        //$this->setClase('profesores\\model\\ProfesorPublicacion');
        $this->setMetodoGestor('getTelecos');
        $this->setPau('u');
    }

    public function getId_dossier()
    {
        return 2001;
    }

    public function setObj_pau($obj_pau)
    {


        switch ($obj_pau) {
            case 'Centro':
                $this->obj = $GLOBALS['container']->get(TelecoCtrRepositoryInterface::class);
                $this->obj = 'src\\ubis\\application\\repositories\\TelecoCtrRepository';
                break;
            case 'CentroDl':
                $this->obj = $GLOBALS['container']->get(TelecoCtrDlRepositoryInterface::class);
                $this->obj = 'src\\ubis\\application\\repositories\\TelecoCtrDlRepository';
                break;
            case 'CentroEx':
                $this->obj = $GLOBALS['container']->get(TelecoCtrExRepositoryInterface::class);
                $this->obj = 'src\\ubis\\application\\repositories\\TelecoCtrExRepository';
                break;
            case 'Casa':
                $this->obj = $GLOBALS['container']->get(TelecoCdcRepositoryInterface::class);
                $this->obj = 'src\\ubis\\application\\repositories\\TelecoCdcRepository';
                break;
            case 'CasaDl':
                $this->obj = $GLOBALS['container']->get(TelecoCdcDlRepositoryInterface::class);
                $this->obj = 'src\\ubis\\application\\repositories\\TelecoCdcDlRepository';
                break;
            case 'CasaEx':
                $this->obj = $GLOBALS['container']->get(TelecoCdcExRepositoryInterface::class);
                $this->obj = 'src\\ubis\\application\\repositories\\TelecoCdcExRepository';
                break;
        }
    }

    public function getDespl_depende()
    {
        $oFicha = $this->getFicha();
        $despl_depende = "<option></option>";
        // para el desplegable depende
        $v1 = $oFicha->tipo_teleco;
        $v2 = $oFicha->id_desc_teleco;
        if (!empty($v2)) {
            $oDepende = $GLOBALS['container']->get(DescTelecoRepositoryInterface::class);
            $aOpciones = $oDepende->getArrayDescTelecoUbis($v1);
            $oDesplegable = new Desplegable('', $aOpciones, $v2, true);
            $despl_depende = $oDesplegable->options();
        } else {
            $despl_depende = "<option></option>";
        }
        return $despl_depende;
    }

    public function getAccion($valor_depende)
    {
        //caso de actualizar el campo depende
        if (isset($this->accion)) {
            if ($this->accion === 'id_desc_teleco') {
                $oDepende = $GLOBALS['container']->get(DescTelecoRepositoryInterface::class);
                $aOpciones = $oDepende->getArrayDescTelecoUbis($valor_depende);
                $oDesplegable = new Desplegable('', $aOpciones, '', true);
                $despl_depende = $oDesplegable->options();
            }
        }

        return $despl_depende;
    }
}
