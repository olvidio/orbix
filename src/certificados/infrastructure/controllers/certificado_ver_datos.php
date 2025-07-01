<?php

use personas\model\entity\Persona;
use src\certificados\application\repositories\CertificadoRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_item = (string)filter_input(INPUT_POST, 'id_item');

$error_txt = '';

$CertificadoRepository = new CertificadoRepository();
$oCertificado = $CertificadoRepository->findById($Qid_item);

$id_nom = $oCertificado->getId_nom();
$nom = $oCertificado->getNom();
$data['idioma'] = $oCertificado->getIdioma();
$data['destino'] = $oCertificado->getDestino();
$data['certificado'] = $oCertificado->getCertificado();
$data['f_certificado'] = $oCertificado->getF_certificado()->getFromLocal();
$data['f_enviado'] = $oCertificado->getF_enviado()->getFromLocal();
$data['firmado'] = $oCertificado->isFirmado();
$data['content'] = $oCertificado->getDocumento();

$oPersona = Persona::NewPersona($id_nom);
$apellidos_nombre = $oPersona->getApellidosNombre();
$data['nom'] = empty($nom) ? $apellidos_nombre : $nom;
$data['apellidos_nombre'] = $apellidos_nombre;
$data['id_nom'] = $id_nom;

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);