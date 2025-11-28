<?php

namespace personas\model;

// necesario para los desplegables de 'depende'

/* No vale el underscore en el nombre */

use core\DatosInfo;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use web\Desplegable;

class Info1001 extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("telecomunicaciones de una persona"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta teleco?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        // No hace falta definir, porque ya se sobreescribe el metodo setObj_pau().
        //$this->setClase('profesores\\model\\ProfesorPublicacion');
        $this->setMetodoGestor('getTelecos');
        $this->setPau('p');
    }

    public function getId_dossier()
    {
        return 1004;
    }

    public function setObj_pau($obj_pau)
    {
        switch ($obj_pau) {
            case 'PersonaN':
            case 'PersonaNax':
            case 'PersonaAgd':
            case 'PersonaS':
            case 'PersonaSSSC':
            case 'PersonaDl':
            case 'TelecoPersonaDl':
                $this->obj = 'personas\\model\\entity\\TelecoPersonaDl';
                break;
            case 'TelecoPersonaEX':
                $this->obj = 'personas\\model\\entity\\TelecoPersonaEx';
                break;
        }
    }

    public function getDespl_depende()
    {
        $oFicha = $this->getFicha();
        $despl_depende = "<option></option>";
        // para el desplegable depende
        $v1 = $oFicha->tipo_teleco;
        $v2 = $oFicha->desc_teleco;
        if (!empty($v2)) {
            $oDepende = $GLOBALS['container']->get(DescTelecoRepositoryInterface::class);
            $aOpciones = $oDepende->getArrayDescTelecoPersonas($v1);
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
            if ($this->accion === 'desc_teleco') {
                $oDepende = $GLOBALS['container']->get(DescTelecoRepositoryInterface::class);
                $aOpciones = $oDepende->getArrayDescTelecoPersonas($valor_depende);
                $oDesplegable = new Desplegable('', $aOpciones, '', true);
                $despl_depende = $oDesplegable->options();
            }
        }

        return $despl_depende;
    }
}
