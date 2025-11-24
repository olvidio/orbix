<?php

namespace src\ubis\application\services;

use src\ubis\application\repositories\DescTelecoRepository;

trait UbiContactsTrait
{
 // Forzamos a que la clase que use este trait tenga este método
    abstract public function getId_ubi();

    /**
     * Devuelve las direcciones de un ubi especificados por
     *
     * @return array de objetos Direccion
     *
     */
    public function getDireccionesGral($ordre = 'principal DESC')
    {
        $aClassName = explode('\\', get_called_class());
        $childClassName = end($aClassName);
        switch ($childClassName) {
            case 'Centro':
                $repoCasaDireccion = 'src\\ubis\\application\\repositories\\RelacionCentroDireccionRepository';
                $repoDireccion = 'src\\ubis\\application\\repositories\\DireccionCentroRepository';
                break;
            case 'CentroDl':
                $repoCasaDireccion = 'src\\ubis\\application\\repositories\\RelacionCentroDlDireccionRepository';
                $repoDireccion = 'src\\ubis\\application\\repositories\\DireccionCentroDlRepository';
                break;
            case 'CentroEx':
                $repoCasaDireccion = 'src\\ubis\\application\\repositories\\RelacionCentroExDireccionRepository';
                $repoDireccion = 'src\\ubis\\application\\repositories\\DireccionCentroExRepository';
                break;
            case 'Casa':
                $repoCasaDireccion = 'src\\ubis\\application\\repositories\\RelacionCasaDireccionRepository';
                $repoDireccion = 'src\\ubis\\application\\repositories\\DireccionCasaRepository';
                break;
            case 'CasaDl':
                $repoCasaDireccion = 'src\\ubis\\application\\repositories\\RelacionCasaDlDireccionRepository';
                $repoDireccion = 'src\\ubis\\application\\repositories\\DireccionCasaDlRepository';
                break;
            case 'CasaEx':
                $repoCasaDireccion = 'src\\ubis\\application\\repositories\\RelacionCasaExDireccionRepository';
                $repoDireccion = 'src\\ubis\\application\\repositories\\DireccionCasaExRepository';
                break;
        }
        $GesUbixDireccion = new $repoCasaDireccion();
        $cUbixDireccion = $GesUbixDireccion->getDireccionesPorUbi($this->getId_ubi());
        $dirs = [];
        if ($cUbixDireccion !== false) {
            foreach ($cUbixDireccion as $aUbixDireccion) {
                $id_direccion = $aUbixDireccion['id_direccion'];
                $dirs[] =  (new $repoDireccion())->findById($id_direccion);
            }
        }
        return $dirs;
    }

    /**
     * Devuelve el e-mail principal o primero de la lista de teleco de una persona
     *
     *    $desc_teleco en la tabla (DB: comun) public.xd_desc_teleco
     *
     *       13    e-mail    principal
     *       20    e-mail    gobierno
     *       15    e-mail    otros
     */
    public function emailPrincipalOPrimero($desc_teleco = 13)
    {
        $aClassName = explode('\\', get_called_class());
        $childClassName = end($aClassName);
        switch ($childClassName) {
            case 'Centro':
                $obj = 'src\ubis\application\repositories\TelecoCtrRepository';
                break;
            case 'CentroDl':
                $obj = 'src\ubis\application\repositories\TelecoCtrDlRepository';
                break;
            case 'CentroEx':
                $obj = 'src\ubis\application\repositories\TelecoCtrExRepository';
                break;
            case 'Casa':
                $obj = 'src\ubis\application\repositories\TelecoCdcRepository';
                break;
            case 'CasaDl':
                $obj = 'src\ubis\application\repositories\TelecoCdcDlRepository';
                break;
            case 'CasaEx':
                $obj = 'src\ubis\application\repositories\TelecoCdcExRepository';
                break;
        }
        $aWhere['id_ubi'] = $this->getId_ubi();
        //case 'e-mail':
        $id_tipo_teleco = 3;
        $aWhere['id_tipo_teleco'] = $id_tipo_teleco;

        if ($desc_teleco !== 13) {
            $aWhere['desc_teleco'] = $desc_teleco;
        }

        $e_mail = '';
        $GesTelecoUbis = new $obj();
        $cTelecos = $GesTelecoUbis->getTelecos($aWhere);
        if (!empty($cTelecos) && count($cTelecos) > 0) {
            $oTeleco = $cTelecos[0];
            $e_mail = $oTeleco->getNum_teleco();
        }
        return $e_mail;
    }

    /**
     * Devuelve los teleco de un ubi especificados por
     *
     *     parámetros $id_ubi,$tipo_teleco,$desc_teleco,$separador
     *
     *    Si $desc_teleco es '*', entonces se añade la descripción entre paréntesis
     *      al final del número...
     */
    function getTeleco($tipo_teleco, $desc_teleco, $separador)
    {
        $aClassName = explode('\\', get_called_class());
        $childClassName = end($aClassName);
        switch ($childClassName) {
            case 'Centro':
                $obj = 'src\ubis\application\repositories\TelecoCtrRepository';
                break;
            case 'CentroDl':
                $obj = 'src\ubis\application\repositories\TelecoCtrDlRepository';
                break;
            case 'CentroEx':
                $obj = 'src\ubis\application\repositories\TelecoCtrExRepository';
                break;
            case 'Casa':
                $obj = 'src\ubis\application\repositories\TelecoCdcRepository';
                break;
            case 'CasaDl':
                $obj = 'src\ubis\application\repositories\TelecoCdcDlRepository';
                break;
            case 'CasaEx':
                $obj = 'src\ubis\application\repositories\TelecoCdcExRepository';
                break;
        }
        $aWhere['id_ubi'] = $this->getId_ubi();
        switch ($tipo_teleco) {
            case 'telf':
                $id_tipo_teleco = 1;
                break;
            case 'fax':
                $id_tipo_teleco = 4;
                break;
            case 'e-mail':
                $id_tipo_teleco = 3;
                break;
        }
        $aWhere['id_tipo_teleco'] = $id_tipo_teleco;
        if ($desc_teleco !== '*' && !empty($desc_teleco)) {
            $aWhere['desc_teleco'] = $desc_teleco;
        }
        $GesTelecoUbis = new $obj();
        $cTelecos = $GesTelecoUbis->getTelecos($aWhere);
        $tels = '';
        $separador = empty($separador) ? ".-<br>" : $separador;
        if ($cTelecos !== false) {
            $DescTelecoRepository = new DescTelecoRepository();
            foreach ($cTelecos as $oTelecoUbi) {
                $iDescTel = $oTelecoUbi->getDesc_teleco();
                $num_teleco = trim($oTelecoUbi->getNum_teleco());
                if ($desc_teleco === "*" && !empty($iDescTel)) {
                    $oDescTel = $DescTelecoRepository->findById((int)$iDescTel);
                    $desc = $oDescTel?->getDescTelecoVo()?->value() ?? '';
                    $tels .= $num_teleco . "(" . $desc . ")" . $separador;
                } else {
                    $tels .= $num_teleco . $separador;
                }
            }
        }
        $tels = substr($tels, 0, -(strlen($separador)));
        return $tels;
    }
}