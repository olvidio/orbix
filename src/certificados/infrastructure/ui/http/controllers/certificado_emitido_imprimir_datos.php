<?php

use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\personas\domain\entity\Persona;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\web\ContestarJson;

$id_nom = (int)filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);

$error_txt = '';
$data = [];

/** @var ConfigSnapshot $oConfig */
$oConfig = $_SESSION['oConfig'];
$error_txt .= $oConfig->formatMissingParametersMessage([
    _('nombre región en latín') => $oConfig->regionLatin,
    _('vstgr') => $oConfig->vstgr,
    _('direccion stgr') => $oConfig->dirStgr,
    _('lugar firma') => $oConfig->lugarFirma,
    _('inicio contador certificados') => $oConfig->iniContadorCertificados,
]);

if ($error_txt === '') {
    $oPersona = Persona::findPersonaEnGlobal($id_nom);
    if ($oPersona === null) {
        $error_txt .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ': line ' . __LINE__;
    } else {
        $data['nombreApellidos'] = $oPersona->getNombreApellidos();
        $data['lugar_nacimiento'] = (string)($oPersona->getLugarNacimientoVo()?->value() ?? $oPersona->getLugar_nacimiento() ?? '');
        $data['f_nacimiento'] = (string)($oPersona->getF_nacimiento()?->getFechaLatin() ?? '');
        $data['nivel_stgr'] = $oPersona->getNivelStgrVo()?->value() ?? '';

        $data['region_latin'] = (string)$oConfig->regionLatin;
        $data['vstgr'] = (string)$oConfig->vstgr;
        $data['dir_stgr'] = (string)$oConfig->dirStgr;
        $data['lugar_firma'] = (string)$oConfig->lugarFirma;
        $data['contador'] = (string)$oConfig->iniContadorCertificados;

        $oHoy = new DateTimeLocal();
        $data['f_certificado'] = $oHoy->getFromLocal();
        $data['any_2digit'] = $oHoy->format('y');
    }
}

ContestarJson::enviar($error_txt, $data);