<?php

use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\personas\domain\entity\Persona;
use web\ContestarJson;

$Qid_item = (int)filter_input(INPUT_POST, 'id_item');

$error_txt = '';

$certificadoEmitidoRepository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);
$oCertificadoEmitido = $certificadoEmitidoRepository->findById($Qid_item);

$id_nom = $oCertificadoEmitido->getId_nom();
$nom = $oCertificadoEmitido->getNomVo()->value();
$data['idioma'] = $oCertificadoEmitido->getIdiomaVo()->value();
$data['destino'] = $oCertificadoEmitido->getDestino();
$data['certificado'] = $oCertificadoEmitido->getCertificado();
$data['f_certificado'] = $oCertificadoEmitido->getF_certificado()?->getFromLocal();
$data['f_enviado'] = $oCertificadoEmitido->getF_enviado()?->getFromLocal();
$data['firmado'] = $oCertificadoEmitido->isFirmado();
$data['content'] = base64_encode($oCertificadoEmitido->getDocumento());

$oPersona = Persona::findPersonaEnGlobal($id_nom);
$apellidos_nombre = $oPersona->getApellidosNombre();
$data['nom'] = empty($nom) ? $apellidos_nombre : $nom;
$data['apellidos_nombre'] = $apellidos_nombre;
$data['id_nom'] = $id_nom;

// envía una Response
ContestarJson::enviar($error_txt, $data);