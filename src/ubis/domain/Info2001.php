<?php

namespace src\ubis\domain;

use core\DatosInfo;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrRepositoryInterface;
use src\ubis\domain\contracts\TelecoUbiRepositoryInterface;
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

    public function getId_dossier(): int
    {
        return 2001;
    }

    public function setObj_pau($obj_pau): void
    {
        switch ($obj_pau) {
            case 'Centro':
                $this->obj = $GLOBALS['container']->get(TelecoCtrRepositoryInterface::class);
                break;
            case 'CentroDl':
                $this->obj = $GLOBALS['container']->get(TelecoCtrDlRepositoryInterface::class);
                break;
            case 'CentroEx':
                $this->obj = $GLOBALS['container']->get(TelecoCtrExRepositoryInterface::class);
                break;
            case 'Casa':
                $this->obj = $GLOBALS['container']->get(TelecoUbiRepositoryInterface::class);
                break;
            case 'CasaDl':
                $this->obj = $GLOBALS['container']->get(TelecoCdcDlRepositoryInterface::class);
                break;
            case 'CasaEx':
                $this->obj = $GLOBALS['container']->get(TelecoCdcExRepositoryInterface::class);
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
        $despl_depende = "<option></option>";
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
