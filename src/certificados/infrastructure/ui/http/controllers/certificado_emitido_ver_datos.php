<?php

use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\shared\web\ContestarJson;

$Qid_item = (int)filter_input(INPUT_POST, 'id_item');

$error_txt = '';

$certificadoEmitidoRepository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);
$oCertificadoEmitido = $certificadoEmitidoRepository->findById($Qid_item);
$data = [];

if ($oCertificadoEmitido === null) {
    $error_txt .= '<br>' . sprintf(
        _('No encuentro certificado emitido con id_item: %d'),
        $Qid_item
    );
    ContestarJson::enviar($error_txt, $data);
    return;
}

$id_nom = $oCertificadoEmitido->getId_nom();
$nom = $oCertificadoEmitido->getNom();
$data['idioma'] = (string)($oCertificadoEmitido->getIdiomaVo()?->value() ?? '');
$data['destino'] = (string)($oCertificadoEmitido->getDestino() ?? '');
$data['certificado'] = (string)($oCertificadoEmitido->getCertificado() ?? '');
$data['f_certificado'] = (string)($oCertificadoEmitido->getF_certificado()?->getFromLocal() ?? '');
$data['f_enviado'] = (string)($oCertificadoEmitido->getF_enviado()?->getFromLocal() ?? '');
$data['firmado'] = (bool)($oCertificadoEmitido->isFirmado() ?? false);
$documento = $oCertificadoEmitido->getDocumento();
$data['content'] = ($documento !== null && $documento !== '') ? base64_encode($documento) : '';

$oPersona = Persona::findPersonaEnGlobal($id_nom);
$apellidos_nombre = '';
if ($oPersona !== null) {
    $apellidos_nombre = $oPersona->getApellidosNombre();
}
$data['nom'] = empty($nom) ? $apellidos_nombre : $nom;
$data['apellidos_nombre'] = $apellidos_nombre;
$data['id_nom'] = $id_nom;

// envía una Response
ContestarJson::enviar($error_txt, $data);