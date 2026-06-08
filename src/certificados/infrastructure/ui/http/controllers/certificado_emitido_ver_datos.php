<?php

use function src\shared\domain\helpers\input_int;

use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CertificadoEmitidoRepositoryInterface $certificadoEmitidoRepository */
$certificadoEmitidoRepository = DependencyResolver::get(CertificadoEmitidoRepositoryInterface::class);

$Qid_item = input_int($_POST, 'id_item');
$error_txt = '';
$data = [];

$oCertificadoEmitido = $certificadoEmitidoRepository->findById($Qid_item);
if ($oCertificadoEmitido === null) {
    $error_txt .= '<br>' . sprintf(_('No encuentro certificado emitido con id_item: %d'), $Qid_item);
    ContestarJson::enviar($error_txt, $data);
    return;
}

$id_nom = (int) ($oCertificadoEmitido->getId_nom() ?? 0);
$nom = (string) ($oCertificadoEmitido->getNom() ?? '');
$data['idioma'] = (string) ($oCertificadoEmitido->getIdiomaVo()?->value() ?? '');
$data['destino'] = (string) ($oCertificadoEmitido->getDestino() ?? '');
$data['certificado'] = (string) ($oCertificadoEmitido->getCertificado() ?? '');
$data['f_certificado'] = $oCertificadoEmitido->getF_certificado()?->getFromLocal() ?? '';
$data['f_enviado'] = $oCertificadoEmitido->getF_enviado()?->getFromLocal() ?? '';
$data['firmado'] = $oCertificadoEmitido->isFirmado();
$documento = $oCertificadoEmitido->getDocumento();
$data['content'] = ($documento !== null && $documento !== '') ? base64_encode($documento) : '';

$oPersona = Persona::findPersonaEnGlobal($id_nom);
$apellidos_nombre = $oPersona !== null ? $oPersona->getApellidosNombre() : '';
$data['nom'] = $nom === '' ? $apellidos_nombre : $nom;
$data['apellidos_nombre'] = $apellidos_nombre;
$data['id_nom'] = $id_nom;

ContestarJson::enviar($error_txt, $data);
