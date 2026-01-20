<?php

namespace src\ubis\application\services;


use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;
use src\ubis\domain\contracts\TelecoUbiRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrRepositoryInterface;

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
                $repoCasaDireccion = $GLOBALS['container']->get(RelacionCentroDireccionRepositoryInterface::class);
                $repoDireccion = $GLOBALS['container']->get(DireccionCentroRepositoryInterface::class);
                break;
            case 'CentroDl':
                $repoCasaDireccion = $GLOBALS['container']->get(RelacionCentroDlDireccionRepositoryInterface::class);
                $repoDireccion = $GLOBALS['container']->get(DireccionCentroDlRepositoryInterface::class);
                break;
            case 'CentroEx':
                $repoCasaDireccion = $GLOBALS['container']->get(RelacionCentroExDireccionRepositoryInterface::class);
                $repoDireccion = $GLOBALS['container']->get(DireccionCentroExRepositoryInterface::class);
                break;
            case 'Casa':
                $repoCasaDireccion = $GLOBALS['container']->get(RelacionCasaDireccionRepositoryInterface::class);
                $repoDireccion = $GLOBALS['container']->get(DireccionCasaRepositoryInterface::class);
                break;
            case 'CasaDl':
                $repoCasaDireccion = $GLOBALS['container']->get(RelacionCasaDlDireccionRepositoryInterface::class);
                $repoDireccion = $GLOBALS['container']->get(DireccionCasaDlRepositoryInterface::class);
                break;
            case 'CasaEx':
                $repoCasaDireccion = $GLOBALS['container']->get(RelacionCasaExDireccionRepositoryInterface::class);
                $repoDireccion = $GLOBALS['container']->get(DireccionCasaExRepositoryInterface::class);
                break;
        }
        $cUbixDireccion = $repoCasaDireccion->getDireccionesPorUbi($this->getIdUbiVo()->value());
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
                $TelecoUbiRepository = $GLOBALS['container']->get(TelecoCtrRepositoryInterface::class);
                break;
            case 'CentroDl':
                $TelecoUbiRepository = $GLOBALS['container']->get(TelecoCtrDlRepositoryInterface::class);
                break;
            case 'CentroEx':
                $TelecoUbiRepository = $GLOBALS['container']->get(TelecoCtrExRepositoryInterface::class);
                break;
            case 'Casa':
                $TelecoUbiRepository = $GLOBALS['container']->get(TelecoUbiRepositoryInterface::class);
                break;
            case 'CasaDl':
                $TelecoUbiRepository = $GLOBALS['container']->get(TelecoCdcDlRepositoryInterface::class);
                break;
            case 'CasaEx':
                $TelecoUbiRepository = $GLOBALS['container']->get(TelecoCdcExRepositoryInterface::class);
                break;
        }
        $aWhere['id_ubi'] = $this->getId_ubi();
        //case 'e-mail':
        $id_tipo_teleco = 3;
        $aWhere['id_tipo_teleco'] = $id_tipo_teleco;

        if ($desc_teleco !== 13) {
            $aWhere['id_desc_teleco'] = $desc_teleco;
        }

        $e_mail = '';
        $cTelecos = $TelecoUbiRepository->getTelecos($aWhere);
        if (!empty($cTelecos) && count($cTelecos) > 0) {
            $oTeleco = $cTelecos[0];
            $e_mail = $oTeleco->getNumTelecoVo()->value();
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
                $TelecoUbiRepository = $GLOBALS['container']->get(TelecoCtrRepositoryInterface::class);
                break;
            case 'CentroDl':
                $TelecoUbiRepository = $GLOBALS['container']->get(TelecoCtrDlRepositoryInterface::class);
                break;
            case 'CentroEx':
                $TelecoUbiRepository = $GLOBALS['container']->get(TelecoCtrExRepositoryInterface::class);
                break;
            case 'Casa':
                $TelecoUbiRepository = $GLOBALS['container']->get(TelecoUbiRepositoryInterface::class);
                break;
            case 'CasaDl':
                $TelecoUbiRepository = $GLOBALS['container']->get(TelecoCdcDlRepositoryInterface::class);
                break;
            case 'CasaEx':
                $TelecoUbiRepository = $GLOBALS['container']->get(TelecoCdcExRepositoryInterface::class);
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
            $aWhere['id_desc_teleco'] = $desc_teleco;
        }
        $cTelecos = $TelecoUbiRepository->getTelecos($aWhere);
        $tels = '';
        $separador = empty($separador) ? ".-<br>" : $separador;
        if ($cTelecos !== false) {
            $DescTelecoRepository = $GLOBALS['container']->get(DescTelecoRepositoryInterface::class);
            foreach ($cTelecos as $oTelecoUbi) {
                $iDescTel = $oTelecoUbi->getId_desc_teleco();
                $num_teleco = trim($oTelecoUbi->getNumTelecoVo()->value());
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