<?php

use personas\model\entity\Persona;
use src\certificados\application\repositories\CertificadoEmitidoRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_item = (int)filter_input(INPUT_POST, 'id_item');

$error_txt = '';

$certificadoEmitidoRepository = new CertificadoEmitidoRepository();
$oCertificadoEmitido = $certificadoEmitidoRepository->findById($Qid_item);

$id_nom = $oCertificadoEmitido->getId_nom();
$nom = $oCertificadoEmitido->getNom();
$data['idioma'] = $oCertificadoEmitido->getIdioma();
$data['destino'] = $oCertificadoEmitido->getDestino();
$data['certificado'] = $oCertificadoEmitido->getCertificado();
$data['f_certificado'] = $oCertificadoEmitido->getF_certificado()->getFromLocal();
$data['f_enviado'] = $oCertificadoEmitido->getF_enviado()->getFromLocal();
$data['firmado'] = $oCertificadoEmitido->isFirmado();
$data['content'] = base64_encode($oCertificadoEmitido->getDocumento());

$oPersona = Persona::NewPersona($id_nom);
$apellidos_nombre = $oPersona->getApellidosNombre();
$data['nom'] = empty($nom) ? $apellidos_nombre : $nom;
$data['apellidos_nombre'] = $apellidos_nombre;
$data['id_nom'] = $id_nom;

// envía una Response
ContestarJson::enviar($error_txt, $data);